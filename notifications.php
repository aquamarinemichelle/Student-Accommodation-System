<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['mark_read'])) {
    $notification_id = (int)$_GET['mark_read'];
    $query = "UPDATE notifications SET is_read = 1 WHERE id = '$notification_id' AND student_id = '{$_SESSION['user_id']}'";
    mysqli_query($con, $query);
    header("Location: notifications.php");
    exit;
}

if (isset($_GET['mark_all_read'])) {
    $query = "UPDATE notifications SET is_read = 1 WHERE student_id = '{$_SESSION['user_id']}' AND is_read = 0";
    mysqli_query($con, $query);
    header("Location: notifications.php");
    exit;
}

if (isset($_GET['delete_notification'])) {
    $notification_id = (int)$_GET['delete_notification'];
    $query = "DELETE FROM notifications WHERE id = '$notification_id' AND student_id = '{$_SESSION['user_id']}'";
    mysqli_query($con, $query);
    header("Location: notifications.php");
    exit;
}

if (isset($_GET['download_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="notifications.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Message', 'Is Read', 'Created At']);

    $csv_query = "SELECT message, is_read, created_at FROM notifications WHERE student_id = '{$_SESSION['user_id']}' ORDER BY created_at DESC";
    $csv_result = mysqli_query($con, $csv_query);

    while ($row = mysqli_fetch_assoc($csv_result)) {
        fputcsv($output, [$row['message'], $row['is_read'] ? 'Yes' : 'No', $row['created_at']]);
    }

    fclose($output);
    exit;
}

if (isset($_GET['download_announcements'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="announcements.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Title', 'Content', 'Posted At']);

    $csv_query = "SELECT title, content, created_at FROM announcements ORDER BY created_at DESC";
    $csv_result = mysqli_query($con, $csv_query);

    while ($row = mysqli_fetch_assoc($csv_result)) {
        fputcsv($output, [$row['title'], $row['content'], $row['created_at']]);
    }

    fclose($output);
    exit;
}

$notification_query = "SELECT id, message, is_read, created_at FROM notifications WHERE student_id = '{$_SESSION['user_id']}' ORDER BY created_at DESC";
$notification_result = mysqli_query($con, $notification_query);
$notifications = mysqli_fetch_all($notification_result, MYSQLI_ASSOC);

$announcement_query = "SELECT title, content, created_at FROM announcements ORDER BY created_at DESC";
$announcement_result = mysqli_query($con, $announcement_query);
$announcements = mysqli_fetch_all($announcement_result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Student Accommodation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-home me-2"></i>Student Accommodation</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="student_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="residence_apply.php"><i class="fas fa-building"></i> Apply</a></li>
                    <li class="nav-item"><a class="nav-link" href="student_profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li class="nav-item"><a class="nav-link active" href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5 pt-5 flex-grow-1">
        <div class="bg-white p-5 rounded shadow">
            <h2 class="text-primary mb-4"><i class="fas fa-bell me-2"></i>Notifications</h2>
            <div class="d-flex justify-content-end mb-3 gap-2">
                <a href="?mark_all_read=1" class="btn btn-outline-primary"><i class="fas fa-check-double me-2"></i>Mark All as Read</a>
                <a href="?download_csv=1" class="btn btn-outline-success"><i class="fas fa-file-csv me-2"></i>Download CSV</a>
            </div>
            <h3 class="mb-3">Application Notifications</h3>
            <?php if (empty($notifications)): ?>
                <p class="text-muted">No notifications at this time.</p>
            <?php else: ?>
                <div class="list-group mb-5">
                    <?php foreach ($notifications as $notification): ?>
                        <div class="list-group-item <?php echo $notification['is_read'] ? '' : 'list-group-item-primary'; ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                    <small class="text-muted"><?php echo $notification['created_at']; ?></small>
                                </div>
                                <div class="btn-group">
                                    <?php if (!$notification['is_read']): ?>
                                        <a href="?mark_read=<?php echo $notification['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-check"></i> Read</a>
                                        <?php endif; ?>
                                        <a href="?delete_notification=<?php echo $notification['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this notification?');">
                                        <i class="fas fa-trash-alt"></i> Delete
                                        </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Announcements</h3>
                <a href="?download_announcements=1" class="btn btn-outline-success">
                <i class="fas fa-file-csv me-2"></i>Download Announcements CSV
                </a>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Posted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $announcement): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                            <td><?php echo htmlspecialchars($announcement['content']); ?></td>
                            <td><?php echo $announcement['created_at']; ?></td>
                        </tr>
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
    <script src="js/scripts.js"></script>
</body>
</html>