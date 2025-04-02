<?php
session_start();
include("../connection.php");

if(isset($_SESSION["user"]) && $_SESSION['usertype'] == 'd' && $_POST) {
    $docid = $_POST['docid'];
    $title = $_POST['title']; // Add the title from the form
    $num_sessions = $_POST['num_sessions'];
    $session_date = $_POST['session_date'];
    $session_time = $_POST['session_time'];
    
    // Validation
    $today = date('Y-m-d');
    if($num_sessions < 1 || $num_sessions > 5) {
        header("Location: schedule.php?error=Number of sessions must be between 1 and 5");
        exit();
    }
    if($session_date < $today) {
        header("Location: schedule.php?error=Session date cannot be in the past");
        exit();
    }
    if(empty($title)) {
        header("Location: schedule.php?error=Session title cannot be empty");
        exit();
    }

    // Include title in the SQL query
    $sql = "INSERT INTO session_requests (docid, title, num_sessions, session_date, session_time) VALUES (?, ?, ?, ?, ?)";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("isiss", $docid, $title, $num_sessions, $session_date, $session_time); // 's' for string (title)
    
    if($stmt->execute()) {
        header("Location: schedule.php?success=Request submitted successfully");
    } else {
        header("Location: schedule.php?error=Failed to submit request: " . $database->error);
    }
    
    $stmt->close();
} else {
    header("Location: ../login.php");
}
?>