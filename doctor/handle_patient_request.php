<?php
session_start();
include("../connection.php");

// Check session and parameters
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
$user_query = $database->query("SELECT docid, docname FROM doctor WHERE docemail='" . $database->real_escape_string($_SESSION["user"]) . "'");
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
$docname = $user_result["docname"];

// Fetch patient request
$request_query = $database->query("SELECT pr.*, p.pname FROM patient_requests pr 
                                 INNER JOIN patient p ON pr.patient_id = p.pid 
                                 WHERE request_id='$request_id' AND doctor_id='$userid'");
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

    // Insert into schedule
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

    // Send notification to admin
    $message = "Dr. $docname approved a session request from " . $request['pname'] . ": " . $request['title'] . " on " . $request['session_date'] . " from " . $request['start_time'] . " to " . $request['end_time'];
    $admin_notify_query = "INSERT INTO admin_notifications (doctor_id, request_id, message, status, timestamp) 
                          VALUES ('$userid', '$request_id', '" . $database->real_escape_string($message) . "', 'pending', NOW())";
    if (!$database->query($admin_notify_query)) {
        header("Location: schedule.php?error=Failed to notify admin: " . $database->error);
        exit();
    }
} elseif ($action == 'reject') {
    // Check if this is coming from the modal form with a rejection reason
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rejection_reason'])) {
        $rejection_reason = $database->real_escape_string($_POST['rejection_reason']);
        $status = 'rejected';
    } else {
        // If accessed directly without POST, redirect back (modal should handle this)
        header("Location: index.php?error=Rejection requires a reason");
        exit();
    }
}

// Update patient request status
$sql = "UPDATE patient_requests SET status = ?, rejection_reason = ? WHERE request_id = ?";
$stmt = $database->prepare($sql);
if ($stmt === false) {
    header("Location: schedule.php?error=Failed to prepare update statement: " . $database->error);
    exit();
}

// Handle rejection reason (NULL for approve)
$rejection_reason = ($action == 'reject' && isset($rejection_reason)) ? $rejection_reason : NULL;
$stmt->bind_param("ssi", $status, $rejection_reason, $request_id);
if ($stmt->execute()) {
    $redirect_page = ($action == 'approve') ? 'schedule.php' : 'index.php';
    header("Location: $redirect_page?success=Request " . $action . "d successfully");
} else {
    header("Location: schedule.php?error=Failed to update request: " . $stmt->error);
}
$stmt->close();

$database->close();
exit();
?>