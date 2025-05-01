<?php
session_start();

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'healthtrack';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

header('Content-Type: application/json');

// At the beginning of the file, add:
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'create':
            $uploadDir = 'uploads/medical_records/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $uploadedFiles = [];
            $errors = [];
            
            if (isset($_FILES['files'])) {
                foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {
                        $file_name = $_FILES['files']['name'][$key];
                        $file_size = $_FILES['files']['size'][$key];
                        $file_type = $_FILES['files']['type'][$key];
                        
                        // Sanitize filename
                        $safe_filename = preg_replace("/[^a-zA-Z0-9.-]/", "_", $file_name);
                        $new_file_name = uniqid() . '_' . $safe_filename;
                        $upload_path = $uploadDir . $new_file_name;
                        
                        if (move_uploaded_file($tmp_name, $upload_path)) {
                            try {
                                // Insert record into database
                                $stmt = $conn->prepare("INSERT INTO medical_records 
                                    (patient_id, file_name, file_path, file_type, file_size, upload_date) 
                                    VALUES (?, ?, ?, ?, ?, NOW())");
                                
                                if ($stmt->execute([
                                    $_SESSION['user_id'],
                                    $file_name,
                                    $upload_path,
                                    $file_type,
                                    $file_size
                                ])) {
                                    $uploadedFiles[] = $file_name;
                                } else {
                                    $errors[] = "Database error for file: $file_name";
                                    // If database insert fails, remove the uploaded file
                                    if (file_exists($upload_path)) {
                                        unlink($upload_path);
                                    }
                                }
                            } catch (PDOException $e) {
                                $errors[] = "Database error: " . $e->getMessage();
                                // Clean up file if database insert fails
                                if (file_exists($upload_path)) {
                                    unlink($upload_path);
                                }
                            }
                        } else {
                            $errors[] = "Failed to upload: $file_name";
                        }
                    } else {
                        $errors[] = "Error uploading file: " . $_FILES['files']['error'][$key];
                    }
                }
            } else {
                $errors[] = "No files were uploaded";
            }
            
            if (empty($errors)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Files uploaded successfully',
                    'files' => $uploadedFiles
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Some files failed to upload',
                    'errors' => $errors
                ]);
            }
            break;
            
        case 'delete':
            $record_id = $_POST['record_id'] ?? null;
            if ($record_id) {
                // Get file path before deletion
                $stmt = $conn->prepare("SELECT file_path FROM medical_records WHERE id = ? AND patient_id = ?");
                $stmt->execute([$record_id, $_SESSION['user_id']]);
                $record = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($record) {
                    // Delete file from storage
                    if (file_exists($record['file_path'])) {
                        unlink($record['file_path']);
                    }
                    
                    // Delete record from database
                    $stmt = $conn->prepare("DELETE FROM medical_records WHERE id = ? AND patient_id = ?");
                    $stmt->execute([$record_id, $_SESSION['user_id']]);
                    
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Record not found']);
                }
            }
            break;
        }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $patient_id = $_SESSION['user_id'];
    
    // Create uploads directory if it doesn't exist
    $upload_dir = 'uploads/medical_records/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . basename($file['name']);
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        try {
            // Insert record into database with file_type
            $stmt = $conn->prepare("INSERT INTO medical_records (patient_id, file_name, file_path, file_type, upload_date) 
                                  VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([
                $patient_id, 
                $file['name'], 
                $filepath,
                $file['type'] // Add the file type
            ]);
            
            echo json_encode(['success' => true, 'message' => 'File uploaded successfully']);
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
    }
    exit();
}

?>

