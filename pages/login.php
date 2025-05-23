<?php
session_start();
require_once '../config/db_connect.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];
            
            if ($user['user_type'] === 'admin') {
                header('Location: ../admin/dashboard.php');
            } 
            else if ($user['user_type'] === 'staff') {
                header('Location: ../staff/staff_panel.php');
            }
            else {
                header('Location: ../pages/index.php');
            }
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    } catch (PDOException $e) {
        $error_message = "An error occurred during login. Please try again.";
    }
}
?>

<link rel="stylesheet" href="../css/login.css" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="login-container">
    <div class="login-content">
        <div class="welcome-section">
            <h1>Welcome</h1>
            <p>Your destination for quality care and improved health.</p>
        </div>
        <div class="login-form-section">
            <h2>Login</h2>

            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="username" id="username" placeholder="Username" required>
                </div>

                <div class="form-group">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>

                <button type="submit" class="btn-primary">Log in</button>
            </form>
            <p>Don't have an account? <a href="../pages/register.php">Register here</a></p>
        </div>
    </div>
    <div class="login-image">
        <img src="../uploads/images/login1.jpg" alt="Welcome Image">
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Display error message if set
    <?php if (!empty($error_message)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo $error_message; ?>',
        });
    <?php endif; ?>
});

// Optional client-side validation (example: ensuring fields are not empty)
function validateLogin() {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();

    if (!username || !password) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Both fields are required!',
        });
        return false;
    }

    return true;
}
</script>
