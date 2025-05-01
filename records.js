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

    function createUploadModal() {
        const modal = document.createElement('div');
        modal.id = 'uploadModal';
        modal.className = 'modal';
        modal.style.display = 'none';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Upload Medical Records</h3>
                    <button class="close-btn">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="dropZone" class="drop-zone">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Drag & drop files here or</p>
                        <button class="btn btn-primary btn-browse">
                            <i class="fas fa-folder-open"></i> Browse Files
                        </button>
                        <input type="file" id="fileInput" multiple style="display: none;">
                    </div>
                    <div class="file-preview" id="filePreview"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline" id="cancelUpload">Cancel</button>
                    <button class="btn btn-primary" id="confirmUpload">Upload</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        return modal;
    }

    // ======================
    // Initialize Elements
    // ======================
    const uploadBtn = document.getElementById('uploadBtn');
    let uploadModal = document.getElementById('uploadModal') || createUploadModal();
    const searchInput = document.querySelector('.search-box input');
    const filterSelect = document.querySelector('.records-filter select');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const applyFilterBtn = document.querySelector('.records-filter .btn-outline');
    const recordsGrid = document.querySelector('.records-grid');

    // Set default dates for filter
    const today = new Date();
    const oneMonthAgo = new Date();
    oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
    
    startDateInput.valueAsDate = oneMonthAgo;
    endDateInput.valueAsDate = today;

    // ======================
    // Modal Functionality
    // ======================
    if (uploadBtn) {
        uploadBtn.addEventListener('click', function() {
            uploadModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    }

    const closeModal = function() {
        uploadModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    };

    uploadModal.querySelector('.close-btn').addEventListener('click', closeModal);
    uploadModal.querySelector('#cancelUpload').addEventListener('click', closeModal);

    uploadModal.addEventListener('click', function(e) {
        if (e.target === uploadModal) {
            closeModal();
        }
    });

    // ======================
    // File Upload Functionality
    // ======================
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const browseBtn = document.querySelector('.btn-browse');
    const filePreview = document.getElementById('filePreview');
    const confirmUploadBtn = document.getElementById('confirmUpload');

    let filesToUpload = [];

    function handleFiles(files) {
        filesToUpload = Array.from(files);
        updateFilePreview();
    }

    function updateFilePreview() {
        filePreview.innerHTML = '';
        
        if (filesToUpload.length === 0) {
            filePreview.innerHTML = '<p class="empty-message">No files selected</p>';
            return;
        }
        
        const list = document.createElement('ul');
        list.className = 'file-list';
        
        filesToUpload.forEach((file, index) => {
            const listItem = document.createElement('li');
            listItem.className = 'file-item';
            
            const fileType = file.name.split('.').pop().toLowerCase();
            const typeIcon = getFileTypeIcon(fileType);
            
            listItem.innerHTML = `
                <div class="file-info">
                    <i class="fas fa-${typeIcon}"></i>
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">${formatFileSize(file.size)}</span>
                </div>
                <button class="btn-remove" data-index="${index}">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            list.appendChild(listItem);
        });
        
        filePreview.appendChild(list);
        
        // Add event listeners to remove buttons
        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                filesToUpload.splice(index, 1);
                updateFilePreview();
            });
        });
    }

    function getFileTypeIcon(fileType) {
        const icons = {
            pdf: 'file-pdf',
            jpg: 'file-image',
            jpeg: 'file-image',
            png: 'file-image',
            gif: 'file-image',
            doc: 'file-word',
            docx: 'file-word',
            xls: 'file-excel',
            xlsx: 'file-excel',
            txt: 'file-alt',
            csv: 'file-csv'
        };
        return icons[fileType] || 'file';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Drag and drop functionality
    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        dropZone.addEventListener('drop', function(e) {
            handleDrop(e);
            highlight(); // Keep highlighted after drop
        });
    }

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight() {
        dropZone.classList.add('highlight');
    }

    function unhighlight() {
        dropZone.classList.remove('highlight');
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    if (browseBtn && fileInput) {
        browseBtn.addEventListener('click', function() {
            fileInput.click();
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
    }

    if (confirmUploadBtn) {
        confirmUploadBtn.addEventListener('click', function() {
            if (filesToUpload.length === 0) {
                showNotification('Please select files to upload', 'error');
                return;
            }
    
            // Create FormData object
            const formData = new FormData();
            filesToUpload.forEach(file => {
                formData.append('files[]', file);
            });
            formData.append('action', 'create');
            
            // Show upload progress
            showNotification(`Uploading ${filesToUpload.length} file(s)...`, 'info');
            
            // Send files to server
            fetch('medical_records_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    showNotification('Files uploaded successfully!', 'success');
                    
                    // Add the new records to the grid
                    addRecordsFromUploads(filesToUpload);
                    
                    // Reset upload state
                    filesToUpload = [];
                    filePreview.innerHTML = '<p class="empty-message">No files selected</p>';
                    fileInput.value = '';
                    
                    // Refresh the page to show new records
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showNotification(data.message || 'Upload failed', 'error');
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                showNotification('An error occurred during upload', 'error');
            });
        });
    }

    // ======================
    // Search and Filter Functionality
    // ======================
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterRecords();
        });
    }

    if (applyFilterBtn) {
        applyFilterBtn.addEventListener('click', function() {
            filterRecords();
        });
    }

    function filterRecords() {
        const searchTerm = searchInput.value.toLowerCase();
        const filterType = filterSelect.value;
        const startDate = startDateInput.valueAsDate;
        const endDate = endDateInput.valueAsDate;
        
        document.querySelectorAll('.record-card').forEach(card => {
            const title = card.querySelector('.record-title').textContent.toLowerCase();
            const meta = card.querySelector('.record-meta').textContent.toLowerCase();
            const tags = Array.from(card.querySelectorAll('.tag')).map(tag => tag.textContent.toLowerCase());
            const dateText = card.querySelector('.record-date').textContent.replace('Uploaded: ', '');
            const uploadDate = new Date(dateText);
            
            // Check search term
            const matchesSearch = searchTerm === '' || 
                title.includes(searchTerm) || 
                meta.includes(searchTerm) || 
                tags.some(tag => tag.includes(searchTerm));
            
            // Check filter type
            const matchesType = filterType === 'All Types' || 
                tags.some(tag => tag.includes(filterType.toLowerCase()));
            
            // Check date range
            const matchesDate = (!startDate || uploadDate >= startDate) && 
                (!endDate || uploadDate <= endDate);
            
            if (matchesSearch && matchesType && matchesDate) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // ======================
    // Record Management
    // ======================
    function addRecordsFromUploads(files) {
        files.forEach(file => {
            const fileType = file.name.split('.').pop().toLowerCase();
            const recordType = getRecordType(fileType);
            const icon = getRecordIcon(recordType);
            
            const recordCard = document.createElement('div');
            recordCard.className = 'record-card';
            recordCard.innerHTML = `
                <div class="record-icon">
                    <i class="fas fa-${icon}"></i>
                </div>
                <h3 class="record-title">${file.name}</h3>
                <p class="record-meta">Uploaded • ${new Date().toLocaleDateString()}</p>
                <div class="record-tags">
                    <span class="tag">${recordType}</span>
                </div>
                <div class="record-actions">
                    <button class="btn btn-outline btn-sm">View Details</button>
                    <span class="record-date">Uploaded: ${new Date().toLocaleDateString()}</span>
                </div>
            `;
            
            recordsGrid.insertBefore(recordCard, recordsGrid.firstChild);
            
            // Add click handler for view button
            recordCard.querySelector('.btn-outline').addEventListener('click', function() {
                showNotification(`Viewing ${file.name}`, 'info');
            });
        });
    }

    function getRecordType(fileType) {
        const types = {
            pdf: 'Document',
            jpg: 'Image',
            jpeg: 'Image',
            png: 'Image',
            gif: 'Image',
            doc: 'Document',
            docx: 'Document',
            xls: 'Spreadsheet',
            xlsx: 'Spreadsheet',
            txt: 'Text',
            csv: 'Data'
        };
        return types[fileType] || 'File';
    }

    function getRecordIcon(recordType) {
        const icons = {
            'Document': 'file-alt',
            'Image': 'file-image',
            'Spreadsheet': 'file-excel',
            'Text': 'file-alt',
            'Data': 'file-csv',
            'Lab Result': 'flask',
            'Prescription': 'file-prescription'
        };
        return icons[recordType] || 'file';
    }

    // Initialize view buttons for existing records
    document.querySelectorAll('.record-card .btn-outline').forEach(button => {
        button.addEventListener('click', function() {
            const recordTitle = this.closest('.record-card').querySelector('.record-title').textContent;
            showNotification(`Viewing ${recordTitle}`, 'info');
        });
    });

    // Add CSS for modal and notifications
    const style = document.createElement('style');
    style.textContent = `
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
            max-width: 600px;
            max-height: 90vh;
            overflow: auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            margin: 0;
            color: #333;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .drop-zone {
            border: 2px dashed #ddd;
            border-radius: 6px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .drop-zone.highlight {
            border-color: var(--primary-color);
            background: #f5faff;
        }
        
        .drop-zone i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 15px;
        }
        
        .file-preview {
            margin-top: 20px;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .file-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .file-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 0;
        }
        
        .file-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }
        
        .file-size {
            color: #999;
            font-size: 12px;
            white-space: nowrap;
        }
        
        .btn-remove {
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
        }
        
        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
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
    `;
    document.head.appendChild(style);
});