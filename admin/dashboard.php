<?php
session_start();
require_once '../config/db_connect.php';
include '../includes/admin_header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: /login.php');
    exit();
}

// Handle button actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_appointment'])) {
        $appointment_id = $_POST['appointment_id'];
        try {
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'Approved' WHERE id = ?");
            $stmt->execute([$appointment_id]);
            $success = "Appointment approved successfully.";
        } catch (PDOException $e) {
            $error = "Error approving appointment: " . $e->getMessage();
        }
    } elseif (isset($_POST['reject_appointment'])) {
        $appointment_id = $_POST['appointment_id'];
        try {
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'Rejected' WHERE id = ?");
            $stmt->execute([$appointment_id]);
            $success = "Appointment rejected successfully.";
        } catch (PDOException $e) {
            $error = "Error rejecting appointment: " . $e->getMessage();
        }
    } elseif (isset($_POST['view_query'])) {
        $query_id = $_POST['query_id'];
        try {
            $stmt = $pdo->prepare("SELECT * FROM queries WHERE id = ?");
            $stmt->execute([$query_id]);
            $query_details = $stmt->fetch();
        } catch (PDOException $e) {
            $error = "Error loading query details: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_query'])) {
        $query_id = $_POST['query_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM queries WHERE id = ?");
            $stmt->execute([$query_id]);
            $success = "Query deleted successfully.";
            $query_details = null; // Clear the box if the query was deleted
        } catch (PDOException $e) {
            $error = "Error deleting query: " . $e->getMessage();
        }
    } elseif (isset($_POST['clear_box'])) {
        $query_details = null; // Clear the query details box
    }
}

// Fetch statistics
try {
    $stats = [
        'total_patients' => $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'patient'")->fetchColumn(),
        'total_doctors' => $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn(),
        'total_staff' => $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'staff'")->fetchColumn(),
        'pending_appointments' => $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Pending'")->fetchColumn(),
    ];

    // Get recent appointments
    $recent_appointments = $pdo->query("
        SELECT a.*, u.username as patient_name, d.name as doctor_name 
        FROM appointments a 
        JOIN users u ON a.patient_id = u.id 
        JOIN doctors d ON a.doctor_id = d.id 
        WHERE a.status = 'Pending' 
        ORDER BY a.appointment_date ASC 
        LIMIT 5
    ")->fetchAll();

    // Get recent queries
    $recent_queries = $pdo->query("
        SELECT q.*, u.username 
        FROM queries q 
        LEFT JOIN users u ON q.user_id = u.id 
        WHERE q.status = 'pending' 
        ORDER BY q.created_at DESC 
        LIMIT 5
    ")->fetchAll();
} catch (PDOException $e) {
    $error = "Error loading dashboard data: " . $e->getMessage();
}
?>

<link rel="stylesheet" href="../css/admindashboard.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<script src="../js/admindashboard.js" defer></script>

<div class="container">
    <h2>Admin Dashboard</h2>

    <?php if (isset($success)) echo "<div class='alert success'>$success</div>"; ?>
    <?php if (isset($error)) echo "<div class='alert error'>$error</div>"; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Patients</h3>
            <p><?php echo $stats['total_patients']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Doctors</h3>
            <p><?php echo $stats['total_doctors']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Staff</h3>
            <p><?php echo $stats['total_staff']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Pending Appointments</h3>
            <p><?php echo $stats['pending_appointments']; ?></p>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Recent Appointments Section -->
        <div>
            <h3>Recent Appointments</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_appointments as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <button type="submit" name="approve_appointment" class="btn-success">Approve</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <button type="submit" name="reject_appointment" class="btn-danger">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Recent Queries Section -->
        <div>
            <h3>Recent Queries</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Subject</th>
                        <th>From</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_queries as $query): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($query['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($query['subject']); ?></td>
                            <td><?php echo htmlspecialchars($query['username'] ?? 'Guest'); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="query_id" value="<?php echo $query['id']; ?>">
                                    <button type="submit" name="view_query" class="btn-primary">View</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- View Query Details -->
    <?php if (isset($query_details)): ?>
        <div class="query-details-box">
            <h3>Query Details</h3>
            <p><strong>Subject:</strong> <?php echo htmlspecialchars($query_details['subject']); ?></p>
            <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($query_details['message'])); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($query_details['status']); ?></p>
            <p><strong>Submitted On:</strong> <?php echo htmlspecialchars($query_details['created_at']); ?></p>
            <form method="POST" style="margin-top: 1rem;">
                <input type="hidden" name="query_id" value="<?php echo $query_details['id']; ?>">
                <button type="submit" name="clear_box" class="btn-secondary">Clear Box</button>
                <button type="submit" name="delete_query" class="btn-danger" onclick="return confirm('Are you sure you want to delete this query?');">Delete Query</button>
            </form>
        </div>
    <?php endif; ?>
</div>
