document.addEventListener('DOMContentLoaded', function() {
    // Initialize the application
    initProfilePage();
});

/**
 * Main initialization function
 */
function initProfilePage() {
    // Load user data
    const userData = loadUserData();
    
    // Initialize UI with user data
    initializeUI(userData);
    
    // Set up event listeners
    setupEventListeners(userData);
    
    // Set up quick links navigation
    setupQuickLinks();
    
    // Set up responsive behavior
    setupResponsiveBehavior();
}

/**
 * Load user data from localStorage or use default values
 */
function loadUserData() {
    return JSON.parse(localStorage.getItem('userData')) || {
        fullName: "Yashwenth Kumar",
        dob: "1985-01-15",
        gender: "male",
        email: "yashwenth@gmail.com",
        phone: "9392023299",
        address: "123 Main Street, Apt 4B, New York, NY 10001",
        bloodType: "A+",
        height: 175,
        weight: 72,
        allergies: "Penicillin, Peanuts",
        conditions: "Asthma (mild)",
        medications: "Albuterol inhaler (as needed)",
        profileImage: "https://via.placeholder.com/150",
        emergencyContacts: [
            {
                name: "Sarah Johnson",
                phone: "5559876543",
                email: "sarah.j@example.com",
                relationship: "Spouse"
            },
            {
                name: "Michael Doe",
                phone: "5554567890",
                email: "michael.d@example.com",
                relationship: "Brother"
            }
        ]
    };
}

/**
 * Initialize the UI with user data
 */
function initializeUI(userData) {
    // Set profile image and name
    document.getElementById('profile-image').src = userData.profileImage;
    document.getElementById('user-name').textContent = userData.fullName.split(' ')[0];
    
    // Personal Information
    document.getElementById('full-name-value').textContent = userData.fullName;
    document.getElementById('dob-value').textContent = formatDate(userData.dob);
    document.getElementById('gender-value').textContent = formatGender(userData.gender);
    document.getElementById('email-value').textContent = userData.email;
    document.getElementById('phone-value').textContent = formatPhone(userData.phone);
    document.getElementById('address-value').textContent = userData.address;
    
    // Set form values
    document.getElementById('full-name').value = userData.fullName;
    document.getElementById('dob').value = userData.dob;
    document.getElementById('gender').value = userData.gender;
    document.getElementById('email').value = userData.email;
    document.getElementById('phone').value = userData.phone;
    document.getElementById('address').value = userData.address;
    
    // Medical Information
    document.getElementById('blood-type-value').textContent = userData.bloodType;
    document.getElementById('height-value').textContent = `${userData.height} cm (${cmToFeet(userData.height)})`;
    document.getElementById('weight-value').textContent = `${userData.weight} kg (${kgToLbs(userData.weight)} lbs)`;
    document.getElementById('allergies-value').textContent = userData.allergies || 'None';
    document.getElementById('conditions-value').textContent = userData.conditions || 'None';
    document.getElementById('medications-value').textContent = userData.medications || 'None';
    
    // Set medical form values
    document.getElementById('blood-type').value = userData.bloodType;
    document.getElementById('height').value = userData.height;
    document.getElementById('weight').value = userData.weight;
    document.getElementById('allergies').value = userData.allergies;
    document.getElementById('conditions').value = userData.conditions;
    document.getElementById('medications').value = userData.medications;
    
    // Render emergency contacts
    renderEmergencyContacts(userData.emergencyContacts);
}

/**
 * Set up all event listeners
 */
function setupEventListeners(userData) {
    // Profile picture upload
    document.getElementById('profile-upload').addEventListener('change', function(e) {
        handleProfileImageUpload(e, userData);
    });
    
    // Form submissions
    document.getElementById('personal-details-edit').addEventListener('submit', function(e) {
        handlePersonalInfoSubmit(e, userData);
    });
    
    document.getElementById('medical-info-edit').addEventListener('submit', function(e) {
        handleMedicalInfoSubmit(e, userData);
    });
    
    // Contact form submission
    document.getElementById('contact-form').addEventListener('submit', function(e) {
        handleEmergencyContactSubmit(e, userData);
    });
    
    // Password form submission
    document.getElementById('password-form').addEventListener('submit', function(e) {
        handlePasswordChange(e, userData);
    });
    
    // Edit profile button
    document.getElementById('edit-profile-btn').addEventListener('click', function() {
        document.querySelector('.edit-btn').click();
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });
}

/**
 * Handle profile image upload
 */
function handleProfileImageUpload(e, userData) {
    const file = e.target.files[0];
    if (file) {
        // Validate file type and size (max 2MB)
        if (!file.type.match('image.*')) {
            showAlert('Please select an image file', 'error');
            return;
        }
        
        if (file.size > 2 * 1024 * 1024) {
            showAlert('Image size should be less than 2MB', 'error');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('profile-image').src = event.target.result;
            // Update user data
            userData.profileImage = event.target.result;
            localStorage.setItem('userData', JSON.stringify(userData));
            showAlert('Profile picture updated successfully!', 'success');
        };
        reader.readAsDataURL(file);
    }
}

/**
 * Handle personal information form submission
 */
function handlePersonalInfoSubmit(e, userData) {
    e.preventDefault();
    
    // Basic validation
    if (!validateRequiredFields(e.target)) {
        return;
    }
    
    // Update user data
    userData.fullName = document.getElementById('full-name').value;
    userData.dob = document.getElementById('dob').value;
    userData.gender = document.getElementById('gender').value;
    userData.email = document.getElementById('email').value;
    userData.phone = document.getElementById('phone').value;
    userData.address = document.getElementById('address').value;
    
    // Update view
    document.getElementById('full-name-value').textContent = userData.fullName;
    document.getElementById('dob-value').textContent = formatDate(userData.dob);
    document.getElementById('gender-value').textContent = formatGender(userData.gender);
    document.getElementById('email-value').textContent = userData.email;
    document.getElementById('phone-value').textContent = formatPhone(userData.phone);
    document.getElementById('address-value').textContent = userData.address;
    document.getElementById('user-name').textContent = userData.fullName.split(' ')[0];
    
    // Save to local storage
    localStorage.setItem('userData', JSON.stringify(userData));
    
    // Switch back to view mode
    toggleEdit('personal-details');
    
    showAlert('Personal information updated successfully!', 'success');
}

/**
 * Handle medical information form submission
 */
function handleMedicalInfoSubmit(e, userData) {
    e.preventDefault();
    
    // Update user data
    userData.bloodType = document.getElementById('blood-type').value;
    userData.height = document.getElementById('height').value;
    userData.weight = document.getElementById('weight').value;
    userData.allergies = document.getElementById('allergies').value;
    userData.conditions = document.getElementById('conditions').value;
    userData.medications = document.getElementById('medications').value;
    
    // Update view
    document.getElementById('blood-type-value').textContent = userData.bloodType;
    document.getElementById('height-value').textContent = `${userData.height} cm (${cmToFeet(userData.height)})`;
    document.getElementById('weight-value').textContent = `${userData.weight} kg (${kgToLbs(userData.weight)} lbs)`;
    document.getElementById('allergies-value').textContent = userData.allergies || 'None';
    document.getElementById('conditions-value').textContent = userData.conditions || 'None';
    document.getElementById('medications-value').textContent = userData.medications || 'None';
    
    // Save to local storage
    localStorage.setItem('userData', JSON.stringify(userData));
    
    // Switch back to view mode
    toggleEdit('medical-info');
    
    showAlert('Medical information updated successfully!', 'success');
}

/**
 * Handle emergency contact form submission
 */
function handleEmergencyContactSubmit(e, userData) {
    e.preventDefault();
    
    if (!validateRequiredFields(e.target)) {
        return;
    }
    
    const newContact = {
        name: document.getElementById('contact-name').value,
        relationship: document.getElementById('contact-relationship').value,
        phone: document.getElementById('contact-phone').value,
        email: document.getElementById('contact-email').value || null
    };
    
    // Add to emergency contacts
    if (!userData.emergencyContacts) {
        userData.emergencyContacts = [];
    }
    userData.emergencyContacts.push(newContact);
    
    // Save to local storage
    localStorage.setItem('userData', JSON.stringify(userData));
    
    // Re-render contacts
    renderEmergencyContacts(userData.emergencyContacts);
    
    // Close modal and reset form
    closeModal('contact-modal');
    e.target.reset();
    
    showAlert('Emergency contact added successfully!', 'success');
}

/**
 * Handle password change form submission
 */
function handlePasswordChange(e, userData) {
    e.preventDefault();
    
    const current = document.getElementById('current-password').value;
    const newPass = document.getElementById('new-password').value;
    const confirm = document.getElementById('confirm-password').value;
    
    // Basic validation
    if (newPass.length < 8) {
        showAlert('Password must be at least 8 characters', 'error');
        return;
    }
    
    if (newPass !== confirm) {
        showAlert("New passwords don't match!", 'error');
        return;
    }
    
    // In a real app, you would validate current password and update
    showAlert("Password changed successfully!", 'success');
    
    // Close modal and reset form
    closeModal('password-modal');
    e.target.reset();
}

/**
 * Render emergency contacts
 */
function renderEmergencyContacts(contacts) {
    const container = document.querySelector('.emergency-contacts');
    if (!container) return;
    
    // Clear existing contacts (except the add button)
    container.innerHTML = '';
    
    if (contacts && contacts.length > 0) {
        contacts.forEach((contact, index) => {
            const contactCard = document.createElement('div');
            contactCard.className = 'contact-card';
            contactCard.innerHTML = `
                <div class="contact-header">
                    <h3>${contact.name}</h3>
                    <div class="contact-actions">
                        <button class="btn-icon" onclick="editEmergencyContact(${index})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icon" onclick="deleteEmergencyContact(${index})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                <p><i class="fas fa-phone"></i> ${formatPhone(contact.phone)}</p>
                ${contact.email ? `<p><i class="fas fa-envelope"></i> ${contact.email}</p>` : ''}
                <p><i class="fas fa-user"></i> ${contact.relationship}</p>
            `;
            container.appendChild(contactCard);
        });
    }
    
    // Add the "Add Contact" button
    const addBtn = document.createElement('div');
    addBtn.className = 'add-contact-btn';
    addBtn.innerHTML = `
        <i class="fas fa-plus-circle" style="font-size: 24px;"></i>
        <span>Add Emergency Contact</span>
    `;
    addBtn.addEventListener('click', openAddContactModal);
    container.appendChild(addBtn);
}

/**
 * Edit emergency contact
 */
window.editEmergencyContact = function(index) {
    const userData = JSON.parse(localStorage.getItem('userData')) || {};
    const contact = userData.emergencyContacts[index];
    
    if (!contact) return;
    
    // Fill the form with contact data
    document.getElementById('contact-name').value = contact.name;
    document.getElementById('contact-relationship').value = contact.relationship;
    document.getElementById('contact-phone').value = contact.phone;
    document.getElementById('contact-email').value = contact.email || '';
    
    // Change the form to edit mode
    const form = document.getElementById('contact-form');
    form.dataset.editIndex = index;
    form.querySelector('button[type="submit"]').textContent = 'Update Contact';
    
    // Update modal title
    document.querySelector('#contact-modal h2').textContent = 'Edit Emergency Contact';
    
    // Open the modal
    openAddContactModal();
    
    // Update form submission to handle edit
    form.onsubmit = function(e) {
        e.preventDefault();
        
        if (!validateRequiredFields(e.target)) {
            return;
        }
        
        // Update the contact
        userData.emergencyContacts[index] = {
            name: document.getElementById('contact-name').value,
            relationship: document.getElementById('contact-relationship').value,
            phone: document.getElementById('contact-phone').value,
            email: document.getElementById('contact-email').value || null
        };
        
        // Save to local storage
        localStorage.setItem('userData', JSON.stringify(userData));
        
        // Re-render contacts
        renderEmergencyContacts(userData.emergencyContacts);
        
        // Close modal and reset form
        closeModal('contact-modal');
        form.reset();
        delete form.dataset.editIndex;
        form.querySelector('button[type="submit"]').textContent = 'Save Contact';
        document.querySelector('#contact-modal h2').textContent = 'Add Emergency Contact';
        
        showAlert('Emergency contact updated successfully!', 'success');
    };
};

/**
 * Delete emergency contact
 */
window.deleteEmergencyContact = function(index) {
    if (!confirm('Are you sure you want to delete this emergency contact?')) {
        return;
    }
    
    const userData = JSON.parse(localStorage.getItem('userData')) || {};
    if (userData.emergencyContacts && userData.emergencyContacts[index]) {
        userData.emergencyContacts.splice(index, 1);
        localStorage.setItem('userData', JSON.stringify(userData));
        renderEmergencyContacts(userData.emergencyContacts);
        showAlert('Emergency contact deleted', 'warning');
    }
};

/**
 * Set up quick links navigation
 */
function setupQuickLinks() {
    const links = document.querySelectorAll('.quick-links a');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links
            links.forEach(l => l.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Scroll to section
            const targetId = this.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

/**
 * Set up responsive behavior
 */
function setupResponsiveBehavior() {
    // Handle window resize
    window.addEventListener('resize', function() {
        // You can add responsive behaviors here
    });
}

/**
 * Toggle edit mode for sections
 */
window.toggleEdit = function(section) {
    const view = document.getElementById(`${section}-view`);
    const edit = document.getElementById(`${section}-edit`);
    
    if (view.style.display === 'none') {
        view.style.display = 'grid';
        edit.style.display = 'none';
    } else {
        view.style.display = 'none';
        edit.style.display = 'block';
        // Scroll to the top of the edit form
        edit.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

/**
 * Open add contact modal
 */
window.openAddContactModal = function() {
    const modal = document.getElementById('contact-modal');
    const form = document.getElementById('contact-form');
    
    // Reset form and set default title
    form.reset();
    form.querySelector('button[type="submit"]').textContent = 'Save Contact';
    document.querySelector('#contact-modal h2').textContent = 'Add Emergency Contact';
    delete form.dataset.editIndex;
    
    modal.style.display = 'flex';
    document.getElementById('contact-name').focus();
};

/**
 * Open password change modal
 */
window.openPasswordModal = function() {
    document.getElementById('password-modal').style.display = 'flex';
    document.getElementById('current-password').focus();
};

/**
 * Close modal
 */
window.closeModal = function(modalId) {
    document.getElementById(modalId).style.display = 'none';
};

/**
 * Show alert/notification
 */
function showAlert(message, type = 'info') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 
                          type === 'error' ? 'times-circle' : 
                          type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(alert);
    
    // Show with animation
    setTimeout(() => {
        alert.classList.add('show');
    }, 10);
    
    // Hide after 5 seconds
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => {
            alert.remove();
        }, 300);
    }, 5000);
}

/**
 * Validate required form fields
 */
function validateRequiredFields(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('error');
            isValid = false;
        } else {
            field.classList.remove('error');
        }
    });
    
    if (!isValid) {
        showAlert('Please fill in all required fields', 'error');
    }
    
    return isValid;
}

/**
 * Format date to readable string
 */
function formatDate(dateString) {
    if (!dateString) return 'Not specified';
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

/**
 * Format gender to readable string
 */
function formatGender(gender) {
    return gender === 'male' ? 'Male' : 
           gender === 'female' ? 'Female' : 
           gender === 'other' ? 'Other' : 'Prefer not to say';
}

/**
 * Format phone number
 */
function formatPhone(phone) {
    if (!phone) return 'Not specified';
    // Simple phone formatting - in a real app you'd use a library
    if (phone.length === 10) {
        return `(${phone.substring(0, 3)}) ${phone.substring(3, 6)}-${phone.substring(6)}`;
    }
    return phone;
}

/**
 * Convert cm to feet and inches
 */
function cmToFeet(cm) {
    const feet = Math.floor(cm / 30.48);
    const inches = Math.round((cm % 30.48) / 2.54);
    return `${feet}'${inches}"`;
}

/**
 * Convert kg to lbs
 */
function kgToLbs(kg) {
    return Math.round(kg * 2.20462);
}

/**
 * Add CSS for alerts and modals
 */
function addDynamicStyles() {
    const style = document.createElement('style');
    style.textContent = `
        /* Alert/Notification Styles */
        .alert {
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
            max-width: 350px;
        }
        
        .alert.show {
            transform: translateX(0);
        }
        
        .alert-success {
            border-left: 4px solid #28a745;
        }
        
        .alert-info {
            border-left: 4px solid #2a7fba;
        }
        
        .alert-warning {
            border-left: 4px solid #ffc107;
        }
        
        .alert-error {
            border-left: 4px solid #dc3545;
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
            padding: 20px;
            position: relative;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
        
        .close-modal:hover {
            color: #333;
        }
        
        /* Form Error Styles */
        .error {
            border-color: #dc3545 !important;
        }
        
        /* Contact Card Styles */
        .contact-card {
            background: #f9fbfe;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .contact-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .contact-actions {
            display: flex;
            gap: 5px;
        }
    `;
    document.head.appendChild(style);
}

// Add dynamic styles when the page loads
addDynamicStyles();