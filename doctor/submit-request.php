<?php
session_start();
include("../connection.php");

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'd' || !$_POST) {
    header("location: ../login.php");
    exit();
}

$docid = (int)$_POST['docid'];
$title = $database->real_escape_string($_POST['title']);
$session_date = $database->real_escape_string($_POST['session_date']);
$start_time = $database->real_escape_string($_POST['start_time']);
$end_time = $database->real_escape_string($_POST['end_time']);

$sql = "INSERT INTO schedule (docid, title, scheduledate, start_time, end_time) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $database->prepare($sql);
if ($stmt === false) {
    header("Location: schedule.php?error=Failed to prepare statement: " . $database->error);
    exit();
}

$stmt->bind_param("issss", $docid, $title, $session_date, $start_time, $end_time);

if ($stmt->execute()) {
    header("Location: schedule.php?success=Session request submitted successfully");
} else {
    header("Location: schedule.php?error=Failed to submit request: " . $stmt->error);
}

$stmt->close();
$database->close();
?>