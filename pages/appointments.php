<?php
session_start();
require_once '../config/db_connect.php';
include '../includes/header.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header('Location: ../pages/login.php');
    exit();
}

// Fetch available doctors for the dropdown
try {
    $stmt = $pdo->query("SELECT * FROM doctors");
    $doctors = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error fetching doctors: " . $e->getMessage();
}

// Initialize notification variables
$success_message = $error_message = "";

// Handle appointment scheduling and redirection to the payment gateway
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_and_schedule'])) {
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $amount = 2500.00; // Fixed amount for the appointment

    // Mock success scenario for demonstration
    $success_message = "Appointment scheduled successfully! Redirecting to payment...";
   
     header("Location: ../pages/payment_gateway.php?type=appointment&id=$doctor_id&amount=$amount&date=$appointment_date&time=$appointment_time");
     exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule an Appointment</title>
    <link rel="stylesheet" href="../css/appointments.css">
</head>
<body>
    <div class="container">
        <div class="appointment-section modern-card">
            <h2 class="section-title">Schedule an Appointment</h2>

            <form method="POST" action="" class="appointment-form">
                <div class="form-group">
                    <label for="doctor_id">Select Doctor:</label>
                    <select name="doctor_id" id="doctor_id" class="form-control" required>
                        <option value="" disabled selected>Select a doctor</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?php echo htmlspecialchars($doctor['id']); ?>">
                                 <?php echo htmlspecialchars($doctor['name']); ?> - <?php echo htmlspecialchars($doctor['specialty']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="appointment_date">Date:</label>
                    <input type="date" name="appointment_date" id="appointment_date" class="form-control" 
                           required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label for="appointment_time">Time:</label>
                    <input type="time" name="appointment_time" id="appointment_time" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="amount">Cost:</label>
                    <input type="text" class="form-control" value="$2500.00" disabled>
                </div>

                <button type="submit" name="pay_and_schedule" class="btn btn-primary">Pay & Schedule</button>
            </form>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notification-container"></div>

    <script>
        function showNotification(type, message) {
            const container = document.getElementById('notification-container');
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerText = message;
            container.appendChild(notification);

            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Show notifications from PHP messages
        <?php if (!empty($success_message)): ?>
        showNotification('success', '<?php echo addslashes($success_message); ?>');
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
        showNotification('error', '<?php echo addslashes($error_message); ?>');
        <?php endif; ?>
    </script>
</body>
</html>

<?php include '../includes/footer.php'; ?>
