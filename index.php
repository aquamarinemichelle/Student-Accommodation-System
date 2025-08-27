<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Accommodation System</title>
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
                    <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Student Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/admin_login.php"><i class="fas fa-user-shield"></i> Admin Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5 pt-5 flex-grow-1">
        <div class="hero-section text-center text-white" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://source.unsplash.com/1600x900/?campus'); background-size: cover; padding: 100px 0;">
            <h1 class="display-4">Welcome to Student Accommodation System</h1>
            <p class="lead">Find your perfect residence with ease and efficiency.</p>
            <a href="register.php" class="btn btn-primary btn-lg">Get Started</a>
        </div>
        <div class="my-5">
            <h2 class="text-primary mb-4">Explore Residences</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Sunny Hills Residence</h5>
                            <p class="card-text">Modern facilities in Pretoria with Wi-Fi and study rooms.</p>
                            <a href="register.php" class="btn btn-outline-primary">Apply Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Green Valley Lodge</h5>
                            <p class="card-text">Affordable accommodation near Johannesburg campus.</p>
                            <a href="register.php" class="btn btn-outline-primary">Apply Now</a>
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
    <script src="js/scripts.js"></script>
</body>
</html>