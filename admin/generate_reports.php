<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['report']) && $_GET['report'] === 'applications') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="applications_report.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Student Name', 'Residence', 'Status', 'Applied At', 'Comments']);

    $query = "SELECT s.name, s.surname, r.name AS residence_name, a.status, a.applied_at, a.comments FROM applications a JOIN students s ON a.student_id = s.id JOIN residences r ON a.residence_id = r.id";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['name'] . ' ' . $row['surname'],
            $row['residence_name'],
            $row['status'],
            $row['applied_at'],
            $row['comments']
        ]);
    }
    fclose($output);
    exit;
}

if (isset($_GET['report']) && $_GET['report'] === 'occupancy') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="occupancy_report.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Residence', 'Location', 'Capacity', 'Available Slots', 'Occupancy Rate']);

    $query = "SELECT name, location, capacity, available_slots, ((capacity - available_slots) / capacity * 100) AS occupancy_rate FROM residences";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['name'],
            $row['location'],
            $row['capacity'],
            $row['available_slots'],
            number_format($row['occupancy_rate'], 2) . '%'
        ]);
    }
    fclose($output);
    exit;
}

if (isset($_GET['report']) && $_GET['report'] === 'students') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="student_report.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Full Name', 'Email', 'Gender', 'Phone', 'Date of Birth']);

    $query = "SELECT name, surname, email, gender, phone, date_of_birth FROM students";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['name'] . ' ' . $row['surname'],
            $row['email'],
            $row['gender'],
            $row['phone'],
            $row['date_of_birth']
        ]);
    }
    fclose($output);
    exit;
}

if (isset($_GET['report']) && $_GET['report'] === 'residences') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="residence_report.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Name', 'Location', 'Capacity', 'Available Slots']);

    $query = "SELECT name, location, capacity, available_slots FROM residences";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['name'],
            $row['location'],
            $row['capacity'],
            $row['available_slots']
        ]);
    }
    fclose($output);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports - Student Accommodation</title>
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
            <h2 class="text-primary mb-4"><i class="fas fa-chart-bar me-2"></i>Generate Reports</h2>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5>Applications Report</h5>
                            <p>Download a CSV file containing all application details.</p>
                            <a href="?report=applications" class="btn btn-primary"><i class="fas fa-download me-2"></i>Download</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5>Occupancy Report</h5>
                            <p>Download a CSV file with residence occupancy rates.</p>
                            <a href="?report=occupancy" class="btn btn-primary"><i class="fas fa-download me-2"></i>Download</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5>Student Report</h5>
                            <p>Download a CSV with student details and contact info.</p>
                            <a href="?report=students" class="btn btn-primary"><i class="fas fa-download me-2"></i>Download</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5>Residence Report</h5>
                            <p>Download a CSV listing all residence details.</p>
                            <a href="?report=residences" class="btn btn-primary"><i class="fas fa-download me-2"></i>Download</a>
                        </div>
                    </div>
                </div>
            </div>
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