document.addEventListener('DOMContentLoaded', function() {
    // ======================
    // Utility Functions
    // ======================
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'error' ? 'exclamation-circle' : type === 'success' ? 'check-circle' : 'info-circle'}"></i>
            <span>${message}</span>
            <button class="close-notification">&times;</button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        notification.querySelector('.close-notification').addEventListener('click', () => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        });
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // ======================
    // Initialize Page
    // ======================
    
    // Set current date
    const today = new Date();
    document.getElementById('current-date').textContent = today.toLocaleDateString('en-US', {
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric'
    });
    
    // ======================
    // Tab Switching
    // ======================
    const tabs = document.querySelectorAll('.settings-tab');
    const contents = document.querySelectorAll('.settings-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show corresponding content
            const tabId = this.getAttribute('data-tab');
            document.getElementById(`${tabId}-settings`).classList.add('active');
        });
    });
    
    // ======================
    // Toggle Switches
    // ======================
    const toggleSwitches = document.querySelectorAll('.toggle-switch input');
    toggleSwitches.forEach(switchEl => {
        // Initialize toggle states
        const label = switchEl.closest('.toggle-label');
        if (label) {
            const offSpan = label.querySelector('span:first-child');
            const onSpan = label.querySelector('span:last-child');
            
            if (switchEl.checked) {
                offSpan.style.opacity = '0.5';
                onSpan.style.opacity = '1';
            } else {
                offSpan.style.opacity = '1';
                onSpan.style.opacity = '0.5';
            }
        }

        // Add change event
        switchEl.addEventListener('change', function() {
            const label = this.closest('.toggle-label');
            if (!label) return;
            
            const offSpan = label.querySelector('span:first-child');
            const onSpan = label.querySelector('span:last-child');
            
            if (this.checked) {
                offSpan.style.opacity = '0.5';
                onSpan.style.opacity = '1';
                showNotification(`${this.closest('.toggle-item').querySelector('h4').textContent} enabled`, 'success');
            } else {
                offSpan.style.opacity = '1';
                onSpan.style.opacity = '0.5';
                showNotification(`${this.closest('.toggle-item').querySelector('h4').textContent} disabled`, 'info');
            }
        });
    });
    
    // ======================
    // Profile Picture Upload
    // ======================
    const changePhotoBtn = document.querySelector('.profile-picture-actions .btn:first-child');
    const removePhotoBtn = document.querySelector('.profile-picture-actions .btn:last-child');
    const profileImg = document.querySelector('.profile-picture img');
    
    if (changePhotoBtn && profileImg) {
        changePhotoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            
            input.onchange = (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        profileImg.src = event.target.result;
                        showNotification('Profile picture updated successfully', 'success');
                    };
                    reader.readAsDataURL(file);
                }
            };
            
            input.click();
        });
    }
    
    if (removePhotoBtn && profileImg) {
        removePhotoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            profileImg.src = 'https://www.gravatar.com/avatar/default?s=200&d=mp';
            showNotification('Profile picture removed', 'info');
        });
    }
    
    // ======================
    // Form Submission
    // ======================
    const saveButtons = document.querySelectorAll('.form-actions .btn-primary');
    saveButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.settings-content');
            const tabId = form.id.replace('-settings', '');
            
            // Simple validation for profile tab
            if (tabId === 'profile') {
                const firstName = document.getElementById('firstName').value;
                const lastName = document.getElementById('lastName').value;
                const email = document.getElementById('email').value;
                
                if (!firstName || !lastName || !email) {
                    showNotification('Please fill in all required fields', 'error');
                    return;
                }
                
                if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
                    showNotification('Please enter a valid email address', 'error');
                    return;
                }
            }
            
            // Password validation for security tab
            if (tabId === 'security') {
                const currentPassword = document.getElementById('currentPassword').value;
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                
                if (currentPassword && newPassword && confirmPassword) {
                    if (newPassword.length < 8) {
                        showNotification('Password must be at least 8 characters', 'error');
                        return;
                    }
                    
                    if (!/[0-9]/.test(newPassword) || !/[!@#$%^&*]/.test(newPassword)) {
                        showNotification('Password must contain at least one number and one special character', 'error');
                        return;
                    }
                    
                    if (newPassword !== confirmPassword) {
                        showNotification('Passwords do not match', 'error');
                        return;
                    }
                }
            }
            
            showNotification(`${tabId.charAt(0).toUpperCase() + tabId.slice(1)} settings saved successfully`, 'success');
            
            // In a real app, you would send data to the server here
            console.log(`Saving ${tabId} settings...`);
        });
    });
    
    // ======================
    // Device Management
    // ======================
    const disconnectButtons = document.querySelectorAll('.device-actions .btn');
    disconnectButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const deviceCard = this.closest('.device-card');
            const deviceName = deviceCard.querySelector('h4').textContent;
            
            if (confirm(`Are you sure you want to disconnect ${deviceName}?`)) {
                deviceCard.style.transform = 'translateX(100%)';
                deviceCard.style.opacity = '0';
                
                setTimeout(() => {
                    deviceCard.remove();
                    showNotification(`${deviceName} disconnected successfully`, 'success');
                }, 300);
            }
        });
    });
    
    // Connect new device
    const connectDeviceBtn = document.querySelector('#devices-settings .btn-primary');
    if (connectDeviceBtn) {
        connectDeviceBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const deviceType = document.getElementById('deviceType').value;
            const deviceBrand = document.getElementById('deviceBrand').value;
            const deviceModel = document.getElementById('deviceModel').value;
            
            if (!deviceType || !deviceBrand || !deviceModel) {
                showNotification('Please fill in all device information', 'error');
                return;
            }
            
            // In a real app, this would connect to the actual device
            // Here we'll just simulate it
            
            const devicesContainer = document.querySelector('.connected-devices');
            const newDevice = document.createElement('div');
            newDevice.className = 'device-card';
            newDevice.innerHTML = `
                <div class="device-info">
                    <div class="device-icon">
                        <i class="fas fa-${getDeviceIcon(deviceType)}"></i>
                    </div>
                    <div class="device-meta">
                        <h4>${deviceBrand} ${deviceModel}</h4>
                        <p>Connected just now • ${getDeviceTypeName(deviceType)}</p>
                    </div>
                </div>
                <div class="device-actions">
                    <button class="btn btn-outline btn-sm">Disconnect</button>
                </div>
            `;
            
            devicesContainer.appendChild(newDevice);
            
            // Add event listener to the new disconnect button
            newDevice.querySelector('.btn').addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm(`Are you sure you want to disconnect ${deviceBrand} ${deviceModel}?`)) {
                    newDevice.style.transform = 'translateX(100%)';
                    newDevice.style.opacity = '0';
                    
                    setTimeout(() => {
                        newDevice.remove();
                        showNotification(`${deviceBrand} ${deviceModel} disconnected successfully`, 'success');
                    }, 300);
                }
            });
            
            // Reset form
            document.getElementById('deviceType').value = '';
            document.getElementById('deviceBrand').value = '';
            document.getElementById('deviceModel').value = '';
            
            showNotification(`${deviceBrand} ${deviceModel} connected successfully`, 'success');
        });
    }
    
    function getDeviceIcon(type) {
        const icons = {
            'fitness-tracker': 'heartbeat',
            'smart-scale': 'weight',
            'glucose-meter': 'tint',
            'blood-pressure': 'heartbeat',
            'other': 'bluetooth'
        };
        return icons[type] || 'bluetooth';
    }
    
    function getDeviceTypeName(type) {
        const names = {
            'fitness-tracker': 'Fitness tracker',
            'smart-scale': 'Smart scale',
            'glucose-meter': 'Glucose meter',
            'blood-pressure': 'Blood pressure monitor',
            'other': 'Health device'
        };
        return names[type] || 'Device';
    }
    
    // ======================
    // Data Export/Deletion
    // ======================
    const exportBtn = document.querySelector('#privacy-settings .btn-outline');
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showNotification('Preparing your data export. You will receive an email shortly.', 'info');
            
            // Simulate export process
            setTimeout(() => {
                showNotification('Your data export is ready. Check your email for the download link.', 'success');
            }, 3000);
        });
    }
    
    const deleteBtn = document.querySelector('#privacy-settings .btn-outline[style*="danger"]');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete your account? This cannot be undone!')) {
                showNotification('Account deletion request received. You will receive a confirmation email.', 'error');
                
                // In a real app, this would trigger account deletion
                console.log('Account deletion requested');
            }
        });
    }
    
    // ======================
    // Notification Badge
    // ======================
    const notificationBell = document.querySelector('.notifications');
    if (notificationBell) {
        notificationBell.addEventListener('click', function() {
            showNotification('You have 3 unread notifications', 'info');
            this.querySelector('.badge').textContent = '0';
        });
    }
    
    // ======================
    // User Profile Dropdown
    // ======================
    const userProfile = document.querySelector('.user-profile');
    if (userProfile) {
        userProfile.addEventListener('click', function() {
            // Create dropdown if it doesn't exist
            let dropdown = document.querySelector('.profile-dropdown');
            
            if (!dropdown) {
                dropdown = document.createElement('div');
                dropdown.className = 'profile-dropdown';
                dropdown.innerHTML = `
                    <a href="profile.html"><i class="fas fa-user"></i> My Profile</a>
                    <a href="settings.html"><i class="fas fa-cog"></i> Settings</a>
                    <a href="logout.html"><i class="fas fa-sign-out-alt"></i> Logout</a>
                `;
                document.body.appendChild(dropdown);
                
                // Position dropdown
                const rect = userProfile.getBoundingClientRect();
                dropdown.style.top = `${rect.bottom + 5}px`;
                dropdown.style.right = `${window.innerWidth - rect.right}px`;
                
                // Close dropdown when clicking outside
                setTimeout(() => {
                    document.addEventListener('click', function closeDropdown(e) {
                        if (!userProfile.contains(e.target) && !dropdown.contains(e.target)) {
                            dropdown.remove();
                            document.removeEventListener('click', closeDropdown);
                        }
                    });
                }, 10);
            } else {
                dropdown.remove();
            }
        });
    }
});

// Add this CSS for notifications
const style = document.createElement('style');
style.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 10px;
        transform: translateX(150%);
        transition: transform 0.3s ease;
        z-index: 1000;
        max-width: 350px;
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification i {
        font-size: 20px;
    }
    
    .notification.info {
        border-left: 4px solid var(--primary-color);
    }
    
    .notification.success {
        border-left: 4px solid var(--success-color);
    }
    
    .notification.error {
        border-left: 4px solid var(--danger-color);
    }
    
    .close-notification {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        margin-left: auto;
        color: #666;
    }
    
    .profile-dropdown {
        position: fixed;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        padding: 10px 0;
        z-index: 1000;
        min-width: 200px;
    }
    
    .profile-dropdown a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        color: #333;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .profile-dropdown a:hover {
        background: #f5f5f5;
        color: var(--primary-color);
    }
    
    .profile-dropdown a i {
        width: 20px;
        text-align: center;
    }
`;
document.head.appendChild(style);