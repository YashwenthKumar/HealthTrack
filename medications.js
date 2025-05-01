document.addEventListener('DOMContentLoaded', function() {
    // Update current date
    const currentDate = document.getElementById('current-date');
    if (currentDate) {
        const date = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        currentDate.textContent = date.toLocaleDateString('en-US', options);
    }

    // Modal elements
    const modal = document.getElementById('medicationModal');
    const addMedicationBtn = document.getElementById('addMedicationBtn');
    const closeModal = document.querySelector('.close-modal');

    // Add medication button
    if (addMedicationBtn && modal) {
        addMedicationBtn.addEventListener('click', function() {
            const form = document.getElementById('medicationForm');
            if (form) form.reset();
            document.getElementById('modalTitle').textContent = 'Add New Medication';
            document.getElementById('medicationId').value = '';
            modal.style.display = 'block';
        });
    }

    // Close modal
    if (closeModal && modal) {
        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    // Medication taken buttons
    document.querySelectorAll('.btn-taken').forEach(btn => {
        btn.addEventListener('click', function() {
            const medId = this.dataset.medId;
            if (!medId) {
                console.error('Medication ID not found');
                return;
            }
            const medCard = this.closest('.medication-card');
            const medName = medCard ? medCard.querySelector('h4')?.textContent : 'Medication';

            fetch('update_medication_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    medication_id: medId,
                    status: 'taken'
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.innerHTML = '<i class="fas fa-check"></i> Taken';
                    this.style.background = 'var(--success-color)';
                    this.disabled = true;
                    showNotification('Success', 'Medication marked as taken', 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error', 'Failed to update medication status', 'error');
            });
        });
    });

    // Snooze buttons
    document.querySelectorAll('.btn-snooze').forEach(btn => {
        btn.addEventListener('click', function() {
            const medId = this.dataset.medId;
            if (!medId) {
                console.error('Medication ID not found');
                return;
            }
            const medCard = this.closest('.medication-card');
            const medName = medCard ? medCard.querySelector('h4')?.textContent : 'Medication';

            fetch('update_medication_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    medication_id: medId,
                    status: 'snoozed'
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification('Snoozed', `Reminder for ${medName} will be shown again in 30 minutes`, 'info');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error', 'Failed to snooze medication reminder', 'error');
            });
        });
    });

    // Edit button logic
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const medId = this.getAttribute('data-med-id');
            if (!medId) return;
            const modal = document.getElementById('medicationModal');
            const form = document.getElementById('medicationForm');
            const titleEl = document.getElementById('modalTitle');
            if (!modal || !form || !titleEl) return;
            titleEl.textContent = 'Edit Medication';
            document.getElementById('medicationId').value = medId;
            fetch(`get_medication.php?id=${medId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('medicationName').value = data.name || '';
                    document.getElementById('medDosage').value = data.dosage || '';
                    document.getElementById('medSchedule').value = data.schedule || 'morning';
                    document.getElementById('medPurpose').value = data.purpose || '';
                    modal.style.display = 'block';
                })
                .catch(() => alert('Failed to load medication details'));
        });
    });

    // Delete button logic
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const medId = this.getAttribute('data-med-id');
            const medName = this.closest('.list-item').querySelector('h4')?.textContent || 'Medication';
            if (!medId) return;
            if (confirm(`Are you sure you want to delete ${medName}?`)) {
                fetch('delete_medication.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ medication_id: medId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('.list-item').remove();
                        showNotification('Success', 'Medication deleted successfully', 'success');
                    }
                })
                .catch(() => showNotification('Error', 'Failed to delete medication', 'error'));
            }
        });
    });

    // Unified form submission for add/edit
    const medicationForm = document.getElementById('medicationForm');
    if (medicationForm) {
        medicationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const medId = document.getElementById('medicationId').value;
            const name = document.getElementById('medicationName').value.trim();
            const dosage = document.getElementById('medDosage').value.trim();
            const schedule = document.getElementById('medSchedule').value;
            const purpose = document.getElementById('medPurpose').value;
            if (!name || !dosage) {
                showNotification('Error', 'Please fill in all required fields', 'error');
                return;
            }
            const url = medId ? 'update_medication.php' : 'add_medication.php';
            const payload = { id: medId, name, dosage, schedule, purpose };
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Success', medId ? 'Medication updated' : 'Medication added', 'success');
                    document.getElementById('medicationModal').style.display = 'none';
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showNotification('Error', data.message || 'Failed to save medication', 'error');
                }
            })
            .catch(() => showNotification('Error', 'Failed to save medication', 'error'));
        });
    }

    // Notification helper function
    function showNotification(title, message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type} show`;
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <div>
                <h4>${title}</h4>
                <p>${message}</p>
            </div>
            <button class="close-notification">&times;</button>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);

        notification.querySelector('.close-notification').addEventListener('click', () => {
            notification.remove();
        });
    }
});
// ... existing code ...
document.addEventListener('DOMContentLoaded', function() {
    // 1. Current Date Display
    updateCurrentDate();
    
    // 2. Medication Reminder Functionality
    setupMedicationReminders();
    
    // 3. Medication List Management
    setupMedicationList();
    
    // 4. Notification System
    setupNotifications();
    
    // 5. Responsive Sidebar Toggle
    setupResponsiveSidebar();
});

// ====================
// CORE FUNCTIONALITIES
// ====================

// 1. Current Date Display
function updateCurrentDate() {
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    const today = new Date();
    document.getElementById('current-date').textContent = today.toLocaleDateString('en-US', options);
    
    // Update time every minute
    setInterval(() => {
        const now = new Date();
        document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', options);
    }, 60000);
}

// 2. Medication Reminder System
function setupMedicationReminders() {
    // Mark medication as taken
    document.querySelectorAll('.btn-taken').forEach(button => {
        button.addEventListener('click', function(e) {
            const card = e.target.closest('.medication-card');
            card.style.opacity = '0.6';
            card.style.backgroundColor = '#e8f5e9';
            
            // In a real app, this would send data to the server
            console.log('Medication marked as taken:', 
                card.querySelector('h4').textContent);
            
            // Show confirmation
            showNotification('Medication recorded!', 'success');
        });
    });
    
    // Snooze functionality
    document.querySelectorAll('.btn-snooze').forEach(button => {
        button.addEventListener('click', function(e) {
            const card = e.target.closest('.medication-card');
            
            // In a real app, this would schedule a new reminder
            console.log('Medication snoozed:', 
                card.querySelector('h4').textContent);
            
            showNotification('Reminder rescheduled for 30 minutes later', 'info');
        });
    });
}

// 3. Medication List Management
function setupMedicationList() {
    // Add medication button
    document.getElementById('addMedicationBtn').addEventListener('click', function() {
        openAddMedicationModal();
    });
    
    // Edit medication buttons
    document.querySelectorAll('.medication-actions .fa-edit').forEach(icon => {
        icon.addEventListener('click', function(e) {
            const medicationName = e.target.closest('.list-item')
                .querySelector('h4').textContent;
            openEditMedicationModal(medicationName);
        });
    });
    
    // Delete medication buttons
    document.querySelectorAll('.medication-actions .fa-trash-alt').forEach(icon => {
        icon.addEventListener('click', function(e) {
            const listItem = e.target.closest('.list-item');
            const medicationName = listItem.querySelector('h4').textContent;
            
            if (confirm(`Are you sure you want to delete ${medicationName}?`)) {
                listItem.style.transform = 'translateX(-100%)';
                listItem.style.opacity = '0';
                
                setTimeout(() => {
                    listItem.remove();
                    showNotification(`${medicationName} removed from your medications`, 'warning');
                }, 300);
            }
        });
    });
}

// 4. Notification System
function setupNotifications() {
    // In a real app, this would connect to a backend service
    const notificationCount = 2; // This would come from an API
    
    if (notificationCount > 0) {
        document.querySelector('.badge').textContent = notificationCount;
        document.querySelector('.fa-bell').classList.add('has-notifications');
    }
    
    // Notification bell click handler
    document.querySelector('.fa-bell').addEventListener('click', function() {
        showNotificationPanel();
    });
}

// 5. Responsive Sidebar
function setupResponsiveSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarNavItems = document.querySelectorAll('.sidebar-nav li');
    
    // Toggle sidebar on mobile
    if (window.innerWidth < 768) {
        sidebar.classList.add('collapsed');
        
        sidebarNavItems.forEach(item => {
            item.addEventListener('click', function() {
                // Close sidebar after selection on mobile
                sidebar.classList.add('collapsed');
            });
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth < 768) {
            sidebar.classList.add('collapsed');
        } else {
            sidebar.classList.remove('collapsed');
        }
    });
}

// ==============
// MODAL FUNCTIONS
// ==============

function openAddMedicationModal() {
    // In a real app, this would show a modal with a form
    const modalHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Medication</h3>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addMedicationForm">
                    <div class="form-group">
                        <label>Medication Name</label>
                        <input type="text" required>
                    </div>
                    <div class="form-group">
                        <label>Dosage</label>
                        <input type="text" required>
                    </div>
                    <div class="form-group">
                        <label>Frequency</label>
                        <select required>
                            <option value="">Select frequency</option>
                            <option>Once daily</option>
                            <option>Twice daily</option>
                            <option>Three times daily</option>
                            <option>As needed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Time of Day</label>
                        <div class="time-options">
                            <label><input type="checkbox" name="time" value="morning"> Morning</label>
                            <label><input type="checkbox" name="time" value="afternoon"> Afternoon</label>
                            <label><input type="checkbox" name="time" value="evening"> Evening</label>
                            <label><input type="checkbox" name="time" value="bedtime"> Bedtime</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Medication</button>
                </form>
            </div>
        </div>
    `;
    
    const modal = document.getElementById('addMedicationModal');
    modal.innerHTML = modalHTML;
    modal.style.display = 'block';
    
    // Close modal when clicking X
    modal.querySelector('.close-modal').addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Form submission
    document.getElementById('addMedicationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // In a real app, this would send data to the server
        showNotification('Medication added successfully!', 'success');
        modal.style.display = 'none';
    });
}

function openEditMedicationModal(medicationName) {
    // Similar to add modal but prefilled with existing data
    console.log('Editing medication:', medicationName);
    showNotification(`Edit form for ${medicationName} would open here`, 'info');
}

// ==================
// UTILITY FUNCTIONS
// ==================

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 
                          type === 'warning' ? 'exclamation-triangle' : 
                          type === 'error' ? 'times-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}

function showNotificationPanel() {
    // In a real app, this would show actual notifications
    const panelHTML = `
        <div class="notifications-panel">
            <div class="panel-header">
                <h4>Notifications (2)</h4>
                <span class="close-panel">&times;</span>
            </div>
            <div class="panel-content">
                <div class="notification-item unread">
                    <i class="fas fa-pills"></i>
                    <div>
                        <p>Time to take your Metformin</p>
                        <small>2 minutes ago</small>
                    </div>
                </div>
                <div class="notification-item unread">
                    <i class="fas fa-calendar-check"></i>
                    <div>
                        <p>Your appointment with Dr. Smith is tomorrow</p>
                        <small>1 hour ago</small>
                    </div>
                </div>
                <div class="notification-item">
                    <i class="fas fa-heartbeat"></i>
                    <div>
                        <p>Weekly health report is ready</p>
                        <small>2 days ago</small>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const panel = document.createElement('div');
    panel.className = 'notifications-overlay';
    panel.innerHTML = panelHTML;
    document.body.appendChild(panel);
    
    // Close panel
    panel.querySelector('.close-panel').addEventListener('click', function() {
        panel.remove();
    });
    
    // Mark as read when clicked
    panel.querySelectorAll('.notification-item.unread').forEach(item => {
        item.addEventListener('click', function() {
            this.classList.remove('unread');
            // In a real app, would update server
        });
    });
}

// =============
// STYLE UPDATES
// =============
// Add this to your CSS
const style = document.createElement('style');
style.textContent = `
    /* Notification Styles */
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
        transform: translateX(120%);
        transition: transform 0.3s ease;
        z-index: 1000;
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification-success {
        border-left: 4px solid var(--success-color);
    }
    
    .notification-info {
        border-left: 4px solid var(--primary-color);
    }
    
    .notification-warning {
        border-left: 4px solid var(--warning-color);
    }
    
    .notification-error {
        border-left: 4px solid var(--danger-color);
    }
    
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1001;
        justify-content: center;
        align-items: center;
    }
    
    .modal-content {
        background: white;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        animation: modalFadeIn 0.3s;
    }
    
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .close-modal {
        font-size: 24px;
        cursor: pointer;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .form-group input, 
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .time-options {
        display: flex;
        gap: 15px;
        margin-top: 5px;
    }
    
    /* Notifications Panel */
    .notifications-overlay {
        position: fixed;
        top: 0;
        right: 0;
        width: 100%;
        max-width: 350px;
        height: 100vh;
        background: white;
        box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        z-index: 1002;
        transform: translateX(100%);
        animation: slideIn 0.3s forwards;
    }
    
    @keyframes slideIn {
        to { transform: translateX(0); }
    }
    
    .panel-header {
        padding: 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .panel-content {
        padding: 10px 0;
    }
    
    .notification-item {
        padding: 12px 15px;
        display: flex;
        gap: 10px;
        cursor: pointer;
    }
    
    .notification-item:hover {
        background: #f5f5f5;
    }
    
    .notification-item.unread {
        background: #f0f7ff;
    }
    
    .notification-item i {
        font-size: 20px;
        color: var(--primary-color);
    }
    
    .notification-item p {
        margin-bottom: 3px;
    }
    
    .notification-item small {
        color: #666;
        font-size: 12px;
    }
    
    /* Responsive Sidebar */
    @media (max-width: 768px) {
        .sidebar.collapsed {
            width: 60px;
            overflow: hidden;
        }
        
        .sidebar.collapsed .logo h1,
        .sidebar.collapsed .sidebar-nav li span {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-nav li a {
            justify-content: center;
        }
    }
`;
document.head.appendChild(style);
document.addEventListener('DOMContentLoaded', function() {
    // Set current date
    document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    // Function to handle medication deletion
    window.deleteMedication = function(medicationId) {
        if (confirm('Are you sure you want to delete this medication?')) {
            fetch('delete_medication.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ medication_id: medicationId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to delete medication: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the medication');
            });
        }
    };

    // Function to handle medication editing
    window.editMedication = function(medicationId) {
        fetch(`get_medication.php?id=${medicationId}`)
            .then(response => response.json())
            .then(medication => {
                // Populate the modal with medication data
                document.getElementById('medicationId').value = medication.id;
                document.getElementById('medicationName').value = medication.name;
                document.getElementById('medicationDosage').value = medication.dosage;
                document.getElementById('medicationSchedule').value = medication.schedule;
                document.getElementById('medicationPurpose').value = medication.purpose;
                
                // Show the modal
                document.getElementById('medicationModal').style.display = 'flex';
            })
            .catch(error => console.error('Error:', error));
    };

    // Add event listener for form submission
    document.getElementById('medicationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            id: document.getElementById('medicationId').value,
            name: document.getElementById('medicationName').value,
            dosage: document.getElementById('medicationDosage').value,
            schedule: document.getElementById('medicationSchedule').value,
            purpose: document.getElementById('medicationPurpose').value
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
                location.reload(); // Reload the page to show updated data
            } else {
                alert('Failed to save medication: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Close modal functionality
    const modal = document.getElementById('medicationModal');
    const closeBtn = document.querySelector('.close-modal');

    closeBtn.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});

// Function to open the modal and populate with medication data
function editMedication(medId) {
    // Fetch medication details via AJAX
    fetch('get_medication.php?id=' + medId)
        .then(response => response.json())
        .then(data => {
            // Populate modal fields
            document.getElementById('medicationId').value = data.id;
            document.getElementById('medicationName').value = data.name;
            document.getElementById('medDosage').value = data.dosage;
            document.getElementById('medSchedule').value = data.schedule;
            document.getElementById('medPurpose').value = data.purpose;
            // Show the modal
            document.getElementById('medicationModal').style.display = 'flex';
        })
        .catch(error => {
            alert('Failed to load medication details.');
        });
}

// Attach event listeners to all edit buttons after DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-edit').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const medId = this.getAttribute('data-med-id');
            editMedication(medId);
        });
    });

    // Optional: Close modal logic
    document.querySelectorAll('.close-modal').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('medicationModal').style.display = 'none';
        });
    });
});


function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 
                          type === 'warning' ? 'exclamation-triangle' : 
                          type === 'error' ? 'times-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}

function showNotificationPanel() {
    // In a real app, this would show actual notifications
    const panelHTML = `
        <div class="notifications-panel">
            <div class="panel-header">
                <h4>Notifications (2)</h4>
                <span class="close-panel">&times;</span>
            </div>
            <div class="panel-content">
                <div class="notification-item unread">
                    <i class="fas fa-pills"></i>
                    <div>
                        <p>Time to take your Metformin</p>
                        <small>2 minutes ago</small>
                    </div>
                </div>
                <div class="notification-item unread">
                    <i class="fas fa-calendar-check"></i>
                    <div>
                        <p>Your appointment with Dr. Smith is tomorrow</p>
                        <small>1 hour ago</small>
                    </div>
                </div>
                <div class="notification-item">
                    <i class="fas fa-heartbeat"></i>
                    <div>
                        <p>Weekly health report is ready</p>
                        <small>2 days ago</small>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const panel = document.createElement('div');
    panel.className = 'notifications-overlay';
    panel.innerHTML = panelHTML;
    document.body.appendChild(panel);
    
    // Close panel
    panel.querySelector('.close-panel').addEventListener('click', function() {
        panel.remove();
    });
    
    // Mark as read when clicked
    panel.querySelectorAll('.notification-item.unread').forEach(item => {
        item.addEventListener('click', function() {
            this.classList.remove('unread');
            // In a real app, would update server
        });
    });
}

// =============
// STYLE UPDATES
// =============
// Add this to your CSS
const style = document.createElement('style');
style.textContent = `
    /* Notification Styles */
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
        transform: translateX(120%);
        transition: transform 0.3s ease;
        z-index: 1000;
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification-success {
        border-left: 4px solid var(--success-color);
    }
    
    .notification-info {
        border-left: 4px solid var(--primary-color);
    }
    
    .notification-warning {
        border-left: 4px solid var(--warning-color);
    }
    
    .notification-error {
        border-left: 4px solid var(--danger-color);
    }
    
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1001;
        justify-content: center;
        align-items: center;
    }
    
    .modal-content {
        background: white;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        animation: modalFadeIn 0.3s;
    }
    
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .close-modal {
        font-size: 24px;
        cursor: pointer;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .form-group input, 
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .time-options {
        display: flex;
        gap: 15px;
        margin-top: 5px;
    }
    
    /* Notifications Panel */
    .notifications-overlay {
        position: fixed;
        top: 0;
        right: 0;
        width: 100%;
        max-width: 350px;
        height: 100vh;
        background: white;
        box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        z-index: 1002;
        transform: translateX(100%);
        animation: slideIn 0.3s forwards;
    }
    
    @keyframes slideIn {
        to { transform: translateX(0); }
    }
    
    .panel-header {
        padding: 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .panel-content {
        padding: 10px 0;
    }
    
    .notification-item {
        padding: 12px 15px;
        display: flex;
        gap: 10px;
        cursor: pointer;
    }
    
    .notification-item:hover {
        background: #f5f5f5;
    }
    
    .notification-item.unread {
        background: #f0f7ff;
    }
    
    .notification-item i {
        font-size: 20px;
        color: var(--primary-color);
    }
    
    .notification-item p {
        margin-bottom: 3px;
    }
    
    .notification-item small {
        color: #666;
        font-size: 12px;
    }
    
    /* Responsive Sidebar */
    @media (max-width: 768px) {
        .sidebar.collapsed {
            width: 60px;
            overflow: hidden;
        }
        
        .sidebar.collapsed .logo h1,
        .sidebar.collapsed .sidebar-nav li span {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-nav li a {
            justify-content: center;
        }
    }
`;
document.head.appendChild(style);