<?php
session_start();
include("../connection.php");

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'd') {
    header("location: ../login.php");
    exit();
}

if (($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['id'])) || 
    (isset($_GET['action']) && isset($_GET['id']))) {
    
    $action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];
    $request_id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

    if ($action == 'approve') {
        // Fetch the patient request
        $request_query = "SELECT * FROM patient_requests WHERE request_id = ?";
        $stmt = $database->prepare($request_query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();

        if (!$request) {
            header("Location: schedule.php?error=Invalid request ID");
            exit();
        }

        // Insert into schedule
        $insert_query = "INSERT INTO schedule (docid, title, scheduledate, start_time, end_time, gmeet_link) 
                         VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $database->prepare($insert_query);
        $gmeet_link = $request['gmeet_request'] ? 'pending' : NULL; // Placeholder for Google Meet link
        $stmt->bind_param("isssss", $request['doctor_id'], $request['title'], $request['session_date'], 
                          $request['start_time'], $request['end_time'], $gmeet_link);
        $stmt->execute();
        $schedule_id = $database->insert_id; // Get the newly inserted schedule ID

        // Update patient request status to approved
        $update_query = "UPDATE patient_requests SET status = 'approved' WHERE request_id = ?";
        $stmt = $database->prepare($update_query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();

        if ($request['gmeet_request']) {
            $notify_query = "INSERT INTO session_requests (docid, schedule_id, title, session_date, start_time, end_time, duration, gmeet_request, status, request_date) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
            $stmt = $database->prepare($notify_query);
            $duration = (strtotime($request['end_time']) - strtotime($request['start_time'])) / 60; // Duration in minutes
            $gmeet_request_flag = 1; // True for Google Meet request
            $stmt->bind_param("iissssii", $request['doctor_id'], $schedule_id, $request['title'], 
                              $request['session_date'], $request['start_time'], $request['end_time'], 
                              $duration, $gmeet_request_flag);
            $stmt->execute();
        }

        header("Location: schedule.php?success=Request approved successfully" . 
               ($request['gmeet_request'] ? " (Admin notified for Google Meet link)" : ""));
        exit();

    } elseif ($action == 'reject' && isset($_POST['rejection_reason'])) {
        // Update request status to rejected with reason
        $reason = $_POST['rejection_reason'];
        $update_query = "UPDATE patient_requests SET status = 'rejected', rejection_reason = ? WHERE request_id = ?";
        $stmt = $database->prepare($update_query);
        $stmt->bind_param("si", $reason, $request_id);
        $stmt->execute();

        header("Location: schedule.php?success=Request rejected successfully");
        exit();
    }
}

header("Location: schedule.php?error=Invalid request");
exit();
?>