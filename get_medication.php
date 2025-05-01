<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "healthtrack");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}

$medication_id = $_GET['id'];
$user_id = $_SESSION['user_id'] ?? 1;

$stmt = $conn->prepare("SELECT * FROM medications WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $medication_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$medication = $result->fetch_assoc();

echo json_encode($medication);

$conn->close();
?>