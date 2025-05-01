<?php
session_start();

// Check if user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'patient') {
    header('Location: login.php');  // Changed from login.html to login.php
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthTrack Pro - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="patient-dashboard.css">
    <style>
    /* Recent Medical Records Section */
    .records-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 10px;
    }
    
    .record-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: transform 0.3s;
    }
    
    .record-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .record-icon {
        width: 50px;
        height: 50px;
        background: #e1f0ff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color, #2a7fba);
        font-size: 20px;
        margin-bottom: 15px;
    }
    
    .record-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 8px;
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: normal;
    }
    
    .record-meta {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
    }
    
    .record-tags {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }
    
    .tag {
        font-size: 12px;
        background: #e1f0ff;
        color: var(--primary-color, #2a7fba);
        padding: 4px 10px;
        border-radius: 12px;
    }
    
    .record-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
    }
    
    .record-date {
        font-size: 12px;
        color: #999;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .records-list {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
    }
    @media (max-width: 768px) {
        .records-list {
            grid-template-columns: 1fr;
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
                    <li class="active">
                        <a href="Patient-dashboard.php">
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
                    <li>
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
                <div class="emergency-contact">
                    <i class="fas fa-phone-alt"></i>
                    <span>Emergency: 108</span>
                </div>
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
                    <h2>Welcome back, <span class="text-primary">Yashwenth</span></h2>
                    <p id="current-date">Monday, June 12, 2023</p>
                    <div class="health-score">
                        <div class="score-circle">
                            <svg width="50" height="50">
                                <circle cx="25" cy="25" r="23" stroke="#f0f0f0" stroke-width="4" fill="none"></circle>
                                <circle cx="25" cy="25" r="23" stroke="var(--primary-color)" stroke-width="4" 
                                        stroke-dasharray="144" stroke-dashoffset="36" fill="none" stroke-linecap="round"></circle>
                            </svg>
                            <span>82</span>
                        </div>
                        <div class="score-info">
                            <h4>Health Score</h4>
                            <p>Good <i class="fas fa-arrow-up text-success"></i> 5%</p>
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="notifications">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </div>
                    <div class="user-profile">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User Profile">
                    </div>
                </div>
            </header>

            <!-- Health Summary -->
            <section class="health-summary">
                <div class="section-header">
                    <h3>Health Summary</h3>
                    <a href="#" class="view-all">View All</a>
                </div>
                <div class="vitals-grid">
                    <div class="vital-card">
                        <div class="vital-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h4>Heart Rate</h4>
                        <p class="vital-value">72 <span>bpm</span></p>
                        <p class="vital-status text-success">Normal</p>
                        <div class="health-timeline">
                            <div class="timeline-item">
                                <div class="timeline-date">Today, 8:30 AM</div>
                                <p>72 bpm (Resting)</p>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-date">Yesterday, 8:15 AM</div>
                                <p>75 bpm (Resting)</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="vital-card">
                        <div class="vital-icon">
                            <i class="fas fa-weight"></i>
                        </div>
                        <h4>Weight</h4>
                        <p class="vital-value">68 <span>kg</span></p>
                        <p class="vital-status text-warning">+0.5kg this week</p>
                        <div class="health-metrics-chart" id="weightChart">
                            <!-- Chart placeholder -->
                        </div>
                    </div>
                    
                    <div class="vital-card">
                        <div class="vital-icon">
                            <i class="fas fa-tint"></i>
                        </div>
                        <h4>Blood Pressure</h4>
                        <p class="vital-value">120/80 <span>mmHg</span></p>
                        <p class="vital-status text-success">Normal</p>
                        <div class="health-timeline">
                            <div class="timeline-item">
                                <div class="timeline-date">Today, 8:30 AM</div>
                                <p>120/80 mmHg</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="vital-card">
                        <div class="vital-icon">
                            <i class="fas fa-walking"></i>
                        </div>
                        <h4>Activity</h4>
                        <p class="vital-value">7,842 <span>steps</span></p>
                        <p class="vital-status text-success">82% of goal</p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 82%"></div>
                        </div>
                    </div>
                </div>
            </section>

         <!-- Medication Schedule - Full Width -->
         <section class="dashboard-section medication-tracker">
            <div class="section-header">
                <h3>Medication Schedule</h3>
                <a href="medication.php" class="view-all">View All</a>
            </div>
            <div class="medication-list">
                <?php
                // Fetch medications for the logged-in user
                $db_host = 'localhost';
                $db_user = 'root';
                $db_pass = '';
                $db_name = 'healthtrack';
                try {
                    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stmt = $conn->prepare("SELECT * FROM medications WHERE user_id = ? ORDER BY next_dose ASC");
                    $stmt->execute([$_SESSION['user_id']]);
                    $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch(PDOException $e) {
                    echo "<p>Unable to load medications.</p>";
                    $medications = [];
                }

                if ($medications && count($medications) > 0):
                    foreach ($medications as $med):
                        // Calculate remaining percentage if you have a 'remaining' or 'quantity' field
                        // For demo, we'll use a random value if not present
                        $remaining = isset($med['remaining']) ? intval($med['remaining']) : rand(20, 90);
                        // Choose icon based on medication type (optional)
                        $icon = (stripos($med['name'], 'insulin') !== false) ? 'fa-syringe' : 'fa-pills';
                ?>
                <div class="medication-card">
                    <div class="medication-icon">
                        <i class="fas <?php echo $icon; ?>"></i>
                    </div>
                    <div class="medication-info">
                        <h4><?php echo htmlspecialchars($med['name']); ?></h4>
                        <p><?php echo htmlspecialchars($med['dosage']); ?><?php if (!empty($med['schedule'])): ?>, <?php echo htmlspecialchars($med['schedule']); ?><?php endif; ?></p>
                    </div>
                    <?php if (stripos($med['name'], 'insulin') !== false): ?>
                        <div class="medication-info">
                            <p>Next dose: <?php echo !empty($med['next_dose']) ? date('g:i A', strtotime($med['next_dose'])) : 'N/A'; ?></p>
                        </div>
                        <button class="btn btn-sm btn-primary">Log Dose</button>
                    <?php else: ?>
                        <div class="medication-progress">
                            <small><?php echo $remaining; ?>% remaining</small>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $remaining; ?>%"></div>
                            </div>
                        </div>
                        <a class="btn btn-sm btn-outline" href="medication-details.php?id=<?php echo $med['id']; ?>">Details</a>
                    <?php endif; ?>
                </div>
                <?php
                    endforeach;
                else:
                ?>
                <p>No medications found.</p>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- Health Goals - Full Width -->
        <?php
        // Fetch health goals for the logged-in user
        $db_host = 'localhost';
        $db_user = 'root';
        $db_pass = '';
        $db_name = 'healthtrack';
        try {
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("SELECT * FROM health_goals WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
            $stmt->execute([$_SESSION['user_id']]);
            $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            $goals = [];
        }
        ?>
        <section class="dashboard-section health-goals">
            <div class="section-header">
                <h3>My Health Goals</h3>
                <a href="health-goals.php" class="view-all">View All</a>
            </div>
            <div class="records-list">
                <?php if ($goals && count($goals) > 0): ?>
                    <?php foreach ($goals as $goal): ?>
                        <div class="record-card">
                            <div class="record-icon">
                                <?php
                                // Choose icon based on goal type
                                $icon = '<i class="fas fa-bullseye"></i>';
                                if (isset($goal['goal_type'])) {
                                    switch (strtolower($goal['goal_type'])) {
                                        case 'weight':
                                            $icon = '<i class="fas fa-weight"></i>';
                                            break;
                                        case 'activity':
                                            $icon = '<i class="fas fa-walking"></i>';
                                            break;
                                        case 'nutrition':
                                            $icon = '<i class="fas fa-utensils"></i>';
                                            break;
                                        case 'sleep':
                                            $icon = '<i class="fas fa-moon"></i>';
                                            break;
                                    }
                                }
                                echo $icon;
                                ?>
                            </div>
                            <h3 class="record-title"><?php echo htmlspecialchars($goal['title']); ?></h3>
                            <p class="record-meta">
                                <?php echo ucfirst($goal['goal_type']); ?> Goal
                                <?php if (!empty($goal['unit'])): ?>
                                    • Target: <?php echo htmlspecialchars($goal['target_value'] . ' ' . $goal['unit']); ?>
                                <?php endif; ?>
                            </p>
                            <div class="record-tags">
                                <span class="tag"><?php echo date('M d', strtotime($goal['start_date'])); ?> - <?php echo date('M d, Y', strtotime($goal['end_date'])); ?></span>
                                <?php if (!empty($goal['reminder_frequency'])): ?>
                                    <span class="tag"><?php echo htmlspecialchars($goal['reminder_frequency']); ?> Reminder</span>
                                <?php endif; ?>
                            </div>
                            <div class="record-actions">
                                <a href="health-goals.php" class="btn btn-outline btn-sm">Details</a>
                                <span class="record-date">Set: <?php echo date('M d, Y', strtotime($goal['created_at'])); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No health goals found.</p>
                <?php endif; ?>
            </div>
        </section>
        <!-- Recent Medical Records - Full Width -->
        <?php
        // Fetch medical records for the logged-in user
        $db_host = 'localhost';
        $db_user = 'root';
        $db_pass = '';
        $db_name = 'healthtrack';
        try {
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("SELECT * FROM medical_records WHERE patient_id = ? ORDER BY upload_date DESC");
            $stmt->execute([$_SESSION['user_id']]);
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
        ?>
        <section class="dashboard-section recent-records">
            <div class="section-header">
                <h3>Recent Medical Records</h3>
                <a href="records.php" class="view-all">View All</a>
            </div>
            <div class="records-list">
                <?php
                // Fetch up to 3 recent records for the dashboard
                $db_host = 'localhost';
                $db_user = 'root';
                $db_pass = '';
                $db_name = 'healthtrack';
                try {
                    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stmt = $conn->prepare("SELECT * FROM medical_records WHERE patient_id = ? ORDER BY upload_date DESC LIMIT 3");
                    $stmt->execute([$_SESSION['user_id']]);
                    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch(PDOException $e) {
                    die("Connection failed: " . $e->getMessage());
                }

                if ($records && count($records) > 0) {
                    foreach ($records as $record) {
                        // Choose icon based on type (optional, fallback to file icon)
                        $icon = '<i class="fas fa-file-medical"></i>';
                        if (isset($record['type'])) {
                            switch (strtolower($record['type'])) {
                                case 'lab':
                                case 'lab results':
                                    $icon = '<i class="fas fa-vial"></i>';
                                    break;
                                case 'prescription':
                                    $icon = '<i class="fas fa-prescription-bottle-alt"></i>';
                                    break;
                                case 'imaging':
                                    $icon = '<i class="fas fa-x-ray"></i>';
                                    break;
                                case 'doctor note':
                                    $icon = '<i class="fas fa-notes-medical"></i>';
                                    break;
                            }
                        }
                        echo '<div class="record-card">';
                        echo '  <div class="record-icon">' . $icon . '</div>';
                        echo '  <h3 class="record-title">' . htmlspecialchars($record['title'] ?? $record['file_name']) . '</h3>';
                        echo '  <p class="record-meta">' . htmlspecialchars($record['uploaded_by'] ?? 'Uploaded') . ' • ' . date('F j, Y', strtotime($record['upload_date'])) . '</p>';
                        echo '  <div class="record-tags">';
                        if (!empty($record['tags'])) {
                            foreach (explode(',', $record['tags']) as $tag) {
                                echo '<span class="tag">' . htmlspecialchars(trim($tag)) . '</span>';
                            }
                        } else {
                            echo '<span class="tag">PDF</span>';
                        }
                        echo '  </div>';
                        echo '  <div class="record-actions">';
                        echo '      <a href="' . htmlspecialchars($record['file_path']) . '" class="btn btn-outline btn-sm" target="_blank">View</a>';
                        echo '      <span class="record-date">Uploaded: ' . date('M d, Y', strtotime($record['upload_date'])) . '</span>';
                        echo '  </div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No recent records found.</p>';
                }
                ?>
            </div>
        </section>
        
        <!-- Upcoming Appointments - Full Width -->
        <section class="dashboard-section appointments">
            <div class="section-header">
                <h3>Upcoming Appointments</h3>
                <a href="consultation.php" class="view-all">View All</a>
            </div>
            <div class="appointment-list">
                <div class="appointment-item">
                    <div class="appointment-date">
                        <div class="date-badge">
                            <span class="day">15</span>
                            <span class="month">JUN</span>
                        </div>
                    </div>
                    <div class="appointment-info">
                        <h4>Follow-up Consultation</h4>
                        <p>Dr. Thanuja • 10:30 AM</p>
                        <div class="appointment-type">
                            <i class="fas fa-video"></i> Video Call
                        </div>
                    </div>
                    <button class="btn btn-primary">Join</button>
                </div>
                
                <div class="appointment-item">
                    <div class="appointment-date">
                        <div class="date-badge">
                            <span class="day">22</span>
                            <span class="month">JUN</span>
                        </div>
                    </div>
                    <div class="appointment-info">
                        <h4>Annual Physical Exam</h4>
                        <p>Dr. Ramu • 2:00 PM</p>
                        <div class="appointment-type">
                            <i class="fas fa-clinic-medical"></i> In-Person
                        </div>
                    </div>
                    <button class="btn btn-outline">Details</button>
                </div>
                
                <div class="appointment-item">
                    <div class="appointment-date">
                        <div class="date-badge">
                            <span class="day">05</span>
                            <span class="month">JUL</span>
                        </div>
                    </div>
                    <div class="appointment-info">
                        <h4>Lab Tests</h4>
                        <p>Diagnostic Center • 8:00 AM</p>
                        <div class="appointment-type">
                            <i class="fas fa-vial"></i> Blood Work
                        </div>
                    </div>
                    <button class="btn btn-outline">Directions</button>
                </div>
            </div>
        </section>
        
        <!-- Wellness Tips - Full Width -->
        <section class="dashboard-section wellness-tips">
            <div class="section-header">
                <h3>Wellness Tips</h3>
                <a href="#" class="view-all">View More Tips</a>
            </div>
            <div class="tips-list">
                <div class="tip-card">
                    <div class="tip-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h4>Healthy Eating</h4>
                    <p>Try adding more leafy greens to your meals today. Vegetables like spinach and kale are packed with nutrients.</p>
                </div>
                <div class="tip-card">
                    <div class="tip-icon">
                        <i class="fas fa-walking"></i>
                    </div>
                    <h4>Activity</h4>
                    <p>Take a 15-minute walk after lunch. Regular movement helps digestion and boosts energy levels.</p>
                </div>
                <div class="tip-card">
                    <div class="tip-icon">
                        <i class="fas fa-moon"></i>
                    </div>
                    <h4>Sleep</h4>
                    <p>Aim for 7-8 hours of sleep tonight. Consistent quality sleep improves overall health and mood.</p>
                </div>
            </div>
        </section>
        
        <!-- My Health Goals - Full Width -->
        <section class="dashboard-section health-goals">
            <div class="section-header">
                <h3>My Health Goals</h3>
                <a href="health-goals.html" class="view-all">View All Goals</a>
            </div>
            <div class="goals-list">
                <div class="goal-item">
                    <div class="goal-progress">
                        <div class="circular-progress" data-value="75">
                            <svg width="60" height="60">
                                <circle cx="30" cy="30" r="25" stroke="#f0f0f0" stroke-width="5" fill="none"></circle>
                                <circle cx="30" cy="30" r="25" stroke="var(--success-color)" stroke-width="5" 
                                        stroke-dasharray="157" stroke-dashoffset="39" fill="none" stroke-linecap="round"></circle>
                            </svg>
                            <span>75%</span>
                        </div>
                    </div>
                    <div class="goal-info">
                        <h4>Lose 5kg</h4>
                        <p>Target: 65kg • Current: 68kg</p>
                        <small>Started: June 1, 2023 • Ends: July 30, 2023</small>
                    </div>
                    <button class="btn btn-outline" style="margin-top: 15px;">Update Progress</button>
                </div>
                
                <div class="goal-item">
                    <div class="goal-progress">
                        <div class="circular-progress" data-value="40">
                            <svg width="60" height="60">
                                <circle cx="30" cy="30" r="25" stroke="#f0f0f0" stroke-width="5" fill="none"></circle>
                                <circle cx="30" cy="30" r="25" stroke="var(--success-color)" stroke-width="5" 
                                        stroke-dasharray="157" stroke-dashoffset="94" fill="none" stroke-linecap="round"></circle>
                            </svg>
                            <span>40%</span>
                        </div>
                    </div>
                    <div class="goal-info">
                        <h4>Daily Steps</h4>
                        <p>Target: 10,000 • Avg: 7,842</p>
                        <small>Started: May 1, 2023 • Monthly Challenge</small>
                    </div>
                    <button class="btn btn-outline" style="margin-top: 15px;">Update Progress</button>
                </div>
            </div>
        </section>
        
        <!-- Emergency Card - Full Width -->
        <section class="emergency-card">
            <div class="emergency-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="emergency-info">
                <h3>Need urgent help?</h3>
                <p>Contact emergency services immediately if you're experiencing a medical emergency</p>
                <button class="btn btn-light btn-sm">
                    <i class="fas fa-phone"></i> Call 108
                </button>
            </div>
        </section>
        
        <!-- Quick Actions - Full Width -->
        <section class="dashboard-section">
            <div class="section-header">
                <h3>Quick Actions</h3>
            </div>
            <div class="actions-grid">
                <a href="health-goals.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-bullseye"></i></div>
                    <h4>Set Health Goals</h4>
                </a>
                <a href="records.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-heartbeat"></i></div>
                    <h4>Add Records</h4>
                </a>
                <a href="medication.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-pills"></i></div>
                    <h4>Track Medication</h4>
                </a>
                <a href="consultation.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-calendar-check"></i></div>
                    <h4>Book Appointment</h4>
                </a>
            </div>
        </section>
    </main>
</div>
<script src="patient-dashboard.js"></script>
</body>
</html>