
<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'healthtrack';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get current date for display
$current_date = date('l, F j, Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthTrack Pro - Appointments</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
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
            background-color: #f5f7fa;
            color: #333;
            overflow-x: hidden;
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
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 20px;
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
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 14px;
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
        
        /* Appointments Page Specific Styles */
        .appointments-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .appointments-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .view-options {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .view-option-btn {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .view-option-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .search-box {
            flex: 1;
            position: relative;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        .filter-dropdown {
            min-width: 200px;
        }
        
        .filter-dropdown select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 1em;
        }
        
        /* Calendar Styles */
        #calendar {
            margin-top: 20px;
        }
        
        .fc .fc-toolbar-title {
            font-size: 1.2em;
            font-weight: 600;
        }
        
        .fc .fc-button {
            background-color: white;
            border: 1px solid #ddd;
            color: #555;
            text-transform: capitalize;
            font-weight: 400;
        }
        
        .fc .fc-button-primary:not(:disabled).fc-button-active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .fc .fc-button-primary:hover {
            background-color: #f5f5f5;
        }
        
        .fc .fc-button-primary:not(:disabled).fc-button-active:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .fc-event {
            cursor: pointer;
            border: none;
            font-size: 12px;
            padding: 2px 4px;
        }
        
        .fc-event .fc-event-main {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .fc-event .fc-event-time {
            font-weight: 500;
        }
        
        .fc-daygrid-event-dot {
            margin: 0 4px 0 0;
            border-color: white !important;
        }
        
        /* Appointments List Styles */
        .appointments-list {
            display: none;
        }
        
        .appointments-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .appointments-table th {
            text-align: left;
            padding: 12px 15px;
            background: #f5f7fa;
            font-weight: 500;
            color: #555;
        }
        
        .appointments-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .appointment-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .patient-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .patient-name {
            font-weight: 500;
        }
        
        .patient-id {
            font-size: 13px;
            color: #999;
        }
        
        .appointment-type {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            background: #e2f0ff;
            color: var(--primary-color);
        }
        
        .appointment-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-scheduled {
            background: #d4edda;
            color: #155724;
        }
        
        .status-completed {
            background: #e2e3e5;
            color: #383d41;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .action-btns {
            display: flex;
            gap: 10px;
        }
        
        .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f7ff;
            color: var(--primary-color);
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .action-btn:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .pagination {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }
        
        .page-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            color: #555;
            border: none;
            cursor: pointer;
        }
        
        .page-btn.active {
            background: var(--primary-color);
            color: white;
        }
        
        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .modal {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            animation: modalFadeIn 0.3s ease;
        }
        
        .modal-header {
            padding: 16px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-content {
            padding: 20px;
        }
        
        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            justify-content: flex-end;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        
        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1001;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .toast-notification.show {
            opacity: 1;
        }
        
        .toast-notification.success {
            background: var(--success-color);
        }
        
        .toast-notification.error {
            background: var(--danger-color);
        }
        
        .toast-notification.info {
            background: var(--primary-color);
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
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
            
            .search-filter {
                flex-direction: column;
                gap: 10px;
            }
            
            .appointments-table {
                display: block;
                overflow-x: auto;
            }
            
            .view-options {
                overflow-x: auto;
                padding-bottom: 10px;
            }
            
            .view-options::-webkit-scrollbar {
                height: 5px;
            }
            
            .view-options::-webkit-scrollbar-thumb {
                background: #ddd;
                border-radius: 5px;
            }
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
                        <a href="doctor-dashboard.html">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="appointments.html">
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
                <a href="logout.html" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h2>Appointment Management</h2>
                    <p id="current-date">Monday, June 12, 2023</p>
                </div>
                <div class="header-right">
                    <button class="btn btn-primary" id="new-appointment-btn">
                        <i class="fas fa-plus"></i> New Appointment
                    </button>
                </div>
            </header>
            
            <div class="appointments-container">
                <div class="appointments-header">
                    <h3>Upcoming Appointments (18)</h3>
                    <div>
                        <button class="btn btn-outline">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                
                <div class="view-options">
                    <button class="view-option-btn active" data-view="calendar">
                        <i class="fas fa-calendar-alt"></i> Calendar View
                    </button>
                    <button class="view-option-btn" data-view="list">
                        <i class="fas fa-list"></i> List View
                    </button>
                </div>
                
                <div class="search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search appointments...">
                    </div>
                    <div class="filter-dropdown">
                        <select id="status-filter">
                            <option>All Status</option>
                            <option>Scheduled</option>
                            <option>Completed</option>
                            <option>Cancelled</option>
                        </select>
                    </div>
                    <div class="filter-dropdown">
                        <select id="type-filter">
                            <option>All Types</option>
                            <option>Consultation</option>
                            <option>Follow-up</option>
                            <option>Check-up</option>
                            <option>Emergency</option>
                        </select>
                    </div>
                </div>
                
                <!-- Calendar View -->
                <div id="calendar"></div>
                
                <!-- List View -->
                <div class="appointments-list">
                    <table class="appointments-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Date & Time</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="appointment-info">
                                        <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Patient" class="patient-avatar">
                                        <div>
                                            <div class="patient-name">Sarah Johnson</div>
                                            <div class="patient-id">ID: PT-100245</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    Jun 15, 2023<br>
                                    10:00 AM - 10:30 AM
                                </td>
                                <td><span class="appointment-type">Consultation</span></td>
                                <td><span class="appointment-status status-scheduled">Scheduled</span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn" title="Start Video Call">
                                            <i class="fas fa-video"></i>
                                        </button>
                                        <button class="action-btn" title="Cancel">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- More appointment rows... -->
                        </tbody>
                    </table>
                    
                    <div class="pagination">
                        <button class="page-btn">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn">2</button>
                        <button class="page-btn">3</button>
                        <button class="page-btn">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- New Appointment Modal -->
    <div class="modal-overlay" id="new-appointment-modal" style="display: none;">
        <div class="modal">
            <div class="modal-header">
                <h3>Schedule New Appointment</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-content">
                <form id="appointment-form">
                    <div class="form-group">
                        <label>Patient</label>
                        <select class="form-control" name="patient" required>
                            <option value="">Select Patient</option>
                            <option value="PT-100245">Sarah Johnson</option>
                            <option value="PT-100189">Michael Chen</option>
                            <option value="PT-100312">Emma Williams</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Appointment Type</label>
                        <select class="form-control" name="type" required>
                            <option value="">Select Type</option>
                            <option value="consultation">Consultation</option>
                            <option value="follow-up">Follow-up</option>
                            <option value="check-up">Check-up</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Time</label>
                        <input type="time" class="form-control" name="time" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Duration (minutes)</label>
                        <select class="form-control" name="duration" required>
                            <option value="15">15 minutes</option>
                            <option value="30" selected>30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60">60 minutes</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="btn btn-outline close-modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Schedule Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Appointment Details Modal -->
    <div class="modal-overlay" id="appointment-details-modal" style="display: none;">
        <div class="modal" style="max-width: 600px;">
            <div class="modal-header">
                <h3>Appointment Details</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-content">
                <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Patient" 
                         style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <h3 style="margin-bottom: 5px;">Sarah Johnson</h3>
                        <p style="color: #666; margin-bottom: 5px;">ID: PT-100245</p>
                        <p style="color: #666;">sarah.j@example.com | (555) 123-4567</p>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; color: #666; margin-bottom: 5px;">Date</label>
                        <p>June 15, 2023</p>
                    </div>
                    <div>
                        <label style="display: block; color: #666; margin-bottom: 5px;">Time</label>
                        <p>10:00 AM - 10:30 AM</p>
                    </div>
                    <div>
                        <label style="display: block; color: #666; margin-bottom: 5px;">Type</label>
                        <p><span class="appointment-type">Consultation</span></p>
                    </div>
                    <div>
                        <label style="display: block; color: #666; margin-bottom: 5px;">Status</label>
                        <p><span class="appointment-status status-scheduled">Scheduled</span></p>
                    </div>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; color: #666; margin-bottom: 5px;">Notes</label>
                    <p>Follow-up on blood test results and discuss new symptoms</p>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel Appointment
                    </button>
                    <button type="button" class="btn btn-primary">
                        <i class="fas fa-video"></i> Start Video Call
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update current date
            updateCurrentDate();
            
            // Add PHP data to calendar
            var appointments = <?php echo json_encode($appointments); ?>;
            
            // Initialize all functionality
            initSidebar();
            initModals();
            initCalendar();
            initViewToggle();
            initSearch();
            initFilters();
            initActionButtons();
            initAppointmentForm();
            
            // Load sample appointment data
            loadAppointmentData();
        });

        // ==================== CORE FUNCTIONS ====================

        function updateCurrentDate() {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', options);
        }

        function loadAppointmentData() {
            // In a real application, this would be an API call
            console.log("Loading appointment data...");
            // Simulate loading
            setTimeout(() => {
                showToast('Appointment data loaded successfully', 'success');
            }, 1000);
        }

        // ==================== SIDEBAR FUNCTIONALITY ====================

        function initSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.createElement('div');
            sidebarToggle.className = 'sidebar-toggle';
            
            // Insert toggle button
            document.querySelector('.top-header').prepend(sidebarToggle);
            
            // Toggle sidebar on mobile
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('collapsed');
                }
            });
        }

        // ==================== MODAL FUNCTIONALITY ====================

        function initModals() {
            // New Appointment Modal
            const newAppointmentBtn = document.getElementById('new-appointment-btn');
            if (newAppointmentBtn) {
                newAppointmentBtn.addEventListener('click', function() {
                    document.getElementById('new-appointment-modal').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                });
            }
            
            // Close modals when clicking X or outside
            document.querySelectorAll('.close-modal').forEach(btn => {
                btn.addEventListener('click', hideAllModals);
            });
            
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) hideAllModals();
                });
            });
        }

        function hideAllModals() {
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.style.display = 'none';
            });
            document.body.style.overflow = 'auto';
        }

        // ==================== CALENDAR FUNCTIONALITY ====================

        function initCalendar() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [
            {
                title: 'Consultation',
                start: new Date().setHours(10, 0, 0, 0),
                end: new Date().setHours(10, 30, 0, 0),
                extendedProps: {
                    patient: 'Sarah Johnson',
                    patientId: 'PT-100245',
                    type: 'Consultation',
                    status: 'Scheduled',
                    avatar: 'https://randomuser.me/api/portraits/women/32.jpg',
                    contact: '(555) 123-4567',
                    notes: 'Follow-up on blood test results'
                },
                color: '#2a7fba'
            },
            {
                title: 'Follow-up',
                start: new Date(new Date().getTime() + 86400000).setHours(14, 0, 0, 0),
                end: new Date(new Date().getTime() + 86400000).setHours(14, 30, 0, 0),
                extendedProps: {
                    patient: 'Michael Chen',
                    patientId: 'PT-100189',
                    type: 'Follow-up',
                    status: 'Scheduled',
                    avatar: 'https://randomuser.me/api/portraits/men/45.jpg',
                    contact: '(555) 234-5678',
                    notes: 'Discuss treatment progress'
                },
                color: '#28a745'
            }
        ],
        eventClick: function(info) {
            showAppointmentDetails(info.event);
        },
        eventContent: function(arg) {
            // Create a custom event element
            const eventEl = document.createElement('div');
            eventEl.className = 'fc-custom-event';
            eventEl.innerHTML = `
                <div class="fc-event-header">
                    <img src="${arg.event.extendedProps.avatar}" alt="${arg.event.extendedProps.patient}" 
                         class="fc-event-avatar">
                    <div class="fc-event-time">${arg.timeText}</div>
                </div>
                <div class="fc-event-body">
                    <div class="fc-event-patient">${arg.event.extendedProps.patient}</div>
                    <div class="fc-event-type">${arg.event.extendedProps.type}</div>
                </div>
            `;
            
            return { domNodes: [eventEl] };
        },
        eventDidMount: function(arg) {
            // Add tooltip with more details
            arg.el.setAttribute('title', 
                `Patient: ${arg.event.extendedProps.patient}\n` +
                `Type: ${arg.event.extendedProps.type}\n` +
                `Time: ${arg.timeText}\n` +
                `Contact: ${arg.event.extendedProps.contact}`
            );
        }
    });
    
    calendar.render();
    
    // Add custom styles for calendar events
    const style = document.createElement('style');
    style.textContent = `
        .fc-custom-event {
            padding: 5px;
            border-radius: 6px;
            font-size: 13px;
            line-height: 1.3;
        }
        .fc-event-header {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 3px;
        }
        .fc-event-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
        }
        .fc-event-time {
            font-weight: 500;
            font-size: 12px;
        }
        .fc-event-patient {
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .fc-event-type {
            font-size: 11px;
            color: #666;
        }
        .fc-daygrid-event {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .fc-daygrid-block-event .fc-event-time {
            font-weight: normal;
        }
        .fc-event-main {
            padding: 2px;
        }
    `;
    document.head.appendChild(style);
}

        // ==================== VIEW TOGGLE FUNCTIONALITY ====================

        function initViewToggle() {
            const viewButtons = document.querySelectorAll('.view-option-btn');
            const calendarView = document.getElementById('calendar');
            const listView = document.querySelector('.appointments-list');
            
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Update active button
                    viewButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Toggle views
                    if (this.dataset.view === 'calendar') {
                        calendarView.style.display = 'block';
                        listView.style.display = 'none';
                    } else {
                        calendarView.style.display = 'none';
                        listView.style.display = 'block';
                    }
                });
            });
        }

        // ==================== APPOINTMENT DETAILS ====================

        function showAppointmentDetails(event) {
            const modal = document.getElementById('appointment-details-modal');
            const extendedProps = event.extendedProps;
            
            // Update modal content with event data
            modal.querySelector('h3').textContent = `Appointment Details: ${extendedProps.patient}`;
            modal.querySelector('img').src = `https://randomuser.me/api/portraits/${extendedProps.patient === 'Sarah Johnson' ? 'women/32' : extendedProps.patient === 'Emma Williams' ? 'women/45' : 'men/45'}.jpg`;
            modal.querySelector('h3 + p').textContent = `ID: ${extendedProps.patientId}`;
            modal.querySelector('h3 + p + p').textContent = `${extendedProps.patient.replace(' ', '.').toLowerCase()}@example.com | (555) 123-4567`;
            
            const startDate = event.start;
            const endDate = event.end || new Date(startDate.getTime() + 30 * 60000); // Default 30 min duration
            
            modal.querySelectorAll('div > div > p')[0].textContent = startDate.toLocaleDateString('en-US', { 
                year: 'numeric', month: 'long', day: 'numeric' 
            });
            
            modal.querySelectorAll('div > div > p')[1].textContent = 
                `${startDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })} - 
                 ${endDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`;
            
            const typeBadge = modal.querySelector('.appointment-type');
            typeBadge.textContent = extendedProps.type;
            
            const statusBadge = modal.querySelector('.appointment-status');
            statusBadge.textContent = extendedProps.status;
            statusBadge.className = `appointment-status status-${extendedProps.status.toLowerCase()}`;
            
            // Show the modal
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // ==================== SEARCH & FILTER FUNCTIONALITY ====================

        function initSearch() {
            const searchInput = document.querySelector('.search-box input');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    filterAppointments(searchTerm);
                });
            }
        }

        function initFilters() {
            const statusFilter = document.getElementById('status-filter');
            const typeFilter = document.getElementById('type-filter');
            
            if (statusFilter) {
                statusFilter.addEventListener('change', filterAppointments);
            }
            
            if (typeFilter) {
                typeFilter.addEventListener('change', filterAppointments);
            }
        }

        function filterAppointments() {
            const searchTerm = document.querySelector('.search-box input').value.toLowerCase();
            const statusFilter = document.getElementById('status-filter').value;
            const typeFilter = document.getElementById('type-filter').value;
            const rows = document.querySelectorAll('.appointments-table tbody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const patientName = row.querySelector('.patient-name').textContent.toLowerCase();
                const patientId = row.querySelector('.patient-id').textContent.toLowerCase();
                const appointmentType = row.querySelector('.appointment-type').textContent;
                const appointmentStatus = row.querySelector('.appointment-status').textContent;
                
                const matchesSearch = searchTerm === '' || 
                                    patientName.includes(searchTerm) || 
                                    patientId.includes(searchTerm);
                
                const matchesStatus = statusFilter === 'All Status' || 
                                    appointmentStatus === statusFilter;
                
                const matchesType = typeFilter === 'All Types' || 
                                  appointmentType === typeFilter;
                
                if (matchesSearch && matchesStatus && matchesType) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            document.querySelector('.appointments-header h3').textContent = `Upcoming Appointments (${visibleCount})`;
        }

        // ==================== ACTION BUTTONS FUNCTIONALITY ====================

        function initActionButtons() {
            // View Details buttons
            document.querySelectorAll('.action-btn[title="View Details"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    // In a real app, this would fetch actual appointment data
                    showToast('Showing appointment details', 'info');
                    document.getElementById('appointment-details-modal').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                });
            });
            
            // Start Video Call buttons
            document.querySelectorAll('.action-btn[title="Start Video Call"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    showToast('Starting video call with patient', 'success');
                });
            });
            
            // Cancel buttons
            document.querySelectorAll('.action-btn[title="Cancel"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to cancel this appointment?')) {
                        showToast('Appointment cancelled', 'error');
                        // In a real app, you would update the status in your database
                        const statusBadge = this.closest('tr').querySelector('.appointment-status');
                        statusBadge.textContent = 'Cancelled';
                        statusBadge.className = 'appointment-status status-cancelled';
                    }
                });
            });
        }

        // ==================== APPOINTMENT FORM FUNCTIONALITY ====================

        function initAppointmentForm() {
            const form = document.getElementById('appointment-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    
                    fetch('save_appointment.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            calendar.refetchEvents();
                            hideAllModals();
                            showToast('Appointment scheduled successfully!', 'success');
                        } else {
                            showToast(data.message || 'Failed to schedule appointment', 'error');
                        }
                    })
                    .catch(error => {
                        showToast('An error occurred while scheduling the appointment', 'error');
                    });
            }
        }

        // ==================== TOAST NOTIFICATION ====================

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast-notification show ${type}`;
            toast.innerHTML = `
                <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>