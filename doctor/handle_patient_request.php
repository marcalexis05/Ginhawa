<?php
session_start();
include("../connection.php");

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'd') {
    header("location: ../login.php");
    exit();
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $request_id = $_GET['id'];
    $useremail = $_SESSION["user"];
    $userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
    $userfetch = $userrow->fetch_assoc();
    $doctor_id = $userfetch["docid"];

    // Verify the request belongs to this doctor
    $check_query = "SELECT * FROM patient_requests WHERE request_id = $request_id AND doctor_id = $doctor_id";
    $check_result = $database->query($check_query);

    if ($check_result->num_rows > 0) {
        $request = $check_result->fetch_assoc();
        
        if ($action == 'approve') {
            // Update patient request status
            $update_query = "UPDATE patient_requests SET status = 'approved' WHERE request_id = $request_id";
            $database->query($update_query);

            // Insert notification for admin
            $admin_notification = "INSERT INTO admin_notifications (message, status, created_at) 
                                  VALUES ('Doctor approved patient request: " . $request['title'] . " on " . $request['session_date'] . " at " . $request['session_time'] . "', 'unread', NOW())";
            $database->query($admin_notification);

            header("location: schedule.php?success=Request approved successfully");
        } elseif ($action == 'reject') {
            $update_query = "UPDATE patient_requests SET status = 'rejected' WHERE request_id = $request_id";
            $database->query($update_query);
            header("location: schedule.php?success=Request rejected successfully");
        }
    } else {
        header("location: schedule.php?error=Invalid request");
    }
} else {
    header("location: schedule.php?error=Invalid action");
}
?>