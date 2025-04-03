<?php
session_start();
include("../connection.php");

header('Content-Type: application/json');

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'd' || !$_POST) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access or invalid request']);
    exit();
}

$docid = $_POST['docid'];
$title = $_POST['title'];
$num_sessions = isset($_POST['num_sessions']) ? (int)$_POST['num_sessions'] : 1; // Default to 1 if not provided
$session_date = $_POST['session_date'];
$session_time = $_POST['start_time']; // Adjusted to match previous form field name
$duration = isset($_POST['duration']) ? (int)$_POST['duration'] : null; // From previous form

// Calculate end time if duration is provided
if ($duration) {
    $start_datetime = new DateTime($session_time);
    $start_datetime->modify("+$duration minutes");
    $end_time = $start_datetime->format('H:i:s');
} else {
    $end_time = null; // Or set a default if applicable
}

// Server-side validation
$today = date('Y-m-d');
if ($num_sessions < 1 || $num_sessions > 5) {
    echo json_encode(['success' => false, 'message' => 'Number of sessions must be between 1 and 5']);
    exit();
}
if ($session_date < $today) {
    echo json_encode(['success' => false, 'message' => 'Session date cannot be in the past']);
    exit();
}
if (empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Session title cannot be empty']);
    exit();
}
if (empty($session_time)) {
    echo json_encode(['success' => false, 'message' => 'Session start time is required']);
    exit();
}
if ($duration && ($duration < 30 || $duration > 120)) { // Assuming reasonable duration limits
    echo json_encode(['success' => false, 'message' => 'Duration must be between 30 and 120 minutes']);
    exit();
}

// Check for overlapping sessions (if applicable)
if ($end_time) {
    $check_overlap = $database->prepare("SELECT * FROM schedule 
        WHERE docid = ? 
        AND scheduledate = ? 
        AND (
            (start_time < ? AND end_time > ?)
        )");
    $check_overlap->bind_param("isss", $docid, $session_date, $end_time, $session_time);
    $check_overlap->execute();
    $result = $check_overlap->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'This time slot is already booked']);
        exit();
    }
    $check_overlap->close();
}

// Insert into session_requests table with prepared statement
$sql = "INSERT INTO session_requests (docid, title, num_sessions, session_date, session_time, duration, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending')";
$stmt = $database->prepare($sql);
$stmt->bind_param("isisis", $docid, $title, $num_sessions, $session_date, $session_time, $duration);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Your session request has been submitted successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit request: ' . $database->error]);
}

$stmt->close();
$database->close();
?>