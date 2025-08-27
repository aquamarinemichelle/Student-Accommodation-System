<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['application_id'])) {
    header("Location: student_dashboard.php");
    exit;
}

$application_id = mysqli_real_escape_string($con, $_GET['application_id']);
$query = "SELECT a.*, s.name, s.surname, r.name AS residence_name 
          FROM applications a 
          JOIN students s ON a.student_id = s.id 
          JOIN residences r ON a.residence_id = r.id 
          WHERE a.id = '$application_id' AND a.student_id = " . $_SESSION['user_id'];
$result = mysqli_query($con, $query);
$application = mysqli_fetch_assoc($result);

if (!$application) {
    header("Location: student_dashboard.php?error=Application not found");
    exit;
}

// Set headers to force download of CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="application_receipt.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Output header row
fputcsv($output, ['Field', 'Value']);

// Output application data
fputcsv($output, ['Student Name', $application['name'] . ' ' . $application['surname']]);
fputcsv($output, ['Residence', $application['residence_name']]);
fputcsv($output, ['Application Status', $application['status']]);
fputcsv($output, ['Application Date', $application['created_at']]);

// Output any other fields you want
fclose($output);
exit;
?>
