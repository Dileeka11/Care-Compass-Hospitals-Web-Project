<?php
session_start();
require_once '../config/db_connect.php';
include '../includes/staff_header.php';
// Verify session and user type
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
    header('Location: ../pages/login.php');
    exit();
}

// Initialize variables for messages
$success = $error = '';

// Handle actions only if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if 'action' exists in the POST data
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Add a laboratory test
        if ($action === 'add') {
            $test_name = $_POST['test_name'];
            $description = $_POST['description'];
            $cost = $_POST['cost'];

            // Handle image upload
            $image_url = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/laboratory/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $image_url = $upload_dir . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $image_url);
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO laboratory_tests (test_name, description, cost, image_url) VALUES (?, ?, ?, ?)");
                $stmt->execute([$test_name, $description, $cost, $image_url]);
                $success = "Test added successfully.";
            } catch (PDOException $e) {
                $error = "Error adding test: " . $e->getMessage();
            }
        }

        // Update a laboratory test
        elseif ($action === 'update') {
            $test_id = $_POST['test_id'];
            $test_name = $_POST['test_name'];
            $description = $_POST['description'];
            $cost = $_POST['cost'];

            // Handle image upload
            $image_url = $_POST['current_image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/laboratory/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $image_url = $upload_dir . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $image_url);
            }

            try {
                $stmt = $pdo->prepare("UPDATE laboratory_tests SET test_name = ?, description = ?, cost = ?, image_url = ? WHERE id = ?");
                $stmt->execute([$test_name, $description, $cost, $image_url, $test_id]);
                $success = "Test updated successfully.";
            } catch (PDOException $e) {
                $error = "Error updating test: " . $e->getMessage();
            }
        }

        // Delete a laboratory test
        elseif ($action === 'delete') {
            $test_id = $_POST['test_id'];
            try {
                $stmt = $pdo->prepare("DELETE FROM laboratory_tests WHERE id = ?");
                $stmt->execute([$test_id]);
                $success = "Test deleted successfully.";
            } catch (PDOException $e) {
                $error = "Error deleting test: " . $e->getMessage();
            }
        }
    }

    // Handle request approvals and rejections
    if (isset($_POST['approve_request'])) {
        $request_id = $_POST['request_id'];
        try {
            $stmt = $pdo->prepare("UPDATE laboratory_requests SET status = 'Approved' WHERE id = ?");
            $stmt->execute([$request_id]);
            $success = "Request approved successfully.";
        } catch (PDOException $e) {
            $error = "Error approving request: " . $e->getMessage();
        }
    } elseif (isset($_POST['reject_request'])) {
        $request_id = $_POST['request_id'];
        try {
            $stmt = $pdo->prepare("UPDATE laboratory_requests SET status = 'Rejected' WHERE id = ?");
            $stmt->execute([$request_id]);
            $success = "Request rejected successfully.";
        } catch (PDOException $e) {
            $error = "Error rejecting request: " . $e->getMessage();
        }
    }
}

// Fetch all laboratory tests
try {
    $stmt = $pdo->query("SELECT * FROM laboratory_tests");
    $tests = $stmt->fetchAll();

    // Fetch all requests
    $requests = $pdo->query("
        SELECT lr.*, lt.test_name, u.username AS patient_name
        FROM laboratory_requests lr
        JOIN laboratory_tests lt ON lr.test_id = lt.id
        JOIN users u ON lr.patient_id = u.id
        ORDER BY lr.created_at DESC
    ")->fetchAll();
} catch (PDOException $e) {
    $error = "Error loading data: " . $e->getMessage();
}
?>

<head>
    <link rel="stylesheet" href="../css/manage_laboratory.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<div class="container">
    <h2>Manage Laboratory Tests</h2>

    <?php if (isset($success)) echo "<div class='alert success'>$success</div>"; ?>
    <?php if (isset($error)) echo "<div class='alert error'>$error</div>"; ?>

    <!-- Add/Edit Laboratory Test Form -->
    <form method="POST" id="labTestForm" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add" id="formAction">
        <input type="hidden" name="test_id" id="testId">
        <input type="hidden" name="current_image" id="currentImage">
        <div>
            <label>Test Name:</label>
            <input type="text" name="test_name" id="testName" required>
        </div>
        <div>
            <label>Description:</label>
            <textarea name="description" id="testDescription" required></textarea>
        </div>
        <div>
            <label>Cost:</label>
            <input type="number" step="0.01" name="cost" id="testCost" required>
        </div>
        <div>
            <label>Image:</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" id="testSubmit">Add Test</button>
    </form>

    <!-- Laboratory Tests List -->
    <table>
        <thead>
            <tr>
                <th>Test Name</th>
                <th>Description</th>
                <th>Cost</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tests as $test): ?>
                <tr>
                    <td><?php echo htmlspecialchars($test['test_name']); ?></td>
                    <td><?php echo htmlspecialchars($test['description']); ?></td>
                    <td>$<?php echo number_format($test['cost'], 2); ?></td>
                    <td><img src="<?php echo htmlspecialchars($test['image_url']); ?>" alt="Test Image" style="width: 100px;"></td>
                    <td>
                        <button type="button" onclick="editTest(<?php echo htmlspecialchars(json_encode($test)); ?>)">Edit</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">
                            <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Test Requests</h3>
    <table>
        <thead>
            <tr>
                <th>Patient</th>
                <th>Test</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['patient_name']); ?></td>
                    <td><?php echo htmlspecialchars($request['test_name']); ?></td>
                    <td><?php echo htmlspecialchars($request['status']); ?></td>
                    <td>
                        <?php if ($request['status'] === 'Pending'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                <button type="submit" name="approve_request" class="btn-success">Approve</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                <button type="submit" name="reject_request" class="btn-danger">Reject</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</div>

<script>
function editTest(test) {
    document.getElementById('formAction').value = 'update';
    document.getElementById('testId').value = test.id;
    document.getElementById('currentImage').value = test.image_url;
    document.getElementById('testName').value = test.test_name;
    document.getElementById('testDescription').value = test.description;
    document.getElementById('testCost').value = test.cost;
    document.getElementById('testSubmit').textContent = 'Update Test';
}
</script>
