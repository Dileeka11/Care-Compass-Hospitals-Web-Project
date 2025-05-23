<?php
session_start();
require_once '../config/db_connect.php';
include '../includes/admin_header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle doctor addition, update, and deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $name = $_POST['name'];
        $specialty = $_POST['specialty'];
        $qualifications = $_POST['qualifications'];
        $contact_info = $_POST['contact_info'];
        $branch = $_POST['branch'];

        // Handle image upload
        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/doctors/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $image_url = $upload_dir . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $image_url);
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO doctors (name, specialty, qualifications, contact_info, branch, image_url) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $specialty, $qualifications, $contact_info, $branch, $image_url]);
            $success = "Doctor added successfully.";
        } catch (PDOException $e) {
            $error = "Error adding doctor: " . $e->getMessage();
        }
    } elseif ($action === 'update') {
        $id = $_POST['doctor_id'];
        $name = $_POST['name'];
        $specialty = $_POST['specialty'];
        $qualifications = $_POST['qualifications'];
        $contact_info = $_POST['contact_info'];
        $branch = $_POST['branch'];

        // Handle image upload
        $image_url = $_POST['current_image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/doctors/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $image_url = $upload_dir . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $image_url);
        }

        try {
            $stmt = $pdo->prepare("UPDATE doctors SET name = ?, specialty = ?, qualifications = ?, contact_info = ?, branch = ?, image_url = ? WHERE id = ?");
            $stmt->execute([$name, $specialty, $qualifications, $contact_info, $branch, $image_url, $id]);
            $success = "Doctor updated successfully.";
        } catch (PDOException $e) {
            $error = "Error updating doctor: " . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        $id = $_POST['doctor_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM doctors WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Doctor removed successfully.";
        } catch (PDOException $e) {
            $error = "Error deleting doctor: " . $e->getMessage();
        }
    }
}

// Fetch all doctors
$doctors = $pdo->query("SELECT * FROM doctors ORDER BY name")->fetchAll();
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/managedoctors.css">
<script src="../js/managedoctors.js" defer></script>

<div class="container">
    <h2>Manage Doctors</h2>

    <?php if (isset($success)) echo "<div class='alert success'>$success</div>"; ?>
    <?php if (isset($error)) echo "<div class='alert error'>$error</div>"; ?>

    <!-- Add/Edit Doctor Form -->
    <form method="POST" id="doctorForm" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add" id="formAction">
        <input type="hidden" name="doctor_id" id="doctorId">
        <input type="hidden" name="current_image" id="currentImage">
        <div>
            <label>Name:</label>
            <input type="text" name="name" id="doctorName" required>
        </div>
        <div>
            <label>Specialty:</label>
            <input type="text" name="specialty" id="doctorSpecialty" required>
        </div>
        <div>
            <label>Qualifications:</label>
            <textarea name="qualifications" id="doctorQualifications" required></textarea>
        </div>
        <div>
            <label>Contact Info:</label>
            <textarea name="contact_info" id="doctorContact" required></textarea>
        </div>
        <div>
            <label>Branch:</label>
            <select name="branch" id="doctorBranch" required>
                <option value="Kandy">Kandy</option>
                <option value="Colombo">Colombo</option>
                <option value="Kurunegala">Kurunegala</option>
            </select>
        </div>
        <div>
            <label>Image:</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" id="doctorSubmit">Add Doctor</button>
    </form>

    <!-- Doctors List -->
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Specialty</th>
                <th>Branch</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($doctors as $doctor): ?>
                <tr>
                    <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                    <td><?php echo htmlspecialchars($doctor['specialty']); ?></td>
                    <td><?php echo htmlspecialchars($doctor['branch']); ?></td>
                    <td>
                        <button type="button" onclick="editDoctor(<?php echo htmlspecialchars(json_encode($doctor)); ?>)">Edit</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                            <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function editDoctor(doctor) {
    document.getElementById('formAction').value = 'update';
    document.getElementById('doctorId').value = doctor.id;
    document.getElementById('currentImage').value = doctor.image_url;
    document.getElementById('doctorName').value = doctor.name;
    document.getElementById('doctorSpecialty').value = doctor.specialty;
    document.getElementById('doctorQualifications').value = doctor.qualifications;
    document.getElementById('doctorContact').value = doctor.contact_info;
    document.getElementById('doctorBranch').value = doctor.branch;
    document.getElementById('doctorSubmit').textContent = 'Update Doctor';
}
</script>
