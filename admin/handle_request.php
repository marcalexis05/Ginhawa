<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'a') {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

if (isset($_GET['action']) && isset($_GET['id'])) {
    $request_id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'approve') {
        // Fetch the request details
        $request_query = $database->query("SELECT * FROM session_requests WHERE request_id='$request_id' AND status='pending'");
        if ($request_query->num_rows > 0) {
            $request = $request_query->fetch_assoc();
            $title = $request['title']; // Use the title provided by the doctor
            $docid = $request['docid'];
            $num_sessions = $request['num_sessions'];
            $session_date = $request['session_date'];
            $session_time = $request['session_time'];

            // Insert into schedule table with the doctor's title
            $insert_query = "INSERT INTO schedule (title, docid, scheduledate, scheduletime, nop) 
                             VALUES ('$title', '$docid', '$session_date', '$session_time', '$num_sessions')";
            if ($database->query($insert_query) === TRUE) {
                // Delete the request from session_requests after approval
                $delete_query = "DELETE FROM session_requests WHERE request_id='$request_id'";
                $database->query($delete_query);
            }
        }
    } elseif ($action == 'reject') {
        // Delete the request from session_requests
        $delete_query = "DELETE FROM session_requests WHERE request_id='$request_id' AND status='pending'";
        $database->query($delete_query);
    }

    // Redirect back to schedule.php
    header("location: schedule.php");
    exit;
} else {
    // Invalid request, redirect back
    header("location: schedule.php");
    exit;
}
?>