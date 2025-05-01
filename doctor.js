        // Sample data for the dashboard
        const doctorData = {
            name: "Dr. Michael Chen",
            specialty: "Cardiologist",
            avatar: "https://images.unsplash.com/photo-1622253692010-333f2da6031d",
            stats: {
                appointments: 24,
                patients: 156,
                prescriptions: 12,
                telemedicine: 8
            },
            todaysAppointments: [
                {
                    patient: "Jennifer Smith",
                    patientId: "PT1001",
                    avatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330",
                    time: "09:00 AM",
                    type: "Follow-up",
                    status: "scheduled"
                },
                {
                    patient: "Michael Johnson",
                    patientId: "PT1002",
                    avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d",
                    time: "10:30 AM",
                    type: "New Patient",
                    status: "scheduled"
                },
                {
                    patient: "Sarah Williams",
                    patientId: "PT1003",
                    avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80",
                    time: "11:15 AM",
                    type: "Video Consultation",
                    status: "completed"
                },
                {
                    patient: "Emily Davis",
                    patientId: "PT1004",
                    avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2",
                    time: "02:00 PM",
                    type: "Annual Checkup",
                    status: "cancelled"
                }
            ]
        };

        // In a real app, you would fetch this data from an API
        document.addEventListener('DOMContentLoaded', function() {
            // Update doctor info
            document.querySelector('.doctor-info h3').textContent = doctorData.name;
            document.querySelector('.doctor-info p').textContent = doctorData.specialty;
            
            // Update stats
            document.querySelector('.stat-value:nth-child(1)').textContent = doctorData.stats.appointments;
            document.querySelector('.stat-value:nth-child(2)').textContent = doctorData.stats.patients;
            document.querySelector('.stat-value:nth-child(3)').textContent = doctorData.stats.prescriptions;
            document.querySelector('.stat-value:nth-child(4)').textContent = doctorData.stats.telemedicine;
            
            // Update welcome banner
            document.querySelector('.welcome-banner h1').textContent = `Good ${getTimeOfDay()}, ${doctorData.name.split(' ')[0]}`;
            document.querySelector('.welcome-banner p').textContent = `You have ${doctorData.todaysAppointments.length} appointments today`;
        });

        function getTimeOfDay() {
            const hour = new Date().getHours();
            if (hour < 12) return 'Morning';
            if (hour < 18) return 'Afternoon';
            return 'Evening';
        }

        // Notification bell click handler
        document.querySelector('.notification-bell').addEventListener('click', function() {
            alert('You have 3 new notifications');
            // In a real app, you would show a dropdown with notifications
        });
        // In doctor.js - Modified safe version
function updateCalendarPeriod(calendar) {
    const periodElement = document.getElementById('calendarPeriod');
    if (!periodElement) return; // Exit if element doesn't exist
    
    const currentView = calendar.view;
    let periodText = '';
    
    if (currentView.type === 'dayGridMonth') {
        periodText = currentView.title;
    } else if (currentView.type === 'timeGridWeek') {
        periodText = 'Week of ' + currentView.title.split('–')[0];
    } else {
        periodText = currentView.title;
    }
    
    periodElement.textContent = periodText;
}
