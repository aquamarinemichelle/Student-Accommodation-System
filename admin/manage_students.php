<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['deactivate_id'])) {
    $deactivate_id = (int)$_GET['deactivate_id'];
    $query = "UPDATE students SET is_active = 0 WHERE id = '$deactivate_id'";
    if (mysqli_query($con, $query)) {
        $success = "Student account deactivated successfully.";
    } else {
        $error = "Failed to deactivate student: " . mysqli_error($con);
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $query = "DELETE FROM students WHERE id = '$delete_id'";
    if (mysqli_query($con, $query)) {
        $success = "Student deleted successfully.";
    } else {
        $error = "Failed to delete student: " . mysqli_error($con);
    }
}


$result = mysqli_query($con, "SELECT * FROM students");
$students = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Student Accommodation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php"><i class="fas fa-home me-2"></i>Student Accommodation</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_residences.php"><i class="fas fa-building"></i> Residences</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_applications.php"><i class="fas fa-file-alt"></i> Applications</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_students.php"><i class="fas fa-users"></i> Students</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                    <li class="nav-item"><a class="nav-link" href="generate_reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5 pt-5 flex-grow-1">
        <div class="bg-white p-5 rounded shadow">
            <h2 class="text-primary mb-4"><i class="fas fa-users me-2"></i>Manage Students</h2>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Student Number</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $student['name'] . ' ' . $student['surname']; ?></td>
                            <td><?php echo $student['student_number']; ?></td>
                            <td><?php echo $student['email']; ?></td>
                            <td><?php echo $student['is_active'] ? 'Active' : 'Deactivated'; ?></td>
                            <td>
                                <?php if ($student['is_active']): ?>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#deactivateModal<?php echo $student['id']; ?>">Deactivate</button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $student['id']; ?>">Delete</button>
                            </td>
                        </tr>
                        <div class="modal fade" id="deactivateModal<?php echo $student['id']; ?>" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deactivateModalLabel">Confirm Deactivation</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to deactivate <?php echo $student['name'] . ' ' . $student['surname']; ?>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <a href="?deactivate_id=<?php echo $student['id']; ?>" class="btn btn-danger">Deactivate</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="modal fade" id="deleteModal<?php echo $student['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $student['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel<?php echo $student['id']; ?>">Confirm Deletion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to permanently delete <?php echo $student['name'] . ' ' . $student['surname']; ?>? This action cannot be undone.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <a href="?delete_id=<?php echo $student['id']; ?>" class="btn btn-danger">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <footer class="bg-primary text-white text-center py-3 mt-auto w-100">
        <div class="container">
            <p class="mb-2">© 2025 Student Accommodation System. All rights reserved.</p>
            <div>
                <a href="https://www.facebook.com/share/1Hsn6A2gLC/" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                <a href="https://wa.me/message/CZZAUV2J6VR3I1" class="text-white me-2"><i class="fab fa-whatsapp"></i></a>
                <a href="https://www.instagram.com/studentaccommodationsystem?igsh=NzZvM3FwMDhsZHEy" class="text-white"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/scripts.js"></script>
</body>
</html>