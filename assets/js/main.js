/**
 * Polyprep - Educational Website
 * Main JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all interactive elements
    initializeDropdowns();
    initializeFileUpload();
    setupFormValidation();
    initializeMobileNav();
    
    // Handle PDF preview thumbnails
    loadPdfThumbnails();
});

/**
 * Initialize mobile navigation toggle
 */
function initializeMobileNav() {
    const navbarToggle = document.getElementById('navbar-toggle');
    const navbarCollapse = document.getElementById('navbar-collapse');
    
    if (navbarToggle && navbarCollapse) {
        navbarToggle.addEventListener('click', function() {
            navbarCollapse.classList.toggle('show');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!navbarToggle.contains(event.target) && !navbarCollapse.contains(event.target)) {
                navbarCollapse.classList.remove('show');
            }
        });
        
        // Close menu when window is resized to desktop size
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                navbarCollapse.classList.remove('show');
            }
        });
    }
}

/**
 * Initialize dropdown menus for Course > Semester > Subject navigation
 */
function initializeDropdowns() {
    // Course dropdown change event
    const courseDropdown = document.getElementById('course-dropdown');
    if (courseDropdown) {
        courseDropdown.addEventListener('change', function() {
            const courseId = this.value;
            if (courseId) {
                fetchSemesters(courseId);
            } else {
                // Reset semester and subject dropdowns
                const semesterDropdown = document.getElementById('semester-dropdown');
                if (semesterDropdown) {
                    semesterDropdown.innerHTML = '<option value="">Select Semester</option>';
                    semesterDropdown.disabled = true;
                }
                
                const subjectDropdown = document.getElementById('subject-dropdown');
                if (subjectDropdown) {
                    subjectDropdown.innerHTML = '<option value="">Select Subject</option>';
                    subjectDropdown.disabled = true;
                }
            }
        });
    }

    // Semester dropdown change event
    const semesterDropdown = document.getElementById('semester-dropdown');
    if (semesterDropdown) {
        semesterDropdown.addEventListener('change', function() {
            const semesterId = this.value;
            if (semesterId) {
                fetchSubjects(semesterId);
            } else {
                // Reset subject dropdown
                const subjectDropdown = document.getElementById('subject-dropdown');
                if (subjectDropdown) {
                    subjectDropdown.innerHTML = '<option value="">Select Subject</option>';
                    subjectDropdown.disabled = true;
                }
            }
        });
    }
}

/**
 * Fetch semesters based on selected course
 * @param {number} courseId - The ID of the selected course
 */
function fetchSemesters(courseId) {
    fetch(`api/get_semesters.php?course_id=${courseId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const semesterDropdown = document.getElementById('semester-dropdown');
            if (!semesterDropdown) return;
            
            semesterDropdown.innerHTML = '<option value="">Select Semester</option>';
            semesterDropdown.disabled = false;
            
            if (data && data.length > 0) {
                data.forEach(semester => {
                    const option = document.createElement('option');
                    option.value = semester.id;
                    option.textContent = semester.name;
                    semesterDropdown.appendChild(option);
                });
            } else {
                showAlert('No semesters found for this course. Please add semesters first.', 'warning');
            }
        })
        .catch(error => {
            console.error('Error fetching semesters:', error);
            showAlert('Error loading semesters. Please try again.', 'danger');
        });
}

/**
 * Fetch subjects based on selected semester
 * @param {number} semesterId - The ID of the selected semester
 */
function fetchSubjects(semesterId) {
    fetch(`api/get_subjects.php?semester_id=${semesterId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const subjectDropdown = document.getElementById('subject-dropdown');
            if (!subjectDropdown) return;
            
            subjectDropdown.innerHTML = '<option value="">Select Subject</option>';
            subjectDropdown.disabled = false;
            
            if (data && data.length > 0) {
                data.forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject.id;
                    option.textContent = subject.name;
                    subjectDropdown.appendChild(option);
                });
            } else {
                showAlert('No subjects found for this semester. Please add subjects first.', 'warning');
            }
        })
        .catch(error => {
            console.error('Error fetching subjects:', error);
            showAlert('Error loading subjects. Please try again.', 'danger');
        });
}

/**
 * Initialize file upload functionality
 */
function initializeFileUpload() {
    const fileInput = document.getElementById('pdf-file');
    const fileLabel = document.querySelector('.file-input-label');
    
    if (fileInput && fileLabel) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                // Update label with file name
                const fileName = this.files[0].name;
                fileLabel.textContent = fileName;
                
                // Validate file type
                const fileExtension = fileName.split('.').pop().toLowerCase();
                if (fileExtension !== 'pdf') {
                    showAlert('Only PDF files are allowed.', 'danger');
                    this.value = '';
                    fileLabel.textContent = 'Choose PDF file...';
                    return;
                }
                
                // Validate file size (max 10MB)
                const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                if (this.files[0].size > maxSize) {
                    showAlert('File size exceeds 10MB limit.', 'danger');
                    this.value = '';
                    fileLabel.textContent = 'Choose PDF file...';
                }
            } else {
                fileLabel.textContent = 'Choose PDF file...';
            }
        });
    }
}

/**
 * Setup form validation for login and upload forms
 */
function setupFormValidation() {
    // Login form validation
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!username || !password) {
                event.preventDefault();
                showAlert('Please enter both username and password.', 'danger');
            }
        });
    }
    
    // Upload form validation
    const uploadForm = document.getElementById('upload-form');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(event) {
            const courseId = document.getElementById('course-dropdown').value;
            const semesterId = document.getElementById('semester-dropdown').value;
            const subjectId = document.getElementById('subject-dropdown').value;
            const fileInput = document.getElementById('pdf-file');
            
            if (!courseId || !semesterId || !subjectId) {
                event.preventDefault();
                showAlert('Please select course, semester, and subject.', 'danger');
                return;
            }
            
            if (!fileInput.files || !fileInput.files[0]) {
                event.preventDefault();
                showAlert('Please select a PDF file to upload.', 'danger');
                return;
            }
        });
    }
}

/**
 * Load PDF thumbnails for note cards
 */
function loadPdfThumbnails() {
    const thumbnailContainers = document.querySelectorAll('.note-thumbnail[data-pdf-url]');
    
    thumbnailContainers.forEach(container => {
        const pdfUrl = container.getAttribute('data-pdf-url');
        if (pdfUrl) {
            // Use PDF.js to generate thumbnail (this would require PDF.js library)
            // For now, we'll just use a placeholder
            const img = document.createElement('img');
            img.src = 'assets/images/pdf-icon.png';
            img.alt = 'PDF Thumbnail';
            container.appendChild(img);
            
            // In a real implementation with PDF.js:
            // pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
            //     pdf.getPage(1).then(page => {
            //         const canvas = document.createElement('canvas');
            //         const context = canvas.getContext('2d');
            //         const viewport = page.getViewport({ scale: 0.5 });
            //         
            //         canvas.width = viewport.width;
            //         canvas.height = viewport.height;
            //         
            //         page.render({
            //             canvasContext: context,
            //             viewport: viewport
            //         }).promise.then(() => {
            //             container.appendChild(canvas);
            //         });
            //     });
            // });
        }
    });
}

/**
 * Display an alert message to the user
 * @param {string} message - The message to display
 * @param {string} type - The type of alert (success, danger, warning, info)
 */
function showAlert(message, type = 'info') {
    const alertsContainer = document.getElementById('alerts-container');
    if (!alertsContainer) {
        console.error('Alerts container not found');
        return;
    }
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    // Add close button
    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'close';
    closeButton.innerHTML = '&times;';
    closeButton.addEventListener('click', function() {
        alert.remove();
    });
    
    alert.appendChild(closeButton);
    alertsContainer.appendChild(alert);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

/**
 * Handle approval/rejection of notes (admin only)
 */
function handleNoteApproval() {
    const approveButtons = document.querySelectorAll('.approve-note');
    const rejectButtons = document.querySelectorAll('.reject-note');
    
    approveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const noteId = this.getAttribute('data-note-id');
            if (confirm('Are you sure you want to approve this note?')) {
                approveNote(noteId);
            }
        });
    });
    
    rejectButtons.forEach(button => {
        button.addEventListener('click', function() {
            const noteId = this.getAttribute('data-note-id');
            if (confirm('Are you sure you want to reject this note?')) {
                rejectNote(noteId);
            }
        });
    });
}

/**
 * Approve a note
 * @param {number} noteId - The ID of the note to approve
 */
function approveNote(noteId) {
    fetch('api/approve_note.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `note_id=${noteId}&action=approve`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Note approved successfully!', 'success');
            // Refresh the pending notes list
            document.getElementById(`note-${noteId}`).remove();
        } else {
            showAlert('Error approving note: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred. Please try again.', 'danger');
    });
}

/**
 * Reject a note
 * @param {number} noteId - The ID of the note to reject
 */
function rejectNote(noteId) {
    fetch('api/approve_note.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `note_id=${noteId}&action=reject`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Note rejected successfully!', 'success');
            // Refresh the pending notes list
            document.getElementById(`note-${noteId}`).remove();
        } else {
            showAlert('Error rejecting note: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred. Please try again.', 'danger');
    });
}
