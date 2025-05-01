<?php
session_start();
// Database connection (replace PDO with MySQLi)
$conn = new mysqli("localhost", "root", "", "healthtrack");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user's existing goals
$user_id = $_SESSION['user_id'];
$goals_query = "SELECT * FROM health_goals WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($goals_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$goals_result = $stmt->get_result();
$goals = $goals_result->fetch_all(MYSQLI_ASSOC);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $goal_type = $_POST['goal_type'];
    $title = $_POST['goal-title'];
    $target_value = $_POST['target-value'];
    $unit = $_POST['target-unit'];
    $start_date = $_POST['start-date'];
    $end_date = $_POST['end-date'];
    $description = $_POST['goal-description'];
    $reminder = $_POST['goal-reminder'];
    $reminder_time = $_POST['reminder-time'] ?? null;
    
    // Validate input
    if (empty($title) || empty($target_value) || empty($start_date) || empty($end_date)) {
        $error = "Please fill in all required fields";
    } else {
        // Insert new goal
        $insert_query = "INSERT INTO health_goals (user_id, goal_type, title, target_value, unit, start_date, end_date, description, reminder_frequency, reminder_time, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("issdssssss", $user_id, $goal_type, $title, $target_value, $unit, $start_date, $end_date, $description, $reminder, $reminder_time);
        
        if ($stmt->execute()) {
            $goal_id = $stmt->insert_id;
            
            // Insert milestones if any
            if (isset($_POST['milestones']) && is_array($_POST['milestones'])) {
                $milestone_query = "INSERT INTO goal_milestones (goal_id, description, target_date) VALUES (?, ?, ?)";
                $milestone_stmt = $conn->prepare($milestone_query);
                
                foreach ($_POST['milestones'] as $milestone) {
                    if (!empty($milestone['description']) && !empty($milestone['date'])) {
                        $milestone_stmt->bind_param("iss", $goal_id, $milestone['description'], $milestone['date']);
                        $milestone_stmt->execute();
                    }
                }
            }
            
            $_SESSION['success_message'] = "Goal saved successfully!";
            header('Location: health-goals.php');
            exit();
        } else {
            $error = "Error saving goal. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthTrack Pro - Set Goals</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="health-goals.css">
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
                   
                    <li class="active">
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

        <!-- Main Content -->
        <main class="main-content">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div style="background: #e6ffed; color: #155724; border: 1px solid #b7f5c2; padding: 12px 18px; border-radius: 6px; margin-bottom: 20px;">
            <?php 
                echo $_SESSION['success_message']; 
                unset($_SESSION['success_message']); // Remove message after displaying
            ?>
        </div>
    <?php endif; ?>
            <!-- Header -->
            <header class="top-header">
    <div class="header-left" style="display: flex; align-items: center; gap: 20px;">
        <h2 style="margin-bottom: 0;">Set New Health Goal</h2>
        <button class="btn btn-outline" id="viewGoalsBtn" type="button" style="margin-left: 10px;">
            <i class="fas fa-eye"></i> View Goals
        </button>
    </div>
    <div class="header-right">
       
        <div class="user-profile">
        </div>
    </div>
</header>
<!-- Goals Content -->
<div class="goals-container">
    <div class="section-header">
        <h3>Create New Goal</h3>
    </div>

                <div class="goal-types">
                    <div class="goal-type active" data-type="weight">
                        <i class="fas fa-weight"></i>
                        <h4>Weight Management</h4>
                    </div>
                    <div class="goal-type" data-type="activity">
                        <i class="fas fa-walking"></i>
                        <h4>Activity</h4>
                    </div>
                    <div class="goal-type" data-type="nutrition">
                        <i class="fas fa-utensils"></i>
                        <h4>Nutrition</h4>
                    </div>
                    <div class="goal-type" data-type="sleep">
                        <i class="fas fa-moon"></i>
                        <h4>Sleep</h4>
                    </div>
                    <div class="goal-type" data-type="medication">
                        <i class="fas fa-pills"></i>
                        <h4>Medication</h4>
                    </div>
                    <div class="goal-type" data-type="other">
                        <i class="fas fa-plus"></i>
                        <h4>Other</h4>
                    </div>
                </div>
                
                <div class="goal-form">
    <form method="POST" id="goalForm">
        <!-- Add this hidden input for goal_type -->
        <input type="hidden" id="goal-type" name="goal_type" value="weight">
        <div class="form-group">
            <label for="goal-title">Goal Title</label>
            <input type="text" id="goal-title" name="goal-title" class="form-control" placeholder="e.g. Lose 5kg in 3 months">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="target-value">Target Value</label>
                <input type="number" id="target-value" name="target-value" class="form-control" placeholder="e.g. 65">
            </div>
            <div class="form-group">
                <label for="target-unit">Unit</label>
                <select id="target-unit" name="target-unit" class="form-control">
                    <option value="kg">kg</option>
                    <option value="lbs">lbs</option>
                    <option value="steps">steps</option>
                    <option value="hours">hours</option>
                    <option value="days">days</option>
                    <option value="times">times</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="start-date">Start Date</label>
                <input type="date" id="start-date" name="start-date" class="form-control">
            </div>
            <div class="form-group">
                <label for="end-date">Target Date</label>
                <input type="date" id="end-date" name="end-date" class="form-control">
            </div>
        </div>
        
        <div class="form-group">
            <label for="goal-description">Description (Optional)</label>
            <textarea id="goal-description" name="goal-description" class="form-control" rows="3" placeholder="Describe your goal..."></textarea>
        </div>
        
        <div class="form-group">
            <label for="goal-reminder">Reminder</label>
            <select id="goal-reminder" name="goal-reminder" class="form-control">
                <option value="none">No reminder</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>
        
        <div class="form-actions">
            <button class="btn btn-outline" id="cancelBtn" type="button">
                Cancel
            </button>
            <button class="btn btn-primary" id="saveGoalBtn" type="submit">
                Save Goal
            </button>
        </div>
    </form>
</div>
            </div>
        </main>
    </div>

    <!-- Stylish Goals Modal/Panel -->
    <div id="goalsModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:12px; max-width:900px; width:90vw; max-height:80vh; overflow-y:auto; padding:32px 24px; box-shadow:0 8px 32px rgba(0,0,0,0.15); position:relative;">
            <button onclick="document.getElementById('goalsModal').style.display='none'" style="position:absolute; top:18px; right:18px; background:none; border:none; font-size:22px; color:#888; cursor:pointer;">
                <i class="fas fa-times"></i>
            </button>
            <h2 style="margin-bottom:24px; font-size:1.5rem; font-weight:600; color:#2a7fba;">My Health Goals</h2>
            <?php if (!empty($goals)): ?>
                <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(270px,1fr)); gap:24px;">
                    <?php foreach ($goals as $goal): ?>
                        <div style="background:#f9fbfe; border-radius:10px; box-shadow:0 2px 8px rgba(42,127,186,0.07); padding:22px 18px; display:flex; flex-direction:column; gap:10px; border:1px solid #e1f0ff;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <span style="background:#e1f0ff; color:#2a7fba; border-radius:8px; padding:8px; font-size:20px;">
                                    <i class="fas fa-bullseye"></i>
                                </span>
                                <span style="font-weight:600; font-size:1.1rem;"><?php echo htmlspecialchars($goal['title']); ?></span>
                            </div>
                            <div style="font-size:13px; color:#666;">
                                <span style="font-weight:500;">Type:</span> <?php echo htmlspecialchars(ucfirst($goal['goal_type'])); ?>
                            </div>
                            <div style="font-size:13px; color:#666;">
                                <span style="font-weight:500;">Target:</span> <?php echo htmlspecialchars($goal['target_value']); ?> <?php echo htmlspecialchars($goal['unit']); ?>
                            </div>
                            <div style="font-size:13px; color:#666;">
                                <span style="font-weight:500;">Duration:</span> <?php echo htmlspecialchars($goal['start_date']); ?> to <?php echo htmlspecialchars($goal['end_date']); ?>
                            </div>
                            <?php if (!empty($goal['description'])): ?>
                                <div style="font-size:13px; color:#444; margin-bottom:4px;">
                                    <span style="font-weight:500;">Description:</span> <?php echo htmlspecialchars($goal['description']); ?>
                                </div>
                            <?php endif; ?>
                            <div style="font-size:12px; color:#999;">
                                Created: <?php echo date('M d, Y', strtotime($goal['created_at'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="margin: 20px 0;">You have not set any goals yet.</p>
            <?php endif; 
            ?>
        </div>
    </div>

    <script>
    // Goal type selection logic
    document.addEventListener('DOMContentLoaded', function() {
        const goalTypes = document.querySelectorAll('.goal-type');
        const goalTypeInput = document.getElementById('goal-type');
        goalTypes.forEach(function(typeDiv) {
            typeDiv.addEventListener('click', function() {
                // Remove 'active' from all
                goalTypes.forEach(function(div) { div.classList.remove('active'); });
                // Add 'active' to clicked
                this.classList.add('active');
                // Set hidden input value
                goalTypeInput.value = this.getAttribute('data-type');
            });
        });

        // View Goals button logic
        const viewGoalsBtn = document.getElementById('viewGoalsBtn');
        const goalsModal = document.getElementById('goalsModal');
        if (viewGoalsBtn && goalsModal) {
            viewGoalsBtn.addEventListener('click', function() {
                goalsModal.style.display = 'flex';
            });
        }
    });
    </script>
</body>
</html>