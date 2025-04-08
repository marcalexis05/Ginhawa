<?php
session_start();
include("../connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $session_date = $_POST['session_date'];
    $start_time = $_POST['start_time'];
    $duration = $_POST['duration'];
    $gmeet_request = isset($_POST['gmeet_request']) ? 1 : 0;
    $docid = $_POST['docid'];

    // Calculate end time
    $start = DateTime::createFromFormat('H:i:s', $start_time);
    $end = clone $start;
    $end->modify("+$duration minutes");
    $end_time = $end->format('H:i:s');

    // Insert the session request into the database (example table: session_requests)
    $sql = "INSERT INTO session_requests (title, session_date, start_time, end_time, gmeet_request, docid, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("ssssii", $title, $session_date, $start_time, $end_time, $gmeet_request, $docid);
    
    if ($stmt->execute()) {
        // Redirect to schedule.php with a success message
        header("Location: schedule.php?success=Session request submitted successfully!");
        exit();
    } else {
        // Redirect with an error message if the insertion fails
        header("Location: schedule.php?error=Failed to submit session request. Please try again.");
        exit();
    }
} else {
    // Redirect with an error if the request method is not POST
    header("Location: schedule.php?error=Invalid request method.");
    exit();
}
?>