<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthTrack Pro - Telemedicine</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2a7fba;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            overflow-x: hidden;
        }
        
        .dashboard-layout {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles (keep your existing sidebar styles) */
        
        .main-content {
            flex: 1;
            padding: 20px;
            width: 100%;
        }
        
        /* Telemedicine Specific Styles */
        .telemedicine-container {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
        }
        
        
        .video-container {
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            aspect-ratio: 16/9;
        }
        
        .video-controls {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.5);
            padding: 15px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .control-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .control-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .control-btn.end-call {
            background: var(--danger-color);
        }
        
        .control-btn.end-call:hover {
            background: #c82333;
        }
        
        .patient-info-sidebar {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .patient-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .patient-details h3 {
            margin-bottom: 5px;
        }
        
        .patient-meta {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .meta-item i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }
        
        .consultation-notes {
            margin-top: 20px;
        }
        
        .notes-textarea {
            width: 100%;
            height: 150px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            resize: none;
            margin-bottom: 15px;
        }
        
        .prescription-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .chat-container {
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        .chat-messages {
            height: 200px;
            overflow-y: auto;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 6px;
        }
        
        .message-input {
            display: flex;
            gap: 10px;
        }
        
        .message-input input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        
        @media (max-width: 1024px) {
            .telemedicine-container {
                grid-template-columns: 1fr;
            }
        }
        
.sidebar {
    width: 250px;
    background: var(--white);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    z-index: 100;
}

.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--light-gray);
}

.logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.logo-icon {
    width: 36px;
    height: 36px;
    background: var(--primary);
    color: var(--white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.sidebar-nav ul {
    list-style: none;
    padding: 1rem 0;
}

.sidebar-nav li a {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: var(--text-medium);
    transition: var(--transition);
}

.sidebar-nav li a:hover {
    color: var(--primary);
    background: var(--primary-light);
}

.sidebar-nav li.active a {
    color: var(--primary);
    background: var(--primary-light);
    border-left: 3px solid var(--primary);
    font-weight: 500;
}

.sidebar-nav li a i {
    width: 24px;
    text-align: center;
    margin-right: 0.75rem;
    font-size: 1rem;
}

.sidebar-footer {
    margin-top: auto;
    padding: 1.5rem;
    border-top: 1px solid var(--light-gray);
}

.logout-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: var(--text-medium);
    transition: var(--transition);
}

.logout-btn:hover {
    color: var(--danger);
}
/* Updated telemedicine layout styles */
/* Updated telemedicine layout styles */
.telemedicine-container {
  display: grid;
  grid-template-columns: 1fr 300px;
  grid-template-rows: auto 1fr;
  gap: 20px;
  height: calc(100vh - 120px); /* Adjust based on your header height */
  min-height: 600px; /* Prevent too much squishing */
}

.video-container {
  grid-column: 1;
  grid-row: 1;
  min-height: 300px; /* Ensure video has reasonable height */
  max-height: 60vh; /* Prevent video from taking too much space */
}

.chat-container {
  grid-column: 1;
  grid-row: 2;
  display: flex;
  flex-direction: column;
  background: white;
  border-radius: 8px;
  padding: 15px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.chat-messages {
  flex-grow: 1;
  overflow-y: auto;
  min-height: 150px; /* Ensure chat has some height */
}

.patient-info-sidebar {
  grid-column: 2;
  grid-row: 1 / span 2;
  display: flex;
  flex-direction: column;
  overflow-y: auto; /* Add scroll if content is long */
}

/* For smaller screens */
@media (max-width: 1024px) {
  .telemedicine-container {
    grid-template-columns: 1fr;
    grid-template-rows: auto auto auto;
    height: auto;
  }
  
  .video-container {
    grid-column: 1;
    grid-row: 1;
    max-height: none;
  }
  
  .chat-container {
    grid-column: 1;
    grid-row: 2;
  }
  
  .patient-info-sidebar {
    grid-column: 1;
    grid-row: 3;
  }
}

    </style>
    <link rel="stylesheet" href="assets/css/doctor-dashboard.css">
    <link rel="stylesheet" href="assets/css/scrollbar.css">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Include your existing sidebar here -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h1>HealthTrack</h1>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="doctor-dashboard.html">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="appointments.html">
                            <i class="fas fa-calendar-check"></i>
                            <span>Appointments</span>
                        </a>
                    </li>
                    <li>
                        <a href="doctor-patient.html">
                            <i class="fas fa-procedures"></i>
                            <span>My Patients</span>
                        </a>
                    </li>
                    <li>
                        <a href="telemedicine.html">
                            <i class="fas fa-video"></i>
                            <span>Telemedicine</span>
                        </a>
                    </li>
                    <li>
                        <a href="doctor-prescriptions.html">
                            <i class="fas fa-prescription-bottle-alt"></i>
                            <span>Prescriptions</span>
                        </a>
                    </li>
                    <li>
                        <a href="doctor-profile.html">
                            <i class="fas fa-user-md"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="doctor-schedule.html">
                            <i class="fas fa-clock"></i>
                            <span>Schedule</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="logout.html" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h2>Telemedicine Consultation</h2>
                    <p id="current-date">Monday, June 12, 2023</p>
                </div>
                <div class="header-right">
                    <div class="consultation-timer">
                        <i class="fas fa-clock"></i>
                        <span id="call-timer">00:00:00</span>
                    </div>
                </div>
            </header>
            
            <div class="telemedicine-container">
                    <div class="video-container">
                        <video id="local-video" autoplay muted></video>
                        <video id="remote-video" autoplay></video>
                        
                        <div class="video-controls">
                            <button class="control-btn" id="mute-btn">
                                <i class="fas fa-microphone"></i>
                            </button>
                            <button class="control-btn" id="video-btn">
                                <i class="fas fa-video"></i>
                            </button>
                            <button class="control-btn" id="screen-share-btn">
                                <i class="fas fa-desktop"></i>
                            </button>
                            <button class="control-btn end-call" id="end-call-btn">
                                <i class="fas fa-phone-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="chat-container">
                        <h4>Chat</h4>
                        <div class="chat-messages" id="chat-messages">
                            <!-- Messages will appear here -->
                        </div>
                        <div class="message-input">
                            <input type="text" id="message-input" placeholder="Type your message...">
                            <button class="btn btn-primary" id="send-btn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                    <div class="patient-info-sidebar">
                        <div class="patient-header">
                            <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Patient" width="60" height="60" style="border-radius:50%">
                            <div class="patient-details">
                                <h3>Sarah Johnson</h3>
                                <p>ID: PT-100245</p>
                            </div>
                        </div>
                        
                        <div class="patient-meta">
                            <div class="meta-item">
                                <i class="fas fa-birthday-cake"></i>
                                <span>42 years old</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-heartbeat"></i>
                                <span>Hypertension, Type 2 Diabetes</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Last visit: June 10, 2023</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-stethoscope"></i>
                                <span>Chief Complaint: Headache and dizziness</span>
                            </div>
                        </div>
                        
                        <div class="consultation-notes">
                            <h4>Consultation Notes</h4>
                            <textarea class="notes-textarea" placeholder="Enter consultation notes..."></textarea>
                            <button class="btn btn-primary">Save Notes</button>
                        </div>
                        
                        <div class="prescription-actions">
                            <button class="btn btn-outline">
                                <i class="fas fa-file-medical"></i> View Records
                            </button>
                            <button class="btn btn-primary">
                                <i class="fas fa-prescription"></i> New Prescription
                            </button>
                        </div>
                    </div>  
            </div>
        </main>
    </div>
    <script src="assets/js/shared.js"></script>
    <script src="assets/js/sidebar.js"></script>
    <script>
       // Telemedicine Consultation Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initSidebar();
    updateCurrentDate();
    startCallTimer();
    initVideoCall();
    initChat();
    initPatientInfo();
    initConsultationNotes();
    initPrescriptionActions();
});

// ==================== CORE FUNCTIONS ====================

function initSidebar() {
    // Set active nav link
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.sidebar-nav li a').forEach(link => {
        const linkPage = link.getAttribute('href').split('/').pop();
        if (linkPage === currentPage) {
            link.parentElement.classList.add('active');
        }
    });

    // Mobile sidebar toggle
    const sidebarToggle = document.createElement('div');
    sidebarToggle.className = 'sidebar-toggle';
    sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
    document.querySelector('.top-header').prepend(sidebarToggle);
    
    sidebarToggle.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('collapsed');
    });
}

function updateCurrentDate() {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', options);
}

// ==================== CALL TIMER FUNCTIONALITY ====================

function startCallTimer() {
    let seconds = 0;
    const timerElement = document.getElementById('call-timer');
    
    const timerInterval = setInterval(() => {
        seconds++;
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        timerElement.textContent = 
            `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }, 1000);

    // Store interval so we can clear it later
    window.callTimerInterval = timerInterval;
}

// ==================== VIDEO CALL FUNCTIONALITY ====================

function initVideoCall() {
    // Elements
    const localVideo = document.getElementById('local-video');
    const remoteVideo = document.getElementById('remote-video');
    const muteBtn = document.getElementById('mute-btn');
    const videoBtn = document.getElementById('video-btn');
    const screenShareBtn = document.getElementById('screen-share-btn');
    const endCallBtn = document.getElementById('end-call-btn');

    // State
    let isMuted = false;
    let isVideoOff = false;
    let isSharingScreen = false;
    let localStream;
    let remoteStream;

    // Mock video streams (in a real app, use getUserMedia and WebRTC)
    function setupMockStreams() {
        // Mock local video (would be replaced with actual camera feed)
        localVideo.srcObject = new MediaStream();
        localVideo.play().catch(e => console.error("Error playing local video:", e));
        
        // Mock remote video (would be replaced with peer connection)
        remoteVideo.srcObject = new MediaStream();
        remoteVideo.play().catch(e => console.error("Error playing remote video:", e));
    }

    // Toggle audio mute
    muteBtn.addEventListener('click', () => {
        isMuted = !isMuted;
        muteBtn.innerHTML = isMuted ? '<i class="fas fa-microphone-slash"></i>' : '<i class="fas fa-microphone"></i>';
        muteBtn.title = isMuted ? "Unmute" : "Mute";
        
        // In a real app: localStream.getAudioTracks()[0].enabled = !isMuted;
        console.log(isMuted ? "Microphone muted" : "Microphone unmuted");
    });

    // Toggle video
    videoBtn.addEventListener('click', () => {
        isVideoOff = !isVideoOff;
        videoBtn.innerHTML = isVideoOff ? '<i class="fas fa-video-slash"></i>' : '<i class="fas fa-video"></i>';
        videoBtn.title = isVideoOff ? "Turn on video" : "Turn off video";
        localVideo.style.opacity = isVideoOff ? '0' : '1';
        
        // In a real app: localStream.getVideoTracks()[0].enabled = !isVideoOff;
        console.log(isVideoOff ? "Video turned off" : "Video turned on");
    });

    // Screen sharing
    screenShareBtn.addEventListener('click', async () => {
        if (isSharingScreen) {
            // Stop screen share
            isSharingScreen = false;
            screenShareBtn.innerHTML = '<i class="fas fa-desktop"></i>';
            screenShareBtn.title = "Share screen";
            
            // In a real app: switch back to camera
            console.log("Screen sharing stopped");
        } else {
            // Start screen share
            isSharingScreen = true;
            screenShareBtn.innerHTML = '<i class="fas fa-stop"></i>';
            screenShareBtn.title = "Stop sharing";
            
            // In a real app: getDisplayMedia()
            console.log("Screen sharing started");
        }
    });

    // End call
    endCallBtn.addEventListener('click', () => {
        if (confirm("End this consultation?")) {
            // Clean up
            clearInterval(window.callTimerInterval);
            
            // In a real app: close all streams and peer connections
            if (localVideo.srcObject) {
                localVideo.srcObject.getTracks().forEach(track => track.stop());
            }
            if (remoteVideo.srcObject) {
                remoteVideo.srcObject.getTracks().forEach(track => track.stop());
            }
            
            console.log("Call ended");
            window.location.href = "doctor-dashboard.html";
        }
    });

    // Initialize mock streams
    setupMockStreams();
}

// ==================== CHAT FUNCTIONALITY ====================

function initChat() {
    const chatMessages = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');

    // Add a message to the chat
    function addMessage(sender, message, isDoctor = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message';
        messageDiv.style.cssText = `
            margin-bottom: 10px;
            padding: 8px 12px;
            border-radius: 6px;
            background-color: ${isDoctor ? '#e1f0ff' : '#f0f0f0'};
            max-width: 80%;
            margin-left: ${isDoctor ? 'auto' : '0'};
            word-wrap: break-word;
        `;

        const senderSpan = document.createElement('span');
        senderSpan.style.cssText = `
            font-weight: 500;
            display: block;
            margin-bottom: 5px;
            color: ${isDoctor ? 'var(--primary-color)' : '#333'};
        `;
        senderSpan.textContent = sender;

        const messageSpan = document.createElement('span');
        messageSpan.textContent = message;

        messageDiv.appendChild(senderSpan);
        messageDiv.appendChild(messageSpan);
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Send a message
    function sendMessage() {
        const message = messageInput.value.trim();
        if (message) {
            addMessage("Dr. Smith", message, true);
            messageInput.value = '';
            
            // Simulate patient response
            setTimeout(() => {
                const responses = [
                    "The pain is mostly in my forehead",
                    "It's a dull ache that comes and goes",
                    "I've been taking ibuprofen but it doesn't help much",
                    "I've also been feeling dizzy sometimes",
                    "The headaches are worse in the morning",
                    "No, I haven't had any nausea with it"
                ];
                const randomResponse = responses[Math.floor(Math.random() * responses.length)];
                addMessage("Sarah Johnson", randomResponse);
            }, 1000 + Math.random() * 2000);
        }
    }

    // Event listeners
    sendBtn.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    // Initial sample messages
    addMessage("Sarah Johnson", "Hello doctor, I've been having these headaches for 3 days now");
    addMessage("Dr. Smith", "Hello Sarah, can you describe the pain?", true);
}

// ==================== PATIENT INFO FUNCTIONALITY ====================

function initPatientInfo() {
    // In a real app, this would fetch patient data from an API
    const patientData = {
        name: "Sarah Johnson",
        id: "PT-100245",
        age: "42 years old",
        conditions: "Hypertension, Type 2 Diabetes",
        lastVisit: "June 10, 2023",
        complaint: "Headache and dizziness",
        avatar: "https://randomuser.me/api/portraits/women/32.jpg"
    };

    // Update patient info in the sidebar
    document.querySelector('.patient-details h3').textContent = patientData.name;
    document.querySelector('.patient-details p').textContent = `ID: ${patientData.id}`;
    document.querySelector('.patient-header img').src = patientData.avatar;
    
    const metaItems = document.querySelectorAll('.meta-item span');
    metaItems[0].textContent = patientData.age;
    metaItems[1].textContent = patientData.conditions;
    metaItems[2].textContent = `Last visit: ${patientData.lastVisit}`;
    metaItems[3].textContent = `Chief Complaint: ${patientData.complaint}`;
}

// ==================== CONSULTATION NOTES FUNCTIONALITY ====================

function initConsultationNotes() {
    const notesTextarea = document.querySelector('.notes-textarea');
    const saveNotesBtn = document.querySelector('.consultation-notes button');

    // Load saved notes (in a real app, from database)
    const savedNotes = localStorage.getItem('consultation-notes-PT-100245') || '';
    notesTextarea.value = savedNotes;

    // Save notes
    saveNotesBtn.addEventListener('click', () => {
        localStorage.setItem('consultation-notes-PT-100245', notesTextarea.value);
        showToast('Notes saved successfully', 'success');
    });
}

// ==================== PRESCRIPTION ACTIONS ====================

function initPrescriptionActions() {
    const viewRecordsBtn = document.querySelector('.prescription-actions button:first-child');
    const newPrescriptionBtn = document.querySelector('.prescription-actions button:last-child');

    viewRecordsBtn.addEventListener('click', () => {
        showToast('Opening patient records...', 'info');
        // In a real app, this would open patient records
    });

    newPrescriptionBtn.addEventListener('click', () => {
        showToast('Opening prescription form...', 'info');
        // In a real app, this would open a prescription form
    });
}

// ==================== TOAST NOTIFICATIONS ====================

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast-notification show ${type}`;
    toast.innerHTML = `
        <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 
                         type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add toast styles if not already present
if (!document.getElementById('toast-styles')) {
    const toastStyles = document.createElement('style');
    toastStyles.id = 'toast-styles';
    toastStyles.textContent = `
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1001;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .toast-notification.show {
            opacity: 1;
        }
        .toast-notification.success {
            background: var(--success-color);
        }
        .toast-notification.error {
            background: var(--danger-color);
        }
        .toast-notification.info {
            background: var(--primary-color);
        }
    `;
    document.head.appendChild(toastStyles);
}
    </script>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Telemedicine Video Call</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f5f7fa; font-family: 'Poppins', sans-serif; }
        .video-call-container { display: flex; flex-direction: column; align-items: center; margin-top: 40px; }
        .videos { display: flex; gap: 20px; }
        video { width: 400px; height: 300px; background: #222; border-radius: 8px; }
        .controls { margin-top: 20px; }
        .btn { padding: 10px 20px; border-radius: 6px; border: none; background: #2a7fba; color: #fff; font-size: 16px; cursor: pointer; }
        .btn.end { background: #dc3545; }
    </style>
</head>
<body>
    <div class="video-call-container">
        <h2>Telemedicine Video Call</h2>
        <div class="videos">
            <video id="localVideo" autoplay muted playsinline></video>
            <video id="remoteVideo" autoplay playsinline></video>
        </div>
        <div class="controls">
            <button class="btn" id="startBtn"><i class="fas fa-video"></i> Start Call</button>
            <button class="btn end" id="endBtn"><i class="fas fa-phone-slash"></i> End Call</button>
        </div>
        <div style="margin-top:20px;">
            <strong>Room ID:</strong>
            <input type="text" id="roomInput" value="room123" style="padding:5px 10px; border-radius:4px; border:1px solid #ccc;">
            <button class="btn" id="joinBtn">Join Room</button>
        </div>
    </div>
    <script>
    // This is a minimal WebRTC demo using PeerJS (https://peerjs.com/)
    // You must include PeerJS from CDN
    </script>
    <script src="https://unpkg.com/peerjs@1.5.2/dist/peerjs.min.js"></script>
    <script>
    let localStream;
    let peer;
    let call;
    const localVideo = document.getElementById('localVideo');
    const remoteVideo = document.getElementById('remoteVideo');
    const startBtn = document.getElementById('startBtn');
    const endBtn = document.getElementById('endBtn');
    const joinBtn = document.getElementById('joinBtn');
    const roomInput = document.getElementById('roomInput');
    let roomId = roomInput.value;

    // Get media stream
    async function getMedia() {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        localVideo.srcObject = localStream;
    }

    // PeerJS setup
    function setupPeer() {
        peer = new Peer('<?php echo $user_role; ?>_' + '<?php echo $user_id; ?>', {
            host: 'peerjs-server.herokuapp.com', // For demo only, use your own PeerJS server for production
            secure: true,
            port: 443
        });

        peer.on('open', id => {
            console.log('My peer ID is: ' + id);
        });

        peer.on('call', incomingCall => {
            getMedia().then(() => {
                incomingCall.answer(localStream);
                incomingCall.on('stream', remoteStream => {
                    remoteVideo.srcObject = remoteStream;
                });
                call = incomingCall;
            });
        });
    }

    joinBtn.onclick = () => {
        roomId = roomInput.value;
        setupPeer();
        alert('Ready! Share your Room ID with the other participant.');
    };

    startBtn.onclick = () => {
        if (!peer) {
            alert('Join a room first!');
            return;
        }
        getMedia().then(() => {
            // For demo, assume doctor calls patient (or vice versa)
            // In real app, you should coordinate roles and IDs via backend
            const targetId = (<?php echo json_encode($user_role); ?> === 'doctor')
                ? 'patient_' + '<?php echo $user_id; ?>'
                : 'doctor_' + '<?php echo $user_id; ?>';
            call = peer.call(targetId, localStream);
            call.on('stream', remoteStream => {
                remoteVideo.srcObject = remoteStream;
            });
        });
    };

    endBtn.onclick = () => {
        if (call) {
            call.close();
            remoteVideo.srcObject = null;
        }
        if (localStream) {
            localStream.getTracks().forEach(track => track.stop());
            localVideo.srcObject = null;
        }
    };
    </script>
</body>
</html>