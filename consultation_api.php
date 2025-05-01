<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "healthtrack");
if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$action = $_GET['action'] ?? '';
$user_id = $_SESSION['user_id'];

switch($action) {
    case 'get_upcoming_appointments':
        $query = "SELECT a.*, d.first_name as doctor_first_name, d.last_name as doctor_last_name, 
                dp.specialty, dp.profile_picture as doctor_image, 
                (SELECT AVG(rating) FROM doctor_reviews WHERE doctor_id = d.user_id) as rating,
                (SELECT COUNT(*) FROM doctor_reviews WHERE doctor_id = d.user_id) as reviews
                FROM appointments a
                JOIN users d ON a.doctor_id = d.id
                JOIN doctor_profiles dp ON d.id = dp.user_id
                WHERE a.patient_id = ? AND a.appointment_date >= CURDATE()
                ORDER BY a.appointment_date, a.appointment_time";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $appointments = [];
        while($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        echo json_encode(['appointments' => $appointments]);
        break;

    case 'get_past_appointments':
        $query = "SELECT a.*, d.first_name as doctor_first_name, d.last_name as doctor_last_name, 
                dp.specialty, dp.profile_picture as doctor_image, 
                (SELECT AVG(rating) FROM doctor_reviews WHERE doctor_id = d.user_id) as rating,
                (SELECT COUNT(*) FROM doctor_reviews WHERE doctor_id = d.user_id) as reviews
                FROM appointments a
                JOIN users d ON a.doctor_id = d.id
                JOIN doctor_profiles dp ON d.id = dp.user_id
                WHERE a.patient_id = ? AND a.appointment_date < CURDATE()
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $appointments = [];
        while($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        echo json_encode(['appointments' => $appointments]);
        break;

    case 'get_doctors':
        $specialty = $_GET['specialty'] ?? '';
        $availability = $_GET['availability'] ?? '';
        $search = $_GET['search'] ?? '';

        $query = "SELECT u.id, u.first_name, u.last_name, dp.specialty, dp.profile_picture,
                (SELECT AVG(rating) FROM doctor_reviews WHERE doctor_id = u.id) as rating,
                (SELECT COUNT(*) FROM doctor_reviews WHERE doctor_id = u.id) as reviews,
                EXISTS(SELECT 1 FROM doctor_availability da 
                    WHERE da.doctor_id = u.id 
                    AND da.day_of_week = DAYNAME(CURDATE())
                    AND CURTIME() BETWEEN da.start_time AND da.end_time) as available_today
                FROM users u
                JOIN doctor_profiles dp ON u.id = dp.user_id
                WHERE u.user_type = 'doctor'";

        if ($specialty) {
            $query .= " AND dp.specialty = ?";
        }
        if ($search) {
            $query .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR dp.specialty LIKE ?)";
        }

        $stmt = $conn->prepare($query);
        
        if ($specialty && $search) {
            $search = "%$search%";
            $stmt->bind_param("ssss", $specialty, $search, $search, $search);
        } else if ($specialty) {
            $stmt->bind_param("s", $specialty);
        } else if ($search) {
            $search = "%$search%";
            $stmt->bind_param("sss", $search, $search, $search);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        
        $doctors = [];
        while($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
        echo json_encode(['doctors' => $doctors]);
        break;

    case 'book_appointment':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        $query = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, 
                consultation_type, symptoms, status) VALUES (?, ?, ?, ?, ?, ?, 'scheduled')";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iissss", $user_id, $data['doctor_id'], $data['date'], 
                        $data['time'], $data['type'], $data['symptoms']);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'appointment_id' => $stmt->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to book appointment']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
}

$conn->close();
?>