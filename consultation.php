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
    
    // Get user information
    $stmt = $conn->prepare("SELECT first_name, last_name, profile_picture FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get specialties for filter
    $specialties_query = "SELECT DISTINCT specialty FROM doctor_profiles ORDER BY specialty";
    $specialties = $conn->query($specialties_query)->fetchAll(PDO::FETCH_COLUMN);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthTrack Pro - Consultations</title>
    <link rel="stylesheet" href="consultations.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
       
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
                        <a href="patient-dashboard.php">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="records.php">
                            <i class="fas fa-file-medical"></i>
                            <span>Medical Records</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="consultation.php">
                            <i class="fas fa-user-md"></i>
                            <span>Doctor Consultations</span>
                        </a>
                    </li>
                    <li>
                        <a href="medication.php">
                            <i class="fas fa-pills"></i>
                            <span>Medications</span>
                        </a>
                    </li>
                    <li>
                        <a href="health-goals.php">
                            <i class="fas fa-bullseye"></i>
                            <span>Health Goals</span>
                        </a>
                    </li>
                    <li>
                        <a href="settings.php">
                            <i class="fas fa-cog"></i>
                            <span>settings</span>
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

        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h2>Doctor Consultations</h2>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <span>Welcome, <span id="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span></span>
                    </div>
                </div>
            </header>

            <div class="consultations-container">
                <div class="tabs">
                    <button class="tab active" data-tab="upcoming">Upcoming</button>
                    <button class="tab" data-tab="past">Past</button>
                    <button class="tab" data-tab="book">Book New</button>
                </div>

                <!-- Upcoming Appointments Section -->
                <section class="tab-content active" id="upcoming">
                    <div class="appointments-list" id="upcoming-appointments">
                        <!-- Appointments will be loaded via JavaScript -->
                    </div>
                </section>

                <!-- Past Appointments Section -->
                <section class="tab-content" id="past">
                    <div class="appointments-list" id="past-appointments">
                        <!-- Appointments will be loaded via JavaScript -->
                    </div>
                </section>

                <!-- Book New Consultation Section -->
                <section class="tab-content" id="book">
                    <div class="book-consultation">
                        <div class="search-doctors">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="doctor-search" placeholder="Search doctors by name or specialty">
                                <button class="btn btn-primary" id="search-btn">Search</button>
                            </div>
                            <div class="filters">
                                <select id="specialty-filter">
                                    <option value="">All Specialties</option>
                                    <?php foreach($specialties as $specialty): ?>
                                        <option value="<?php echo htmlspecialchars($specialty); ?>">
                                            <?php echo htmlspecialchars($specialty); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <select id="availability-filter">
                                    <option value="">Any Availability</option>
                                    <option value="today">Today</option>
                                    <option value="tomorrow">Tomorrow</option>
                                    <option value="this-week">This Week</option>
                                </select>
                            </div>
                        </div>

                        <div class="doctors-grid" id="doctors-list">
                            <!-- Doctors will be loaded via JavaScript -->
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Booking Modal -->
    <div class="modal" id="booking-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Book Appointment</h3>
            <form id="booking-form">
                <input type="hidden" id="doctor-id">
                <div class="form-group">
                    <label for="appointment-date">Date</label>
                    <input type="date" id="appointment-date" required>
                </div>
                <div class="form-group">
                    <label for="appointment-time">Time</label>
                    <select id="appointment-time" required>
                        <option value="">Select Time Slot</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="consultation-type">Consultation Type</label>
                    <select id="consultation-type" required>
                        <option value="video">Video Consultation</option>
                        <option value="clinic">In-Clinic Visit</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="symptoms">Symptoms/Reason</label>
                    <textarea id="symptoms" rows="3" placeholder="Briefly describe your symptoms or reason for consultation"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Confirm Booking</button>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal" id="confirmation-modal">
        <div class="modal-content">
            <div class="confirmation-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>Appointment Booked!</h3>
            <p id="confirmation-message"></p>
            <button class="btn btn-primary" id="close-confirmation">Done</button>
        </div>
    </div>

    <script src="consultation.js"></script>
</body>
</html>
<?php $conn = null; ?>