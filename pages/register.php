<?php
session_start();
require_once '../config/db_connect.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Server-side validation
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, 'patient')");
            $stmt->execute([$username, $email, $hashed_password]);
            
            $success_message = "Registration successful. Redirecting to login page...";
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $error_message = "Username or email already exists.";
            } else {
                $error_message = "An unexpected error occurred. Please try again later.";
            }
        }
    }
}
?>

<link rel="stylesheet" href="../css/register.css" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="register-container">
    <div class="register-content">
        <div class="welcome-section">
            <h1>Welcome</h1>
            <p>Discover a new level of healthcare. Join us today.</p>
        </div>
        <div class="register-form-section">
            <h2>Register</h2>

            <form method="POST" action="" onsubmit="return validateRegistration()">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>

                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>

                <div class="form-group">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>

                <div class="form-group">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                </div>

                <button type="submit" class="btn-primary">Register</button>
            </form>

            <p>Already have an account? <a href="../pages/login.php">Login here</a></p>
        </div>
    </div>
    <div class="register-image">
        <img src="../uploads/images/register.jpg" alt="Welcome Image">
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Display error message
    <?php if (!empty($error_message)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo $error_message; ?>',
        });
    <?php endif; ?>

    // Display success message
    <?php if (!empty($success_message)): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?php echo $success_message; ?>',
        }).then(() => {
            window.location.href = '../pages/login.php';
        });
    <?php endif; ?>
});

function validateRegistration() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    if (password !== confirmPassword) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Passwords do not match!',
        });
        return false;
    }

    if (password.length < 6) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Password must be at least 6 characters long!',
        });
        return false;
    }

    return true;
}
</script>
