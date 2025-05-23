// manageservices.js
document.addEventListener('DOMContentLoaded', function() {
    // Notification System
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);

        // Trigger reflow for animation
        notification.offsetHeight;
        notification.classList.add('show');

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // Form Handling
    const serviceForm = document.getElementById('serviceForm');
    if (serviceForm) {
        serviceForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('serviceSubmit');
            const action = document.getElementById('formAction').value;
            submitBtn.disabled = true;
            submitBtn.textContent = action === 'add' ? 'Adding...' : 'Updating...';
        });
    }

    // Image Upload Preview
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

                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.style.width = '100px';
                    preview.style.marginTop = '10px';
                    
                    const container = imageInput.parentElement;
                    const oldPreview = container.querySelector('img');
                    if (oldPreview) {
                        container.removeChild(oldPreview);
                    }
                    container.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Status Badge Styling
    document.querySelectorAll('td').forEach(td => {
        if (['Pending', 'Approved', 'Rejected'].includes(td.textContent.trim())) {
            const status = td.textContent.trim().toLowerCase();
            td.innerHTML = `<span class="status status-${status}">${td.textContent}</span>`;
        }
    });

    // Handle existing notifications
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

    // Enhanced Delete Confirmation
    window.confirmDelete = function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
            e.target.closest('form').submit();
        }
    };

    // Form Reset Function
    window.resetForm = function() {
        serviceForm.reset();
        document.getElementById('formAction').value = 'add';
        document.getElementById('serviceId').value = '';
        document.getElementById('currentImage').value = '';
        document.getElementById('serviceSubmit').textContent = 'Add Service';
        
        const preview = imageInput.parentElement.querySelector('img');
        if (preview) {
            preview.remove();
        }
    };

    // Currency Formatting
    const costInput = document.getElementById('serviceCost');
    if (costInput) {
        costInput.addEventListener('blur', function(e) {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    }
});