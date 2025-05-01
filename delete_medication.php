<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "healthtrack");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}

$data = json_decode(file_get_contents('php://input'), true);
$medication_id = $data['medication_id'];
$user_id = $_SESSION['user_id'] ?? 1;

$stmt = $conn->prepare("DELETE FROM medications WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $medication_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete medication']);
}

$conn->close();
?>