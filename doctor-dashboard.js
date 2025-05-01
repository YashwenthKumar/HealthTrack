// doctor-dashboard.js

document.addEventListener('DOMContentLoaded', function() {
  // Initialize the dashboard
  initDashboard();
  
  // Set up event listeners
  setupEventListeners();
  
  // Load initial data
  loadDashboardData();
});

function initDashboard() {
  // Initialize date display
  updateCurrentDate();
  
  // Initialize any UI components
  initAppointmentCards();
  initPatientQueue();
  initRecentPatients();
  initMessages();
  
  // Check for new notifications periodically
  startNotificationCheck();
}

function setupEventListeners() {
  // Appointment card buttons
  document.querySelectorAll('.appointment-actions .btn-primary').forEach(button => {
      button.addEventListener('click', function() {
          const appointmentCard = this.closest('.appointment-card');
          startAppointment(appointmentCard);
      });
  });
  
  document.querySelectorAll('.appointment-actions .btn-outline').forEach(button => {
      button.addEventListener('click', function() {
          const appointmentCard = this.closest('.appointment-card');
          showAppointmentDetails(appointmentCard);
      });
  });
  
  // Queue buttons
  document.querySelectorAll('.queue-card .btn-primary').forEach(button => {
      button.addEventListener('click', function() {
          const queueCard = this.closest('.queue-card');
          beginConsultation(queueCard);
      });
  });
  
  // Patient profile buttons
  document.querySelectorAll('.patient-card .btn-outline').forEach(button => {
      button.addEventListener('click', function() {
          const patientCard = this.closest('.patient-card');
          viewPatientProfile(patientCard);
      });
  });
  
  // Message cards
  document.querySelectorAll('.message-card').forEach(card => {
      card.addEventListener('click', function() {
          openMessage(this);
      });
  });
  
  // Notification bell
  document.querySelector('.notifications').addEventListener('click', showNotifications);
}

function updateCurrentDate() {
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  const today = new Date();
  document.getElementById('current-date').textContent = today.toLocaleDateString('en-US', options);
}

function loadDashboardData() {
  // In a real application, this would fetch data from your backend API
  console.log('Loading dashboard data...');
  
  // Simulate API calls with setTimeout
  setTimeout(() => {
      updateAppointmentStats();
      updateQueueStats();
  }, 500);
}

function updateAppointmentStats() {
  // This would come from API in real app
  const stats = {
      todayAppointments: 12,
      inQueue: 5,
      newMessages: 3
  };
  
  document.querySelectorAll('.stat-item h4')[0].textContent = stats.todayAppointments;
  document.querySelectorAll('.stat-item h4')[1].textContent = stats.inQueue;
  document.querySelectorAll('.stat-item h4')[2].textContent = stats.newMessages;
}

function updateQueueStats() {
  // Update queue badge count
  const queueItems = document.querySelectorAll('.queue-card').length;
  document.querySelector('.notifications .badge').textContent = queueItems;
}

function initAppointmentCards() {
  // Add any appointment card specific initialization
  document.querySelectorAll('.appointment-card').forEach(card => {
      const type = card.querySelector('.appointment-type');
      if (type.textContent.includes('Video')) {
          card.classList.add('video-consultation');
      } else {
          card.classList.add('in-person');
      }
  });
}

function initPatientQueue() {
  // Initialize queue cards with status colors
  document.querySelectorAll('.queue-card').forEach(card => {
      const status = card.querySelector('.queue-status');
      if (status.classList.contains('status-urgent')) {
          card.style.borderLeft = '4px solid #ff5252';
      } else if (status.classList.contains('status-in-progress')) {
          card.style.borderLeft = '4px solid #4caf50';
      } else {
          card.style.borderLeft = '4px solid #2196f3';
      }
  });
}

function initRecentPatients() {
  // Add hover effects to patient cards
  document.querySelectorAll('.patient-card').forEach(card => {
      card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-5px)';
          this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
      });
      
      card.addEventListener('mouseleave', function() {
          this.style.transform = '';
          this.style.boxShadow = '';
      });
  });
}

function initMessages() {
  // Mark unread messages
  document.querySelectorAll('.message-status.status-unread').forEach(status => {
      status.closest('.message-card').classList.add('unread');
  });
}

function startAppointment(card) {
  const patientName = card.querySelector('.patient-info h4').textContent;
  const appointmentTime = card.querySelector('.appointment-time').textContent;
  const isVideo = card.querySelector('.appointment-type').textContent.includes('Video');
  
  console.log(`Starting appointment with ${patientName} at ${appointmentTime}`);
  
  if (isVideo) {
      // Redirect to telemedicine page or open video modal
      alert(`Starting video consultation with ${patientName}`);
      // window.location.href = 'telemedicine.html?patient=' + encodeURIComponent(patientName);
  } else {
      // Redirect to consultation page or open in-person modal
      alert(`Starting in-person consultation with ${patientName}`);
      // window.location.href = 'consultation.html?patient=' + encodeURIComponent(patientName);
  }
}

function showAppointmentDetails(card) {
  const patientName = card.querySelector('.patient-info h4').textContent;
  const patientId = card.querySelector('.patient-info p').textContent;
  const appointmentTime = card.querySelector('.appointment-time').textContent;
  
  // In a real app, this would open a modal with more details
  console.log(`Showing details for appointment with ${patientName} (${patientId})`);
  alert(`Appointment Details:\nPatient: ${patientName}\nID: ${patientId}\nTime: ${appointmentTime}`);
}

function beginConsultation(card) {
  const patientName = card.querySelector('.queue-patient-info h4').textContent;
  const reason = card.querySelector('.queue-reason').textContent;
  
  console.log(`Beginning consultation with ${patientName} for ${reason}`);
  alert(`Beginning consultation with ${patientName}\nReason: ${reason}`);
  
  // Update status to "In Progress"
  const status = card.querySelector('.queue-status');
  status.textContent = 'In Progress';
  status.classList.remove('status-waiting', 'status-urgent');
  status.classList.add('status-in-progress');
  card.style.borderLeft = '4px solid #4caf50';
  
  // Change button to "View Details"
  const button = card.querySelector('.btn-primary');
  button.textContent = 'View Details';
  button.classList.add('btn-outline');
  button.classList.remove('btn-primary');
}

function viewPatientProfile(card) {
  const patientName = card.querySelector('.patient-details h4').textContent;
  const patientId = card.querySelector('.patient-details p').textContent;
  
  console.log(`Viewing profile for ${patientName} (${patientId})`);
  // In a real app, redirect to patient profile page
  // window.location.href = `patient-profile.html?id=${patientId}`;
  alert(`Redirecting to profile page for ${patientName} (${patientId})`);
}

function openMessage(card) {
  const sender = card.querySelector('.message-sender').textContent;
  const content = card.querySelector('.message-preview').textContent;
  
  console.log(`Opening message from ${sender}`);
  
  // Mark as read
  const status = card.querySelector('.message-status');
  if (status.classList.contains('status-unread')) {
      status.classList.remove('status-unread');
      status.classList.add('status-read');
      card.classList.remove('unread');
      
      // Update unread count
      const unreadCount = document.querySelectorAll('.message-status.status-unread').length;
      document.querySelector('.notifications .badge').textContent = unreadCount;
  }
  
  // In a real app, this would open a modal or redirect to messages
  alert(`Message from ${sender}:\n\n${content}`);
}

function showNotifications() {
  // In a real app, this would show a dropdown with notifications
  const notificationCount = document.querySelector('.notifications .badge').textContent;
  alert(`You have ${notificationCount} new notifications`);
}

function startNotificationCheck() {
  // Simulate checking for new notifications periodically
  setInterval(() => {
      // In a real app, this would make an API call
      const newNotifications = Math.floor(Math.random() * 3); // Random 0-2
      if (newNotifications > 0) {
          console.log(`New notifications: ${newNotifications}`);
          // Update badge
          const current = parseInt(document.querySelector('.notifications .badge').textContent);
          document.querySelector('.notifications .badge').textContent = current + newNotifications;
          
          // Show subtle notification
          showToast(`You have ${newNotifications} new notification(s)`);
      }
  }, 30000); // Check every 30 seconds
}

function showToast(message) {
  // Create toast notification
  const toast = document.createElement('div');
  toast.className = 'toast-notification';
  toast.textContent = message;
  document.body.appendChild(toast);
  
  // Show toast
  setTimeout(() => {
      toast.classList.add('show');
  }, 100);
  
  // Hide after 3 seconds
  setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => {
          toast.remove();
      }, 300);
  }, 3000);
}

// Utility function to simulate API calls
function fetchData(endpoint) {
  return new Promise((resolve) => {
      setTimeout(() => {
          // Simulated data - in real app this would be a fetch() call
          const mockData = {
              '/api/appointments': { count: 12, appointments: [] },
              '/api/queue': { count: 5, patients: [] },
              '/api/messages': { count: 3, messages: [] }
          };
          
          resolve(mockData[endpoint] || {});
      }, 500);
  });
}