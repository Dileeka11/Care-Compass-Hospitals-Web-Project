<?php
session_start();
require_once '../config/db_connect.php';
include '../includes/staff_header.php'; 

// Check if the user is logged in and is staff
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
    header('Location: ../pages/login.php');
    exit();
}

// Fetch relevant statistics for staff
try {
    $stats = [
        'total_patients' => $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'patient'")->fetchColumn(),
        'total_appointments' => $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn(),
        'total_tests' => $pdo->query("SELECT COUNT(*) FROM laboratory_tests")->fetchColumn(),
    ];

    $recent_requests = $pdo->query("
        SELECT r.*, u.username AS patient_name, lt.test_name 
        FROM laboratory_requests r
        JOIN users u ON r.patient_id = u.id
        JOIN laboratory_tests lt ON r.test_id = lt.id
        ORDER BY r.created_at DESC
        LIMIT 5
    ")->fetchAll();
} catch (PDOException $e) {
    $error = "Error loading dashboard data: " . $e->getMessage();
}
?>

<link rel="stylesheet" href="../css/admindashboard.css">
<div class="container">
    <h2>Staff Panel</h2>

    <?php if (isset($error)) echo "<div class='alert error'>$error</div>"; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Patients</h3>
            <p><?php echo $stats['total_patients']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Appointments</h3>
            <p><?php echo $stats['total_appointments']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Tests Available</h3>
            <p><?php echo $stats['total_tests']; ?></p>
        </div>
    </div>

    <div class="dashboard-grid">
        <div>
            <h3>Recent Laboratory Requests</h3>
            <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Test</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['test_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
