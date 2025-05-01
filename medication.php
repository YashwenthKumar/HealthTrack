<?php
// Start the session
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "healthtrack");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data (assuming user is logged in)
$user_id = $_SESSION['user_id'] ?? 1; // Default to 1 for testing
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch medications for the user
$medications_query = "SELECT * FROM medications WHERE user_id = ? ORDER BY next_dose ASC";
$stmt = $conn->prepare($medications_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$medications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Group medications by time of day
$morning_meds = array_filter($medications, function($med) {
    return strpos(strtolower($med['schedule']), 'morning') !== false;
});
$afternoon_meds = array_filter($medications, function($med) {
    return strpos(strtolower($med['schedule']), 'afternoon') !== false;
});
$evening_meds = array_filter($medications, function($med) {
    return strpos(strtolower($med['schedule']), 'evening') !== false;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthTrack Pro - Medications</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="medication.css">
</head>
<body>
    <div class="dashboard-container">
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
                    <li class="active">
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
                    
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h2>Medication Management</h2>
                    <p id="current-date"></p>
                </div>
                <div class="user-profile">
                    <span>Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                </div>
            </header>

            <div class="medications-container">
                <div class="medications-header">
                    <h1>My Medications</h1>
                    <button class="btn btn-primary" id="addMedicationBtn">
                        <i class="fas fa-plus"></i> Add Medication
                    </button>
                </div>

                <!-- Today's Medications Section -->
                <div class="medication-reminder">
                    <h2><i class="fas fa-bell"></i> Today's Medications</h2>
                    <div class="reminder-cards">
                        <!-- Morning Medications -->
                        <div class="time-slot morning">
                            <h3>Morning <span>6:00 AM - 12:00 PM</span></h3>
                            <?php foreach ($morning_meds as $med): ?>
                                <div class="medication-card" data-med-id="<?php echo $med['id']; ?>">
                                    <div class="medication-info">
                                        <h4><?php echo htmlspecialchars($med['name']); ?></h4>
                                        <p><?php echo htmlspecialchars($med['dosage']); ?></p>
                                        <p><i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($med['purpose']); ?></p>
                                    </div>
                                    <div class="medication-actions">
                                        <button class="btn-icon btn-edit" data-med-id="<?php echo $med['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-delete" data-med-id="<?php echo $med['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Afternoon Medications -->
                        <div class="time-slot afternoon">
                            <h3>Afternoon <span>12:00 PM - 6:00 PM</span></h3>
                            <?php foreach ($afternoon_meds as $med): ?>
                                <div class="medication-card" data-med-id="<?php echo $med['id']; ?>">
                                    <div class="medication-info">
                                        <h4><?php echo htmlspecialchars($med['name']); ?></h4>
                                        <p><?php echo htmlspecialchars($med['dosage']); ?></p>
                                        <p><i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($med['purpose']); ?></p>
                                    </div>
                                    <div class="medication-actions">
                                        <button class="btn-icon btn-edit" data-med-id="<?php echo $med['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-delete" data-med-id="<?php echo $med['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Evening Medications -->
                        <div class="time-slot evening">
                            <h3>Evening <span>6:00 PM - 12:00 AM</span></h3>
                            <?php foreach ($evening_meds as $med): ?>
                                <div class="medication-card" data-med-id="<?php echo $med['id']; ?>">
                                    <div class="medication-info">
                                        <h4><?php echo htmlspecialchars($med['name']); ?></h4>
                                        <p><?php echo htmlspecialchars($med['dosage']); ?></p>
                                        <p><i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($med['purpose']); ?></p>
                                    </div>
                                    <div class="medication-actions">
                                        <button class="btn-icon btn-edit" data-med-id="<?php echo $med['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-delete" data-med-id="<?php echo $med['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- All Medications List -->
                <div class="medication-list">
                    <h2><i class="fas fa-list"></i> All Medications</h2>
                    <div class="list-header">
                        <span>Medication</span>
                        <span>Dosage</span>
                        <span>Frequency</span>
                        <span>Next Dose</span>
                        <span>Actions</span>
                    </div>
                    <div class="list-items">
                        <?php foreach ($medications as $med): ?>
    <div class="list-item" data-med-id="<?php echo $med['id']; ?>">
        <div class="medication-name">
            <i class="fas fa-pills"></i>
            <div>
                <h4><?php echo htmlspecialchars($med['name']); ?></h4>
                <p><?php echo htmlspecialchars($med['purpose']); ?></p>
            </div>
        </div>
        <div class="medication-dosage"><?php echo htmlspecialchars($med['dosage']); ?></div>
        <div class="medication-frequency"><?php echo htmlspecialchars($med['schedule']); ?></div>
        <div class="medication-next">
            <?php
            if (!empty($med['next_dose']) && strtotime($med['next_dose']) !== false) {
                echo date('M d, Y h:i A', strtotime($med['next_dose']));
            } else {
                echo "N/A";
            }
            ?>
        </div>
        <div class="medication-actions">
            <button class="btn-icon btn-edit" data-med-id="<?php echo $med['id']; ?>">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn-icon btn-delete" data-med-id="<?php echo $med['id']; ?>">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
<?php endforeach; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Medication Modal -->
    <div class="modal" id="medicationModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3 id="modalTitle">Add New Medication</h3>
            <form id="medicationForm">
                <input type="hidden" id="medicationId" name="id">
                <div class="form-group">
                    <label for="medicationName">Medication Name</label>
                    <input type="text" id="medicationName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="medDosage">Dosage</label>
                    <input type="text" id="medDosage" name="dosage" required>
                </div>
                <div class="form-group">
                    <label for="medSchedule">Schedule</label>
                    <select id="medSchedule" name="schedule" required>
                        <option value="morning">Morning</option>
                        <option value="afternoon">Afternoon</option>
                        <option value="evening">Evening</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="medPurpose">Purpose</label>
                    <textarea id="medPurpose" name="purpose" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Medication</button>
            </form>
        </div>
    </div>

    
    <!-- Edit Medication Modal -->
    <div id="editMedicationModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Edit Medication</h2>
            <form id="editMedicationForm">
                <input type="hidden" id="edit_medication_id">
                <div class="form-group">
                    <label for="edit_medication_name">Medication Name</label>
                    <input type="text" id="edit_medication_name" required>
                </div>
                <div class="form-group">
                    <label for="edit_medication_dosage">Dosage</label>
                    <input type="text" id="edit_medication_dosage" required>
                </div>
                <div class="form-group">
                    <label for="edit_medication_schedule">Schedule</label>
                    <select id="edit_medication_schedule" required>
                        <option value="Morning">Morning</option>
                        <option value="Afternoon">Afternoon</option>
                        <option value="Evening">Evening</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_medication_purpose">Purpose</label>
                    <textarea id="edit_medication_purpose" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
    <!-- Edit Medication Modal -->
    <div id="editMedicationModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Edit Medication</h2>
            <form id="editMedicationForm">
                <input type="hidden" id="edit_medication_id" name="medication_id">
                <div class="form-group">
                    <label for="edit_medication_name">Medication Name</label>
                    <input type="text" id="edit_medication_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="edit_medication_dosage">Dosage</label>
                    <input type="text" id="edit_medication_dosage" name="dosage" required>
                </div>
                <div class="form-group">
                    <label for="edit_medication_schedule">Schedule</label>
                    <select id="edit_medication_schedule" name="schedule" required>
                        <option value="Morning">Morning</option>
                        <option value="Afternoon">Afternoon</option>
                        <option value="Evening">Evening</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_medication_purpose">Purpose</label>
                    <textarea id="edit_medication_purpose" name="purpose" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="medications.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add Medication Button Handler
    var addBtn = document.getElementById('addMedicationBtn');
    var modal = document.getElementById('medicationModal');
    var form = document.getElementById('medicationForm');
    var titleEl = document.getElementById('modalTitle');
    var closeBtns = document.querySelectorAll('.close-modal');

    if (addBtn && modal && form && titleEl) {
        addBtn.addEventListener('click', function() {
            // Reset form for new medication
            form.reset();
            document.getElementById('medicationId').value = '';
            titleEl.textContent = 'Add New Medication';
            modal.style.display = 'block';
        });
    }

    // Close modal on close button click
    closeBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            btn.closest('.modal').style.display = 'none';
        });
    });

    // Close modal when clicking outside modal content
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    };

    // Edit Medication Handler for both cards and list
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const medId = this.getAttribute('data-med-id');
            const modal = document.getElementById('medicationModal');
            const form = document.getElementById('medicationForm');
            const titleEl = document.getElementById('modalTitle');
            if (!modal || !form || !titleEl) return;
            titleEl.textContent = 'Edit Medication';
            document.getElementById('medicationId').value = medId;
            // Fetch medication details
            fetch(`get_medication.php?id=${medId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('medicationName').value = data.name || '';
                    document.getElementById('medDosage').value = data.dosage || '';
                    document.getElementById('medSchedule').value = data.schedule || 'morning';
                    document.getElementById('medPurpose').value = data.purpose || '';
                    modal.style.display = 'block';
                })
                .catch(error => {
                    alert('Failed to load medication details');
                });
        });
    });

    // Add this block to handle form submission for adding/editing medication
    var medicationForm = document.getElementById('medicationForm');
    if (medicationForm) {
        medicationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = {
                id: document.getElementById('medicationId').value,
                name: document.getElementById('medicationName').value,
                dosage: document.getElementById('medDosage').value,
                schedule: document.getElementById('medSchedule').value,
                purpose: document.getElementById('medPurpose').value
            };
            fetch('save_medication.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Optionally, reload the page or update the medication list dynamically
                    window.location.reload();
                } else {
                    alert('Failed to save medication: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error saving medication');
            });
        });
    }
});
</script>
</body>
</html>
