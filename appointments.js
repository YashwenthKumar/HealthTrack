document.addEventListener('DOMContentLoaded', function() {
  // Update current date
  updateCurrentDate();
  
  // Initialize all functionality
  initSidebar();
  initModals();
  initSearch();
  initFilters();
  initSorting();
  initPagination();
  initActionButtons();
  initPatientForm();
  
  // Load sample patient data (in a real app, this would be an API call)
  loadPatientData();
});

// ==================== CORE FUNCTIONS ====================

function updateCurrentDate() {
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', options);
}

function loadPatientData() {
  // In a real application, this would be an API call
  console.log("Loading patient data...");
  // Simulate loading
  setTimeout(() => {
      showToast('Patient data loaded successfully');
  }, 1000);
}

// ==================== SIDEBAR FUNCTIONALITY ====================

function initSidebar() {
  const sidebar = document.querySelector('.sidebar');
  const sidebarToggle = document.createElement('div');
  sidebarToggle.className = 'sidebar-toggle';
  sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
  
  // Insert toggle button
  document.querySelector('.top-header').prepend(sidebarToggle);
  
  // Toggle sidebar on mobile
  sidebarToggle.addEventListener('click', function() {
      sidebar.classList.toggle('collapsed');
  });
  
  // Handle window resize
  window.addEventListener('resize', function() {
      if (window.innerWidth > 768) {
          sidebar.classList.remove('collapsed');
      }
  });
}

// ==================== MODAL FUNCTIONALITY ====================

function initModals() {
  // Add Patient Modal
  const addPatientBtn = document.querySelector('.btn-primary');
  if (addPatientBtn) {
      addPatientBtn.addEventListener('click', showAddPatientModal);
  }
  
  // Close modals when clicking X or outside
  document.querySelectorAll('.close-modal').forEach(btn => {
      btn.addEventListener('click', hideAllModals);
  });
  
  document.querySelectorAll('.modal-overlay').forEach(modal => {
      modal.addEventListener('click', function(e) {
          if (e.target === modal) hideAllModals();
      });
  });
  
  // Export modal
  const exportBtn = document.querySelector('.export-options .btn-outline');
  if (exportBtn) exportBtn.addEventListener('click', () => showModal('export-modal'));
}

function showAddPatientModal() {
  const modal = document.createElement('div');
  modal.className = 'modal-overlay';
  modal.id = 'add-patient-modal';
  modal.innerHTML = `
      <div class="modal">
          <div class="modal-header">
              <h3>Add New Patient</h3>
              <button class="close-modal">&times;</button>
          </div>
          <div class="modal-content">
              <form id="patient-form">
                  <div class="form-group">
                      <label>Full Name</label>
                      <input type="text" class="form-control" name="fullName" required>
                  </div>
                  <div class="form-group">
                      <label>Email</label>
                      <input type="email" class="form-control" name="email" required>
                  </div>
                  <div class="form-group">
                      <label>Phone</label>
                      <input type="tel" class="form-control" name="phone" required>
                  </div>
                  <div class="form-group">
                      <label>Date of Birth</label>
                      <input type="date" class="form-control" name="dob" required>
                  </div>
                  <div class="form-group">
                      <label>Gender</label>
                      <select class="form-control" name="gender" required>
                          <option value="">Select</option>
                          <option value="Male">Male</option>
                          <option value="Female">Female</option>
                          <option value="Other">Other</option>
                      </select>
                  </div>
                  <div class="modal-actions">
                      <button type="button" class="btn btn-outline close-modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">Save Patient</button>
                  </div>
              </form>
          </div>
      </div>
  `;
  document.body.appendChild(modal);
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
  
  // Initialize form validation
  initPatientForm();
}

function showModal(modalId) {
  hideAllModals();
  const modal = document.getElementById(modalId);
  if (modal) {
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
  }
}

function hideAllModals() {
  document.querySelectorAll('.modal-overlay').forEach(modal => {
      modal.remove();
  });
  document.body.style.overflow = 'auto';
}

// ==================== PATIENT FORM FUNCTIONALITY ====================

function initPatientForm() {
  const form = document.getElementById('patient-form');
  if (form) {
      form.addEventListener('submit', function(e) {
          e.preventDefault();
          const formData = new FormData(this);
          const patientData = Object.fromEntries(formData.entries());
          
          // In a real app, you would send this to your backend
          console.log('Submitting patient data:', patientData);
          showToast('Patient added successfully!');
          hideAllModals();
          
          // Simulate adding to the table
          addPatientToTable({
              id: 'PT-' + Math.floor(100000 + Math.random() * 900000),
              name: patientData.fullName,
              email: patientData.email,
              phone: patientData.phone,
              lastVisit: new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }),
              status: 'Active',
              avatar: `https://randomuser.me/api/portraits/${patientData.gender === 'Female' ? 'women' : 'men'}/${Math.floor(Math.random() * 100)}.jpg`
          });
      });
  }
}

function addPatientToTable(patient) {
  const tbody = document.querySelector('.patients-table tbody');
  const newRow = document.createElement('tr');
  
  newRow.innerHTML = `
      <td>
          <div class="patient-info">
              <img src="${patient.avatar}" alt="Patient" class="patient-avatar">
              <div>
                  <div class="patient-name">${patient.name}</div>
                  <div class="patient-id">ID: ${patient.id}</div>
              </div>
          </div>
      </td>
      <td>${patient.email}<br>${patient.phone}</td>
      <td>${patient.lastVisit}</td>
      <td><span class="status-badge status-active">${patient.status}</span></td>
      <td>
          <div class="action-btns">
              <button class="action-btn" title="View Profile" data-patient-id="${patient.id}">
                  <i class="fas fa-eye"></i>
              </button>
              <button class="action-btn" title="Message" data-patient-id="${patient.id}">
                  <i class="fas fa-comment"></i>
              </button>
              <button class="action-btn" title="Edit" data-patient-id="${patient.id}">
                  <i class="fas fa-edit"></i>
              </button>
          </div>
      </td>
  `;
  
  tbody.prepend(newRow);
  initActionButtons(); // Reinitialize buttons for the new row
  updatePatientCount();
}

function updatePatientCount() {
  const count = document.querySelectorAll('.patients-table tbody tr').length;
  document.querySelector('.patients-header h3').textContent = `All Patients (${count})`;
}

// ==================== ACTION BUTTONS FUNCTIONALITY ====================

function initActionButtons() {
  // View Profile
  document.querySelectorAll('.action-btn[title="View Profile"]').forEach(btn => {
      btn.addEventListener('click', function() {
          const patientId = this.getAttribute('data-patient-id');
          viewPatientProfile(patientId);
      });
  });
  
  // Message
  document.querySelectorAll('.action-btn[title="Message"]').forEach(btn => {
      btn.addEventListener('click', function() {
          const patientId = this.getAttribute('data-patient-id');
          startChatWithPatient(patientId);
      });
  });
  
  // Edit
  document.querySelectorAll('.action-btn[title="Edit"]').forEach(btn => {
      btn.addEventListener('click', function() {
          const patientId = this.getAttribute('data-patient-id');
          editPatientRecord(patientId);
      });
  });
}

function viewPatientProfile(patientId) {
  // In a real app, this would fetch patient details from an API
  const patientRow = document.querySelector(`.action-btn[data-patient-id="${patientId}"]`).closest('tr');
  const patientData = {
      name: patientRow.querySelector('.patient-name').textContent,
      id: patientRow.querySelector('.patient-id').textContent.replace('ID: ', ''),
      email: patientRow.cells[1].textContent.split('\n')[0],
      phone: patientRow.cells[1].textContent.split('\n')[1],
      lastVisit: patientRow.cells[2].textContent,
      status: patientRow.querySelector('.status-badge').textContent,
      avatar: patientRow.querySelector('.patient-avatar').src
  };
  
  const modal = document.createElement('div');
  modal.className = 'modal-overlay';
  modal.innerHTML = `
      <div class="modal" style="max-width: 700px;">
          <div class="modal-header">
              <h3>Patient Profile: ${patientData.name}</h3>
              <button class="close-modal">&times;</button>
          </div>
          <div class="modal-content">
              <div style="display: flex; gap: 30px; margin-bottom: 20px;">
                  <div>
                      <img src="${patientData.avatar}" alt="${patientData.name}" 
                           style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;">
                  </div>
                  <div style="flex: 1;">
                      <h3 style="margin-bottom: 10px;">${patientData.name}</h3>
                      <p><strong>Patient ID:</strong> ${patientData.id}</p>
                      <p><strong>Status:</strong> <span class="status-badge ${patientData.status === 'Active' ? 'status-active' : 'status-inactive'}">${patientData.status}</span></p>
                      <p><strong>Last Visit:</strong> ${patientData.lastVisit}</p>
                  </div>
              </div>
              
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                  <div class="form-group">
                      <label>Email</label>
                      <input type="email" class="form-control" value="${patientData.email}" readonly>
                  </div>
                  <div class="form-group">
                      <label>Phone</label>
                      <input type="tel" class="form-control" value="${patientData.phone}" readonly>
                  </div>
              </div>
              
              <div class="modal-actions">
                  <button type="button" class="btn btn-outline close-modal">Close</button>
                  <button type="button" class="btn btn-primary" id="start-consultation-btn">
                      <i class="fas fa-video"></i> Start Consultation
                  </button>
              </div>
          </div>
      </div>
  `;
  
  document.body.appendChild(modal);
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
  
  // Add event listener for consultation button
  modal.querySelector('#start-consultation-btn')?.addEventListener('click', function() {
      showToast(`Starting consultation with ${patientData.name}`);
      hideAllModals();
  });
  
  // Close modal handler
  modal.querySelector('.close-modal').addEventListener('click', hideAllModals);
}

function startChatWithPatient(patientId) {
  const patientName = document.querySelector(`.action-btn[data-patient-id="${patientId}"]`)
                      .closest('tr').querySelector('.patient-name').textContent;
  
  const modal = document.createElement('div');
  modal.className = 'modal-overlay';
  modal.innerHTML = `
      <div class="modal" style="max-width: 600px;">
          <div class="modal-header">
              <h3>Message ${patientName}</h3>
              <button class="close-modal">&times;</button>
          </div>
          <div class="modal-content">
              <div style="height: 300px; overflow-y: auto; margin-bottom: 15px; border: 1px solid #eee; padding: 15px; border-radius: 6px;">
                  <div style="text-align: center; color: #999; margin-top: 100px;">
                      <i class="fas fa-comments" style="font-size: 24px; margin-bottom: 10px;"></i>
                      <p>Your message history with ${patientName}</p>
                  </div>
              </div>
              <div style="display: flex; gap: 10px;">
                  <input type="text" class="form-control" placeholder="Type your message...">
                  <button class="btn btn-primary" style="min-width: 80px;">
                      <i class="fas fa-paper-plane"></i> Send
                  </button>
              </div>
          </div>
      </div>
  `;
  
  document.body.appendChild(modal);
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
  
  // Close modal handler
  modal.querySelector('.close-modal').addEventListener('click', hideAllModals);
}

function editPatientRecord(patientId) {
  const patientRow = document.querySelector(`.action-btn[data-patient-id="${patientId}"]`).closest('tr');
  const patientData = {
      name: patientRow.querySelector('.patient-name').textContent,
      id: patientRow.querySelector('.patient-id').textContent.replace('ID: ', ''),
      email: patientRow.cells[1].textContent.split('\n')[0],
      phone: patientRow.cells[1].textContent.split('\n')[1],
      lastVisit: patientRow.cells[2].textContent,
      status: patientRow.querySelector('.status-badge').textContent
  };
  
  const modal = document.createElement('div');
  modal.className = 'modal-overlay';
  modal.innerHTML = `
      <div class="modal" style="max-width: 600px;">
          <div class="modal-header">
              <h3>Edit Patient: ${patientData.name}</h3>
              <button class="close-modal">&times;</button>
          </div>
          <div class="modal-content">
              <form id="edit-patient-form">
                  <input type="hidden" name="patientId" value="${patientId}">
                  
                  <div class="form-group">
                      <label>Full Name</label>
                      <input type="text" class="form-control" name="fullName" value="${patientData.name}" required>
                  </div>
                  
                  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                      <div class="form-group">
                          <label>Email</label>
                          <input type="email" class="form-control" name="email" value="${patientData.email}" required>
                      </div>
                      <div class="form-group">
                          <label>Phone</label>
                          <input type="tel" class="form-control" name="phone" value="${patientData.phone}" required>
                      </div>
                  </div>
                  
                  <div class="form-group">
                      <label>Status</label>
                      <select class="form-control" name="status" required>
                          <option value="Active" ${patientData.status === 'Active' ? 'selected' : ''}>Active</option>
                          <option value="Inactive" ${patientData.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                      </select>
                  </div>
                  
                  <div class="modal-actions">
                      <button type="button" class="btn btn-outline close-modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                  </div>
              </form>
          </div>
      </div>
  `;
  
  document.body.appendChild(modal);
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
  
  // Form submission
  modal.querySelector('#edit-patient-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const updatedData = Object.fromEntries(formData.entries());
      
      // Update the table row
      patientRow.querySelector('.patient-name').textContent = updatedData.fullName;
      patientRow.cells[1].innerHTML = `${updatedData.email}<br>${updatedData.phone}`;
      
      const statusBadge = patientRow.querySelector('.status-badge');
      statusBadge.textContent = updatedData.status;
      statusBadge.className = `status-badge status-${updatedData.status.toLowerCase()}`;
      
      showToast('Patient record updated successfully');
      hideAllModals();
  });
  
  // Close modal handler
  modal.querySelector('.close-modal').addEventListener('click', hideAllModals);
}

// ==================== SEARCH & FILTER FUNCTIONALITY ====================

function initSearch() {
  const searchInput = document.querySelector('.search-box input');
  if (searchInput) {
      searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          filterPatients(searchTerm, document.querySelector('.filter-dropdown select').value);
      });
  }
}

function initFilters() {
  const statusFilter = document.querySelector('.filter-dropdown select');
  if (statusFilter) {
      statusFilter.addEventListener('change', function() {
          filterPatients(document.querySelector('.search-box input').value.toLowerCase(), this.value);
      });
  }
}

function filterPatients(searchTerm, statusFilter) {
  const rows = document.querySelectorAll('.patients-table tbody tr');
  let visibleCount = 0;
  
  rows.forEach(row => {
      const patientName = row.querySelector('.patient-name').textContent.toLowerCase();
      const patientId = row.querySelector('.patient-id').textContent.toLowerCase();
      const patientEmail = row.cells[1].textContent.toLowerCase();
      const patientStatus = row.querySelector('.status-badge').textContent;
      
      const matchesSearch = searchTerm === '' || 
                          patientName.includes(searchTerm) || 
                          patientId.includes(searchTerm) || 
                          patientEmail.includes(searchTerm);
      
      const matchesStatus = statusFilter === 'All Status' || 
                          (statusFilter === 'Active' && patientStatus === 'Active') || 
                          (statusFilter === 'Inactive' && patientStatus === 'Inactive');
      
      if (matchesSearch && matchesStatus) {
          row.style.display = '';
          visibleCount++;
      } else {
          row.style.display = 'none';
      }
  });
  
  document.querySelector('.patients-header h3').textContent = `All Patients (${visibleCount})`;
}

// ==================== SORTING FUNCTIONALITY ====================

function initSorting() {
  const sortFilter = document.querySelectorAll('.filter-dropdown select')[1];
  if (sortFilter) {
      sortFilter.addEventListener('change', function() {
          sortPatients(this.value);
      });
  }
}

function sortPatients(sortBy) {
  const tbody = document.querySelector('.patients-table tbody');
  const rows = Array.from(tbody.querySelectorAll('tr'));
  
  rows.sort((a, b) => {
      const nameA = a.querySelector('.patient-name').textContent.toLowerCase();
      const nameB = b.querySelector('.patient-name').textContent.toLowerCase();
      const dateA = new Date(a.cells[2].textContent);
      const dateB = new Date(b.cells[2].textContent);
      
      if (sortBy === 'Sort by: Name (A-Z)') {
          return nameA.localeCompare(nameB);
      } else if (sortBy === 'Sort by: Name (Z-A)') {
          return nameB.localeCompare(nameA);
      } else { // Sort by: Recent (default)
          return dateB - dateA;
      }
  });
  
  // Remove all rows
  while (tbody.firstChild) {
      tbody.removeChild(tbody.firstChild);
  }
  
  // Add sorted rows
  rows.forEach(row => tbody.appendChild(row));
}

// ==================== PAGINATION FUNCTIONALITY ====================

function initPagination() {
  const pageBtns = document.querySelectorAll('.page-btn:not(.active)');
  pageBtns.forEach(btn => {
      btn.addEventListener('click', function() {
          document.querySelector('.page-btn.active').classList.remove('active');
          this.classList.add('active');
          showToast(`Loading page ${this.textContent}`);
          // In a real app, you would load the corresponding data here
      });
  });
}

// ==================== TOAST NOTIFICATION ====================

function showToast(message, type = 'info') {
  const toast = document.createElement('div');
  toast.className = `toast-notification show ${type}`;
  toast.innerHTML = `
      <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
      <span>${message}</span>
  `;
  
  document.body.appendChild(toast);
  
  setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Add some CSS for the toast types
const toastStyles = document.createElement('style');
toastStyles.textContent = `
  .toast-notification.success {
      background: #28a745;
  }
  .toast-notification.error {
      background: #dc3545;
  }
  .toast-notification.info {
      background: #17a2b8;
  }
  .toast-notification i {
      margin-right: 8px;
  }
`;
document.head.appendChild(toastStyles);