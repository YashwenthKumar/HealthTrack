<?php
session_start();
$conn = new mysqli("localhost", "root", "", "healthtrack");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to handle database errors
function handleDatabaseError($conn, $query_description) {
    error_log("Database Error ({$query_description}): " . $conn->error);
    return null;
}

// Fetch user data
try {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    if (!$stmt) {
        handleDatabaseError($conn, "prepare user query");
        exit("Database error occurred. Please try again later.");
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        header("Location: logout.php");
        exit();
    }

    // Fetch medical information
    $stmt = $conn->prepare("SELECT * FROM medical_info WHERE user_id = ?");
    if (!$stmt) {
        handleDatabaseError($conn, "prepare medical info query");
        exit("Database error occurred. Please try again later.");
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $medical_info = $result->fetch_assoc();

    // If medical info doesn't exist, create an empty record
    if (!$medical_info) {
        $create_medical_info = $conn->prepare("INSERT INTO medical_info (user_id) VALUES (?)");
        if (!$create_medical_info) {
            handleDatabaseError($conn, "prepare create medical info");
            exit("Database error occurred. Please try again later.");
        }
        $create_medical_info->bind_param("i", $user_id);
        $create_medical_info->execute();
        
        // Fetch the newly created empty record
        $stmt->execute();
        $medical_info = $stmt->get_result()->fetch_assoc();
    }

    // Fetch emergency contacts
    $stmt = $conn->prepare("SELECT * FROM emergency_contacts WHERE user_id = ?");
    if (!$stmt) {
        handleDatabaseError($conn, "prepare emergency contacts query");
        exit("Database error occurred. Please try again later.");
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $emergency_contacts = $result->fetch_all(MYSQLI_ASSOC);

    // Fetch medical history
    $stmt = $conn->prepare("SELECT * FROM medical_history WHERE user_id = ? ORDER BY date DESC");
    if (!$stmt) {
        handleDatabaseError($conn, "prepare medical history query");
        exit("Database error occurred. Please try again later.");
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $medical_history = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Error in profile.php: " . $e->getMessage());
    exit("An error occurred. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthTrack Pro - My Profile</title>
    <link rel="stylesheet" href="profile.css">
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
                        <a href="profile.php">
                            <i class="fas fa-user-circle"></i>
                            <span>My Profile</span>
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
                    <h2>My Profile</h2>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="https://via.placeholder.com/40" alt="User Profile">
                        <span>Welcome, <span id="user-name"><?php echo htmlspecialchars($user['full_name']); ?></span></span>
                    </div>
                </div>
            </header>

            <div class="profile-container">
                <div class="profile-header">
                    <h1>Personal Information</h1>
                    <button class="btn btn-primary" id="edit-profile-btn">Edit Profile</button>
                </div>

                <div class="profile-content">
                    <div class="profile-sidebar">
                        <div class="profile-card">
                            <div class="profile-picture">
                                <img src="https://via.placeholder.com/150" alt="Profile Picture" id="profile-image">
                                <div class="upload-btn">
                                    <button class="btn btn-outline">
                                        <i class="fas fa-camera"></i> Change Photo
                                    </button>
                                    <input type="file" id="profile-upload" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="profile-card quick-links">
                            <h3>Quick Links</h3>
                            <ul>
                                <li><a href="#personal-info" class="active"><i class="fas fa-user"></i> Personal Information</a></li>
                                <li><a href="#medical-info"><i class="fas fa-heartbeat"></i> Medical Information</a></li>
                                <li><a href="#emergency-contacts"><i class="fas fa-phone-alt"></i> Emergency Contacts</a></li>
                                <li><a href="#medical-history"><i class="fas fa-history"></i> Medical History</a></li>
                                <li><a href="#account-settings"><i class="fas fa-cog"></i> Account Settings</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="profile-main">
                        <!-- Personal Information Section -->
                        <section class="profile-section" id="personal-info">
                            <div class="section-header">
                                <h2>Personal Details</h2>
                                <button class="edit-btn" onclick="toggleEdit('personal-details')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>

                            <div class="profile-details" id="personal-details-view">
                                <div class="detail-item">
                                    <label>Full Name</label>
                                    <div class="value" id="full-name-value"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <label>Date of Birth</label>
                                    <div class="value" id="dob-value"><?php echo date('F j, Y', strtotime($user['date_of_birth'])); ?></div>
                                </div>
                                <div class="detail-item">
                                    <label>Gender</label>
                                    <div class="value" id="gender-value"><?php echo htmlspecialchars($user['gender'] ?? 'Not specified'); ?></div>
                                </div>
                                <div class="detail-item">
                                    <label>Email</label>
                                    <div class="value" id="email-value"><?php echo htmlspecialchars($user['email']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <label>Phone Number</label>
                                    <div class="value" id="phone-value"><?php echo htmlspecialchars($user['phone']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <label>Address</label>
                                    <div class="value" id="address-value"><?php echo htmlspecialchars($user['address']); ?></div>
                                </div>
                            </div>

                            <form class="edit-form" id="personal-details-edit" method="POST" action="profile.php">
                                <div class="profile-details">
                                    <div class="form-group">
                                        <label for="full-name">Full Name</label>
                                        <input type="text" id="full-name" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="dob">Date of Birth</label>
                                        <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <select id="gender" name="gender">
                                            <option value="male" <?php if(($user['gender'] ?? '')=='male') echo 'selected'; ?>>Male</option>
                                            <option value="female" <?php if(($user['gender'] ?? '')=='female') echo 'selected'; ?>>Female</option>
                                            <option value="other" <?php if(($user['gender'] ?? '')=='other') echo 'selected'; ?>>Other</option>
                                            <option value="prefer-not-to-say" <?php if(($user['gender'] ?? '')=='prefer-not-to-say') echo 'selected'; ?>>Prefer not to say</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea id="address" name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="button" class="btn btn-outline" onclick="toggleEdit('personal-details')">Cancel</button>
                                    <button type="submit" class="btn btn-primary" name="update_personal">Save Changes</button>
                                </div>
                            </form>
                        </section>

                        <!-- Medical Information Section -->
                        <section class="profile-section" id="medical-info">
                            <div class="section-header">
                                <h2>Medical Information</h2>
                                <button class="edit-btn" onclick="toggleEdit('medical-info')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
<div class="profile-details" id="medical-info-view">
    <div class="detail-item">
        <label>Blood Type</label>
        <div class="value" id="blood-type-value"><?php echo htmlspecialchars($medical_info['blood_type'] ?? 'Not specified'); ?></div>
    </div>
    <div class="detail-item">
        <label>Height</label>
        <div class="value" id="height-value"><?php echo htmlspecialchars($medical_info['height'] ?? 'Not specified'); ?> cm</div>
    </div>
    <div class="detail-item">
        <label>Weight</label>
        <div class="value" id="weight-value"><?php echo htmlspecialchars($medical_info['weight'] ?? 'Not specified'); ?> kg</div>
    </div>
    <div class="detail-item">
        <label>Allergies</label>
        <div class="value" id="allergies-value"><?php echo htmlspecialchars($medical_info['allergies'] ?? 'None'); ?></div>
    </div>
    <div class="detail-item">
        <label>Chronic Conditions</label>
        <div class="value" id="conditions-value"><?php echo htmlspecialchars($medical_info['chronic_conditions'] ?? 'None'); ?></div>
    </div>
    <div class="detail-item">
        <label>Current Medications</label>
        <div class="value" id="medications-value"><?php echo htmlspecialchars($medical_info['current_medications'] ?? 'None'); ?></div>
    </div>
</div>

                            <form id="medical-info-form" class="edit-form">
                                <div class="profile-details">
                                    <div class="form-group">
                                        <label for="blood_type">Blood Type</label>
                                        <select name="blood_type" id="blood_type" required>
                                            <option value="A+" <?php echo ($medical_info['blood_type'] ?? '') === 'A+' ? 'selected' : ''; ?>>A+</option>
                                            <option value="A-" <?php echo ($medical_info['blood_type'] ?? '') === 'A-' ? 'selected' : ''; ?>>A-</option>
                                            <option value="B+" <?php echo ($medical_info['blood_type'] ?? '') === 'B+' ? 'selected' : ''; ?>>B+</option>
                                            <option value="B-" <?php echo ($medical_info['blood_type'] ?? '') === 'B-' ? 'selected' : ''; ?>>B-</option>
                                            <option value="AB+" <?php echo ($medical_info['blood_type'] ?? '') === 'AB+' ? 'selected' : ''; ?>>AB+</option>
                                            <option value="AB-" <?php echo ($medical_info['blood_type'] ?? '') === 'AB-' ? 'selected' : ''; ?>>AB-</option>
                                            <option value="O+" <?php echo ($medical_info['blood_type'] ?? '') === 'O+' ? 'selected' : ''; ?>>O+</option>
                                            <option value="O-" <?php echo ($medical_info['blood_type'] ?? '') === 'O-' ? 'selected' : ''; ?>>O-</option>
                                            <option value="unknown" <?php echo ($medical_info['blood_type'] ?? '') === 'unknown' ? 'selected' : ''; ?>>Unknown</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="height">Height (cm)</label>
                                        <input type="number" step="0.01" name="height" id="height" value="<?php echo htmlspecialchars($medical_info['height'] ?? ''); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="weight">Weight (kg)</label>
                                        <input type="number" step="0.01" name="weight" id="weight" value="<?php echo htmlspecialchars($medical_info['weight'] ?? ''); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="allergies">Allergies</label>
                                        <textarea name="allergies" id="allergies"><?php echo htmlspecialchars($medical_info['allergies'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="chronic_conditions">Chronic Conditions</label>
                                        <textarea name="chronic_conditions" id="chronic_conditions"><?php echo htmlspecialchars($medical_info['chronic_conditions'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="current_medications">Current Medications</label>
                                        <textarea name="current_medications" id="current_medications"><?php echo htmlspecialchars($medical_info['current_medications'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="button" class="btn btn-outline" onclick="toggleEdit('medical-info')">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </section>

                        <!-- Emergency Contacts Section -->
                        <section class="profile-section" id="emergency-contacts">
                            <div class="section-header">
                                <h2>Emergency Contacts</h2>
                                <button class="btn btn-outline" onclick="openAddContactModal()">
                                    <i class="fas fa-plus"></i> Add Contact
                                </button>
                            </div>

<div class="emergency-contacts">
    <?php foreach ($emergency_contacts as $contact): ?>
    <div class="contact-card">
        <h3><?php echo htmlspecialchars($contact['name']); ?></h3>
        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($contact['phone']); ?></p>
        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($contact['email']); ?></p>
        <p><i class="fas fa-user"></i> <?php echo htmlspecialchars($contact['relationship']); ?></p>
    </div>
    <?php endforeach; ?>
    <div class="add-contact-btn" onclick="openAddContactModal()">
        <i class="fas fa-plus-circle" style="font-size: 24px;"></i>
        <span>Add Emergency Contact</span>
    </div>
</div>
                        </section>

                        <!-- Medical History Section -->
                        <section class="profile-section" id="medical-history">
                            <div class="section-header">
                                <h2>Medical History</h2>
                                <button class="btn btn-outline">
                                    <i class="fas fa-plus"></i> Add Record
                                </button>
                            </div>

<div class="medical-history-list">
    <?php foreach ($medical_history as $record): ?>
    <div class="medical-history-item">
        <h3><?php echo htmlspecialchars($record['title']); ?></h3>
        <div class="date"><?php echo date('F j, Y', strtotime($record['date'])); ?></div>
        <div class="description">
            <?php echo htmlspecialchars($record['description']); ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

                                <div class="medical-history-item">
                                    <h3>Asthma Follow-up</h3>
                                    <div class="date">March 3, 2023</div>
                                    <div class="description">
                                        Reported occasional shortness of breath during exercise. 
                                        Doctor adjusted inhaler dosage and recommended pulmonary function test.
                                    </div>
                                </div>
                                <div class="medical-history-item">
                                    <h3>Flu Vaccination</h3>
                                    <div class="date">October 12, 2022</div>
                                    <div class="description">
                                        Received annual flu vaccine. No adverse reactions reported.
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Emergency Contact Modal -->
    <div class="modal" id="contact-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('contact-modal')">&times;</span>
            <h2>Add Emergency Contact</h2>
            <form id="contact-form">
                <div class="form-group">
                    <label for="contact-name">Full Name</label>
                    <input type="text" id="contact-name" required>
                </div>
                <div class="form-group">
                    <label for="contact-relationship">Relationship</label>
                    <input type="text" id="contact-relationship" required>
                </div>
                <div class="form-group">
                    <label for="contact-phone">Phone Number</label>
                    <input type="tel" id="contact-phone" required>
                </div>
                <div class="form-group">
                    <label for="contact-email">Email (Optional)</label>
                    <input type="email" id="contact-email">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('contact-modal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Contact</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal" id="password-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('password-modal')">&times;</span>
            <h2>Change Password</h2>
            <form id="password-form">
                <div class="form-group">
                    <label for="current-password">Current Password</label>
                    <input type="password" id="current-password" required>
                </div>
                <div class="form-group">
                    <label for="new-password">New Password</label>
                    <input type="password" id="new-password" required>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirm New Password</label>
                    <input type="password" id="confirm-password" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('password-modal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
<script src="patients.js"></script>
</body>
</html>

<?php
// Start session and include DB connection here
// session_start();
// include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_personal'])) {
    $full_name = $_POST['full_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, date_of_birth = ?, gender = ?, email = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $full_name, $dob, $gender, $email, $phone, $address, $user_id);
    $stmt->execute();

    // Refresh $user variable after update
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}
