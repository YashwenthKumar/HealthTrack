<?php
session_start();
$conn = new mysqli("localhost", "root", "", "healthtrack");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'] ?? 1;

// Check if this is an update (id exists) or new medication
if (isset($data['id']) && !empty($data['id'])) {
    // Update existing medication
    $stmt = $conn->prepare("UPDATE medications SET name = ?, dosage = ?, schedule = ?, purpose = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssssii", $data['name'], $data['dosage'], $data['schedule'], $data['purpose'], $data['id'], $user_id);
} else {
    // Insert new medication
    $stmt = $conn->prepare("INSERT INTO medications (name, dosage, schedule, purpose, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $data['name'], $data['dosage'], $data['schedule'], $data['purpose'], $user_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>