<?php
session_start();

// Add database connection using PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname=healthtrack", 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch existing user settings
$user_settings = [];
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $conn->prepare("SELECT * FROM user_settings WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $user_settings = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching user settings: " . $e->getMessage());
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    // Validate required fields
    if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email'])) {
        $_SESSION['error_message'] = "Please fill in all required fields";
        header("Location: settings.php");
        exit();
    }

    // Get form data with proper fallbacks
    $first_name = $_POST['first_name'] ?? ($user_settings['first_name'] ?? '');
    $last_name = $_POST['last_name'] ?? ($user_settings['last_name'] ?? '');
    $email = $_POST['email'] ?? ($user_settings['email'] ?? '');
    $phone = $_POST['phone'] ?? ($user_settings['phone'] ?? '');
    $dob = $_POST['dob'] ?? ($user_settings['dob'] ?? '');
    $gender = $_POST['gender'] ?? ($user_settings['gender'] ?? '');
    $blood_type = $_POST['blood_type'] ?? ($user_settings['blood_type'] ?? '');
    $allergies = $_POST['allergies'] ?? ($user_settings['allergies'] ?? '');
    $conditions = $_POST['conditions'] ?? ($user_settings['conditions'] ?? '');
    
    // Get user ID from session
    $user_id = $_SESSION['user_id'] ?? 0;
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Add debug logging
        error_log("Updating profile for user ID: $user_id");
        
        // Update users table
        $stmt = $conn->prepare("UPDATE users SET 
            first_name = :first_name, 
            last_name = :last_name, 
            email = :email, 
            phone = :phone
            WHERE id = :user_id");
        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':phone' => $phone,
            ':user_id' => $user_id
        ]);
        
        // Add debug logging
        error_log("Updated users table, affected rows: " . $stmt->rowCount());
        
        // Update or insert user_settings
        $check = $conn->prepare("SELECT COUNT(*) FROM user_settings WHERE user_id = :user_id");
        $check->execute([':user_id' => $user_id]);
        $exists = $check->fetchColumn();
        
        if ($exists) {
            $stmt = $conn->prepare("UPDATE user_settings SET 
                dob = :dob, 
                gender = :gender, 
                blood_type = :blood_type, 
                allergies = :allergies, 
                conditions = :conditions 
                WHERE user_id = :user_id");
        } else {
            $stmt = $conn->prepare("INSERT INTO user_settings 
                (user_id, dob, gender, blood_type, allergies, conditions) 
                VALUES 
                (:user_id, :dob, :gender, :blood_type, :allergies, :conditions)");
        }
        
        $stmt->execute([
            ':dob' => $dob,
            ':gender' => $gender,
            ':blood_type' => $blood_type,
            ':allergies' => $allergies,
            ':conditions' => $conditions,
            ':user_id' => $user_id
        ]);
        
        $conn->commit();
        $_SESSION['success_message'] = "Profile updated successfully!";
    } catch(PDOException $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = "Error updating profile: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthTrack Pro - Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="settings.css">
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
            <?php
            if (!empty($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger">'.htmlspecialchars($_SESSION['error_message']).'</div>';
                unset($_SESSION['error_message']);
            }
            if (!empty($_SESSION['success_message'])) {
                echo '<div class="alert alert-success">'.htmlspecialchars($_SESSION['success_message']).'</div>';
                unset($_SESSION['success_message']);
            }
            ?>
            <!-- Header -->
            <header class="top-header">
                <div class="header-left">
                    <h2>Account Settings</h2>
                    <p id="current-date">Monday, June 12, 2023</p>
                </div>

            </header>

            <!-- Settings Container -->
            <div class="settings-container">
                <div class="settings-header">
                    <h2><i class="fas fa-cog"></i> Settings</h2>
                </div>
                
                <!-- Settings Tabs -->
                <div class="settings-tabs">
                    <div class="settings-tab active" data-tab="profile">Profile</div>
                    <div class="settings-tab" data-tab="security">Security</div>
                    <div class="settings-tab" data-tab="notifications">Notifications</div>
                    <div class="settings-tab" data-tab="privacy">Privacy</div>
                    <div class="settings-tab" data-tab="devices">Connected Devices</div>
                </div>
                
                <!-- Profile Settings -->
                <div class="settings-content active" id="profile-settings">
                    <div class="settings-section">
                        <h3>Personal Information</h3>
                        <div class="profile-picture">
                            <?php
                            $profilePic = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'https://randomuser.me/api/portraits/men/32.jpg';
                            
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
                                $targetDir = "uploads/";
                                if (!is_dir($targetDir)) {
                                    mkdir($targetDir, 0777, true);
                                }
                                $fileName = basename($_FILES["profile_picture"]["name"]);
                                $targetFile = $targetDir . uniqid() . "_" . $fileName;
                                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                            
                                if (in_array($imageFileType, $allowedTypes) && move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
                                    $_SESSION['profile_pic'] = $targetFile;
                                    $profilePic = $targetFile;
                                }
                            }
                            ?>
                            <div class="profile-picture">
                                <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" id="profilePicPreview">
                                <form method="POST" enctype="multipart/form-data" style="display:inline;">
                                    <input type="file" name="profile_picture" id="profilePictureInput" style="display:none;" accept="image/*" onchange="this.form.submit()">
                                    <div class="profile-picture-actions">
                                        <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('profilePictureInput').click();">
                                            <i class="fas fa-camera"></i> Change Photo
                                        </button>
                                        <button type="submit" name="remove_photo" class="btn btn-outline btn-sm">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Add the form tag here -->
                        <form method="POST" action="settings.php">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <input type="text" id="firstName" name="first_name" class="form-control" value="<?php 
                                        echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : 
                                        (isset($user_settings['first_name']) ? htmlspecialchars($user_settings['first_name']) : 'Yashwenth'); 
                                    ?>">
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" id="lastName" name="last_name" class="form-control" value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : 'Kumar'; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : 'yashwenth@example.com'; ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '+91 9392023299'; ?>">
                            </div>
                            <div class="form-group">
                                <label for="dob">Date of Birth</label>
                                <input type="date" id="dob" name="dob" class="form-control" value="<?php echo isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : '2006-07-17'; ?>">
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender" class="form-control">
                                    <option value="male" <?php echo (isset($_POST['gender']) && $_POST['gender']=='male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo (isset($_POST['gender']) && $_POST['gender']=='female') ? 'selected' : ''; ?>>Female</option>
                                    <option value="other" <?php echo (isset($_POST['gender']) && $_POST['gender']=='other') ? 'selected' : ''; ?>>Other</option>
                                    <option value="prefer-not-to-say" <?php echo (isset($_POST['gender']) && $_POST['gender']=='prefer-not-to-say') ? 'selected' : ''; ?>>Prefer not to say</option>
                                </select>
                            </div>
                            <!-- Health Information -->
                            <div class="settings-section">
                                <h3>Health Information</h3>
                                <div class="form-group">
                                    <label for="bloodType">Blood Type</label>
                                    <select id="bloodType" name="blood_type" class="form-control">
                                        <option value="">Select blood type</option>
                                        <option value="A+" <?php if((isset($_POST['blood_type']) && $_POST['blood_type']=='A+')) echo 'selected'; ?>>A+</option>
                                        <option value="A-" <?php if((isset($_POST['blood_type']) && $_POST['blood_type']=='A-')) echo 'selected'; ?>>A-</option>
                                        <option value="B+" <?php if((isset($_POST['blood_type']) && $_POST['blood_type']=='B+')) echo 'selected'; ?>>B+</option>
                                        <option value="B-" <?php if((isset($_POST['blood_type']) && $_POST['blood_type']=='B-')) echo 'selected'; ?>>B-</option>
                                        <option value="AB+" <?php if((isset($_POST['blood_type']) && $_POST['blood_type']=='AB+')) echo 'selected'; ?>>AB+</option>
                                        <option value="AB-" <?php if((isset($_POST['blood_type']) && $_POST['blood_type']=='AB-')) echo 'selected'; ?>>AB-</option>
                                        <option value="O+" <?php if((isset($_POST['blood_type']) && $_POST['blood_type']=='O+')) echo 'selected'; ?>>O+</option>
                                        <option value="O-" <?php if((isset($_POST['blood_type']) && $_POST['blood_type']=='O-')) echo 'selected'; ?>>O-</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="allergies">Allergies</label>
                                    <textarea id="allergies" name="allergies" class="form-control" rows="3" placeholder="List any allergies you have"><?php echo isset($_POST['allergies']) ? htmlspecialchars($_POST['allergies']) : 'Penicillin, Peanuts'; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="conditions">Medical Conditions</label>
                                    <textarea id="conditions" name="conditions" class="form-control" rows="3" placeholder="List any chronic conditions"><?php echo isset($_POST['conditions']) ? htmlspecialchars($_POST['conditions']) : 'Type 2 Diabetes'; ?></textarea>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button class="btn btn-outline" type="reset">Cancel</button>
                                <button class="btn btn-primary" type="submit" name="save_profile">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- END Profile Settings -->
                <div class="settings-content" id="security-settings">
                    <div class="settings-section">
                        <h3>Password</h3>
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" id="currentPassword" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" id="newPassword" class="form-control">
                            <small class="text-muted">Minimum 8 characters with at least one number and one special character</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirmPassword">Confirm New Password</label>
                            <input type="password" id="confirmPassword" class="form-control">
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h3>Two-Factor Authentication</h3>
                        <div class="toggle-item">
                            <div class="toggle-info">
                                <h4>Enable Two-Factor Authentication</h4>
                                <p>Add an extra layer of security to your account</p>
                            </div>
                            <label class="toggle-label">
                                <span>Off</span>
                                <div class="toggle-switch">
                                    <input type="checkbox">
                                    <span class="toggle-slider"></span>
                                </div>
                                <span>On</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h3>Active Sessions</h3>
                        <div class="device-card">
                            <div class="device-info">
                                <div class="device-icon">
                                    <i class="fas fa-desktop"></i>
                                </div>
                                <div class="device-meta">
                                    <h4>Windows 10 - Chrome</h4>
                                    <p>Current session • Bangalore, India</p>
                                </div>
                            </div>
                            <div class="device-actions">
                                <button class="btn btn-outline btn-sm">Log Out</button>
                            </div>
                        </div>
                        
                        <div class="device-card">
                            <div class="device-info">
                                <div class="device-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="device-meta">
                                    <h4>iPhone 13 - Safari</h4>
                                    <p>Last active 2 hours ago • Bangalore, India</p>
                                </div>
                            </div>
                            <div class="device-actions">
                                <button class="btn btn-outline btn-sm">Log Out</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button class="btn btn-outline">Cancel</button>
                        <button class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
                
                <!-- Notification Settings -->
                <div class="settings-content" id="notification-settings">
                    <div class="settings-section">
                        <h3>Notification Preferences</h3>
                        <div class="toggle-container">
                            <div class="toggle-item">
                                <div class="toggle-info">
                                    <h4>Email Notifications</h4>
                                    <p>Receive important updates via email</p>
                                </div>
                                <label class="toggle-label">
                                    <span>Off</span>
                                    <div class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </div>
                                    <span>On</span>
                                </label>
                            </div>
                            
                            <div class="toggle-item">
                                <div class="toggle-info">
                                    <h4>SMS Notifications</h4>
                                    <p>Receive urgent alerts via text message</p>
                                </div>
                                <label class="toggle-label">
                                    <span>Off</span>
                                    <div class="toggle-switch">
                                        <input type="checkbox">
                                        <span class="toggle-slider"></span>
                                    </div>
                                    <span>On</span>
                                </label>
                            </div>
                            
                            <div class="toggle-item">
                                <div class="toggle-info">
                                    <h4>Push Notifications</h4>
                                    <p>Get instant alerts on your devices</p>
                                </div>
                                <label class="toggle-label">
                                    <span>Off</span>
                                    <div class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </div>
                                    <span>On</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h3>Notification Types</h3>
                        <div class="toggle-container">
                            <div class="toggle-item">
                                <div class="toggle-info">
                                    <h4>Appointment Reminders</h4>
                                    <p>Get reminders before your appointments</p>
                                </div>
                                <label class="toggle-label">
                                    <span>Off</span>
                                    <div class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </div>
                                    <span>On</span>
                                </label>
                            </div>
                            
                            <div class="toggle-item">
                                <div class="toggle-info">
                                    <h4>Medication Alerts</h4>
                                    <p>Reminders to take your medications</p>
                                </div>
                                <label class="toggle-label">
                                    <span>Off</span>
                                    <div class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </div>
                                    <span>On</span>
                                </label>
                            </div>
                            
                            <div class="toggle-item">
                                <div class="toggle-info">
                                    <h4>Test Results</h4>
                                    <p>Alerts when new test results are available</p>
                                </div>
                                <label class="toggle-label">
                                    <span>Off</span>
                                    <div class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </div>
                                    <span>On</span>
                                </label>
                            </div>
                            
                            <div class="toggle-item">
                                <div class="toggle-info">
                                    <h4>Health Tips</h4>
                                    <p>Weekly health and wellness tips</p>
                                </div>
                                <label class="toggle-label">
                                    <span>Off</span>
                                    <div class="toggle-switch">
                                        <input type="checkbox">
                                        <span class="toggle-slider"></span>
                                    </div>
                                    <span>On</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button class="btn btn-outline">Cancel</button>
                        <button class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
                
                <!-- Privacy Settings -->
                <div class="settings-content" id="privacy-settings">
                    <div class="settings-section">
                        <h3>Data Sharing</h3>
                        <div class="toggle-container">
                            <div class="toggle-item">
                                <div class="toggle-info">
                                    <h4>Share with Healthcare Providers</h4>
                                    <p>Allow doctors and clinics you visit to access your medical records</p>
                                </div>
                                <label class="toggle-label">
                                    <span>Off</span>
                                    <div class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </div>
                                    <span>On</span>
                                </label>
                            </div>
                            
                            <div class="toggle-item">
                                <div class="toggle-info">
                                    <h4>Share for Research</h4>
                                    <p>Anonymously contribute your health data to medical research</p>
                                </div>
                                <label class="toggle-label">
                                    <span>Off</span>
                                    <div class="toggle-switch">
                                        <input type="checkbox">
                                        <span class="toggle-slider"></span>
                                    </div>
                                    <span>On</span>
                                </label>
                            </div>
                            
                            <div class="toggle-item">
                                <div class="toggle-info">
                                    <h4>Emergency Access</h4>
                                    <p>Allow emergency responders to access critical health information</p>
                                </div>
                                <label class="toggle-label">
                                    <span>Off</span>
                                    <div class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </div>
                                    <span>On</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h3>Data Download & Deletion</h3>
                        <div class="form-group">
                            <label>Download Your Data</label>
                            <button class="btn btn-outline"><i class="fas fa-download"></i> Request Data Export</button>
                            <small class="text-muted">You'll receive an email with a link to download all your health data</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Delete Account</label>
                            <button class="btn btn-outline" style="color: var(--danger-color); border-color: var(--danger-color);">
                                <i class="fas fa-trash"></i> Delete My Account
                            </button>
                            <small class="text-muted">This will permanently delete all your data and cannot be undone</small>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button class="btn btn-outline">Cancel</button>
                        <button class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
                
                <!-- Connected Devices -->
                <div class="settings-content" id="devices-settings">
                    <div class="settings-section">
                        <h3>Connected Health Devices</h3>
                        <div class="connected-devices">
                            <div class="device-card">
                                <div class="device-info">
                                    <div class="device-icon">
                                        <i class="fas fa-heartbeat"></i>
                                    </div>
                                    <div class="device-meta">
                                        <h4>Fitbit Charge 4</h4>
                                        <p>Connected 3 days ago • Heart rate, activity tracking</p>
                                    </div>
                                </div>
                                <div class="device-actions">
                                    <button class="btn btn-outline btn-sm">Disconnect</button>
                                </div>
                            </div>
                            
                            <div class="device-card">
                                <div class="device-info">
                                    <div class="device-icon">
                                        <i class="fas fa-weight"></i>
                                    </div>
                                    <div class="device-meta">
                                        <h4>Withings Body+ Scale</h4>
                                        <p>Connected 2 weeks ago • Weight, body composition</p>
                                    </div>
                                </div>
                                <div class="device-actions">
                                    <button class="btn btn-outline btn-sm">Disconnect</button>
                                </div>
                            </div>
                            
                            <div class="device-card">
                                <div class="device-info">
                                    <div class="device-icon">
                                        <i class="fas fa-tint"></i>
                                    </div>
                                    <div class="device-meta">
                                        <h4>OneTouch Glucose Meter</h4>
                                        <p>Connected 1 month ago • Blood glucose readings</p>
                                    </div>
                                </div>
                                <div class="device-actions">
                                    <button class="btn btn-outline btn-sm">Disconnect</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h3>Connect New Device</h3>
                        <div class="form-group">
                            <label for="deviceType">Device Type</label>
                            <select id="deviceType" class="form-control">
                                <option value="">Select device type</option>
                                <option value="fitness-tracker">Fitness Tracker</option>
                                <option value="smart-scale">Smart Scale</option>
                                <option value="glucose-meter">Glucose Meter</option>
                                <option value="blood-pressure">Blood Pressure Monitor</option>
                                <option value="other">Other Device</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="deviceBrand">Device Brand</label>
                            <select id="deviceBrand" class="form-control">
                                <option value="">Select brand</option>
                                <option value="fitbit">Fitbit</option>
                                <option value="apple">Apple</option>
                                <option value="withings">Withings</option>
                                <option value="onetouch">OneTouch</option>
                                <option value="omron">Omron</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="deviceModel">Device Model</label>
                            <input type="text" id="deviceModel" class="form-control" placeholder="Enter device model">
                        </div>
                        
                        <button class="btn btn-primary"><i class="fas fa-link"></i> Connect Device</button>
                    </div>
                    
                    <div class="form-actions">
                        <button class="btn btn-outline">Cancel</button>
                        <button class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="settings.js"></script>

</body>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get current page path
    const currentPage = window.location.pathname.split('/').pop();
    
    // Find all sidebar links
    const sidebarLinks = document.querySelectorAll('.sidebar-nav li a');
    
    // Remove active class from all links
    sidebarLinks.forEach(link => {
        link.parentElement.classList.remove('active');
    });
    
    // Add active class to current page link
    sidebarLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.parentElement.classList.add('active');
        }
    });
});
</script>
</body>
</html>
