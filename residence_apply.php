<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$result = mysqli_query($con, "SELECT * FROM residences WHERE available_slots > 0");
$residences = mysqli_fetch_all($result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $preferences = [$_POST['preference1'], $_POST['preference2'], $_POST['preference3']];
    $primary_residence_id = $preferences[0];

    mysqli_begin_transaction($con);
    try {
        $query = "INSERT INTO applications (student_id, residence_id) VALUES ('{$_SESSION['user_id']}', '$primary_residence_id')";
        mysqli_query($con, $query);
        $application_id = mysqli_insert_id($con);

        for ($i = 0; $i < 3; $i++) {
            if ($preferences[$i]) {
                $query = "INSERT INTO application_preferences (application_id, residence_id, preference_rank) VALUES ('$application_id', '{$preferences[$i]}', " . ($i + 1) . ")";
                mysqli_query($con, $query);
            }
        }

        $query = "UPDATE residences SET available_slots = available_slots - 1 WHERE id = '$primary_residence_id'";
        mysqli_query($con, $query);

        mysqli_commit($con);
        header("Location: student_dashboard.php?success=Application submitted successfully");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($con);
        $error = "Application failed: " . mysqli_error($con);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Residence - Student Accommodation</title>
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
        <div class="bg-white p-5 rounded shadow" style="max-width: 600px; margin: auto;">
            <h2 class="text-primary mb-4"><i class="fas fa-building me-2"></i>Apply for Residence</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="preference1" class="form-label">1st Preference</label>
                    <select class="form-control" id="preference1" name="preference1" required>
                        <option value="">Select Residence</option>
                        <?php foreach ($residences as $residence): ?>
                            <option value="<?php echo $residence['id']; ?>"><?php echo $residence['name']; ?> (<?php echo $residence['location']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="preference2" class="form-label">2nd Preference (Optional)</label>
                    <select class="form-control" id="preference2" name="preference2">
                        <option value="">Select Residence</option>
                        <?php foreach ($residences as $residence): ?>
                            <option value="<?php echo $residence['id']; ?>"><?php echo $residence['name']; ?> (<?php echo $residence['location']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="preference3" class="form-label">3rd Preference (Optional)</label>
                    <select class="form-control" id="preference3" name="preference3">
                        <option value="">Select Residence</option>
                        <?php foreach ($residences as $residence): ?>
                            <option value="<?php echo $residence['id']; ?>"><?php echo $residence['name']; ?> (<?php echo $residence['location']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Submit Application</button>
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
    <script src="js/scripts.js"></script>
</body>
</html>