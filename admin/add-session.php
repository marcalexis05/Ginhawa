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
    $date = $_POST["date"];
    $start_time = $_POST["start_time"]; // e.g., "15:00:00"
    $duration = (int)$_POST["duration"]; // e.g., "30" (minutes)

    // Calculate end_time
    $start_datetime = strtotime($start_time);
    $end_datetime = $start_datetime + ($duration * 60); // Convert minutes to seconds
    $end_time = date("H:i:s", $end_datetime); // e.g., "15:30:00"

    // Check the number of sessions for the doctor on the given date
    $count_sql = "SELECT COUNT(*) as session_count FROM schedule WHERE docid = ? AND scheduledate = ?";
    $count_stmt = $database->prepare($count_sql);
    if ($count_stmt === false) {
        die("Prepare failed: " . $database->error);
    }
    $count_stmt->bind_param("is", $docid, $date);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $session_count = $count_result->fetch_assoc()["session_count"];
    $count_stmt->close();

    if ($session_count >= 5) {
        // Limit exceeded, redirect with error message
        header("location: schedule.php?action=add-session-limit-exceeded&title=" . urlencode($title) . "&date=" . urlencode($date));
        exit;
    }

    // Check for overlapping sessions
    $check_sql = "
        SELECT COUNT(*) as conflict_count 
        FROM schedule 
        WHERE docid = ? 
        AND scheduledate = ? 
        AND (
            (start_time < ? AND end_time > ?) 
            OR (start_time < ? AND end_time > ?) 
            OR (start_time >= ? AND end_time <= ?)
        )";
    $check_stmt = $database->prepare($check_sql);
    if ($check_stmt === false) {
        die("Prepare failed: " . $database->error);
    }

    // Bind parameters: docid, date, end_time, start_time (twice for overlap check), start_time, end_time
    $check_stmt->bind_param("isssssss", $docid, $date, $end_time, $start_time, $end_time, $start_time, $start_time, $end_time);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $conflict_count = $check_result->fetch_assoc()["conflict_count"];
    $check_stmt->close();

    if ($conflict_count > 0) {
        // Conflict found, redirect with error message
        header("location: schedule.php?action=add-session-conflict&title=" . urlencode($title) . "&date=" . urlencode($date) . "&start_time=" . urlencode($start_time));
        exit;
    }

    // No conflict and under limit, insert the new session
    $sql = "INSERT INTO schedule (docid, title, scheduledate, start_time, end_time) VALUES (?, ?, ?, ?, ?)";
    $stmt = $database->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $database->error);
    }

    $stmt->bind_param("issss", $docid, $title, $date, $start_time, $end_time);
    $result = $stmt->execute();

    if ($result) {
        header("location: schedule.php?action=session-added&title=" . urlencode($title));
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $database->close();
}
?>