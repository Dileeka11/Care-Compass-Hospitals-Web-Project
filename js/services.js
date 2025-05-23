document.addEventListener("DOMContentLoaded", function () {
    const notification = document.querySelector(".notification-popup");
    if (notification) {
        setTimeout(() => {
            notification.style.display = "none";
        }, 4000); // Hides the notification after 4 seconds
    }
});
