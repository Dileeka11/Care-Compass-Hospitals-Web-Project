<?php
session_start();
require_once '../config/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header('Location: ../pages/login.php');
    exit();
}

// Fetch laboratory tests with optional search
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
    $stmt = $pdo->prepare("SELECT * FROM laboratory_tests WHERE test_name LIKE :search OR description LIKE :search");
    $stmt->execute(['search' => '%' . $searchQuery . '%']);
} else {
    $stmt = $pdo->query("SELECT * FROM laboratory_tests");
}
$tests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratory Tests</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/laboratory.css">
</head>
<body class="lab-tests-page">
    <div class="container mt-5">
        <h2 class="text-center mb-5 fw-bold text-primary">Laboratory Facilities</h2>

        <p>At Care Compass, we are dedicated to providing you with the highest standard of care 
            through our advanced laboratory facilities. Our state-of-the-art diagnostic tools and 
            experienced team of professionals work tirelessly to deliver fast, accurate, and reliable 
            test results, ensuring that you receive the best possible care. Whether it's routine tests, 
            specialized screenings, or complex diagnostics, our laboratory services are designed to 
            support your health journey with precision and compassion. Trust Care Compass for all your 
            testing needs, and let us guide you toward a healthier future with confidence and care.</p>

</br>

            <div class="services-image">
                 <img src="../uploads/images/lab1.jpg" alt="Services Image">
            </div>
            <br/>
            <br/>
            <br/>
        <!-- Search Form -->
        <form method="GET" action="lab_tests.php" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" 
                       class="form-control" placeholder="Search tests by name or description">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

</br>
<h3 class="text-center mb-5 fw-bold text-primary">Laboratory Facilities</h3>
        <!-- Popup Notification -->
        <?php if (isset($_SESSION['payment_success'])): ?>
            <div class="notification-popup">
                <p><?php echo $_SESSION['payment_success']; unset($_SESSION['payment_success']); ?></p>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php if (count($tests) > 0): ?>
                <?php foreach ($tests as $test): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card fixed-card shadow border-0">
                            <img src="<?php echo htmlspecialchars($test['image_url']); ?>" 
                                 class="card-img-top fixed-image rounded-top" 
                                 alt="Test Image">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-primary fw-bold"><?php echo htmlspecialchars($test['test_name']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($test['description']); ?></p>
                                <p class="mb-1"><strong>Cost:</strong> Rs.<?php echo number_format($test['cost'], 2); ?></p>
                                <a href="../pages/payment_gateway.php?type=test&id=<?php echo $test['id']; ?>&amount=<?php echo $test['cost']; ?>" 
                                   class="btn btn-primary mt-auto btn-custom">Pay & Request</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center text-muted">No tests found matching your search.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide notification popup
        document.addEventListener('DOMContentLoaded', function () {
            const popup = document.querySelector('.notification-popup');
            if (popup) {
                setTimeout(() => {
                    popup.style.display = 'none';
                }, 3000);
            }
        });
    </script>
</body>
</html>
