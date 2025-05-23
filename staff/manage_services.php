<?php
session_start();
require_once '../config/db_connect.php';
include '../includes/staff_header.php';
// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
    header('Location: ../pages/login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if 'action' key exists in POST data
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'add') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $cost = $_POST['cost'];
            $branch = $_POST['branch'];

            // Handle image upload
            $image_url = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/services/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $image_url = $upload_dir . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $image_url);
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO services (name, description, cost, branch, image_url) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $description, $cost, $branch, $image_url]);
                $success = "Service added successfully.";
            } catch (PDOException $e) {
                $error = "Error adding service: " . $e->getMessage();
            }
        } elseif ($action === 'update') {
            $id = $_POST['service_id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $cost = $_POST['cost'];
            $branch = $_POST['branch'];

            // Handle image upload
            $image_url = $_POST['current_image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/services/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $image_url = $upload_dir . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $image_url);
            }

            try {
                $stmt = $pdo->prepare("UPDATE services SET name = ?, description = ?, cost = ?, branch = ?, image_url = ? WHERE id = ?");
                $stmt->execute([$name, $description, $cost, $branch, $image_url, $id]);
                $success = "Service updated successfully.";
            } catch (PDOException $e) {
                $error = "Error updating service: " . $e->getMessage();
            }
        } elseif ($action === 'delete') {
            $id = $_POST['service_id'];
            try {
                $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
                $stmt->execute([$id]);
                $success = "Service deleted successfully.";
            } catch (PDOException $e) {
                $error = "Error deleting service: " . $e->getMessage();
            }
        }
    } elseif (isset($_POST['approve_request'])) {
        $request_id = $_POST['request_id'];
        try {
            $stmt = $pdo->prepare("UPDATE service_requests SET status = 'Approved' WHERE id = ?");
            $stmt->execute([$request_id]);
            $success = "Request approved successfully.";
        } catch (PDOException $e) {
            $error = "Error approving request: " . $e->getMessage();
        }
    } elseif (isset($_POST['reject_request'])) {
        $request_id = $_POST['request_id'];
        try {
            $stmt = $pdo->prepare("UPDATE service_requests SET status = 'Rejected' WHERE id = ?");
            $stmt->execute([$request_id]);
            $success = "Request rejected successfully.";
        } catch (PDOException $e) {
            $error = "Error rejecting request: " . $e->getMessage();
        }
    }
}

// Fetch all services
try {
    $stmt = $pdo->query("SELECT * FROM services");
    $services = $stmt->fetchAll();

    // Fetch all requests
    $requests = $pdo->query(
        "SELECT sr.*, s.name AS service_name, u.username AS patient_name
        FROM service_requests sr
        JOIN services s ON sr.service_id = s.id
        JOIN users u ON sr.patient_id = u.id
        ORDER BY sr.created_at DESC"
    )->fetchAll();
} catch (PDOException $e) {
    $error = "Error loading data: " . $e->getMessage();
}
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/manageservices.css">
<script src="../js/manageservices.js" defer></script>

<div class="container">
    <h2>Manage Services</h2>

    <?php if (isset($success)) echo "<div class='alert success'>$success</div>"; ?>
    <?php if (isset($error)) echo "<div class='alert error'>$error</div>"; ?>

    <!-- Add/Edit Service Form -->
    <form method="POST" id="serviceForm" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add" id="formAction">
        <input type="hidden" name="service_id" id="serviceId">
        <input type="hidden" name="current_image" id="currentImage">
        <div>
            <label>Name:</label>
            <input type="text" name="name" id="serviceName" required>
        </div>
        <div>
            <label>Description:</label>
            <textarea name="description" id="serviceDescription" required></textarea>
        </div>
        <div>
            <label>Cost:</label>
            <input type="number" step="0.01" name="cost" id="serviceCost" required>
        </div>
        <div>
            <label>Branch:</label>
            <select name="branch" id="serviceBranch" required>
                <option value="Kandy">Kandy</option>
                <option value="Colombo">Colombo</option>
                <option value="Kurunegala">Kurunegala</option>
            </select>
        </div>
        <div>
            <label>Image:</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" id="serviceSubmit">Add Service</button>
    </form>

    <!-- Services List -->
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Cost</th>
                <th>Branch</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $service): ?>
                <tr>
                    <td><?php echo htmlspecialchars($service['name']); ?></td>
                    <td><?php echo htmlspecialchars($service['description']); ?></td>
                    <td>$<?php echo number_format($service['cost'], 2); ?></td>
                    <td><?php echo htmlspecialchars($service['branch']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($service['image_url']); ?>" alt="Service Image" style="width: 100px;"></td>
                    <td>
                        <button type="button" onclick="editService(<?php echo htmlspecialchars(json_encode($service)); ?>)">Edit</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                            <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Service Requests</h3>
    <table>
        <thead>
            <tr>
                <th>Patient</th>
                <th>Service</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['patient_name']); ?></td>
                    <td><?php echo htmlspecialchars($request['service_name']); ?></td>
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

<script>
function editService(service) {
    document.getElementById('formAction').value = 'update';
    document.getElementById('serviceId').value = service.id;
    document.getElementById('currentImage').value = service.image_url;
    document.getElementById('serviceName').value = service.name;
    document.getElementById('serviceDescription').value = service.description;
    document.getElementById('serviceCost').value = service.cost;
    document.getElementById('serviceBranch').value = service.branch;
    document.getElementById('serviceSubmit').textContent = 'Update Service';
}
</script>
