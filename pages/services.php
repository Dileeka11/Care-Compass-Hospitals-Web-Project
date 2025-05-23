<?php
session_start();
require_once '../config/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header('Location: ../pages/login.php');
    exit();
}

// Fetch services with optional search
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
    $stmt = $pdo->prepare("SELECT * FROM services WHERE name LIKE :search OR branch LIKE :search");
    $stmt->execute(['search' => '%' . $searchQuery . '%']);
} else {
    $stmt = $pdo->query("SELECT * FROM services");
}
$services = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Medical Services</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/services.css">
</head>
<body class="services-page">
    <div class="container mt-5">
        <h2 class="text-center mb-5 fw-bold text-primary">Our Medical Services</h2>
        <p>Care Compass Hospitals has been a driving force in transforming the healthcare landscape. 
            For 25 years, we have been dedicated to providing world-class medical care while 
            ensuring affordability and accessibility for all. We understand that service excellence is an 
            ongoing journey, and we continuously strive to enhance our offerings to deliver exceptional 
            healthcare experiences to our patients.</p>

        <p> At Care Compass Hospitals, we encompass both clinical and non-clinical care, 
            always prioritizing quality and continuous improvement. Our 400-bed multi-specialty 
            hospital is situated on 5 acres of beautifully landscaped grounds, 
            offering state-of-the-art facilities equipped with cutting-edge technology and staffed by a 
            highly experienced and well-trained team. We provide a comprehensive range of the latest diagnostic 
            and high-end medical technologies.</p>

</br>
            <div class="services-image">
                 <img src="../uploads/images/service1.jpg" alt="Services Image">
            </div>
            <br/>
            <br/>
            <br/>
        <!-- Search Form -->
        <form method="GET" action="../pages/services.php" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" 
                       class="form-control" placeholder="Search services by name or branch">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <!-- Popup Notification -->
        <?php if (isset($_SESSION['payment_success'])): ?>
            <div class="notification-popup">
                <p><?php echo $_SESSION['payment_success']; unset($_SESSION['payment_success']); ?></p>
            </div>
        <?php endif; ?>

        </br>
        <h3 class="text-center mb-5 fw-bold text-primary">Services</h3>
        <div class="row g-4">
            <?php if (count($services) > 0): ?>
                <?php foreach ($services as $service): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card fixed-card shadow border-0">
                            <img src="<?php echo htmlspecialchars($service['image_url']); ?>" 
                                 class="card-img-top img-fluid rounded-top" 
                                 alt="Service Image">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-primary fw-bold"><?php echo htmlspecialchars($service['name']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($service['description']); ?></p>
                                <p class="mb-1"><strong>Cost:</strong> Rs.<?php echo number_format($service['cost'], 2); ?></p>
                                <p><strong>Branch:</strong> <?php echo htmlspecialchars($service['branch']); ?></p>
                                <a href="../pages/payment_gateway.php?type=service&id=<?php echo $service['id']; ?>&amount=<?php echo $service['cost']; ?>" 
                                   class="btn btn-primary mt-auto btn-custom">Pay & Request</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center text-muted">No services found matching your search.</p>
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
    <?php include '../includes/footer.php'; ?>
</body>
</html>
