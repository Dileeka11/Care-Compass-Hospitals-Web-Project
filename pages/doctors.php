<?php
session_start();
require_once '../config/db_connect.php';
include '../includes/header.php';

$specialty = isset($_GET['specialty']) ? $_GET['specialty'] : 'all';
$branch = isset($_GET['branch']) ? $_GET['branch'] : 'all';

try {
    if ($specialty !== 'all' && $branch !== 'all') {
        $stmt = $pdo->prepare("SELECT * FROM doctors WHERE specialty = ? AND branch = ?");
        $stmt->execute([$specialty, $branch]);
    } elseif ($specialty !== 'all') {
        $stmt = $pdo->prepare("SELECT * FROM doctors WHERE specialty = ?");
        $stmt->execute([$specialty]);
    } elseif ($branch !== 'all') {
        $stmt = $pdo->prepare("SELECT * FROM doctors WHERE branch = ?");
        $stmt->execute([$branch]);
    } else {
        $stmt = $pdo->query("SELECT * FROM doctors");
    }
    $doctors = $stmt->fetchAll();

    $specialtyStmt = $pdo->query("SELECT DISTINCT specialty FROM doctors ORDER BY specialty");
    $specialties = $specialtyStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $error_message = "Error loading doctors: " . $e->getMessage();
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/doctors.css">

<div class="container py-5">
    <h2 class="text-center mb-4">Find Your Medical Specialist</h2>

    <div class="row mb-4 justify-content-center">
        <div class="col-md-4">
            <select id="specialtySelect" class="form-select" onchange="filterDoctors()">
                <option value="all">All Specialties</option>
                <?php foreach ($specialties as $spec): ?>
                    <option value="<?php echo htmlspecialchars($spec); ?>"
                            <?php echo $specialty === $spec ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($spec); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <select id="branchSelect" class="form-select" onchange="filterDoctors()">
                <option value="all">All Branches</option>
                <option value="Kandy" <?php echo $branch === 'Kandy' ? 'selected' : ''; ?>>Kandy</option>
                <option value="Colombo" <?php echo $branch === 'Colombo' ? 'selected' : ''; ?>>Colombo</option>
                <option value="Kurunegala" <?php echo $branch === 'Kurunegala' ? 'selected' : ''; ?>>Kurunegala</option>
            </select>
        </div>
    </div>

    <div class="row">
        <?php foreach ($doctors as $doctor): ?>
            <div class="col-md-4 mb-4">
                <div class="doctor-card">
                    <img src="<?php echo htmlspecialchars($doctor['image_url']); ?>" alt="Doctor Image" class="doctor-image">
                    <div class="doctor-info">
                        <h3><?php echo htmlspecialchars($doctor['name']); ?></h3>
                        <p class="specialty"><?php echo htmlspecialchars($doctor['specialty']); ?></p>
                        <p class="qualifications"><?php echo htmlspecialchars($doctor['qualifications']); ?></p>
                        <p class="branch">Branch: <?php echo htmlspecialchars($doctor['branch']); ?></p>
                    </div>
                    <a href="../pages/appointments.php?doctor_id=<?php echo $doctor['id']; ?>" class="btn-primary">Book Appointment</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function filterDoctors() {
    const specialty = document.getElementById('specialtySelect').value;
    const branch = document.getElementById('branchSelect').value;
    window.location.href = `doctors.php?specialty=${specialty}&branch=${branch}`;
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification-popup';
    notification.innerHTML = `
        <p>${message}</p>
        <button onclick="closeNotification(this)">Close</button>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
}

function closeNotification(button) {
    button.parentElement.remove();
}
</script>

<?php include '../includes/footer.php'; ?>
