<?php
session_start();
include("../connection.php");

// Check session and GET parameters
if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'd' || !isset($_GET['action']) || !isset($_GET['id'])) {
    header("location: ../login.php");
    exit();
}

// Verify database connection
if (!$database) {
    header("Location: schedule.php?error=Database connection failed: " . mysqli_connect_error());
    exit();
}

$request_id = $_GET['id'];
$action = $_GET['action'];

// Fetch doctor ID
$user_query = $database->query("SELECT docid FROM doctor WHERE docemail='" . $database->real_escape_string($_SESSION["user"]) . "'");
if ($user_query === false) {
    header("Location: schedule.php?error=Failed to fetch doctor ID: " . $database->error);
    exit();
}
$user_result = $user_query->fetch_assoc();
if (!$user_result) {
    header("Location: schedule.php?error=Doctor not found");
    exit();
}
$userid = $user_result["docid"];

// Fetch patient request
$request_query = $database->query("SELECT * FROM patient_requests WHERE request_id='$request_id' AND doctor_id='$userid'");
if ($request_query === false) {
    header("Location: schedule.php?error=Request query failed: " . $database->error);
    exit();
}
if ($request_query->num_rows == 0) {
    header("Location: schedule.php?error=Invalid request");
    exit();
}
$request = $request_query->fetch_assoc();

$status = $action == 'approve' ? 'approved' : 'rejected';

if ($action == 'approve') {
    // Check constraints before approving
    $session_date = $request['session_date'];
    $start_time = $request['start_time'];
    $end_time = $request['end_time'];
    $duration = $request['duration'];

    // Check max sessions per day
    $doctor_check = $database->query("SELECT COUNT(*) FROM schedule 
        WHERE docid = '$userid' AND scheduledate = '$session_date'");
    if ($doctor_check === false) {
        header("Location: schedule.php?error=Max sessions check failed: " . $database->error);
        exit();
    }
    if ($doctor_check->fetch_row()[0] >= 5) {
        header("Location: schedule.php?error=Cannot approve: Maximum of 5 sessions reached for this day");
        exit();
    }

    // Check for overlapping sessions
    $overlap_check = $database->query("SELECT * FROM schedule 
        WHERE docid = '$userid' AND scheduledate = '$session_date' 
        AND ((start_time < '$end_time' AND end_time > '$start_time'))");
    if ($overlap_check === false) {
        header("Location: schedule.php?error=Overlap check failed: " . $database->error);
        exit();
    }
    if ($overlap_check->num_rows > 0) {
        header("Location: schedule.php?error=Cannot approve: Time slot overlaps with an existing session");
        exit();
    }

    // Insert into schedule (removed 'nop')
    $sql = "INSERT INTO schedule (docid, title, scheduledate, start_time, duration, end_time) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $database->prepare($sql);
    if ($stmt === false) {
        header("Location: schedule.php?error=Failed to prepare insert statement: " . $database->error);
        exit();
    }
    $stmt->bind_param("isssss", $userid, $request['title'], $session_date, $start_time, $duration, $end_time);
    if (!$stmt->execute()) {
        header("Location: schedule.php?error=Failed to insert into schedule: " . $stmt->error);
        $stmt->close();
        exit();
    }
    $stmt->close();
}

// Update patient request status
$sql = "UPDATE patient_requests SET status = ? WHERE request_id = ?";
$stmt = $database->prepare($sql);
if ($stmt === false) {
    header("Location: schedule.php?error=Failed to prepare update statement: " . $database->error);
    exit();
}
$stmt->bind_param("si", $status, $request_id);
if ($stmt->execute()) {
    header("Location: schedule.php?success=Request " . $action . "d successfully");
} else {
    header("Location: schedule.php?error=Failed to update request: " . $stmt->error);
}
$stmt->close();

$database->close();
?>