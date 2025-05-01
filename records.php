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
    
    // Fetch medical records
    $stmt = $conn->prepare("SELECT * FROM medical_records WHERE patient_id = ? ORDER BY upload_date DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records - HealthTrack Pro</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/records.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        }
        
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
            height: 100vh;
            position: fixed;
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
        
        .sidebar-nav li a:hover, 
        .sidebar-nav li.active a {
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
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 240px;
            padding: 30px;
        }
        
        /* Header */
        .records-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .records-header h1 {
            font-size: 28px;
            color: #333;
        }
        
        .records-actions {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
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
        
        .search-box {
            position: relative;
            width: 250px;
        }
        
        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        
        /* Filter Section */
        .records-filter {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .filter-group label {
            font-size: 14px;
            color: #666;
        }
        
        .filter-group select, 
        .filter-group input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        /* Records Grid */
        .records-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
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
            color: var(--primary-color);
            font-size: 20px;
            margin-bottom: 15px;
        }
        
        .record-title {
  word-wrap: break-word;       /* Breaks long words */
  overflow-wrap: break-word;   /* Ensures word wrap compatibility */
  white-space: normal;         /* Allows text to wrap normally */
}

        
        .record-meta {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .record-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
        
        .tag {
            font-size: 12px;
            background: #e1f0ff;
            color: var(--primary-color);
            padding: 4px 10px;
            border-radius: 12px;
        }
        
        .record-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }
        
        .record-date {
            font-size: 12px;
            color: #999;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .records-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
            
            .records-filter {
                flex-direction: column;
                align-items: flex-start;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                flex-direction: row;
                overflow-x: auto;
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .sidebar-nav ul {
                display: flex;
                padding: 0;
            }
            
            .sidebar-nav li a {
                padding: 15px 12px;
                white-space: nowrap;
            }
            
            .sidebar-header, 
            .sidebar-footer {
                display: none;
            }
            
            .records-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .records-actions {
                width: 100%;
                flex-direction: column;
                gap: 10px;
            }
            
            .search-box {
                width: 100%;
            }
        }
        @media (max-width: 1200px) {
            .vitals-grid, .tips-list, 
            .records-list, .goals-list,
            .appointment-list, .actions-grid {
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
            
            .vitals-grid, .tips-list, 
            .records-list, .goals-list,
            .appointment-list, .actions-grid {
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
                        <a href="patient-dashboard.php">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="active">
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
               
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="records-header">
                <h1>Medical Records</h1>
                <div class="records-actions">
                    <button class="btn btn-primary" id="uploadBtn">
                        <i class="fas fa-plus"></i> Upload New
                    </button>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search records...">
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="records-filter">
                <div class="filter-group">
                    <label>Filter by:</label>
                    <select>
                        <option>All Types</option>
                        <option>Lab Results</option>
                        <option>Prescriptions</option>
                        <option>Doctor Notes</option>
                        <option>Imaging</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Date Range:</label>
                    <input type="date" id="startDate">
                    <span>to</span>
                    <input type="date" id="endDate">
                </div>
                <button class="btn btn-outline">Apply Filters</button>
            </div>

            <!-- Records Grid -->
            <div class="records-grid">
                <!-- Record Card 1 -->
                <div class="record-card">
                    <div class="record-icon">
                        <i class="fas fa-vial"></i>
                    </div>
                    <h3 class="record-title">Blood Test Results</h3>
                    <p class="record-meta">LabCorp • May 15, 2023</p>
                    <div class="record-tags">
                        <span class="tag">CBC</span>
                        <span class="tag">Lipid Panel</span>
                        <span class="tag">Glucose</span>
                    </div>
                    <div class="record-actions">
                        <button class="btn btn-outline btn-sm">View Details</button>
                        <span class="record-date">Uploaded: May 16, 2023</span>
                    </div>
                </div>
                
                <!-- Record Card 2 -->
                <div class="record-card">
                    <div class="record-icon">
                        <i class="fas fa-prescription-bottle-alt"></i>
                    </div>
                    <h3 class="record-title">Prescription</h3>
                    <p class="record-meta">Dr.Ramu • June 10, 2023</p>
                    <div class="record-tags">
                        <span class="tag">Metformin</span>
                        <span class="tag">500mg</span>
                    </div>
                    <div class="record-actions">
                        <button class="btn btn-outline btn-sm">View Details</button>
                        <span class="record-date">Uploaded: June 10, 2023</span>
                    </div>
                </div>
                
                <!-- Record Card 3 -->
                <div class="record-card">
                    <div class="record-icon">
                        <i class="fas fa-x-ray"></i>
                    </div>
                    <h3 class="record-title">X-ray Results</h3>
                    <p class="record-meta">Radiology Dept. • May 28, 2023</p>
                    <div class="record-tags">
                        <span class="tag">Chest</span>
                        <span class="tag">Images</span>
                    </div>
                    <div class="record-actions">
                        <button class="btn btn-outline btn-sm">View Details</button>
                        <span class="record-date">Uploaded: May 29, 2023</span>
                    </div>
                </div>
                
                <!-- More record cards would be dynamically inserted here -->
            </div>
        </main>
    </div>

    <script>
    // Add this to your existing JavaScript
    const records = <?php echo json_encode($records); ?>;
    
    function getFileIcon(fileType) {
        if (fileType.includes('pdf')) return 'fa-file-pdf';
        if (fileType.includes('doc')) return 'fa-file-word';
        if (fileType.includes('image')) return 'fa-file-image';
        return 'fa-file-medical';
    }
    
    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    
    function viewRecord(recordId) {
        const record = records.find(r => r.id === recordId);
        if (record) {
            window.open(record.file_path, '_blank');
        }
    }
    
    function deleteRecord(recordId) {
        if (!confirm('Are you sure you want to delete this record?')) {
            return;
        }
    
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('record_id', recordId);
    
        fetch('medical_records_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const recordElement = document.querySelector(`.record-card[data-id="${recordId}"]`);
                if (recordElement) {
                    recordElement.remove();
                }
                alert('Record deleted successfully');
            } else {
                alert('Failed to delete record: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the record');
        });
    }
    
    function displayRecords() {
        const recordsContainer = document.querySelector('.records-grid');
        if (!recordsContainer) return;
        
        recordsContainer.innerHTML = records.map(record => `
            <div class="record-card" data-id="${record.id}">
                <div class="record-icon">
                    <i class="fas ${getFileIcon(record.file_type)}"></i>
                </div>
                <div class="record-info">
                    <h3 class="record-title">${record.file_name}</h3>
                    <p class="record-meta">Uploaded: ${formatDate(record.upload_date)}</p>
                    <div class="record-tags">
                        <span class="tag">${record.file_type.split('/')[1].toUpperCase()}</span>
                    </div>
                </div>
                <div class="record-actions">
                    <button onclick="viewRecord(${record.id})" class="btn btn-outline btn-sm" title="View">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button onclick="deleteRecord(${record.id})" class="btn btn-outline btn-sm delete" title="Delete">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    // File upload handling
    document.addEventListener('DOMContentLoaded', function() {
        displayRecords();
        
        const uploadBtn = document.getElementById('uploadBtn');
        uploadBtn.addEventListener('click', function() {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.multiple = true;
            fileInput.accept = '.pdf,.doc,.docx,.jpg,.jpeg,.png';
            
            fileInput.click();
            
            fileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    const formData = new FormData();
                    formData.append('action', 'create');
                    
                    Array.from(this.files).forEach(file => {
                        formData.append('files[]', file);
                    });
                    
                    fetch('medical_records_handler.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Files uploaded successfully!');
                            location.reload();
                        } else {
                            alert('Upload failed: ' + (data.message || 'Unknown error'));
                            console.error('Upload errors:', data.errors);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred during upload');
                    });
                }
            });
        });
    });
    </script>
    <script src="records.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadBtn = document.getElementById('uploadBtn');
            
            uploadBtn.addEventListener('click', function() {
                // Create a file input element
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = '.pdf,.doc,.docx,.jpg,.jpeg,.png';
                
                fileInput.click();
                
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const formData = new FormData();
                        formData.append('file', this.files[0]);
                        
                        fetch('medical_records_handler.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('File uploaded successfully!');
                                location.reload(); // Refresh to show new record
                            } else {
                                alert('Upload failed: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred during upload');
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>