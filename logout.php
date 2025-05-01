<?php
session_start();

// Get user data before destroying session
$user_name = $_SESSION['user_name'] ?? 'User';

// Only destroy session if actually logging out
if (!isset($_GET['return'])) {
    // Clear all session variables
    $_SESSION = array();
    // Destroy the session
    session_destroy();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthTrack Pro - Logout</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            background-blend-mode: overlay;
            background-color: rgba(245, 247, 250, 0.9);
        }
        
        .logout-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .logo {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        
        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        
        p {
            color: #666;
            margin-bottom: 30px;
            font-size: 15px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            font-size: 15px;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
            width: 100%;
        }
        
        .btn-primary:hover {
            background: #236fa3;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #ddd;
            color: #666;
            margin-top: 15px;
            width: 100%;
        }
        
        .btn-outline:hover {
            background: #f5f5f5;
            transform: translateY(-2px);
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
            display: block;
            border: 3px solid var(--primary-color);
        }
        
        .btn i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-heartbeat"></i>
            </div>
            <h1>HealthTrack</h1>
        </div>
        
                
        <h1 id="welcomeMessage">Ready to leave, <?php echo htmlspecialchars($user_name); ?></h1>
        <p>You're about to sign out of your HealthTrack account. Make sure to save any changes before logging out.</p>
        
        <button class="btn btn-primary" id="logoutBtn">
            <i class="fas fa-sign-out-alt"></i> Log Out Now
        </button>
        
        <button class="btn btn-outline" id="cancelBtn">
            <i class="fas fa-arrow-left"></i> Return to Dashboard
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get user data from localStorage
            const userName = localStorage.getItem('userName') || '<?php echo $user_name; ?>';
            const userType = localStorage.getItem('userType') || 'patient';
            const userEmail = localStorage.getItem('userEmail') || '';
            
            // Set welcome message
            document.getElementById('welcomeMessage').textContent = `Ready to leave, ${userName}`;
            
            
            // Logout button
            document.getElementById('logoutBtn').addEventListener('click', function() {
                // Show loading state
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging out...';
                this.disabled = true;
                
                // Clear user session
                localStorage.removeItem('authToken');
                localStorage.removeItem('userType');
                localStorage.removeItem('userName');
                localStorage.removeItem('userEmail');
                
                // Redirect to login page after delay
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 1000);
            });
            
            // Cancel/Return button - redirects to appropriate dashboard
            document.getElementById('cancelBtn').addEventListener('click', function() {
                const dashboardUrl = userType === 'doctor' 
                    ? 'doctor-dashboard.php' 
                    : 'patient-dashboard.php';
                window.location.href = dashboardUrl + '?return=true';
            });
        });
    </script>
</body>
</html>