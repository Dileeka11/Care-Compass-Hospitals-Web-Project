<?php
session_start();
require_once '../config/db_connect.php';
include '../includes/admin_header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_staff'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, 'staff')");
            $stmt->execute([$username, $email, $password]);
            $success = "Staff member added successfully.";
        } catch (PDOException $e) {
            $error = ($e->getCode() == '23000') ? "Username or email already exists." : "Error adding staff: " . $e->getMessage();
        }
    } elseif (isset($_POST['edit_staff'])) {
        $id = $_POST['staff_id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        try {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ? AND user_type = 'staff'");
            $stmt->execute([$username, $email, $id]);
            $success = "Staff member updated successfully.";
        } catch (PDOException $e) {
            $error = "Error updating staff: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_staff'])) {
        $id = $_POST['staff_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND user_type = 'staff'");
            $stmt->execute([$id]);
            $success = "Staff member deleted successfully.";
        } catch (PDOException $e) {
            $error = "Error deleting staff: " . $e->getMessage();
        }
    }
}

// Fetch all staff members
try {
    $stmt = $pdo->query("SELECT * FROM users WHERE user_type = 'staff' ORDER BY created_at DESC");
    $staff_members = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching staff members: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
    <link rel="stylesheet" href="../css/admindashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #3498db;
            color: #fff;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #3498db;
            color: #fff;
        }
        .btn-secondary {
            background-color: #f39c12;
            color: #fff;
        }
        .btn-danger {
            background-color: #e74c3c;
            color: #fff;
        }
        button:hover {
            opacity: 0.9;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert.success {
            background-color: #2ecc71;
            color: #fff;
        }
        .alert.error {
            background-color: #e74c3c;
            color: #fff;
        }
    </style>
</head>
<body>


    <div class="container">
        <h2>Manage Staff</h2>

        <?php if (isset($success)) echo "<div class='alert success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert error'>$error</div>"; ?>

        <h3>Add Staff Member</h3>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="add_staff" class="btn-primary">Add Staff</button>
        </form>
    </br>
        <h3>Existing Staff Members</h3>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($staff_members as $staff): ?>
                    <tr>
                        <form method="POST" id="form-<?php echo $staff['id']; ?>">
                            <td>
                                <input type="text" name="username" value="<?php echo htmlspecialchars($staff['username']); ?>" required disabled id="username-<?php echo $staff['id']; ?>">
                            </td>
                            <td>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($staff['email']); ?>" required disabled id="email-<?php echo $staff['id']; ?>">
                            </td>
                            <td>
                                <input type="hidden" name="staff_id" value="<?php echo $staff['id']; ?>">
                                <button type="button" id="edit-btn-<?php echo $staff['id']; ?>" onclick="toggleEdit(<?php echo $staff['id']; ?>)" class="btn-secondary">Edit</button>
                                <button type="submit" name="edit_staff" id="save-btn-<?php echo $staff['id']; ?>" class="btn-primary" style="display: none;">Save</button>
                                <button type="submit" name="delete_staff" class="btn-danger" onclick="return confirm('Are you sure?');">Delete</button>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleEdit(staffId) {
            const usernameField = document.getElementById(`username-${staffId}`);
            const emailField = document.getElementById(`email-${staffId}`);
            const editButton = document.getElementById(`edit-btn-${staffId}`);
            const saveButton = document.getElementById(`save-btn-${staffId}`);

            if (usernameField.disabled) {
                // Enable fields for editing
                usernameField.disabled = false;
                emailField.disabled = false;
                editButton.style.display = 'none';
                saveButton.style.display = 'inline-block';
            } else {
                // Disable fields after saving
                usernameField.disabled = true;
                emailField.disabled = true;
                editButton.style.display = 'inline-block';
                saveButton.style.display = 'none';
            }
        }
    </script>
</body>
</html>