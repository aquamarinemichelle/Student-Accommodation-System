<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $application_id = (int)$_POST['application_id'];
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $comments = mysqli_real_escape_string($con, $_POST['comments']);

    $query = "UPDATE applications SET status = '$status', comments = '$comments' WHERE id = '$application_id'";
    if (mysqli_query($con, $query)) {
        $success = "Application updated successfully.";
    } else {
        $error = "Failed to update application: " . mysqli_error($con);
    }
}

$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';
$query = "SELECT a.*, s.name AS student_name, s.surname, r.name AS residence_name FROM applications a JOIN students s ON a.student_id = s.id JOIN residences r ON a.residence_id = r.id";
if ($status_filter) {
    $query .= " WHERE a.status = '$status_filter'";
}
$result = mysqli_query($con, $query);
$applications = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applications - Student Accommodation</title>
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
            <h2 class="text-primary mb-4"><i class="fas fa-file-alt me-2"></i>Manage Applications</h2>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="GET" class="mb-3">
                <div class="input-group">
                    <select class="form-control" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Residence</th>
                        <th>Status</th>
                        <th>Applied At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><?php echo $application['student_name'] . ' ' . $application['surname']; ?></td>
                            <td><?php echo $application['residence_name']; ?></td>
                            <td><?php echo ucfirst($application['status']); ?></td>
                            <td><?php echo $application['applied_at']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $application['id']; ?>">Update</button>
                            </td>
                        </tr>
                        <div class="modal fade" id="updateModal<?php echo $application['id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel">Update Application</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST">
                                            <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                            <div class="mb-3">
                                                <label for="status<?php echo $application['id']; ?>" class="form-label">Status</label>
                                                <select class="form-control" id="status<?php echo $application['id']; ?>" name="status">
                                                    <option value="pending" <?php echo $application['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="approved" <?php echo $application['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                                    <option value="rejected" <?php echo $application['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="comments<?php echo $application['id']; ?>" class="form-label">Comments</label>
                                                <textarea class="form-control" id="comments<?php echo $application['id']; ?>" name="comments"><?php echo $application['comments']; ?></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
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