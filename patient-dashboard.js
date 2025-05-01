document.addEventListener('DOMContentLoaded', function() {
    // ======================
    // Utility Functions
    // ======================
    function formatDate(date) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }

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
    document.getElementById('current-date').textContent = formatDate(today);
    
    // ======================
    // Notification System
    // ======================
    const notificationBell = document.querySelector('.notifications');
    if (notificationBell) {
        notificationBell.addEventListener('click', function() {
            showNotification('You have 3 unread notifications', 'info');
        });
    }

    // ======================
    // Health Score Animation
    // ======================
    const healthScoreCircle = document.querySelector('.score-circle circle:nth-child(2)');
    if (healthScoreCircle) {
        // Animate the health score circle
        healthScoreCircle.style.strokeDashoffset = '36'; // 82% score (144 * 0.18)
        
        // Add animation
        setTimeout(() => {
            healthScoreCircle.style.transition = 'stroke-dashoffset 1s ease-in-out';
        }, 500);
    }

    // ======================
    // Medication Tracker
    // ======================
    const medicationCards = document.querySelectorAll('.medication-card');
    medicationCards.forEach(card => {
        const refillBtn = card.querySelector('.btn-outline');
        if (refillBtn) {
            refillBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const medName = card.querySelector('h4').textContent;
                showNotification(`Refill request sent for ${medName}`, 'success');
            });
        }
        
        const logBtn = card.querySelector('.btn-primary');
        if (logBtn) {
            logBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const medName = card.querySelector('h4').textContent;
                showNotification(`Dose logged for ${medName}`, 'success');
                
                // Update progress bar
                const progress = card.querySelector('.progress-fill');
                if (progress) {
                    const currentWidth = parseFloat(progress.style.width);
                    const newWidth = Math.max(0, currentWidth - 10);
                    progress.style.width = `${newWidth}%`;
                    card.querySelector('small').textContent = `${newWidth}% remaining`;
                }
            });
        }
    });

    // ======================
    // Appointment System
    // ======================
    const appointmentItems = document.querySelectorAll('.appointment-item');
    appointmentItems.forEach(item => {
        const joinBtn = item.querySelector('.btn-primary');
        if (joinBtn) {
            joinBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const appointmentTitle = item.querySelector('h4').textContent;
                showNotification(`Connecting to ${appointmentTitle}...`, 'info');
                // In a real app, this would launch the video call
            });
        }
        
        const detailBtns = item.querySelectorAll('.btn-outline');
        detailBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const appointmentTitle = item.querySelector('h4').textContent;
                showNotification(`Showing details for ${appointmentTitle}`, 'info');
            });
        });
    });

    // ======================
    // Health Goals Progress
    // ======================
    const goalItems = document.querySelectorAll('.goal-item');
    goalItems.forEach(item => {
        const updateBtn = item.querySelector('.btn-outline');
        if (updateBtn) {
            updateBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const goalTitle = item.querySelector('h4').textContent;
                const currentProgress = parseInt(item.querySelector('.goal-progress span').textContent);
                const newProgress = Math.min(100, currentProgress + 10);
                
                // Update progress circle
                const circle = item.querySelector('.goal-progress circle:nth-child(2)');
                const circumference = 2 * Math.PI * 25;
                const offset = circumference - (newProgress / 100) * circumference;
                circle.style.strokeDashoffset = offset;
                
                // Update percentage
                item.querySelector('.goal-progress span').textContent = `${newProgress}%`;
                
                showNotification(`Progress updated for ${goalTitle}`, 'success');
            });
        }
    });

    // ======================
    // Emergency Button
    // ======================
    const emergencyBtn = document.querySelector('.emergency-card .btn-light');
    if (emergencyBtn) {
        emergencyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to call emergency services?')) {
                showNotification('Connecting to emergency services...', 'error');
                // In a real app, this would initiate a call
            }
        });
    }

    // ======================
    // Quick Actions
    // ======================


    // ======================
    // Medical Records Interaction
    // ======================
    const recordItems = document.querySelectorAll('.record-item');
    recordItems.forEach(item => {
        const viewBtn = item.querySelector('.record-action');
        if (viewBtn) {
            viewBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const recordTitle = item.querySelector('h4').textContent;
                showNotification(`Opening ${recordTitle}...`, 'info');
            });
        }
    });

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

    // ======================
    // Responsive Sidebar
    // ======================
    function handleSidebarResize() {
        const sidebar = document.querySelector('.sidebar');
        if (window.innerWidth <= 768) {
            sidebar.classList.add('mobile');
        } else {
            sidebar.classList.remove('mobile');
        }
    }
    
    window.addEventListener('resize', handleSidebarResize);
    handleSidebarResize();
});

// ======================
// Chart Initialization (Example)
// ======================
function initCharts() {
    // Weight Chart
    const weightCtx = document.getElementById('weightChart');
    if (weightCtx) {
        // This would be replaced with actual Chart.js initialization
        weightCtx.innerHTML = `
            <div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #666;">
                <i class="fas fa-chart-line" style="font-size: 24px;"></i>
                <span style="margin-left: 10px;">Weight trend chart</span>
            </div>
        `;
    }
    
    // In a real implementation, you would use Chart.js like this:
    /*
    if (typeof Chart !== 'undefined' && weightCtx) {
        new Chart(weightCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Weight (kg)',
                    data: [70, 69.5, 69, 68.5, 68, 68],
                    borderColor: 'var(--primary-color)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
    */
}

// Initialize charts when Chart.js is loaded
if (typeof Chart !== 'undefined') {
    initCharts();
} else {
    // Load Chart.js dynamically if needed
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
    script.onload = initCharts;
    document.head.appendChild(script);
}