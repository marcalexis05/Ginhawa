<?php
session_start();
include("../connection.php");

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'p' || !$_POST) {
    header("location: ../login.php");
    exit;
}

$patient_id = $_POST['patient_id'];
$doctor_id = $_POST['doctor_id'];
$title = $_POST['title'];
$session_date = $_POST['session_date'];
$session_time = $_POST['session_time'];

// Validation
$today = date('Y-m-d');
if ($session_date < $today) {
    header("Location: doctors.php?error=Session date cannot be in the past");
    exit();
}
if (empty($title)) {
    header("Location: doctors.php?error=Session title cannot be empty");
    exit();
}

// Check for duplicate pending request
$check = $database->query("SELECT * FROM patient_requests WHERE patient_id='$patient_id' AND doctor_id='$doctor_id' AND status='pending'");
if ($check->num_rows > 0) {
    header("Location: doctors.php?action=request&id=$doctor_id&error=Request already pending!");
    exit;
}

// Insert the request
$sql = "INSERT INTO patient_requests (patient_id, doctor_id, title, session_date, session_time, request_date, status) 
        VALUES (?, ?, ?, ?, ?, NOW(), 'pending')";
$stmt = $database->prepare($sql);
$stmt->bind_param("iisss", $patient_id, $doctor_id, $title, $session_date, $session_time);

if ($stmt->execute()) {
    header("Location: doctors.php?action=request&id=$doctor_id&success=Request submitted successfully!");
} else {
    header("Location: doctors.php?action=request&id=$doctor_id&error=Failed to submit request: " . $database->error);
}

$stmt->close();
?>