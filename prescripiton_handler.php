
<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'create':
            $patient_id = $_POST['patient_id'];
            $doctor_id = $_POST['doctor_id'];
            $medication = $_POST['medication'];
            $dosage = $_POST['dosage'];
            $frequency = $_POST['frequency'];
            $duration = $_POST['duration'];
            $instructions = $_POST['instructions'];
            
            $stmt = $conn->prepare("INSERT INTO prescriptions (patient_id, doctor_id, medication, dosage, frequency, duration, instructions, status) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
            $stmt->execute([$patient_id, $doctor_id, $medication, $dosage, $frequency, $duration, $instructions]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'update':
            $prescription_id = $_POST['prescription_id'];
            $status = $_POST['status'];
            
            $stmt = $conn->prepare("UPDATE prescriptions SET status = ? WHERE id = ?");
            $stmt->execute([$status, $prescription_id]);
            
            echo json_encode(['success' => true]);
            break;
    }
}
?>