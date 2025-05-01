<?php
session_start();

// Check if user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'doctor') {
    header('Location: login.php');
    exit;
}

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'healthtrack';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get doctor's information
    $doctor_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'doctor'");
    $stmt->execute([$doctor_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get today's appointments
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND date = ?");
    $stmt->execute([$doctor_id, $today]);
    $today_appointments = $stmt->fetchColumn();

    // Get patients in queue
    $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND status = 'waiting'");
    $stmt->execute([$doctor_id]);
    $queue_count = $stmt->fetchColumn();

    // Get unread messages count
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM messages WHERE doctor_id = ? AND status = 'unread'");
        $stmt->execute([$doctor_id]);
        $unread_messages = $stmt->fetchColumn();
    } catch(PDOException $e) {
        // If table doesn't exist, set unread messages to 0
        $unread_messages = 0;
    }

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthTrack Pro - Doctor Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/doctor-dashboard.css">
    <link rel="stylesheet" href="assets/css/scrollbar.css">
    <style>
        :root {
    --primary-color: #2a7fba;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}
body {
overflow-x: hidden; /* Add this to prevent horizontal scroll */
width: 100%; /* Ensure body takes full width */
}

.main-content {
max-width: 100%; /* Remove fixed max-width if it exists */
padding: 20px 15px; /* Adjust padding if needed */
}
body {
    background-color: #f5f7fa;
    color: #333;
}

.text-primary { color: var(--primary-color); }
.text-success { color: var(--success-color); }
.text-warning { color: var(--warning-color); }
.text-danger { color: var(--danger-color); }

.dashboard-layout {
    display: flex;
    min-height: 100vh;
}

/* Sidebar Styles */
.sidebar {
    width: 240px;
    background: #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
}

.logo-icon {
    width: 30px;
    height: 30px;
    background: var(--primary-color);
    color: white;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar-nav ul {
    list-style: none;
    padding: 15px 0;
}

.sidebar-nav li a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    color: #555;
    text-decoration: none;
    transition: all 0.3s;
}

.sidebar-nav li a:hover, .sidebar-nav li.active a {
    background: #f0f7ff;
    color: var(--primary-color);
}

.sidebar-nav li a i {
    width: 20px;
    text-align: center;
}

.sidebar-footer {
    margin-top: auto;
    padding: 15px;
    border-top: 1px solid #eee;
}

.emergency-contact {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    color: var(--danger-color);
}

.logout-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #555;
    text-decoration: none;
}

/* Main Content Styles */
.main-content {
    flex: 1;
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
}

/* Header Styles */
.top-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

.header-left h2 {
    font-size: 20px;
    margin-bottom: 5px;
}

#current-date {
    font-size: 14px;
    color: #666;
    margin-bottom: 5px;
}

.stats-summary {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-top: 10px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.stat-icon {
    width: 30px;
    height: 30px;
    background: #e1f0ff;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
}

.stat-info h4 {
    font-size: 14px;
    margin: 0;
    font-weight: 600;
}

.stat-info p {
    font-size: 12px;
    margin: 0;
    color: #666;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.notifications {
    position: relative;
    cursor: pointer;
    font-size: 18px;
}

.notifications .badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: var(--danger-color);
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.user-profile img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    cursor: pointer;
    border: 2px solid var(--primary-color);
}

/* Dashboard Sections */
.dashboard-section {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    margin-bottom: 25px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h3 {
    font-size: 18px;
    font-weight: 600;
}

.view-all {
    font-size: 14px;
    color: var(--primary-color);
    text-decoration: none;
}

/* Today's Schedule */
.schedule-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.appointment-card {
    background: #f9fbfe;
    border-radius: 8px;
    padding: 15px;
    transition: transform 0.3s;
}

.appointment-card:hover {
    transform: translateY(-3px);
}

.appointment-time {
    font-size: 14px;
    color: var(--primary-color);
    font-weight: 500;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.appointment-time i {
    font-size: 16px;
}

.appointment-patient {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.patient-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.patient-info h4 {
    font-size: 16px;
    margin-bottom: 3px;
}

.patient-info p {
    font-size: 13px;
    color: #666;
}

.appointment-type {
    font-size: 13px;
    color: #666;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 15px;
}

.appointment-actions {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
    font-size: 13px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: #236fa3;
}

.btn-outline {
    background: transparent;
    border: 1px solid #ddd;
    color: #666;
}

.btn-outline:hover {
    background: #f5f5f5;
}

/* Patient Queue */
.queue-list {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.queue-card {
    background: #f9fbfe;
    border-radius: 8px;
    padding: 15px;
    transition: transform 0.3s;
}

.queue-card:hover {
    transform: translateY(-3px);
}

.queue-status {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    margin-bottom: 10px;
}

.status-waiting {
    background: #fff3cd;
    color: #856404;
}

.status-in-progress {
    background: #cce5ff;
    color: #004085;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-urgent {
    background: #f8d7da;
    color: #721c24;
}

.queue-patient {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.queue-patient-info h4 {
    font-size: 16px;
    margin-bottom: 3px;
}

.queue-patient-info p {
    font-size: 13px;
    color: #666;
}

.queue-reason {
    font-size: 14px;
    margin-bottom: 15px;
    line-height: 1.4;
}

/* Recent Patients */
.patients-list {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.patient-card {
    background: #f9fbfe;
    border-radius: 8px;
    padding: 15px;
    transition: transform 0.3s;
}

.patient-card:hover {
    transform: translateY(-3px);
}

.patient-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.patient-details h4 {
    font-size: 16px;
    margin-bottom: 3px;
}

.patient-details p {
    font-size: 13px;
    color: #666;
}

.patient-meta {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 15px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
}

.meta-item i {
    color: var(--primary-color);
    width: 16px;
    text-align: center;
}

/* Messages */
.messages-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.message-card {
    display: flex;
    gap: 15px;
    padding: 15px;
    background: #f9fbfe;
    border-radius: 8px;
    transition: transform 0.3s;
}

.message-card:hover {
    transform: translateX(5px);
}

.message-content {
    flex: 1;
    min-width: 0;
}

.message-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.message-sender {
    font-weight: 600;
    font-size: 15px;
}

.message-time {
    font-size: 12px;
    color: #666;
}

.message-preview {
    font-size: 14px;
    color: #666;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.message-status {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-top: 5px;
}

.status-unread {
    background: var(--primary-color);
}

.status-read {
    background: #ccc;
}

/* Statistics */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.stat-card {
    background: #f9fbfe;
    border-radius: 8px;
    padding: 15px;
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-3px);
}

.stat-icon-large {
    width: 40px;
    height: 40px;
    background: #e1f0ff;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 18px;
    margin-bottom: 15px;
}

.stat-card h4 {
    font-size: 16px;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 22px;
    font-weight: 600;
    margin: 5px 0;
}

.stat-value span {
    font-size: 14px;
    font-weight: normal;
    color: #666;
}

.stat-trend {
    font-size: 13px;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.trend-up {
    color: var(--success-color);
}

.trend-down {
    color: var(--danger-color);
}

/* Quick Actions */
.actions-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 20px;
    background: #f9fbfe;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s;
}

.action-card:hover {
    background: #e1f0ff;
    transform: translateY(-3px);
}

.action-icon {
    width: 50px;
    height: 50px;
    background: #e1f0ff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 20px;
}

.action-card h4 {
    font-size: 16px;
    text-align: center;
}

/* Responsive Adjustments */
@media (max-width: 1200px) {
    .schedule-grid, .queue-list, 
    .patients-list, .stats-grid,
    .actions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard-layout {
        flex-direction: column;
    }
    
    .sidebar {
        width: 100%;
        flex-direction: row;
        overflow-x: auto;
    }
    
    .sidebar-nav ul {
        display: flex;
        padding: 0;
    }
    
    .sidebar-nav li a {
        padding: 15px 12px;
        white-space: nowrap;
    }
    
    .sidebar-header, .sidebar-footer {
        display: none;
    }
    
    .schedule-grid, .queue-list, 
    .patients-list, .stats-grid,
    .actions-grid {
        grid-template-columns: 1fr;
    }
    
    .top-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .header-right {
        width: 100%;
        justify-content: space-between;
    }
}
        /* Toast Notifications */
.toast-notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #333;
    color: white;
    padding: 12px 24px;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 1000;
}

.toast-notification.show {
    opacity: 1;
}


    /* Add these styles to your existing CSS */
    .dashboard-layout {
        position: relative;
    }
    
    .sidebar {
        position: fixed;
        height: 100vh;
        overflow-y: auto; /* Enable scrolling if content exceeds viewport height */
        z-index: 100; /* Ensure sidebar stays above other content */
    }
    
    .main-content {
        margin-left: 240px; /* Match the width of your sidebar */
        width: calc(100% - 240px); /* Adjust width to account for fixed sidebar */
        overflow-x: hidden;
    }
    
    /* For responsive adjustments */
    @media (max-width: 992px) {
        .sidebar {
            width: 60px;
        }
        .main-content {
            margin-left: 60px;
            width: calc(100% - 60px);
        }
    }
    
    @media (max-width: 768px) {
        .sidebar {
            position: static;
            width: 100%;
            height: auto;
        }
        .main-content {
            margin-left: 0;
            width: 100%;
        }
    }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h1>HealthTrack</h1>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="doctor-dashboard.php">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="appointments.php">
                            <i class="fas fa-calendar-check"></i>
                            <span>Appointments</span>
                        </a>
                    </li>
                    <li>
                        <a href="doctor-patient.html">
                            <i class="fas fa-procedures"></i>
                            <span>My Patients</span>
                        </a>
                    </li>
                    <li>
                        <a href="telemedicine.html">
                            <i class="fas fa-video"></i>
                            <span>Telemedicine</span>
                        </a>
                    </li>
                    <li>
                        <a href="doctor-prescriptions.html">
                            <i class="fas fa-prescription-bottle-alt"></i>
                            <span>Prescriptions</span>
                        </a>
                    </li>
                    <li>
                        <a href="doctor-profile.html">
                            <i class="fas fa-user-md"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="doctor-schedule.html">
                            <i class="fas fa-clock"></i>
                            <span>Schedule</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Compact Header -->
            <header class="top-header">
                <div class="header-left">
                    <h2>Welcome, <span class="text-primary">Dr. Smith</span></h2>
                    <p id="current-date">Monday, June 12, 2023</p>
                    <div class="stats-summary">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div class="stat-info">
                                <h4>12</h4>
                                <p>Today's Appointments</p>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <div class="stat-info">
                                <h4>5</h4>
                                <p>In Queue</p>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="stat-info">
                                <h4>3</h4>
                                <p>New Messages</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="notifications">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </div>
                    <div class="user-profile">
                        <img src="https://randomuser.me/api/portraits/men/46.jpg" alt="Doctor Profile">
                    </div>
                </div>
            </header>

            <!-- Today's Schedule -->
            <section class="dashboard-section today-schedule">
                <div class="section-header">
                    <h3>Today's Schedule</h3>
                    <a href="doctor-schedule.html" class="view-all">View Full Schedule</a>
                </div>
                <div class="schedule-grid">
                    <div class="appointment-card">
                        <div class="appointment-time">
                            <i class="far fa-clock"></i> 9:00 AM - 9:30 AM
                        </div>
                        <div class="appointment-patient">
                            <img src="assets/images/prudhvi.jpeg" alt="Patient" class="patient-avatar">
                            <div class="patient-info">
                                <h4>Prudhvi</h4>
                                <p>ID: PT-100245</p>
                            </div>
                        </div>
                        <div class="appointment-type">
                            <i class="fas fa-video"></i> Video Consultation
                        </div>
                        <div class="appointment-actions">
                            <button class="btn btn-primary">Start</button>
                            <button class="btn btn-outline">Details</button>
                        </div>
                    </div>
                    
                    <div class="appointment-card">
                        <div class="appointment-time">
                            <i class="far fa-clock"></i> 10:00 AM - 10:30 AM
                        </div>
                        <div class="appointment-patient">
                            <img src="assets/images/Kishore.jpeg" alt="Patient" class="patient-avatar">
                            <div class="patient-info">
                                <h4>Kishore</h4>
                                <p>ID: PT-100189</p>
                            </div>
                        </div>
                        <div class="appointment-type">
                            <i class="fas fa-clinic-medical"></i> In-Person
                        </div>
                        <div class="appointment-actions">
                            <button class="btn btn-primary">Start</button>
                            <button class="btn btn-outline">Details</button>
                        </div>
                    </div>
                    
                    <div class="appointment-card">
                        <div class="appointment-time">
                            <i class="far fa-clock"></i> 11:15 AM - 11:45 AM
                        </div>
                        <div class="appointment-patient">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Patient" class="patient-avatar">
                            <div class="patient-info">
                                <h4>Navanit</h4>
                                <p>ID: PT-100312</p>
                            </div>
                        </div>
                        <div class="appointment-type">
                            <i class="fas fa-video"></i> Video Consultation
                        </div>
                        <div class="appointment-actions">
                            <button class="btn btn-primary">Start</button>
                            <button class="btn btn-outline">Details</button>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Patient Queue -->
            <section class="dashboard-section patient-queue">
                <div class="section-header">
                    <h3>Patient Queue</h3>
                    <a href="doctor-patients.html" class="view-all">View All Patients</a>
                </div>
                <div class="queue-list">
                    <div class="queue-card">
                        <span class="queue-status status-waiting">Waiting</span>
                        <div class="queue-patient">
                            <img src="https://randomuser.me/api/portraits/men/22.jpg" alt="Patient" class="patient-avatar">
                            <div class="queue-patient-info">
                                <h4>Yeswanth Kosuri</h4>
                                <p>ID: PT-100278</p>
                            </div>
                        </div>
                        <p class="queue-reason">Follow-up for hypertension management</p>
                        <button class="btn btn-primary">Begin Consultation</button>
                    </div>
                    
                    <div class="queue-card">
                        <span class="queue-status status-waiting">Waiting</span>
                        <div class="queue-patient">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Patient" class="patient-avatar">
                            <div class="queue-patient-info">
                                <h4>Yaswanth Ponnur</h4>
                                <p>ID: PT-100301</p>
                            </div>
                        </div>
                        <p class="queue-reason">Annual physical examination</p>
                        <button class="btn btn-primary">Begin Consultation</button>
                    </div>
                    
                    <div class="queue-card">
                        <span class="queue-status status-in-progress">In Progress</span>
                        <div class="queue-patient">
                            <img src="https://randomuser.me/api/portraits/men/65.jpg" alt="Patient" class="patient-avatar">
                            <div class="queue-patient-info">
                                <h4>Siddi Sathvik</h4>
                                <p>ID: PT-100245</p>
                            </div>
                        </div>
                        <p class="queue-reason">Diabetes management consultation</p>
                        <button class="btn btn-outline">View Details</button>
                    </div>
                    
                    <div class="queue-card">
                        <span class="queue-status status-urgent">Urgent</span>
                        <div class="queue-patient">
                            <img src="https://randomuser.me/api/portraits/women/29.jpg" alt="Patient" class="patient-avatar">
                            <div class="queue-patient-info">
                                <h4>Dhanush</h4>
                                <p>ID: PT-100332</p>
                            </div>
                        </div>
                        <p class="queue-reason">Acute abdominal pain evaluation</p>
                        <button class="btn btn-primary">Prioritize</button>
                    </div>
                </div>
            </section>
            
            <!-- Recent Patients -->
            <section class="dashboard-section recent-patients">
                <div class="section-header">
                    <h3>Recent Patients</h3>
                    <a href="doctor-patients.html" class="view-all">View All Patients</a>
                </div>
                <div class="patients-list">
                    <div class="patient-card">
                        <div class="patient-header">
                            <img src="https://randomuser.me/api/portraits/women/63.jpg" alt="Patient" class="patient-avatar">
                            <div class="patient-details">
                                <h4>Dinesh</h4>
                                <p>ID: PT-100287</p>
                            </div>
                        </div>
                        <div class="patient-meta">
                            <div class="meta-item">
                                <i class="fas fa-birthday-cake"></i>
                                <span>42 years</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-heartbeat"></i>
                                <span>Hypertension</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Last visit: 2 days ago</span>
                            </div>
                        </div>
                        <button class="btn btn-outline">View Profile</button>
                    </div>
                    
                    <div class="patient-card">
                        <div class="patient-header">
                            <img src="https://randomuser.me/api/portraits/men/33.jpg" alt="Patient" class="patient-avatar">
                            <div class="patient-details">
                                <h4>Rajesh</h4>
                                <p>ID: PT-100256</p>
                            </div>
                        </div>
                        <div class="patient-meta">
                            <div class="meta-item">
                                <i class="fas fa-birthday-cake"></i>
                                <span>58 years</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-heartbeat"></i>
                                <span>Type 2 Diabetes</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Last visit: 1 week ago</span>
                            </div>
                        </div>
                        <button class="btn btn-outline">View Profile</button>
                    </div>
                    
                    <div class="patient-card">
                        <div class="patient-header">
                            <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Patient" class="patient-avatar">
                            <div class="patient-details">
                                <h4>Adithya</h4>
                                <p>ID: PT-100298</p>
                            </div>
                        </div>
                        <div class="patient-meta">
                            <div class="meta-item">
                                <i class="fas fa-birthday-cake"></i>
                                <span>35 years</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-heartbeat"></i>
                                <span>Asthma</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Last visit: 3 days ago</span>
                            </div>
                        </div>
                        <button class="btn btn-outline">View Profile</button>
                    </div>
                    
                    <div class="patient-card">
                        <div class="patient-header">
                            <img src="https://randomuser.me/api/portraits/men/28.jpg" alt="Patient" class="patient-avatar">
                            <div class="patient-details">
                                <h4>Parvez</h4>
                                <p>ID: PT-100267</p>
                            </div>
                        </div>
                        <div class="patient-meta">
                            <div class="meta-item">
                                <i class="fas fa-birthday-cake"></i>
                                <span>50 years</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-heartbeat"></i>
                                <span>High Cholesterol</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Last visit: 2 weeks ago</span>
                            </div>
                        </div>
                        <button class="btn btn-outline">View Profile</button>
                    </div>
                </div>
            </section>
            
            <!-- Messages -->
            <section class="dashboard-section messages">
                <div class="section-header">
                    <h3>Recent Messages</h3>
                    <a href="doctor-messages.html" class="view-all">View All Messages</a>
                </div>
                <div class="messages-list">
                    <div class="message-card">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Patient" class="patient-avatar">
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-sender">Yaswenth Kosuri</span>
                                <span class="message-time">10:45 AM</span>
                            </div>
                            <p class="message-preview">Hello Doctor, I've been experiencing some side effects from the new medication you prescribed last week. Should I stop taking it?</p>
                        </div>
                        <div class="message-status status-unread"></div>
                    </div>
                    
                    <div class="message-card">
                        <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Patient" class="patient-avatar">
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-sender">Yaswanth Ponnur</span>
                                <span class="message-time">Yesterday</span>
                            </div>
                            <p class="message-preview">Thank you for the consultation yesterday. I've scheduled the tests you recommended and will share the results when available.</p>
                        </div>
                        <div class="message-status status-read"></div>
                    </div>
                    
                    <div class="message-card">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Patient" class="patient-avatar">
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-sender">Siddi Sathvik</span>
                                <span class="message-time">Jun 10</span>
                            </div>
                            <p class="message-preview">I wanted to check if I need to adjust my insulin dosage based on my recent blood sugar readings which have been consistently...</p>
                        </div>
                        <div class="message-status status-unread"></div>
                    </div>
                </div>
            </section>
            
            <!-- Practice Statistics -->
            <section class="dashboard-section practice-stats">
                <div class="section-header">
                    <h3>Practice Statistics</h3>
                    <a href="#" class="view-all">View Reports</a>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon-large">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <h4>Total Patients</h4>
                        <p class="stat-value">248 <span>patients</span></p>
                        <p class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i> 12% from last month
                        </p>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon-large">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h4>Monthly Appointments</h4>
                        <p class="stat-value">186 <span>appointments</span></p>
                        <p class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i> 8% from last month
                        </p>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon-large">
                            <i class="fas fa-video"></i>
                        </div>
                        <h4>Telehealth</h4>
                        <p class="stat-value">72 <span>consultations</span></p>
                        <p class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i> 15% from last month
                        </p>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon-large">
                            <i class="fas fa-prescription-bottle-alt"></i>
                        </div>
                        <h4>Prescriptions</h4>
                        <p class="stat-value">134 <span>issued</span></p>
                        <p class="stat-trend trend-down">
                            <i class="fas fa-arrow-down"></i> 5% from last month
                        </p>
                    </div>
                </div>
            </section>
            
            <!-- Quick Actions -->
            <section class="dashboard-section quick-actions">
                <div class="section-header">
                    <h3>Quick Actions</h3>
                </div>
                <div class="actions-grid">
                    <a href="doctor-schedule.html" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <h4>Add Availability</h4>
                    </a>
                    <a href="doctor-prescriptions.html" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-prescription"></i>
                        </div>
                        <h4>New Prescription</h4>
                    </a>
                    <a href="doctor-patients.html" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h4>Add Patient</h4>
                    </a>
                    <a href="doctor-messages.html" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-comment-medical"></i>
                        </div>
                        <h4>Send Message</h4>
                    </a>
                </div>
            </section>
        </main>
    </div>
    <script src="assets/js/sidebar.js"></script>
    <script src="assets/js/shared.js"></script>
    <script src="assets/js/doctor-dashboard.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update current date
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const today = new Date();
            document.getElementById('current-date').textContent = today.toLocaleDateString('en-US', options);
        });

        function startConsultation(appointmentId) {
                // Handle starting consultation
                window.location.href = `consultation.php?appointment_id=${appointmentId}`;
            }

            function viewDetails(appointmentId) {
                // Handle viewing appointment details
                window.location.href = `appointment-details.php?id=${appointmentId}`;
            }

            // Update current date
            document.addEventListener('DOMContentLoaded', function() {
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const currentDate = new Date().toLocaleDateString('en-US', options);
                document.getElementById('current-date').textContent = currentDate;
            });

    </script>
</body>
</html>