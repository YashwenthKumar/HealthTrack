document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    document.querySelectorAll('.toggle-password').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🔒';
        });
    });

    // Password strength indicator
    const passwordInput = document.getElementById('registerPassword');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strengthBars = document.querySelectorAll('.strength-bar');
            const strengthText = document.getElementById('strengthLevel');
            
            // Reset
            strengthBars.forEach(bar => {
                bar.style.backgroundColor = '#eee';
                bar.style.opacity = '0.3';
            });
            
            // Very weak (just length)
            if (password.length > 0) {
                strengthBars[0].style.backgroundColor = '#ff4d4f';
                strengthBars[0].style.opacity = '1';
                strengthText.textContent = 'Very weak';
            }
            
            // Weak (length >= 6)
            if (password.length >= 6) {
                strengthBars[1].style.backgroundColor = '#ffa940';
                strengthBars[1].style.opacity = '1';
                strengthText.textContent = 'Weak';
            }
            
            // Moderate (length >= 8 with numbers and uppercase)
            if (password.length >= 8 && /[A-Z]/.test(password) && /[0-9]/.test(password)) {
                strengthBars[2].style.backgroundColor = '#52c41a';
                strengthBars[2].style.opacity = '1';
                strengthText.textContent = 'Moderate';
            }
            
            // Strong (length >= 10 with special chars)
            if (password.length >= 10 && /[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) {
                strengthBars.forEach(bar => {
                    bar.style.backgroundColor = '#52c41a';
                    bar.style.opacity = '1';
                });
                strengthText.textContent = 'Strong';
            }
        });
    }

    // Form validation
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            if (!document.getElementById('agreeTerms').checked) {
                alert('You must agree to the terms and conditions');
                return;
            }
            
            // Simulate registration
            console.log('Registration submitted');
            window.location.href = 'dashboard.html';
        });
    }

    // Login form handling
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            if (!email || !password) {
                alert('Please fill in all fields');
                return;
            }
            
            // Simulate login
            console.log('Login attempted with:', email);
            window.location.href = 'dashboard.html';
        });
    }
});