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
            $date = $_POST['date'];
            $time = $_POST['time'];
            $reason = $_POST['reason'];
            
            $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, date, time, reason, status) VALUES (?, ?, ?, ?, ?, 'scheduled')");
            $stmt->execute([$patient_id, $doctor_id, $date, $time, $reason]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'update':
            $appointment_id = $_POST['appointment_id'];
            $status = $_POST['status'];
            
            $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
            $stmt->execute([$status, $appointment_id]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'cancel':
            $appointment_id = $_POST['appointment_id'];
            
            $stmt = $conn->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
            $stmt->execute([$appointment_id]);
            
            echo json_encode(['success' => true]);
            break;
    }
}
?>