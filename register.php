<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'healthtrack';

$errors = [];
$conn = null;

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $errors[] = "Database connection failed: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $agreeTerms = isset($_POST['agreeTerms']);

    // Validation
    if (empty($firstName) || empty($lastName)) {
        $errors[] = "First and last name are required";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (!in_array($role, ['patient', 'doctor'])) {
        $errors[] = "Invalid role selected";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }
    if (!$agreeTerms) {
        $errors[] = "You must agree to the terms and conditions";
    }

    if (empty($errors) && $conn) {
        try {
            // Check if email exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Email already registered";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, role, password) 
                                      VALUES (?, ?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$firstName, $lastName, $email, $phone, $role, $hashedPassword])) {
                    // Instead of setting session variables and redirecting to dashboard,
                    // redirect to login page with a success message
                    header("Location: login.php?registered=1");
                    exit();
                } else {
                    $errors[] = "Registration failed. Please try again.";
                }
            }
        } catch (PDOException $e) {
            $errors[] = "An error occurred during registration. Please try again.";
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthTrack Pro - Register</title>
    <link rel="stylesheet" href="assets/css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
          :root {
    --primary: #1a73e8; /* Google blue - trustworthy and professional */
    --primary-dark: #0d47a1;
    --primary-light: #e8f0fe;
    --secondary: #34a853; /* Green for health/positive actions */
    --text-dark: #202124;
    --text-medium: #5f6368;
    --text-light: #ffffff;
    --border-color: #dadce0;
    --success: #34a853;
    --warning: #fbbc05;
    --error: #ea4335;
    --background: #f8f9fa;
}

body {
    font-family: 'Roboto', sans-serif;
    background-color: var(--background);
    margin: 0;
    padding: 0;
    color: var(--text-dark);
    line-height: 1.6;
}

.auth-container {
    max-width: 480px;
    margin: 40px auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    padding: 40px;
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.logo {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.logo-icon {
    width: 48px;
    height: 48px;
    background: var(--primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
}

.logo-icon svg {
    width: 24px;
    height: 24px;
}

.logo-text {
    font-size: 24px;
    font-weight: 500;
    color: var(--primary);
    margin: 0;
}

.auth-header p {
    color: var(--text-medium);
    margin: 0;
    font-size: 15px;
}

.auth-form {
    display: flex;
    flex-direction: column;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-dark);
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    font-size: 15px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 2px var(--primary-light);
}

.password-wrapper {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    user-select: none;
    font-size: 18px;
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.toggle-password:hover {
    opacity: 1;
}

.btn {
    padding: 12px 24px;
    font-size: 15px;
    font-weight: 500;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary {
    background-color: var(--primary);
    color: white;
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    box-shadow: 0 2px 6px rgba(26, 115, 232, 0.3);
}

.auth-footer {
    text-align: center;
    margin-top: 20px;
    font-size: 14px;
    color: var(--text-medium);
}

.auth-footer a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
}

.auth-footer a:hover {
    text-decoration: underline;
}

/* Password strength indicator */
.strength-bar[data-strength="1"] {
    background-color: var(--error);
}

.strength-bar[data-strength="2"] {
    background-color: var(--warning);
}

.strength-bar[data-strength="3"] {
    background-color: var(--success);
}

.strength-text {
    font-size: 13px;
}

/* Terms agreement checkbox */
.terms-agreement input[type="checkbox"] {
    margin-top: 3px;
}

.terms-agreement label {
    font-size: 13px;
    color: var(--text-medium);
    line-height: 1.5;
}

/* Responsive adjustments */
@media (max-width: 600px) {
    .auth-container {
        margin: 20px;
        padding: 25px;
    }
    
    .form-row {
        flex-direction: column;
        gap: 0;
    }
}

/* Trust indicators */
.trust-badges {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.trust-badge {
    display: flex;
    align-items: center;
    font-size: 12px;
    color: var(--text-medium);
}

.trust-badge svg {
    width: 16px;
    height: 16px;
    margin-right: 6px;
    fill: var(--primary);
}

/* Add this inside your auth-header after the logo */
.trust-seal {
    margin-top: 20px;
    padding: 12px;
    background-color: var(--primary-light);
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    font-size: 13px;
    color: var(--primary-dark);
    font-weight: 500;
}

.trust-seal svg {
    width: 18px;
    height: 18px;
    margin-right: 8px;
    fill: var(--primary);
}
    </style>
</head>
<body>
    <div class="auth-container register-container">
        <div class="auth-header">
            <div class="logo">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm-1.06 13.54L7.4 12l1.41-1.41 2.12 2.12 4.24-4.24 1.41 1.41-5.64 5.66z" fill="#ffffff"/>
                    </svg>
                </div>
                <h1 class="logo-text">HealthTrack Pro</h1>
            </div>
            <p>Create your personal health account</p>
        </div>

        <div class="error-message" id="errorMessage">
            <!-- Errors will be inserted here by JavaScript -->
        </div>
        <?php if (!empty($errors)): ?>
            <div class="error-message" style="display: block;">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form class="auth-form" id="registerForm" method="POST" action="register.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" class="form-control" 
                           placeholder="John" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" class="form-control" 
                           placeholder="Doe" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="your@email.com" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" 
                       placeholder="+1 (123) 456-7890">
            </div>

            <div class="form-group">
                <label for="role">I am a:</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="" disabled selected>Select account type</option>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
                    <i class="toggle-password" data-target="password">👁️</i>
                </div>
                <div class="password-strength">
                    <div class="strength-meter">
                        <div class="strength-bar" data-strength="1"></div>
                        <div class="strength-bar" data-strength="2"></div>
                        <div class="strength-bar" data-strength="3"></div>
                    </div>
                    <div class="strength-text">Password strength: <span id="strengthLevel">Weak</span></div>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm your password" required>
            </div>

            <div class="terms-agreement">
                <input type="checkbox" id="agreeTerms" name="agreeTerms" required>
                <label for="agreeTerms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
            </div>

            <button type="submit" class="btn btn-primary">Create Account</button>

            <div class="auth-footer">
                Already have an account? <a href="login.php">Sign in</a>
            </div>
        </form>
    </div>

    <script src="assets/js/auth.js"></script>
    <script>
        // Client-side validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('errorMessage').innerHTML = '';
            
            // Get form values
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const agreeTerms = document.getElementById('agreeTerms').checked;
            const role = document.getElementById('role').value;
            
            // Validate inputs
            const errors = [];
            
            if (!firstName || !lastName) {
                errors.push('First and last name are required');
            }
            
            if (!email.includes('@') || !email.includes('.')) {
                errors.push('Invalid email format');
            }
            
            if (password.length < 8) {
                errors.push('Password must be at least 8 characters');
            }
            
            if (password !== confirmPassword) {
                errors.push('Passwords do not match');
            }
            
            if (!agreeTerms) {
                errors.push('You must agree to the terms and conditions');
            }
            
            if (errors.length > 0) {
                const errorContainer = document.getElementById('errorMessage');
                errorContainer.style.display = 'block';
                errors.forEach(error => {
                    errorContainer.innerHTML += `<p>${error}</p>`;
                });
            } else {
                // If validation passes, submit the form
                this.submit();
            }
        });
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const strengthBars = document.querySelectorAll('.strength-bar');
            const strengthText = document.getElementById('strengthLevel');
            const password = this.value;
            
            // Reset all bars
            strengthBars.forEach(bar => {
                bar.style.background = '#eee';
            });
            
            if (password.length === 0) {
                strengthText.textContent = 'Weak';
                return;
            }
            
            // Very simple strength check
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Update UI
            for (let i = 0; i < strength; i++) {
                strengthBars[i].style.background = i < 2 ? '#ff4d4d' : i < 4 ? '#4CAF50' : '#4CAF50';
            }
            
            strengthText.textContent = 
                strength < 2 ? 'Weak' : 
                strength < 4 ? 'Medium' : 'Strong';
        });
        
        // Toggle password visibility
        document.querySelector('.toggle-password').addEventListener('click', function() {
            const target = document.getElementById(this.getAttribute('data-target'));
            target.type = target.type === 'password' ? 'text' : 'password';
            this.textContent = target.type === 'password' ? '👁️' : '🔒';
        });
    </script>
</body>
</html>