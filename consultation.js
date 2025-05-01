document.addEventListener('DOMContentLoaded', function() {
    // Tab Switching
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            tab.classList.add('active');
            const tabId = tab.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
            
            // Load data when switching tabs
            if (tabId === 'upcoming') {
                loadUpcomingAppointments();
            } else if (tabId === 'past') {
                loadPastAppointments();
            } else if (tabId === 'book') {
                loadDoctors();
            }
        });
    });
            
            // Modal Elements
            const bookingModal = document.getElementById('booking-modal');
            const confirmationModal = document.getElementById('confirmation-modal');
            const closeModalButtons = document.querySelectorAll('.close-modal, #close-confirmation');
            
            // Close modals when clicking close button or outside
            closeModalButtons.forEach(button => {
                button.addEventListener('click', () => {
                    bookingModal.style.display = 'none';
                    confirmationModal.style.display = 'none';
                });
            });
            
            window.addEventListener('click', (e) => {
                if (e.target === bookingModal) {
                    bookingModal.style.display = 'none';
                }
                if (e.target === confirmationModal) {
                    confirmationModal.style.display = 'none';
                }
            });
            
            // Load user data from session/local storage
            const userName = localStorage.getItem('userName') || 'John Doe';
            document.getElementById('user-name').textContent = userName;
            
            // Sample data - in a real app, this would come from an API
            const userAppointments = {
                upcoming: [
                    {
                        id: 1,
                        doctor: {
                            name: "Dr. Thanuja",
                            specialty: "Cardiologist",
                            image: "https://t3.ftcdn.net/jpg/01/14/89/28/360_F_114892812_Va0KWhvmSUOoYNEoHCAOJ8uYXzBiD8vY.jpg",
                            rating: 4.7,
                            reviews: 128
                        },
                        date: "2023-06-20",
                        time: "10:00 AM - 10:30 AM",
                        type: "video",
                        status: "confirmed"
                    },
                    {
                        id: 2,
                        doctor: {
                            name: "Dr.Ramu",
                            specialty: "General Physician",
                            image: "https://content3.jdmagicbox.com/v2/comp/vijayawada/q8/0866px866.x866.170617194532.y2q8/catalogue/ram-hospitals-tadepalli-vijayawada-general-physician-doctors-osuhx69gsl.jpg",
                            rating: 5.0,
                            reviews: 89
                        },
                        date: "2023-06-25",
                        time: "2:00 PM - 2:30 PM",
                        type: "clinic",
                        status: "confirmed"
                    }
                ],
                past: [
                    {
                        id: 3,
                        doctor: {
                            name: "Dr.Krishna",
                            specialty: "Dermatologist",
                            image: "https://static.vecteezy.com/system/resources/thumbnails/028/287/384/small/a-mature-indian-male-doctor-on-a-white-background-ai-generated-photo.jpg",
                            rating: 4.5,
                            reviews: 76
                        },
                        date: "2023-05-15",
                        time: "11:00 AM - 11:30 AM",
                        type: "video",
                        status: "completed"
                    }
                ]
            };
            
            const doctorsList = [
                {
                    id: 101,
                    name: "Dr.Sanketh",
                    specialty: "Cardiologist",
                    image: "https://www.shutterstock.com/image-photo/portrait-shot-happy-smiling-dental-600nw-2165296755.jpg",
                    rating: 4.7,
                    reviews: 128,
                    available: true,
                    availableToday: true
                },
                {
                    id: 102,
                    name: "Dr.Prabhu",
                    specialty: "General Physician",
                    image: "https://png.pngtree.com/background/20250206/original/pngtree-indian-male-doctor-poses-with-arms-crossed-in-portrait-photo-picture-image_14952526.jpg",
                    rating: 5.0,
                    reviews: 89,
                    available: true,
                    availableToday: true
                },
                {
                    id: 103,
                    name: "Dr.Sankar",
                    specialty: "Dermatologist",
                    image: "https://www.shutterstock.com/image-photo/medicine-healthcare-people-concept-happy-260nw-1019723806.jpg",
                    rating: 4.5,
                    reviews: 76,
                    available: false,
                    availableToday: false
                },
                {
                    id: 104,
                    name: "Dr.Dinesh",
                    specialty: "Neurologist",
                    image: "https://images.jdmagicbox.com/v2/comp/indore/t5/0731px731.x731.240805153644.j2t5/catalogue/doctor-service-for-home-mr-10-rd-indore-general-physician-doctors-wknnf0r6oy-250.jpg",
                    rating: 4.8,
                    reviews: 112,
                    available: true,
                    availableToday: false
                }
            ];
            
            // Load upcoming appointments
            function loadUpcomingAppointments() {
                const container = document.getElementById('upcoming-appointments');
                container.innerHTML = '';
                
                if (userAppointments.upcoming.length === 0) {
                    container.innerHTML = '<p class="no-appointments">No upcoming appointments scheduled.</p>';
                    return;
                }
                
                userAppointments.upcoming.forEach(appointment => {
                    const appointmentCard = createAppointmentCard(appointment);
                    container.appendChild(appointmentCard);
                });
            }
            
            // Load past appointments
            function loadPastAppointments() {
                const container = document.getElementById('past-appointments');
                container.innerHTML = '';
                
                if (userAppointments.past.length === 0) {
                    container.innerHTML = '<p class="no-appointments">No past appointments found.</p>';
                    return;
                }
                
                userAppointments.past.forEach(appointment => {
                    const appointmentCard = createAppointmentCard(appointment, true);
                    container.appendChild(appointmentCard);
                });
            }
            
            // Create appointment card HTML
            function createAppointmentCard(appointment, isPast = false) {
                const card = document.createElement('div');
                card.className = `appointment-card ${isPast ? 'past' : 'upcoming'}`;
                
                const stars = [];
                const fullStars = Math.floor(appointment.doctor.rating);
                const hasHalfStar = appointment.doctor.rating % 1 >= 0.5;
                
                for (let i = 0; i < fullStars; i++) {
                    stars.push('<i class="fas fa-star"></i>');
                }
                if (hasHalfStar) {
                    stars.push('<i class="fas fa-star-half-alt"></i>');
                }
                
                card.innerHTML = `
                    <div class="card-main">
                        <div class="doctor-info">
                            <img src="${appointment.doctor.image}" alt="${appointment.doctor.name}">
                            <div>
                                <h3>${appointment.doctor.name}</h3>
                                <p>${appointment.doctor.specialty}</p>
                                <div class="rating">
                                    ${stars.join('')}
                                    <span>${appointment.doctor.rating} (${appointment.doctor.reviews} reviews)</span>
                                </div>
                            </div>
                        </div>
                        <div class="appointment-details">
                            <div class="detail">
                                <i class="fas fa-calendar-alt"></i>
                                <span>${formatDate(appointment.date)}</span>
                            </div>
                            <div class="detail">
                                <i class="fas fa-clock"></i>
                                <span>${appointment.time}</span>
                            </div>
                            <div class="detail">
                                <i class="fas ${appointment.type === 'video' ? 'fa-video' : 'fa-clinic-medical'}"></i>
                                <span>${appointment.type === 'video' ? 'Video Consultation' : 'In-Clinic Visit'}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-actions">
                        ${isPast ? 
                            `<button class="btn btn-outline" onclick="viewAppointmentDetails(${appointment.id})">
                                <i class="fas fa-file-medical"></i> View Details
                            </button>` : 
                            `<button class="btn btn-primary" onclick="${appointment.type === 'video' ? 'joinConsultation(' + appointment.id + ')' : 'viewClinicDirections(' + appointment.id + ')'}">
                                <i class="fas ${appointment.type === 'video' ? 'fa-video' : 'fa-map-marker-alt'}"></i> ${appointment.type === 'video' ? 'Join Consultation' : 'Get Directions'}
                            </button>
                            <button class="btn btn-outline" onclick="rescheduleAppointment(${appointment.id})">
                                <i class="fas fa-calendar-alt"></i> Reschedule
                            </button>
                            <button class="btn btn-outline" onclick="cancelAppointment(${appointment.id})">
                                <i class="fas fa-times"></i> Cancel
                            </button>`
                        }
                    </div>
                `;
                
                return card;
            }
            
            // Load doctors list
            function loadDoctors(filter = '', specialty = '', availability = '') {
                const container = document.getElementById('doctors-list');
                container.innerHTML = '';
                
                let filteredDoctors = [...doctorsList];
                
                // Apply filters
                if (filter) {
                    const searchTerm = filter.toLowerCase();
                    filteredDoctors = filteredDoctors.filter(doctor => 
                        doctor.name.toLowerCase().includes(searchTerm) || 
                        doctor.specialty.toLowerCase().includes(searchTerm)
                    );
                }
                
                if (specialty) {
                    filteredDoctors = filteredDoctors.filter(doctor => 
                        doctor.specialty === specialty
                    );
                }
                
                if (availability === 'today') {
                    filteredDoctors = filteredDoctors.filter(doctor => 
                        doctor.availableToday
                    );
                } else if (availability === 'tomorrow' || availability === 'this-week') {
                    filteredDoctors = filteredDoctors.filter(doctor => 
                        doctor.available
                    );
                }
                
                if (filteredDoctors.length === 0) {
                    container.innerHTML = '<p class="no-doctors">No doctors found matching your criteria.</p>';
                    return;
                }
                
                filteredDoctors.forEach(doctor => {
                    const doctorCard = createDoctorCard(doctor);
                    container.appendChild(doctorCard);
                });
            }
            
            // Create doctor card HTML
            function createDoctorCard(doctor) {
                const card = document.createElement('div');
                card.className = 'doctor-card';
                
                const stars = [];
                for (let i = 0; i < 5; i++) {
                    if (i < Math.floor(doctor.rating)) {
                        stars.push('<i class="fas fa-star"></i>');
                    } else if (i === Math.floor(doctor.rating) && doctor.rating % 1 >= 0.5) {
                        stars.push('<i class="fas fa-star-half-alt"></i>');
                    } else {
                        stars.push('<i class="far fa-star"></i>');
                    }
                }
                
                card.innerHTML = `
                    <img src="${doctor.image}" alt="${doctor.name}">
                    <div class="doctor-details">
                        <h3>${doctor.name}</h3>
                        <p class="specialty">${doctor.specialty}</p>
                        <div class="rating">
                            ${stars.join('')}
                            <span>${doctor.rating} (${doctor.reviews} reviews)</span>
                        </div>
                        <p class="availability">
                            <i class="fas fa-circle ${doctor.available ? 'available' : 'unavailable'}"></i> 
                            ${doctor.available ? (doctor.availableToday ? 'Available today' : 'Available this week') : 'Not available'}
                        </p>
                        <button class="btn btn-primary btn-book" onclick="openBookingModal(${doctor.id})" ${!doctor.available ? 'disabled' : ''}>
                            Book Appointment
                        </button>
                    </div>
                `;
                
                return card;
            }
            
            // Open booking modal
            window.openBookingModal = function(doctorId) {
                const doctor = doctorsList.find(d => d.id === doctorId);
                if (!doctor) return;
                
                document.getElementById('doctor-id').value = doctorId;
                
                // Set minimum date to today
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('appointment-date').min = today;
                
                // Clear and populate time slots
                const timeSelect = document.getElementById('appointment-time');
                timeSelect.innerHTML = '<option value="">Select Time Slot</option>';
                
                // Sample time slots - in a real app, this would come from the doctor's availability
                const timeSlots = [
                    '09:00 AM - 09:30 AM',
                    '10:00 AM - 10:30 AM',
                    '11:00 AM - 11:30 AM',
                    '02:00 PM - 02:30 PM',
                    '03:00 PM - 03:30 PM',
                    '04:00 PM - 04:30 PM'
                ];
                
                timeSlots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    timeSelect.appendChild(option);
                });
                
                bookingModal.style.display = 'flex';
            }
            
            // Format date for display
            function formatDate(dateString) {
                const options = { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' };
                return new Date(dateString).toLocaleDateString('en-US', options);
            }
            
    // Fix specialty filter population
    const specialtyFilter = document.getElementById('specialty-filter');
    specialtyFilter.innerHTML = '<option value="">All Specialties</option>';
    const uniqueSpecialties = [...new Set(doctorsList.map(doctor => doctor.specialty))];
    uniqueSpecialties.forEach(specialty => {
        const option = document.createElement('option');
        option.value = specialty;
        option.textContent = specialty;
        specialtyFilter.appendChild(option);
    });

    // Add event listener for specialty and availability filters
    document.getElementById('specialty-filter').addEventListener('change', () => {
        const searchTerm = document.getElementById('doctor-search').value;
        const specialty = document.getElementById('specialty-filter').value;
        const availability = document.getElementById('availability-filter').value;
        loadDoctors(searchTerm, specialty, availability);
    });

    document.getElementById('availability-filter').addEventListener('change', () => {
        const searchTerm = document.getElementById('doctor-search').value;
        const specialty = document.getElementById('specialty-filter').value;
        const availability = document.getElementById('availability-filter').value;
        loadDoctors(searchTerm, specialty, availability);
    });

    // Initialize the page with doctors list
    loadDoctors();
            
            // Booking form submission
            document.getElementById('booking-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const doctorId = parseInt(document.getElementById('doctor-id').value);
                const date = document.getElementById('appointment-date').value;
                const time = document.getElementById('appointment-time').value;
                const type = document.getElementById('consultation-type').value;
                const symptoms = document.getElementById('symptoms').value;
                
                const doctor = doctorsList.find(d => d.id === doctorId);
                if (!doctor) return;
                
                // In a real app, you would send this data to your backend
                console.log('Booking appointment:', { doctorId, date, time, type, symptoms });
                
                // Create a new appointment (simulated)
                const newAppointment = {
                    id: Date.now(),
                    doctor: {
                        name: doctor.name,
                        specialty: doctor.specialty,
                        image: doctor.image,
                        rating: doctor.rating,
                        reviews: doctor.reviews
                    },
                    date: date,
                    time: time,
                    type: type,
                    status: "confirmed",
                    symptoms: symptoms
                };
                
                userAppointments.upcoming.push(newAppointment);
                
                // Show confirmation
                document.getElementById('confirmation-message').innerHTML = `
                    Your appointment with <strong>${doctor.name}</strong> has been booked for<br>
                    <strong>${formatDate(date)} at ${time}</strong>.<br><br>
                    ${type === 'video' ? 
                        'A video consultation link will be sent to your email.' : 
                        'Please arrive 15 minutes before your scheduled time.'}
                `;
                
                bookingModal.style.display = 'none';
                confirmationModal.style.display = 'flex';
                
                // Reset form
                this.reset();
            });
            
            // Initialize the page
            loadUpcomingAppointments();
        });

    // Initialize functions for appointment actions
    window.viewAppointmentDetails = function(appointmentId) {
        const appointment = [...userAppointments.upcoming, ...userAppointments.past]
            .find(a => a.id === appointmentId);
        if (!appointment) return;
        alert(`Viewing details for appointment ${appointmentId}`);
    };

    window.joinConsultation = function(appointmentId) {
        const appointment = userAppointments.upcoming.find(a => a.id === appointmentId);
        if (!appointment) return;
        alert(`Joining video consultation with ${appointment.doctor.name}`);
    };

    window.viewClinicDirections = function(appointmentId) {
        const appointment = userAppointments.upcoming.find(a => a.id === appointmentId);
        if (!appointment) return;
        alert(`Getting directions to clinic for appointment with ${appointment.doctor.name}`);
    };

    window.rescheduleAppointment = function(appointmentId) {
        const appointment = userAppointments.upcoming.find(a => a.id === appointmentId);
        if (!appointment) return;
        alert(`Rescheduling appointment with ${appointment.doctor.name}`);
    };

    window.cancelAppointment = function(appointmentId) {
        const appointment = userAppointments.upcoming.find(a => a.id === appointmentId);
        if (!appointment) return;
        
        if (confirm(`Are you sure you want to cancel your appointment with ${appointment.doctor.name}?`)) {
            // Remove from upcoming appointments
            const index = userAppointments.upcoming.findIndex(a => a.id === appointmentId);
            if (index > -1) {
                userAppointments.upcoming.splice(index, 1);
                loadUpcomingAppointments();
            }
        }
    };
        document.addEventListener('DOMContentLoaded', function() {
            // ======================
            // Helper Functions
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
        
            function formatDate(dateString) {
                const options = { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' };
                return new Date(dateString).toLocaleDateString('en-US', options);
            }
        
            function getTimeSlots() {
                return [
                    '09:00 AM - 09:30 AM',
                    '10:00 AM - 10:30 AM',
                    '11:00 AM - 11:30 AM',
                    '02:00 PM - 02:30 PM',
                    '03:00 PM - 03:30 PM',
                    '04:00 PM - 04:30 PM'
                ];
            }
        
            // ======================
            // Initialize Elements
            // ======================
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            const bookingModal = document.getElementById('booking-modal');
            const confirmationModal = document.getElementById('confirmation-modal');
            const closeModalButtons = document.querySelectorAll('.close-modal, #close-confirmation');
            const searchBtn = document.getElementById('search-btn');
            const doctorSearch = document.getElementById('doctor-search');
            const specialtyFilter = document.getElementById('specialty-filter');
            const availabilityFilter = document.getElementById('availability-filter');
            const bookingForm = document.getElementById('booking-form');
            const appointmentDate = document.getElementById('appointment-date');
            const appointmentTime = document.getElementById('appointment-time');
            const consultationType = document.getElementById('consultation-type');
            const symptomsTextarea = document.getElementById('symptoms');
        
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            appointmentDate.min = today;
        
            // Load user data from session/local storage
            const userName = localStorage.getItem('userName') || 'Yashwenth';
            document.getElementById('user-name').textContent = userName;
        
            // Sample data - in a real app, this would come from an API
            const userAppointments = {
                upcoming: [
                    {
                        id: 1,
                        doctor: {
                            name: "Dr. Thanuja",
                            specialty: "Cardiologist",
                            image: "https://t3.ftcdn.net/jpg/01/14/89/28/360_F_114892812_Va0KWhvmSUOoYNEoHCAOJ8uYXzBiD8vY.jpg",
                            rating: 4.7,
                            reviews: 128
                        },
                        date: new Date(Date.now() + 86400000 * 2).toISOString().split('T')[0], // 2 days from now
                        time: "10:00 AM - 10:30 AM",
                        type: "video",
                        status: "confirmed",
                        symptoms: "Chest pain and shortness of breath"
                    },
                    {
                        id: 2,
                        doctor: {
                            name: "Dr.Ramu",
                            specialty: "General Physician",
                            image: "https://content3.jdmagicbox.com/v2/comp/vijayawada/q8/0866px866.x866.170617194532.y2q8/catalogue/ram-hospitals-tadepalli-vijayawada-general-physician-doctors-osuhx69gsl.jpg",
                            rating: 5.0,
                            reviews: 89
                        },
                        date: new Date(Date.now() + 86400000 * 5).toISOString().split('T')[0], // 5 days from now
                        time: "2:00 PM - 2:30 PM",
                        type: "clinic",
                        status: "confirmed",
                        symptoms: "Annual checkup"
                    }
                ],
                past: [
                    {
                        id: 3,
                        doctor: {
                            name: "Dr.Krishna",
                            specialty: "Dermatologist",
                            image: "https://static.vecteezy.com/system/resources/thumbnails/028/287/384/small/a-mature-indian-male-doctor-on-a-white-background-ai-generated-photo.jpg",
                            rating: 4.5,
                            reviews: 76
                        },
                        date: "2023-05-15",
                        time: "11:00 AM - 11:30 AM",
                        type: "video",
                        status: "completed",
                        symptoms: "Skin rash and itching",
                        notes: "Diagnosed with contact dermatitis. Prescribed topical cream."
                    }
                ]
            };
        
            const doctorsList = [
                {
                    id: 101,
                    name: "Dr.Sanketh",
                    specialty: "Cardiologist",
                    image: "https://www.shutterstock.com/image-photo/portrait-shot-happy-smiling-dental-600nw-2165296755.jpg",
                    rating: 4.7,
                    reviews: 128,
                    available: true,
                    availableToday: true,
                    languages: ["English", "Hindi", "Tamil"],
                    experience: "12 years",
                    education: "MD, Cardiology"
                },
                {
                    id: 102,
                    name: "Dr.Prabhu",
                    specialty: "General Physician",
                    image: "https://png.pngtree.com/background/20250206/original/pngtree-indian-male-doctor-poses-with-arms-crossed-in-portrait-photo-picture-image_14952526.jpg",
                    rating: 5.0,
                    reviews: 89,
                    available: true,
                    availableToday: true,
                    languages: ["English", "Telugu"],
                    experience: "8 years",
                    education: "MBBS, MD"
                },
                {
                    id: 103,
                    name: "Dr.Sankar",
                    specialty: "Dermatologist",
                    image: "https://www.shutterstock.com/image-photo/medicine-healthcare-people-concept-happy-260nw-1019723806.jpg",
                    rating: 4.5,
                    reviews: 76,
                    available: false,
                    availableToday: false,
                    languages: ["English", "Malayalam"],
                    experience: "15 years",
                    education: "MD, Dermatology"
                },
                {
                    id: 104,
                    name: "Dr.Dinesh",
                    specialty: "Neurologist",
                    image: "https://images.jdmagicbox.com/v2/comp/indore/t5/0731px731.x731.240805153644.j2t5/catalogue/doctor-service-for-home-mr-10-rd-indore-general-physician-doctors-wknnf0r6oy-250.jpg",
                    rating: 4.8,
                    reviews: 112,
                    available: true,
                    availableToday: false,
                    languages: ["English", "Hindi"],
                    experience: "10 years",
                    education: "DM, Neurology"
                }
            ];
        
            // ======================
            // Tab Switching
            // ======================
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active');
                    const tabId = tab.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                    
                    // Load data when switching tabs
                    if (tabId === 'upcoming') {
                        loadUpcomingAppointments();
                    } else if (tabId === 'past') {
                        loadPastAppointments();
                    } else if (tabId === 'book') {
                        loadDoctors();
                    }
                });
            });
        
            // ======================
            // Modal Handling
            // ======================
            closeModalButtons.forEach(button => {
                button.addEventListener('click', () => {
                    bookingModal.style.display = 'none';
                    confirmationModal.style.display = 'none';
                });
            });
            
            window.addEventListener('click', (e) => {
                if (e.target === bookingModal) {
                    bookingModal.style.display = 'none';
                }
                if (e.target === confirmationModal) {
                    confirmationModal.style.display = 'none';
                }
            });
        
            // ======================
            // Appointment Functions
            // ======================
            function loadUpcomingAppointments() {
                const container = document.getElementById('upcoming-appointments');
                container.innerHTML = '';
                
                if (userAppointments.upcoming.length === 0) {
                    container.innerHTML = '<p class="no-appointments">No upcoming appointments scheduled.</p>';
                    return;
                }
                
                // Sort by date (earliest first)
                const sortedAppointments = [...userAppointments.upcoming].sort((a, b) => 
                    new Date(a.date) - new Date(b.date));
                
                sortedAppointments.forEach(appointment => {
                    const appointmentCard = createAppointmentCard(appointment);
                    container.appendChild(appointmentCard);
                });
            }
        
            function loadPastAppointments() {
                const container = document.getElementById('past-appointments');
                container.innerHTML = '';
                
                if (userAppointments.past.length === 0) {
                    container.innerHTML = '<p class="no-appointments">No past appointments found.</p>';
                    return;
                }
                
                // Sort by date (most recent first)
                const sortedAppointments = [...userAppointments.past].sort((a, b) => 
                    new Date(b.date) - new Date(a.date));
                
                sortedAppointments.forEach(appointment => {
                    const appointmentCard = createAppointmentCard(appointment, true);
                    container.appendChild(appointmentCard);
                });
            }
        
            function createAppointmentCard(appointment, isPast = false) {
                const card = document.createElement('div');
                card.className = `appointment-card ${isPast ? 'past' : 'upcoming'}`;
                
                const stars = [];
                const fullStars = Math.floor(appointment.doctor.rating);
                const hasHalfStar = appointment.doctor.rating % 1 >= 0.5;
                
                for (let i = 0; i < fullStars; i++) {
                    stars.push('<i class="fas fa-star"></i>');
                }
                if (hasHalfStar) {
                    stars.push('<i class="fas fa-star-half-alt"></i>');
                }
                
                card.innerHTML = `
                    <div class="card-main">
                        <div class="doctor-info">
                            <img src="${appointment.doctor.image}" alt="${appointment.doctor.name}">
                            <div>
                                <h3>${appointment.doctor.name}</h3>
                                <p>${appointment.doctor.specialty}</p>
                                <div class="rating">
                                    ${stars.join('')}
                                    <span>${appointment.doctor.rating} (${appointment.doctor.reviews} reviews)</span>
                                </div>
                            </div>
                        </div>
                        <div class="appointment-details">
                            <div class="detail">
                                <i class="fas fa-calendar-alt"></i>
                                <span>${formatDate(appointment.date)}</span>
                            </div>
                            <div class="detail">
                                <i class="fas fa-clock"></i>
                                <span>${appointment.time}</span>
                            </div>
                            <div class="detail">
                                <i class="fas ${appointment.type === 'video' ? 'fa-video' : 'fa-clinic-medical'}"></i>
                                <span>${appointment.type === 'video' ? 'Video Consultation' : 'In-Clinic Visit'}</span>
                            </div>
                            ${isPast && appointment.notes ? `
                            <div class="detail">
                                <i class="fas fa-file-medical"></i>
                                <span>${appointment.notes}</span>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    <div class="card-actions">
                        ${isPast ? 
                            `<button class="btn btn-outline view-details-btn" data-id="${appointment.id}">
                                <i class="fas fa-file-medical"></i> View Details
                            </button>` : 
                            `<button class="btn btn-primary action-btn" data-action="${appointment.type === 'video' ? 'join' : 'directions'}" data-id="${appointment.id}">
                                <i class="fas ${appointment.type === 'video' ? 'fa-video' : 'fa-map-marker-alt'}"></i> ${appointment.type === 'video' ? 'Join Consultation' : 'Get Directions'}
                            </button>
                            <button class="btn btn-outline action-btn" data-action="reschedule" data-id="${appointment.id}">
                                <i class="fas fa-calendar-alt"></i> Reschedule
                            </button>
                            <button class="btn btn-outline action-btn" data-action="cancel" data-id="${appointment.id}">
                                <i class="fas fa-times"></i> Cancel
                            </button>`
                        }
                    </div>
                `;
                
                return card;
            }
        
            // ======================
            // Doctor Functions
            // ======================
            function loadDoctors(filter = '', specialty = '', availability = '') {
                const container = document.getElementById('doctors-list');
                container.innerHTML = '';
                
                let filteredDoctors = [...doctorsList];
                
                // Apply filters
                if (filter) {
                    const searchTerm = filter.toLowerCase();
                    filteredDoctors = filteredDoctors.filter(doctor => 
                        doctor.name.toLowerCase().includes(searchTerm) || 
                        doctor.specialty.toLowerCase().includes(searchTerm)
                    );
                }
                
                if (specialty) {
                    filteredDoctors = filteredDoctors.filter(doctor => 
                        doctor.specialty === specialty
                    );
                }
                
                if (availability === 'today') {
                    filteredDoctors = filteredDoctors.filter(doctor => 
                        doctor.availableToday
                    );
                } else if (availability === 'tomorrow' || availability === 'this-week') {
                    filteredDoctors = filteredDoctors.filter(doctor => 
                        doctor.available
                    );
                }
                
                if (filteredDoctors.length === 0) {
                    container.innerHTML = '<p class="no-doctors">No doctors found matching your criteria.</p>';
                    return;
                }
                
                filteredDoctors.forEach(doctor => {
                    const doctorCard = createDoctorCard(doctor);
                    container.appendChild(doctorCard);
                });
            }
        
            function createDoctorCard(doctor) {
                const card = document.createElement('div');
                card.className = 'doctor-card';
                
                const stars = [];
                for (let i = 0; i < 5; i++) {
                    if (i < Math.floor(doctor.rating)) {
                        stars.push('<i class="fas fa-star"></i>');
                    } else if (i === Math.floor(doctor.rating) && doctor.rating % 1 >= 0.5) {
                        stars.push('<i class="fas fa-star-half-alt"></i>');
                    } else {
                        stars.push('<i class="far fa-star"></i>');
                    }
                }
                
                card.innerHTML = `
                    <img src="${doctor.image}" alt="${doctor.name}">
                    <div class="doctor-details">
                        <h3>${doctor.name}</h3>
                        <p class="specialty">${doctor.specialty}</p>
                        <div class="rating">
                            ${stars.join('')}
                            <span>${doctor.rating} (${doctor.reviews} reviews)</span>
                        </div>
                        <div class="doctor-meta">
                            <p><i class="fas fa-graduation-cap"></i> ${doctor.education}</p>
                            <p><i class="fas fa-briefcase"></i> ${doctor.experience} experience</p>
                            <p><i class="fas fa-language"></i> ${doctor.languages.join(', ')}</p>
                        </div>
                        <p class="availability">
                            <i class="fas fa-circle ${doctor.available ? 'available' : 'unavailable'}"></i> 
                            ${doctor.available ? (doctor.availableToday ? 'Available today' : 'Available this week') : 'Not available'}
                        </p>
                        <button class="btn btn-primary btn-book" data-id="${doctor.id}" ${!doctor.available ? 'disabled' : ''}>
                            Book Appointment
                        </button>
                    </div>
                `;
                
                return card;
            }
        
            // ======================
            // Event Listeners
            // ======================
            // Search and filter doctors
            searchBtn.addEventListener('click', () => {
                const searchTerm = doctorSearch.value;
                const specialty = specialtyFilter.value;
                const availability = availabilityFilter.value;
                loadDoctors(searchTerm, specialty, availability);
            });
        
            // Search when pressing Enter
            doctorSearch.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    searchBtn.click();
                }
            });
        
            // Filter change triggers new search
            specialtyFilter.addEventListener('change', () => searchBtn.click());
            availabilityFilter.addEventListener('change', () => searchBtn.click());
        
            // Dynamic event delegation for appointment actions
            document.addEventListener('click', function(e) {
                // Doctor booking buttons
                if (e.target.classList.contains('btn-book') || e.target.closest('.btn-book')) {
                    const btn = e.target.classList.contains('btn-book') ? e.target : e.target.closest('.btn-book');
                    const doctorId = parseInt(btn.getAttribute('data-id'));
                    openBookingModal(doctorId);
                }
                
                // Appointment action buttons
                if (e.target.classList.contains('action-btn') || e.target.closest('.action-btn')) {
                    const btn = e.target.classList.contains('action-btn') ? e.target : e.target.closest('.action-btn');
                    const action = btn.getAttribute('data-action');
                    const appointmentId = parseInt(btn.getAttribute('data-id'));
                    handleAppointmentAction(action, appointmentId);
                }
                
                // View details buttons (past appointments)
                if (e.target.classList.contains('view-details-btn') || e.target.closest('.view-details-btn')) {
                    const btn = e.target.classList.contains('view-details-btn') ? e.target : e.target.closest('.view-details-btn');
                    const appointmentId = parseInt(btn.getAttribute('data-id'));
                    viewAppointmentDetails(appointmentId);
                }
            });
        
            // Booking form submission
            bookingForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const doctorId = document.getElementById('doctor-id').value;
                const appointmentDate = document.getElementById('appointment-date').value;
                const appointmentTime = document.getElementById('appointment-time').value;
                const consultationType = document.getElementById('consultation-type').value;
                const symptoms = document.getElementById('symptoms').value;

                // Validate required fields
                if (!doctorId || !appointmentDate || !appointmentTime) {
                    alert('Please fill in all required fields');
                    return;
                }

                // Create the data object
                const appointmentData = {
                    doctor_id: doctorId,
                    appointment_date: appointmentDate,
                    appointment_time: appointmentTime,
                    consultation_type: consultationType,
                    symptoms: symptoms
                };

                // Send the data to the server
                fetch('save_appointment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(appointmentData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hide booking modal
                        document.getElementById('booking-modal').style.display = 'none';
                        
                        // Show confirmation modal
                        const confirmationModal = document.getElementById('confirmation-modal');
                        const confirmationMessage = document.getElementById('confirmation-message');
                        confirmationMessage.textContent = data.message || 'Your appointment has been booked successfully!';
                        confirmationModal.style.display = 'block';
                        
                        // Reset form
                        document.getElementById('booking-form').reset();
                        
                        // Refresh appointments list if it exists
                        if (typeof loadUpcomingAppointments === 'function') {
                            loadUpcomingAppointments();
                        }
                    } else {
                        alert(data.message || 'Failed to book appointment. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while booking the appointment. Please try again.');
                });
            });
        
            // Date change updates available time slots
            appointmentDate.addEventListener('change', function() {
                // In a real app, this would fetch available slots from the server
                const timeSelect = document.getElementById('appointment-time');
                timeSelect.innerHTML = '<option value="">Select Time Slot</option>';
                
                const timeSlots = getTimeSlots();
                timeSlots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    timeSelect.appendChild(option);
                });
            });
        
            // ======================
            // Core Functions
            // ======================
            function openBookingModal(doctorId) {
                const doctor = doctorsList.find(d => d.id === doctorId);
                if (!doctor) return;
                
                document.getElementById('doctor-id').value = doctorId;
                
                // Set minimum date to today
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('appointment-date').min = today;
                document.getElementById('appointment-date').value = today;
                
                // Clear and populate time slots
                const timeSelect = document.getElementById('appointment-time');
                timeSelect.innerHTML = '<option value="">Select Time Slot</option>';
                
                const timeSlots = getTimeSlots();
                timeSlots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    timeSelect.appendChild(option);
                });
                
                bookingModal.style.display = 'flex';
            }
        
            function handleAppointmentAction(action, appointmentId) {
                const appointment = [...userAppointments.upcoming, ...userAppointments.past].find(a => a.id === appointmentId);
                if (!appointment) return;
                
                switch(action) {
                    case 'join':
                        joinConsultation(appointment);
                        break;
                    case 'directions':
                        viewClinicDirections(appointment);
                        break;
                    case 'reschedule':
                        rescheduleAppointment(appointment);
                        break;
                    case 'cancel':
                        cancelAppointment(appointment);
                        break;
                }
            }
        
            function joinConsultation(appointment) {
                showNotification(`Joining video consultation with ${appointment.doctor.name}`, 'info');
                // In a real app, this would launch the video call interface
            }
        
            function viewClinicDirections(appointment) {
                showNotification(`Showing directions to ${appointment.doctor.name}'s clinic`, 'info');
                // In a real app, this would open a map with directions
            }
        
            function rescheduleAppointment(appointment) {
                openBookingModal(appointment.doctor.id);
                // Pre-fill the form with existing appointment details
                document.getElementById('appointment-date').value = appointment.date;
                document.getElementById('consultation-type').value = appointment.type;
                document.getElementById('symptoms').value = appointment.symptoms || '';
                
                showNotification('Please select new date and time for your appointment', 'info');
            }
        
            function cancelAppointment(appointment) {
                if (confirm(`Are you sure you want to cancel your appointment with ${appointment.doctor.name} on ${formatDate(appointment.date)}?`)) {
                    // Remove from upcoming appointments
                    const index = userAppointments.upcoming.findIndex(a => a.id === appointment.id);
                    if (index !== -1) {
                        userAppointments.upcoming.splice(index, 1);
                    }
                    
                    // Add to past appointments as canceled
                    userAppointments.past.push({
                        ...appointment,
                        status: "canceled",
                        notes: "Appointment canceled by patient"
                    });
                    
                    loadUpcomingAppointments();
                    showNotification('Appointment canceled successfully', 'success');
                }
            }
        
            function viewAppointmentDetails(appointmentId) {
                const appointment = userAppointments.past.find(a => a.id === appointmentId);
                if (!appointment) return;
                
                // Create a detailed view modal
                const modal = document.createElement('div');
                modal.className = 'modal';
                modal.style.display = 'flex';
                modal.innerHTML = `
                    <div class="modal-content detailed-view">
                        <span class="close-modal">&times;</span>
                        <h3>Appointment Details</h3>
                        <div class="doctor-info">
                            <img src="${appointment.doctor.image}" alt="${appointment.doctor.name}">
                            <div>
                                <h4>${appointment.doctor.name}</h4>
                                <p>${appointment.doctor.specialty}</p>
                            </div>
                        </div>
                        <div class="appointment-details">
                            <div class="detail">
                                <strong>Date:</strong> ${formatDate(appointment.date)}
                            </div>
                            <div class="detail">
                                <strong>Time:</strong> ${appointment.time}
                            </div>
                            <div class="detail">
                                <strong>Type:</strong> ${appointment.type === 'video' ? 'Video Consultation' : 'In-Clinic Visit'}
                            </div>
                            <div class="detail">
                                <strong>Status:</strong> ${appointment.status}
                            </div>
                            ${appointment.symptoms ? `
                            <div class="detail">
                                <strong>Symptoms/Reason:</strong> ${appointment.symptoms}
                            </div>
                            ` : ''}
                            ${appointment.notes ? `
                            <div class="detail">
                                <strong>Doctor's Notes:</strong> ${appointment.notes}
                            </div>
                            ` : ''}
                        </div>
                        <button class="btn btn-primary close-detailed-view">Close</button>
                    </div>
                `;
                
                document.body.appendChild(modal);
                
                // Close button
                modal.querySelector('.close-modal, .close-detailed-view').addEventListener('click', () => {
                    modal.remove();
                });
                
                // Close when clicking outside
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.remove();
                    }
                });
            }
        
            // ======================
            // Initialize Page
            // ======================
            loadUpcomingAppointments();
            
            // Add CSS for notifications and modals
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
                
                .modal {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0,0,0,0.5);
                    z-index: 1000;
                    align-items: center;
                    justify-content: center;
                }
                
                .modal-content {
                    background: white;
                    border-radius: 8px;
                    width: 90%;
                    max-width: 500px;
                    padding: 20px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
                }
                
                .modal-content.detailed-view {
                    max-width: 600px;
                }
                
                .close-modal {
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    font-size: 24px;
                    cursor: pointer;
                    color: #999;
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
                .form-group select,
                .form-group textarea {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }
                
                .doctor-info {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    margin-bottom: 20px;
                }
                
                .doctor-info img {
                    width: 60px;
                    height: 60px;
                    border-radius: 50%;
                    object-fit: cover;
                }
                
                .appointment-details .detail {
                    margin-bottom: 10px;
                    padding-bottom: 10px;
                    border-bottom: 1px solid #eee;
                }
                
                .doctor-meta {
                    margin: 10px 0;
                    font-size: 14px;
                    color: #666;
                }
                
                .doctor-meta i {
                    width: 20px;
                    color: var(--primary-color);
                }
                
                .availability .available {
                    color: var(--success-color);
                }
                
                .availability .unavailable {
                    color: var(--danger-color);
                }
            `;
            document.head.appendChild(style);
        });