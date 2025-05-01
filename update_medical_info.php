<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "healthtrack");

if ($conn->connect_error) {
    error_log('Database connection failed: ' . $conn->connect_error);
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];

// Debug logging
error_log('Raw input: ' . file_get_contents('php://input'));
error_log('Decoded data: ' . print_r($data, true));
error_log('User ID: ' . $user_id);

// First check if a record exists
$check = $conn->prepare("SELECT user_id FROM medical_info WHERE user_id = ?");
$check->bind_param("i", $user_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Update existing record
    $stmt = $conn->prepare("UPDATE medical_info SET blood_type = ?, height = ?, weight = ?, allergies = ?, chronic_conditions = ?, current_medications = ? WHERE user_id = ?");
    if (!$stmt) {
        error_log('Prepare failed: ' . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
        exit();
    }

    $stmt->bind_param("ssssssi", 
        $data['blood_type'],
        $data['height'],
        $data['weight'],
        $data['allergies'],
        $data['chronic_conditions'],
        $data['current_medications'],
        $user_id
    );
} else {
    // Insert new record
    $stmt = $conn->prepare("INSERT INTO medical_info (user_id, blood_type, height, weight, allergies, chronic_conditions, current_medications) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        error_log('Prepare failed: ' . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
        exit();
    }

    $stmt->bind_param("issssss", 
        $user_id,
        $data['blood_type'],
        $data['height'],
        $data['weight'],
        $data['allergies'],
        $data['chronic_conditions'],
        $data['current_medications']
    );
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Execute failed: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Failed to save medical information']);
}

$stmt->close();
$conn->close();
?>