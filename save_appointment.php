<?php
session_start();
header('Content-Type: application/json');

// Add debugging
error_log('Received appointment request: ' . file_get_contents('php://input'));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'healthtrack';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get data from either POST or JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Add debug logging
    error_log('Decoded input: ' . print_r($input, true));
    
    if ($input === null) {
        // If not JSON, use POST data
        $doctor_id = $_POST['doctor_id'] ?? null;
        $appointment_date = $_POST['appointment_date'] ?? null;
        $appointment_time = $_POST['appointment_time'] ?? null;
        $symptoms = $_POST['symptoms'] ?? '';
    } else {
        // Use JSON data - match the frontend field names
        $doctor_id = $input['doctor_id'] ?? null;
        $appointment_date = $input['date'] ?? null;  // Changed from appointment_date
        $appointment_time = $input['time'] ?? null;  // Changed from appointment_time
        $symptoms = $input['symptoms'] ?? '';
    }
    
    $patient_id = $_SESSION['user_id'];
    
    // Add debug logging
    error_log('Processing appointment with data: ' . print_r([
        'patient_id' => $patient_id,
        'doctor_id' => $doctor_id,
        'date' => $appointment_date,
        'time' => $appointment_time,
        'symptoms' => $symptoms
    ], true));
    
    // Validate required fields
    if (empty($doctor_id) || empty($appointment_date) || empty($appointment_time)) {
        error_log('Missing required fields');
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    // Insert appointment into database
    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, date, time, reason, status) 
                           VALUES (?, ?, ?, ?, ?, 'scheduled')");
    
    try {
        $result = $stmt->execute([$patient_id, $doctor_id, $appointment_date, $appointment_time, $symptoms]);
        if ($result) {
            error_log('Appointment saved successfully');
            echo json_encode(['success' => true, 'message' => 'Appointment saved successfully']);
        } else {
            error_log('Failed to save appointment: ' . print_r($stmt->errorInfo(), true));
            echo json_encode(['success' => false, 'message' => 'Failed to save appointment']);
        }
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
    
} catch (PDOException $e) {
    error_log('Database connection error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
}

$conn = null;
?>
// Add this after session_start();
error_log('Received appointment request: ' . file_get_contents('php://input'));