<?php
session_start();

// Check if the user is logged in and has the correct user type
if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'd') {
    header("location: ../login.php");
    exit();
}

include("../connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['id'])) {
    $action = $_POST['action'];
    $request_id = $_POST['id'];

    // Fetch the request details
    $request_query = "SELECT * FROM patient_requests WHERE request_id = $request_id AND doctor_id = {$_SESSION['docid']} AND status = 'pending'";
    $request_result = $database->query($request_query);

    if ($request_result->num_rows == 0) {
        header("location: schedule.php?error=Invalid request");
        exit();
    }

    $request = $request_result->fetch_assoc();
    $patient_id = $request['patient_id'];
    $doctor_id = $request['doctor_id'];
    $session_date = $request['session_date'];
    $start_time = $request['start_time'];
    $duration = $request['duration'];
    $gmeet_request = $request['gmeet_request'];

    if ($action == 'approve') {
        // Ensure a title is provided
        if (!isset($_POST['title']) || empty(trim($_POST['title']))) {
            header("location: schedule.php?error=Session title is required");
            exit();
        }

        $title = trim($_POST['title']);

        // Calculate end time based on duration
        $start_datetime = DateTime::createFromFormat('H:i:s', $start_time);
        $end_datetime = clone $start_datetime;
        $end_datetime->modify("+$duration minutes");
        $end_time = $end_datetime->format('H:i:s');

        // Insert the session into the schedule table
        $insert_schedule_query = "INSERT INTO schedule (title, docid, scheduledate, start_time, end_time, gmeet_link) 
                                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $database->prepare($insert_schedule_query);
        if (!$stmt) {
            header("location: schedule.php?error=Failed to prepare schedule insert query: " . $database->error);
            exit();
        }
        $gmeet_link = $gmeet_request ? '' : NULL; // Placeholder for Google Meet link (to be set by admin if requested)
        $stmt->bind_param("sissss", $title, $doctor_id, $session_date, $start_time, $end_time, $gmeet_link);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $schedule_id = $stmt->insert_id;

            // Update the patient request status to approved
            $update_request_query = "UPDATE patient_requests SET status = 'approved' WHERE request_id = $request_id";
            $database->query($update_request_query);

            // Create an appointment for the patient
            $insert_appointment_query = "INSERT INTO appointment (pid, scheduleid, appodate, appotime) 
                                        VALUES (?, ?, ?, ?)";
            $stmt = $database->prepare($insert_appointment_query);
            $stmt->bind_param("iiss", $patient_id, $schedule_id, $session_date, $start_time);
            $stmt->execute();

            header("location: schedule.php?success=Request approved successfully");
        } else {
            header("location: schedule.php?error=Failed to create session");
        }
        $stmt->close();
    } elseif ($action == 'reject') {
        // Handle rejection (unchanged)
        $rejection_reason = isset($_POST['rejection_reason']) ? $_POST['rejection_reason'] : 'No reason provided';
        $update_query = "UPDATE patient_requests SET status = 'rejected', rejection_reason = ? WHERE request_id = ?";
        $stmt = $database->prepare($update_query);
        $stmt->bind_param("si", $rejection_reason, $request_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header("location: schedule.php?success=Request rejected successfully");
        } else {
            header("location: schedule.php?error=Failed to reject request");
        }
        $stmt->close();
    }
} else {
    header("location: schedule.php?error=Invalid request");
}
?>