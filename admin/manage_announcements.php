<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_announcement'])) {
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $content = mysqli_real_escape_string($con, $_POST['content']);

    $query = "INSERT INTO announcements (title, content) VALUES ('$title', '$content')";
    if (mysqli_query($con, $query)) {
        $success = "Announcement posted successfully.";
    } else {
        $error = "Failed to post announcement: " . mysqli_error($con);
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $query = "DELETE FROM announcements WHERE id = '$delete_id'";
    if (mysqli_query($con, $query)) {
        $success = "Announcement deleted successfully.";
    } else {
        $error = "Failed to delete announcement: " . mysqli_error($con);
    }
}

$result = mysqli_query($con, "SELECT * FROM announcements ORDER BY created_at DESC");
$announcements = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements - Student Accommodation</title>
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
            <h2 class="text-primary mb-4"><i class="fas fa-bullhorn me-2"></i>Manage Announcements</h2>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" class="mb-5">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" required></textarea>
                </div>
                <button type="submit" name="add_announcement" class="btn btn-primary">Post Announcement</button>
            </form>
            <h3 class="mb-3">Announcements</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Posted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $announcement): ?>
                        <tr>
                            <td><?php echo $announcement['title']; ?></td>
                            <td><?php echo $announcement['content']; ?></td>
                            <td><?php echo $announcement['created_at']; ?></td>
                            <td>
                                <a href="?delete_id=<?php echo $announcement['id']; ?>" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $announcement['id']; ?>">Delete</a>
                            </td>
                        </tr>
                        <div class="modal fade" id="deleteModal<?php echo $announcement['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this announcement?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <a href="?delete_id=<?php echo $announcement['id']; ?>" class="btn btn-danger">Delete</a>
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