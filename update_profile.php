<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "healthtrack");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE users SET full_name = ?, date_of_birth = ?, gender = ?, email = ?, phone = ?, address = ? WHERE id = ?");
$stmt->bind_param("ssssssi", 
    $data['full_name'],
    $data['date_of_birth'],
    $data['gender'],
    $data['email'],
    $data['phone'],
    $data['address'],
    $user_id
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}

$conn->close();
?>