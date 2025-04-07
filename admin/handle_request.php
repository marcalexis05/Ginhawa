<?php
session_start();
include("../connection.php");

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'a' || !isset($_GET['action']) || !isset($_GET['id'])) {
    header("Location: ../login.php");
    exit();
}

if (!$database) {
    header("Location: schedule.php?error=Database connection failed: " . mysqli_connect_error());
    exit();
}

$request_id = $database->real_escape_string($_GET['id']);
$action = $_GET['action'];

$request_query = $database->query("SELECT * FROM session_requests WHERE request_id='$request_id'");
if ($request_query === false || $request_query->num_rows == 0) {
    header("Location: schedule.php?error=" . ($request_query === false ? "Request query failed: " . $database->error : "Invalid request"));
    exit();
}
$request = $request_query->fetch_assoc();

$status = $action == 'approve' ? 'approved' : 'rejected';

if ($action == 'approve') {
    $docid = $request['docid'];
    $schedule_id = $request['schedule_id'];
    $session_date = $request['session_date'];
    $start_time = $request['start_time'];
    $end_time = $request['end_time'];
    $duration = $request['duration'];

    $start_dt = DateTime::createFromFormat('H:i:s', $start_time);
    $end_dt = DateTime::createFromFormat('H:i:s', $end_time);
    if (!$start_dt || !$end_dt || $end_dt <= $start_dt) {
        header("Location: schedule.php?error=Invalid time range in request");
        exit();
    }

    $doctor_check = $database->query("SELECT COUNT(*) FROM schedule WHERE docid = '$docid' AND scheduledate = '$session_date'");
    if ($doctor_check === false || $doctor_check->fetch_row()[0] >= 5) {
        header("Location: schedule.php?error=" . ($doctor_check === false ? "Max sessions check failed: " . $database->error : "Cannot approve: Maximum of 5 sessions reached for this day"));
        exit();
    }

    $overlap_check = $database->query("SELECT * FROM schedule WHERE docid = '$docid' AND scheduledate = '$session_date' AND ((start_time < '$end_time' AND end_time > '$start_time')) AND scheduleid != '$schedule_id'");
    if ($overlap_check === false || $overlap_check->num_rows > 0) {
        header("Location: schedule.php?error=" . ($overlap_check === false ? "Overlap check failed: " . $database->error : "Cannot approve: Time slot overlaps with an existing session"));
        exit();
    }

    // Check if gmeet_request exists and is true
    $gmeet_link = isset($request['gmeet_request']) && $request['gmeet_request'] ? '' : null;
    if ($schedule_id) {
        $sql = "UPDATE schedule SET gmeet_link = ? WHERE scheduleid = ?";
        $stmt = $database->prepare($sql);
        if ($stmt === false || !$stmt->bind_param("si", $gmeet_link, $schedule_id) || !$stmt->execute()) {
            header("Location: schedule.php?error=Failed to update schedule: " . ($stmt === false ? $database->error : $stmt->error));
            $stmt->close();
            exit();
        }
        $stmt->close();
    } else {
        // If it's a new session, insert it
        $title = $request['title'];
        $sql = "INSERT INTO schedule (docid, title, scheduledate, start_time, end_time, gmeet_link) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $database->prepare($sql);
        if ($stmt === false || !$stmt->bind_param("isssss", $docid, $title, $session_date, $start_time, $end_time, $gmeet_link) || !$stmt->execute()) {
            header("Location: schedule.php?error=Failed to insert schedule: " . ($stmt === false ? $database->error : $stmt->error));
            $stmt->close();
            exit();
        }
        $stmt->close();
    }
}

$sql = "UPDATE session_requests SET status = ? WHERE request_id = ?";
$stmt = $database->prepare($sql);
if ($stmt === false || !$stmt->bind_param("si", $status, $request_id) || !$stmt->execute()) {
    header("Location: schedule.php?error=Failed to update request: " . ($stmt === false ? $database->error : $stmt->error));
} else {
    $message = "Request " . $action . "d successfully";
    if (isset($request['gmeet_request']) && $request['gmeet_request'] && $action == 'approve') {
        $message .= " (Edit Google Meet link as needed)";
    }
    header("Location: schedule.php?success=" . urlencode($message));
}

$stmt->close();
$database->close();
?>