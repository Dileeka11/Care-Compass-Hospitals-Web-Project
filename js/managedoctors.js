// managedoctors.js
document.addEventListener('DOMContentLoaded', function() {
    // Notification System
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);

        // Trigger reflow to enable animation
        notification.offsetHeight;
        notification.classList.add('show');

        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // Handle form submission
    const doctorForm = document.getElementById('doctorForm');
    doctorForm.addEventListener('submit', function(e) {
        const action = document.getElementById('formAction').value;
        const submitBtn = document.getElementById('doctorSubmit');
        submitBtn.disabled = true;
        submitBtn.textContent = action === 'add' ? 'Adding...' : 'Updating...';
    });

    // Handle existing notifications from PHP
    const existingSuccess = document.querySelector('.alert.success');
    const existingError = document.querySelector('.alert.error');

    if (existingSuccess) {
        showNotification(existingSuccess.textContent, 'success');
        existingSuccess.remove();
    }

    if (existingError) {
        showNotification(existingError.textContent, 'error');
        existingError.remove();
    }

    // Image preview functionality
    const imageInput = document.querySelector('input[type="file"]');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB

                if (file.size > maxSize) {
                    showNotification('Image size should be less than 5MB', 'error');
                    this.value = '';
                    return;
                }

                if (!file.type.match('image.*')) {
                    showNotification('Please select an image file', 'error');
                    this.value = '';
                    return;
                }
            }
        });
    }

    // Enhanced delete confirmation
    const deleteButtons = document.querySelectorAll('button[onclick*="confirm"]');
    deleteButtons.forEach(button => {
        button.onclick = function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this doctor? This action cannot be undone.')) {
                this.closest('form').submit();
            }
        };
    });

    // Form reset functionality
    window.resetForm = function() {
        doctorForm.reset();
        document.getElementById('formAction').value = 'add';
        document.getElementById('doctorId').value = '';
        document.getElementById('currentImage').value = '';
        document.getElementById('doctorSubmit').textContent = 'Add Doctor';
    };

    // Enhanced edit functionality
    window.editDoctor = function(doctor) {
        document.getElementById('formAction').value = 'update';
        document.getElementById('doctorId').value = doctor.id;
        document.getElementById('currentImage').value = doctor.image_url || '';
        document.getElementById('doctorName').value = doctor.name;
        document.getElementById('doctorSpecialty').value = doctor.specialty;
        document.getElementById('doctorQualifications').value = doctor.qualifications;
        document.getElementById('doctorContact').value = doctor.contact_info;
        document.getElementById('doctorBranch').value = doctor.branch;
        document.getElementById('doctorSubmit').textContent = 'Update Doctor';

        // Scroll to form
        doctorForm.scrollIntoView({ behavior: 'smooth' });
    };
});