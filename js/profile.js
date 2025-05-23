document.addEventListener('DOMContentLoaded', () => {
    const cancelButtons = document.querySelectorAll('.cancel-appointment');

    cancelButtons.forEach(button => {
        button.addEventListener('click', () => {
            const appointmentId = button.getAttribute('data-appointment-id');
            if (confirm('Are you sure you want to cancel this appointment?')) {
                // Add your AJAX or fetch request here to cancel the appointment.
                console.log(`Cancelled appointment ID: ${appointmentId}`);
                alert('Appointment cancelled successfully!');
                button.textContent = 'Cancelled';
                button.disabled = true;
                button.classList.remove('btn-danger');
                button.classList.add('btn-disabled');
            }
        });
    });
});
