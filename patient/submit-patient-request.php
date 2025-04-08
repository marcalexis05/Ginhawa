<?php
session_start();
include("../connection.php");

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
    header("location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = $database->real_escape_string($_POST["description"]);
    $session_date = $database->real_escape_string($_POST["session_date"]);
    $start_time = $database->real_escape_string($_POST["start_time"]);
    $duration = (int)$_POST["duration"];
    $gmeet_request = isset($_POST["gmeet_request"]) ? 1 : 0;
    $patient_id = (int)$_POST["patient_id"];
    $doctor_id = (int)$_POST["doctor_id"];

    // Calculate end_time
    $start_datetime = DateTime::createFromFormat('H:i:s', $start_time);
    if ($start_datetime === false) {
        header("Location: doctors.php?error=Invalid start time format");
        exit();
    }
    $end_datetime = clone $start_datetime;
    $total_minutes = $start_datetime->format('H') * 60 + $start_datetime->format('i') + $duration;
    $break_start = 12 * 60; // 12:00 PM
    $break_end = 13 * 60;   // 1:00 PM
    if ($total_minutes > $break_start && ($start_datetime->format('H') * 60 + $start_datetime->format('i')) < $break_end) {
        $total_minutes += ($break_end - $break_start); // Add break time
    }
    $end_datetime->setTime(floor($total_minutes / 60), $total_minutes % 60);
    $end_time = $end_datetime->format('H:i:s');

    // Validate time constraints
    $start_hour = (int)$start_datetime->format('H');
    $end_hour = (int)$end_datetime->format('H');
    if ($start_hour < 8 || $end_hour >= 18) {
        header("Location: doctors.php?error=Session must be between 08:00 and 18:00");
        exit();
    }

    // Insert into patient_requests table
    $sql = "INSERT INTO patient_requests (patient_id, doctor_id, description, session_date, start_time, end_time, duration, gmeet_request, request_date, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("iissssii", $patient_id, $doctor_id, $description, $session_date, $start_time, $end_time, $duration, $gmeet_request);

    if ($stmt->execute()) {
        header("Location: doctors.php?success=Session request submitted successfully");
    } else {
        header("Location: doctors.php?error=Failed to submit request: " . $stmt->error);
    }

    $stmt->close();
} else {
    header("Location: doctors.php?error=Invalid request method");
}

$database->close();
?>