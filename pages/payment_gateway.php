<?php
session_start();
require_once '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

// Validate session variables
$patient_id = $_SESSION['user_id'];
$patient_name = $_SESSION['user_name'] ?? 'Unknown Patient';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_type = $_POST['service_type'];
    $service_id = $_POST['service_id'];
    $amount = $_POST['amount'];
    $card_number = $_POST['card_number'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];

    if ($service_type === 'appointment') {
        // Insert appointment details into the database
        $appointment_date = $_GET['date'];
        $appointment_time = $_GET['time'];
    
        $stmt = $pdo->prepare("INSERT INTO appointments (doctor_id, patient_id, appointment_date, appointment_time, status) 
                               VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$service_id, $patient_id, $appointment_date, $appointment_time]);
    
        $_SESSION['payment_success'] = "Payment successful and appointment scheduled!";
        header("Location: ../pages/appointments.php");
        exit();
    } elseif ($service_type === 'service') {
        // Insert service request details into the database
        $stmt = $pdo->prepare("INSERT INTO service_requests (service_id, patient_id, status) 
                               VALUES (?, ?, 'Pending')");
        $stmt->execute([$service_id, $patient_id]);
    
        $_SESSION['payment_success'] = "Payment successful for the service!";
        header("Location: ../pages/services.php");
        exit();
    } elseif ($service_type === 'test') {
        // Insert laboratory request details into the database
        $stmt = $pdo->prepare("INSERT INTO laboratory_requests (test_id, patient_id, status) 
                               VALUES (?, ?, 'Pending')");
        $stmt->execute([$service_id, $patient_id]);
    
        $_SESSION['payment_success'] = "Payment successful for the laboratory test!";
        header("Location: ../pages/laboratory.php");
        exit();
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    .back-button {
      display: inline-block;
      padding: 10px;
      background-color:rgb(141, 130, 123);
      border-radius: 5px;
      text-decoration: none;
      color: #333;
      margin-right: 200px;
    }

    .back-button i {
      margin-right: 5px;
    }
  </style>
    
</head>
<body>

  <a href="javascript:history.back()" class="back-button">
  <i class="fa-regular fa-circle-left"></i> Back </a>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center bg-primary text-white">
                    <h3>Payment Gateway</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    <form method="POST">
                        <input type="hidden" name="service_type" value="<?php echo htmlspecialchars($_GET['type'] ?? ''); ?>">
                        <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
                        <input type="hidden" name="amount" value="<?php echo htmlspecialchars($_GET['amount'] ?? ''); ?>">
                        
                        <div class="mb-3">
                            <label for="card_number" class="form-label">Card Number</label>
                            <input type="text" name="card_number" id="card_number" class="form-control" maxlength="16" required>
                        </div>
                        <div class="mb-3">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="text" name="expiry_date" id="expiry_date" class="form-control" placeholder="MM/YY" required>
                        </div>
                        <div class="mb-3">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="text" name="cvv" id="cvv" class="form-control" maxlength="3" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="text" value="$<?php echo htmlspecialchars($_GET['amount'] ?? '0.00'); ?>" class="form-control" disabled>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Pay Now</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
</body>
</html>
