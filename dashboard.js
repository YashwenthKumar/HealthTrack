document.addEventListener('DOMContentLoaded', function() {
    const userType = localStorage.getItem('userType');
    if(userType !== 'patient') {
        window.location.href = 'login.html'; // Redirect if not patient
    }
    // Rest of your dashboard code
    document.addEventListener('DOMContentLoaded', function() {
        // Set current date
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const currentDate = new Date().toLocaleDateString('en-US', options);
        document.getElementById('current-date').textContent = currentDate;
        
        // Load health summary data
        loadHealthSummary();
        
        // Load recent records
        loadRecentRecords();
        
        // Load upcoming appointments
        loadAppointments();
        
        // Notification bell click
        const notificationBell = document.querySelector('.notifications');
        if (notificationBell) {
            notificationBell.addEventListener('click', function() {
                alert('Notifications feature will be implemented');
            });
        }
    });
    
    function loadHealthSummary() {
        // Mock data - in real app, fetch from backend
        const healthData = {
            heartRate: 72,
            bloodPressure: "120/80",
            glucose: 98,
            weight: 68
        };
        
        // Update the UI with this data
        // In a real app, you would update each element individually
    }
    
    function loadRecentRecords() {
        // Mock data - in real app, fetch from backend
        const records = [
            {
                type: "lab",
                title: "Blood Test Results",
                date: "June 10, 2023",
                doctor: "Dr. Sarah Johnson"
            },
            {
                type: "prescription",
                title: "Prescription",
                date: "June 5, 2023",
                doctor: "Dr. Michael Chen"
            }
        ];
        
        // In a real app, you would dynamically generate record items
    }
    
    function loadAppointments() {
        // Mock data - in real app, fetch from backend
        const appointments = [
            {
                day: "15",
                month: "JUN",
                title: "Annual Checkup",
                time: "10:00 AM - 11:00 AM",
                doctor: "Dr. Sarah Johnson"
            }
        ];
        
        // In a real app, you would dynamically generate appointment items
    }
});