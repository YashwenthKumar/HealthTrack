<?php
session_start();

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Query the database using PDO
    $sql = "SELECT * FROM users WHERE email = ? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email, $role]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        // Redirect based on role
        if ($user['role'] === 'patient') {
            header('Location: patient-dashboard.php');
        } else {
            header('Location: doctor-dashboard.php');
        }
        exit();
    } else {
        $error_message = "Invalid email or password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthTrack Pro - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Global Styles */
        :root {
            --primary-color: #2a7fba;
            --secondary-color: #3bb4c1;
            --accent-color: #048ba8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
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
            line-height: 1.6;
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
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Login Card Styles */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .login-header img {
            width: 80px;
            margin-bottom: 15px;
        }
        
        .login-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .login-header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(42, 127, 186, 0.2);
        }
        
        .role-selection {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .role-option {
            flex: 1;
            text-align: center;
        }
        
        .role-radio {
            display: none;
        }
        
        .role-label {
            display: block;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .role-radio:checked + .role-label {
            background: #e1f0ff;
            border-color: var(--primary-color);
        }
        
        .role-icon {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--primary-color);
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn:hover {
            background-color: var(--accent-color);
            transform: translateY(-2px);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        /* Responsive Styles */
        @media (max-width: 576px) {
            .login-card {
                max-width: 100%;
                margin: 20px;
            }
            
            .role-selection {
                flex-direction: column;
            }
        }
  
        /* Add these styles for the error message */
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #f5c6cb;
            display: none;
        }
        
        .error-message.show {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #c3e6cb;
            display: none;
        }
        
        .success-message.show {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h1> <i class="fas fa-heartbeat"></i> Welcome Back</h1>
                    <p>Please login to access your healthcare portal</p>
                </div>
                
                <div class="login-body">
                    <?php if (isset($_GET['registered']) && $_GET['registered'] == '1'): ?>
                        <div class="success-message show">
                            Registration successful! Please login with your credentials.
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="error-message show">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        
                        <div class="role-selection">
                            <div class="role-option">
                                <input type="radio" name="role" id="patient-role" class="role-radio" value="patient" checked>
                                <label for="patient-role" class="role-label">
                                    <div class="role-icon"><i class="fas fa-user-injured"></i></div>
                                    <div>Patient</div>
                                </label>
                            </div>
                            
                            <div class="role-option">
                                <input type="radio" name="role" id="doctor-role" class="role-radio" value="doctor">
                                <label for="doctor-role" class="role-label">
                                    <div class="role-icon"><i class="fas fa-user-md"></i></div>
                                    <div>Doctor</div>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn">Login</button>
                        
                        <div class="login-footer">
                            <p>Don't have an account? <a href="register.php">Sign up</a></p>
                            <p><a href="forgot-password.html">Forgot password?</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const role = document.querySelector('input[name="role"]:checked').value;
            
            // Show loading state
            const btn = document.querySelector('.btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Authenticating...';
            btn.disabled = true;
            
            // In a real app, you would make an API call here
            // For now, we'll simulate the login process
            authenticateUser(email, password, role);
        });
        
        async function authenticateUser(email, password, role) {
            try {
                // This would be replaced with actual API call in production
                // const response = await fetch('/api/login', {
                //     method: 'POST',
                //     headers: { 'Content-Type': 'application/json' },
                //     body: JSON.stringify({ email, password, role })
                // });
                // const data = await response.json();
                
                // Simulated response for demo purposes
                const data = {
                    success: true,
                    userType: role, // 'doctor' or 'patient'
                    token: 'simulated-token-12345',
                    name: role === 'doctor' ? 'Dr. Smith' : 'Patient Name'
                };
                
                if (data.success) {
                    // Store user session information
                    localStorage.setItem('authToken', data.token);
                    localStorage.setItem('userType', data.userType);
                    localStorage.setItem('userName', data.name);
                    localStorage.setItem('userEmail', email);
                    
                    // Redirect to appropriate dashboard
                    if (data.userType === 'doctor') {
                        window.location.href = 'doctor-dashboard.php';
                    } else {
                        // Fix: Correct the case in the file name
                        window.location.href = 'patient-dashboard.php';
                    }
                } else {
                    // Handle login failure
                    alert('Login failed. Please check your credentials.');
                    document.querySelector('.btn').innerHTML = 'Login';
                    document.querySelector('.btn').disabled = false;
                }
            } catch (error) {
                console.error('Login error:', error);
                alert('An error occurred during login. Please try again.');
                document.querySelector('.btn').innerHTML = 'Login';
                document.querySelector('.btn').disabled = false;
            }
        }
        
        // Demo credentials for testing (remove in production)
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('demo') === '1') {
                document.getElementById('email').value = 'doctor@healthtrack.com';
                document.getElementById('password').value = 'doctor123';
                document.getElementById('doctor-role').checked = true;
            } else if (urlParams.get('demo') === '2') {
                document.getElementById('email').value = 'patient@healthtrack.com';
                document.getElementById('password').value = 'patient123';
                document.getElementById('patient-role').checked = true;
            }
        });
    </script>
</body>
</html>