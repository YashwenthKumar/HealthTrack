document.addEventListener('DOMContentLoaded', function() {
    // Tab Switching
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            tabButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Filter consultations (in a real app, this would fetch from server)
            const tabName = this.getAttribute('data-tab');
            filterConsultations(tabName);
        });
    });
    
    // Filter consultations by status
    function filterConsultations(status) {
        const consultations = document.querySelectorAll('.consultation-card');
        
        consultations.forEach(card => {
            if(status === 'all') {
                card.style.display = 'block';
            } else {
                if(card.classList.contains(status)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            }
        });
    }
    
    // Notification bell
    document.querySelector('.notification-bell').addEventListener('click', function() {
        alert('You have 3 unread notifications');
    });
    
    // Demo action buttons
    document.querySelectorAll('.consultation-actions button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = this.querySelector('i').className;
            
            if(icon.includes('video')) {
                alert('Starting video consultation...');
            } else if(icon.includes('file-medical')) {
                alert('Opening patient records...');
            } else if(icon.includes('calendar-plus')) {
                alert('Scheduling follow-up...');
            }
        });
    });
});