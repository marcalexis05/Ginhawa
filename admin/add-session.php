<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION['usertype'] != 'a') {
    header("location: ../login.php");
    exit;
}

if ($_POST) {
    include("../connection.php");
    
    $title = $_POST["title"];
    $docid = $_POST["docid"];
    $nop = $_POST["nop"];
    $date = $_POST["date"];
    $start_time = $_POST["start_time"]; // e.g., "16:00:00"
    $duration = (int)$_POST["duration"]; // e.g., "30" (minutes)
    
    // Calculate end_time by adding duration to start_time
    $start_datetime = strtotime($start_time);
    $end_datetime = $start_datetime + ($duration * 60); // Convert minutes to seconds
    $end_time = date("H:i:s", $end_datetime); // Format as 24-hour time (e.g., "16:30:00")
    
    // Insert into database
    $sql = "INSERT INTO schedule (docid, title, scheduledate, start_time, end_time, nop) 
            VALUES ($docid, '$title', '$date', '$start_time', '$end_time', $nop)";
    $result = $database->query($sql);
    
    if ($result) {
        header("location: schedule.php?action=session-added&title=" . urlencode($title));
    } else {
        echo "Error: " . $database->error; // Debugging output
    }
}
?>