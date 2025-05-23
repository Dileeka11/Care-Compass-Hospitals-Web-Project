<?php
session_start();
require_once '../config/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT a.*, d.name as doctor_name 
                          FROM appointments a 
                          JOIN doctors d ON a.doctor_id = d.id 
                          WHERE a.patient_id = ? 
                          ORDER BY a.appointment_date DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $appointments = $stmt->fetchAll();
} catch(PDOException $e) {
    $error_message = "Error loading profile data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="../css/profile.css">
</head>
<body>
    <div class="container">
        <div class="profile-section modern-card">
            <h2 class="section-title">My Profile</h2>
            <div class="profile-info">
                <h3>Personal Information</h3>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>

        <div class="appointments-section modern-card">
            <h2 class="section-title">My Appointments</h2>
            <?php if ($appointments): ?>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Doctor</th>
                            <th>Status</th>
                            <th>Action</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo $appointment['appointment_date']; ?></td>
                                <td><?php echo $appointment['appointment_time']; ?></td>
                                <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($appointment['status']); ?>">
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($appointment['status'] === 'pending'): ?>
                                        <button class="btn-danger cancel-appointment" 
                                                data-appointment-id="<?php echo $appointment['id']; ?>">
                                            Cancel
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-disabled" disabled>Completed</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No appointments scheduled.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/profile.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
