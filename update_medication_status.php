<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "healthtrack");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}

$data = json_decode(file_get_contents('php://input'), true);
$medication_id = $data['medication_id'];
$status = $data['status'];
$user_id = $_SESSION['user_id'] ?? 1;

if ($status === 'taken') {
    // Update last taken time and next dose
    $stmt = $conn->prepare("UPDATE medications SET last_taken = NOW(), next_dose = DATE_ADD(NOW(), INTERVAL 24 HOUR) WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $medication_id, $user_id);
} else if ($status === 'snoozed') {
    // Update next dose to 30 minutes later
    $stmt = $conn->prepare("UPDATE medications SET next_dose = DATE_ADD(NOW(), INTERVAL 30 MINUTE) WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $medication_id, $user_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}

$conn->close();
?>