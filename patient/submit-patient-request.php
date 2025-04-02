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
$start_time = $_POST['start_time'];
$duration = $_POST['duration'];

date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');
$current_time = date('H:i:s');

// Check if the session is for today and the start time has passed
if ($session_date === $today) {
    if ($start_time <= $current_time) {
        header("Location: doctors.php?error=Cannot request a session for a time that has already passed today");
        exit();
    }
}

$start_datetime = new DateTime("$session_date $start_time");
$start_datetime->modify("+$duration minutes");
$end_time = $start_datetime->format('H:i:s');

if ($session_date < $today) {
    header("Location: doctors.php?error=Session date cannot be in the past");
    exit();
}
if (empty($title)) {
    header("Location: doctors.php?error=Session title cannot be empty");
    exit();
}

$patient_check = $database->query("SELECT COUNT(*) FROM appointment a 
    INNER JOIN schedule s ON a.scheduleid = s.scheduleid 
    WHERE a.pid = '$patient_id' AND s.scheduledate = '$session_date'");
if ($patient_check->fetch_row()[0] > 0) {
    header("Location: doctors.php?error=You can only book 1 session per day");
    exit();
}

$doctor_check = $database->query("SELECT COUNT(*) FROM schedule 
    WHERE docid = '$doctor_id' AND scheduledate = '$session_date'");
if ($doctor_check->fetch_row()[0] >= 5) {
    header("Location: doctors.php?error=Doctor has reached the maximum of 5 sessions for this day");
    exit();
}

$overlap_check = $database->query("SELECT * FROM schedule 
    WHERE docid = '$doctor_id' AND scheduledate = '$session_date' 
    AND ((start_time < '$end_time' AND end_time > '$start_time'))");
if ($overlap_check->num_rows > 0) {
    header("Location: doctors.php?error=Time slot overlaps with an existing session");
    exit();
}

$check = $database->query("SELECT * FROM patient_requests 
    WHERE patient_id = '$patient_id' AND doctor_id = '$doctor_id' AND status = 'pending'");
if ($check->num_rows > 0) {
    header("Location: doctors.php?action=request&id=$doctor_id&error=Request already pending!");
    exit;
}

$sql = "INSERT INTO patient_requests (patient_id, doctor_id, title, session_date, start_time, duration, end_time, request_date, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')";
$stmt = $database->prepare($sql);
$stmt->bind_param("iisssss", $patient_id, $doctor_id, $title, $session_date, $start_time, $duration, $end_time);

if ($stmt->execute()) {
    header("Location: doctors.php?action=request&id=$doctor_id&success=Request submitted successfully!");
} else {
    header("Location: doctors.php?action=request&id=$doctor_id&error=Failed to submit request: " . $database->error);
}

$stmt->close();
?>