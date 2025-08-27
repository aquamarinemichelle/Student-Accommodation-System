<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notification'])) {
    $recipient_type = mysqli_real_escape_string($con, $_POST['recipient_type']);
    $message = mysqli_real_escape_string($con, $_POST['message']);

    if (empty($message)) {
        $error = "Message cannot be empty.";
    } else {
        if ($recipient_type === 'individual' && isset($_POST['student_id'])) {
            $student_id = (int)$_POST['student_id'];
            $query = "INSERT INTO notifications (student_id, message) VALUES ('$student_id', '$message')";
            if (mysqli_query($con, $query)) {
                $success = "Notification sent successfully to the selected student.";
            } else {
                $error = "Failed to send notification: " . mysqli_error($con);
            }
        } elseif ($recipient_type === 'all') {
            $student_query = "SELECT id FROM students WHERE is_active = 1";
            $student_result = mysqli_query($con, $student_query);
            $success_count = 0;
            while ($student = mysqli_fetch_assoc($student_result)) {
                $student_id = $student['id'];
                $query = "INSERT INTO notifications (student_id, message) VALUES ('$student_id', '$message')";
                if (mysqli_query($con, $query)) {
                    $success_count++;
                }
            }
            if ($success_count > 0) {
                $success = "Notification sent successfully to $success_count students.";
            } else {
                $error = "Failed to send notification to any students: " . mysqli_error($con);
            }
        } else {
            $error = "Invalid recipient selection.";
        }
    }
}

$total_students = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS count FROM students"))['count'];
$total_residences = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS count FROM residences"))['count'];
$pending_applications = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS count FROM applications WHERE status = 'pending'"))['count'];

$students_query = "SELECT id, name, surname, student_number FROM students WHERE is_active = 1 ORDER BY name";
$students_result = mysqli_query($con, $students_query);
$students = mysqli_fetch_all($students_result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student Accommodation</title>
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
                    <li class="nav-item"><a class="nav-link active" href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
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
            <h2 class="text-primary mb-4"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h2>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                            <h5>Total Students</h5>
                            <p class="display-6"><?php echo $total_students; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-building fa-2x text-primary mb-2"></i>
                            <h5>Total Residences</h5>
                            <p class="display-6"><?php echo $total_residences; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                            <h5>Pending Applications</h5>
                            <p class="display-6"><?php echo $pending_applications; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <h3 class="mb-3">Quick Actions</h3>
            <div class="row mb-5">
                <div class="col-md-3 mb-3">
                    <a href="manage_residences.php" class="btn btn-primary w-100"><i class="fas fa-building me-2"></i>Manage Residences</a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="manage_applications.php" class="btn btn-primary w-100"><i class="fas fa-file-alt me-2"></i>Manage Applications</a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="manage_students.php" class="btn btn-primary w-100"><i class="fas fa-users me-2"></i>Manage Students</a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="manage_announcements.php" class="btn btn-primary w-100"><i class="fas fa-bullhorn me-2"></i>Manage Announcements</a>
                </div>
            </div>
            <h3 class="mb-3">Send Notification</h3>
            <form method="POST" class="mb-5">
                <div class="mb-3">
                    <label class="form-label">Recipient</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="recipient_type" id="recipient_individual" value="individual" checked>
                        <label class="form-check-label" for="recipient_individual">Individual Student</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="recipient_type" id="recipient_all" value="all">
                        <label class="form-check-label" for="recipient_all">All Students</label>
                    </div>
                </div>
                <div class="mb-3" id="student_select">
                    <label for="student_id" class="form-label">Select Student</label>
                    <select class="form-control" id="student_id" name="student_id">
                        <option value="">-- Select a Student --</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['id']; ?>">
                                <?php echo $student['name'] . ' ' . $student['surname'] . ' (' . $student['student_number'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                </div>
                <button type="submit" name="send_notification" class="btn btn-primary"><i class="fas fa-bell me-2"></i>Send Notification</button>
            </form>
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
    <script>
        document.querySelectorAll('input[name="recipient_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('student_select').style.display = this.value === 'individual' ? 'block' : 'none';
                if (this.value === 'all') {
                    document.getElementById('student_id').value = '';
                }
            });
        });
    </script>
</body>
</html>