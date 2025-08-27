<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_residence'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $location = mysqli_real_escape_string($con, $_POST['location']);
    $capacity = (int)$_POST['capacity'];
    $available_slots = $capacity;

    $query = "INSERT INTO residences (name, description, location, capacity, available_slots) VALUES ('$name', '$description', '$location', '$capacity', '$available_slots')";
    if (mysqli_query($con, $query)) {
        $residence_id = mysqli_insert_id($con);
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_path = '../images/uploads/' . time() . '_' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
            $query = "INSERT INTO residence_images (residence_id, image_path) VALUES ('$residence_id', '$image_path')";
            mysqli_query($con, $query);
        }
        $success = "Residence added successfully.";
    } else {
        $error = "Failed to add residence: " . mysqli_error($con);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_residence'])) {
    $res_id = (int)$_POST['residence_id'];
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $location = mysqli_real_escape_string($con, $_POST['location']);
    $capacity = (int)$_POST['capacity'];

    // Update residence
    $query = "UPDATE residences SET name='$name', description='$description', location='$location', capacity='$capacity' WHERE id='$res_id'";
    if (mysqli_query($con, $query)) {
        // Optional: handle image update
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_path = '../images/uploads/' . time() . '_' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);

            // Update image or insert if not exists
            $img_check = mysqli_query($con, "SELECT * FROM residence_images WHERE residence_id = '$res_id'");
            if (mysqli_num_rows($img_check)) {
                mysqli_query($con, "UPDATE residence_images SET image_path = '$image_path' WHERE residence_id = '$res_id'");
            } else {
                mysqli_query($con, "INSERT INTO residence_images (residence_id, image_path) VALUES ('$res_id', '$image_path')");
            }
        }

        $success = "Residence updated successfully.";
    } else {
        $error = "Failed to update residence: " . mysqli_error($con);
    }
}


if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $query = "DELETE FROM residences WHERE id = '$delete_id'";
    if (mysqli_query($con, $query)) {
        $query = "DELETE FROM residence_images WHERE residence_id = '$delete_id'";
        mysqli_query($con, $query);
        $success = "Residence deleted successfully.";
    } else {
        $error = "Failed to delete residence: " . mysqli_error($con);
    }
}

$filter_location = '';
$where_clause = '';
if (isset($_GET['filter_location']) && !empty(trim($_GET['filter_location']))) {
    $filter_location = mysqli_real_escape_string($con, $_GET['filter_location']);
    $where_clause = "WHERE r.location LIKE '%$filter_location%'";
}

$query = "SELECT r.*, ri.image_path FROM residences r LEFT JOIN residence_images ri ON r.id = ri.residence_id $where_clause";
$result = mysqli_query($con, $query);
$residences = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Residences - Student Accommodation</title>
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
            <h2 class="text-primary mb-4"><i class="fas fa-building me-2"></i>Manage Residences</h2>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data" class="mb-5">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="capacity" class="form-label">Capacity</label>
                    <input type="number" class="form-control" id="capacity" name="capacity" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>
                <button type="submit" name="add_residence" class="btn btn-primary">Add Residence</button>
            </form>
            <form method="GET" class="row mb-4">
    <div class="col-md-6">
        <label for="filter_location" class="form-label">Filter by Location</label>
        <input type="text" name="filter_location" id="filter_location" class="form-control" value="<?php echo isset($_GET['filter_location']) ? htmlspecialchars($_GET['filter_location']) : ''; ?>" placeholder="Enter location">
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <a href="manage_residences.php" class="btn btn-secondary w-100">Clear Filter</a>
    </div>
</form>

            <h3 class="mb-4">Residence List</h3>
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php foreach ($residences as $residence): ?>
        <div class="col">
            <div class="card h-100 shadow-sm">
                <?php if ($residence['image_path']): ?>
                    <img src="<?php echo $residence['image_path']; ?>" class="card-img-top" alt="Residence Image" style="height: 200px; object-fit: cover;">
                <?php else: ?>
                    <img src="../images/default-placeholder.png" class="card-img-top" alt="No Image" style="height: 200px; object-fit: cover;">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $residence['name']; ?></h5>
                    <p class="card-text"><strong>Location:</strong> <?php echo $residence['location']; ?></p>
                    <p class="card-text"><strong>Capacity:</strong> <?php echo $residence['capacity']; ?></p>
                    <p class="card-text"><strong>Available Slots:</strong> <?php echo $residence['available_slots']; ?></p>
                </div>
                <div class="card-footer text-end bg-white border-top-0">
                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $residence['id']; ?>">Delete</button>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $residence['id']; ?>">Edit</button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal<?php echo $residence['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete <strong><?php echo $residence['name']; ?></strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <a href="?delete_id=<?php echo $residence['id']; ?>" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Modal -->
<div class="modal fade" id="editModal<?php echo $residence['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $residence['id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel<?php echo $residence['id']; ?>">Edit Residence - <?php echo $residence['name']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <input type="hidden" name="residence_id" value="<?php echo $residence['id']; ?>">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($residence['name']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($residence['location']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" required><?php echo htmlspecialchars($residence['description']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacity</label>
                        <input type="number" name="capacity" class="form-control" value="<?php echo $residence['capacity']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image (optional)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_residence" class="btn btn-success">Update Residence</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <?php endforeach; ?>
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