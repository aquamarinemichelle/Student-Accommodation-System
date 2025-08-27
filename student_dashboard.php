<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$location_filter = isset($_GET['location']) ? mysqli_real_escape_string($con, $_GET['location']) : '';
$query = "SELECT * FROM residences WHERE available_slots > 0";
if ($location_filter) {
    $query .= " AND location LIKE '%$location_filter%'";
}
$result = mysqli_query($con, $query);
$residences = mysqli_fetch_all($result, MYSQLI_ASSOC);

$query = "SELECT a.*, r.name AS residence_name FROM applications a JOIN residences r ON a.residence_id = r.id WHERE a.student_id = " . $_SESSION['user_id'];
$result = mysqli_query($con, $query);
$applications = mysqli_fetch_all($result, MYSQLI_ASSOC);

$result = mysqli_query($con, "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3");
$announcements = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Student Accommodation</title>
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
                    <li class="nav-item"><a class="nav-link" href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5 pt-5 flex-grow-1">
        <div class="bg-white p-5 rounded shadow">
            <h2 class="text-primary mb-4"><i class="fas fa-tachometer-alt me-2"></i>Student Dashboard</h2>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-building fa-2x text-primary mb-2"></i>
                            <h5>Apply for Residence</h5>
                            <a href="residence_apply.php" class="btn btn-primary">Apply Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-user fa-2x text-primary mb-2"></i>
                            <h5>View Profile</h5>
                            <a href="student_profile.php" class="btn btn-primary">Edit Profile</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-bell fa-2x text-primary mb-2"></i>
                            <h5>Notifications</h5>
                            <a href="notifications.php" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                </div>
            </div>
            <h3 class="mb-3">Latest Announcements</h3>
            <?php foreach ($announcements as $announcement): ?>
                <div class="alert alert-info">
                    <strong><?php echo $announcement['title']; ?></strong>: <?php echo $announcement['content']; ?>
                    <small>(<?php echo $announcement['created_at']; ?>)</small>
                </div>
            <?php endforeach; ?>
            <h3 class="mt-5">Available Residences</h3>
            <form method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" name="location" placeholder="Filter by location" value="<?php echo $location_filter; ?>">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
            <div class="row">
                <?php foreach ($residences as $residence): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $residence['name']; ?></h5>
                                <p class="card-text"><?php echo $residence['description']; ?></p>
                                <p><strong>Location:</strong> <?php echo $residence['location']; ?></p>
                                <p><strong>Available Slots:</strong> <?php echo $residence['available_slots']; ?></p>
                                <a href="residence_apply.php?residence_id=<?php echo $residence['id']; ?>" class="btn btn-primary">Apply</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <h3 class="mt-5">Your Applications</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Residence</th>
                        <th>Status</th>
                        <th>Comments</th>
                        <th>Applied At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><?php echo $application['residence_name']; ?></td>
                            <td><?php echo ucfirst($application['status']); ?></td>
                            <td><?php echo $application['comments'] ?: 'None'; ?></td>
                            <td><?php echo $application['applied_at']; ?></td>
                            <td>
                                <a href="application_receipt.php?application_id=<?php echo $application['id']; ?>" class="btn btn-sm btn-outline-primary">Download Receipt</a>
                            </td>
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