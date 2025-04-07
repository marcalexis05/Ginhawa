<?php
session_start();
include("../connection.php");
include("../email_helper.php"); // Include the email helper

// Check if the user is an admin and the request is a POST
if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'a' || !$_POST) {
    header("Location: ../login.php");
    exit();
}

// Sanitize input
$scheduleid = $database->real_escape_string($_POST['scheduleid']);
$gmeet_link = $database->real_escape_string($_POST['gmeet_link']);

// Validate Google Meet link format
if ($gmeet_link && !preg_match('/^https:\/\/meet\.google\.com\/[a-z]{3}-[a-z]{4}-[a-z]{3}$/', $gmeet_link)) {
    header("Location: schedule.php?action=edit&id=$scheduleid&error=Invalid Google Meet link format");
    exit();
}

// Update the Google Meet link in the schedule table
$sql = "UPDATE schedule SET gmeet_link = ? WHERE scheduleid = ?";
$stmt = $database->prepare($sql);
$stmt->bind_param("si", $gmeet_link, $scheduleid);

if ($stmt->execute()) {
    // Fetch session details for email content
    $session_query = $database->query("SELECT s.title, s.scheduledate, s.start_time, s.end_time, d.docemail, d.docname 
                                      FROM schedule s 
                                      INNER JOIN doctor d ON s.docid = d.docid 
                                      WHERE s.scheduleid = '$scheduleid'");
    $session = $session_query->fetch_assoc();

    $title = $session['title'];
    $scheduledate = $session['scheduledate'];
    $start_time = date('h:i A', strtotime($session['start_time']));
    $end_time = date('h:i A', strtotime($session['end_time']));
    $docemail = $session['docemail'];
    $docname = $session['docname'];

    // Fetch patient emails for the session
    $patient_query = $database->query("SELECT p.pemail, p.pname 
                                      FROM appointment a 
                                      INNER JOIN patient p ON a.pid = p.pid 
                                      WHERE a.scheduleid = '$scheduleid'");

    // Doctor email content
    $doctor_subject = "Updated Google Meet Link for Your Scheduled Session";
    $doctor_body = "
        <p>Dear Dr. $docname,</p>
        <p>We are pleased to inform you that the Google Meet link for your upcoming session has been updated. Below are the details:</p>
        <ul>
            <li><strong>Session Title:</strong> $title</li>
            <li><strong>Date:</strong> $scheduledate</li>
            <li><strong>Time:</strong> $start_time - $end_time</li>
            <li><strong>Google Meet Link:</strong> <a href='$gmeet_link'>$gmeet_link</a></li>
        </ul>
        <p>Please ensure you join the session at the scheduled time. If you have any questions, feel free to contact the administration.</p>
        <p>Best regards,<br>Ginhawa Administration Team</p>
    ";

    // Send email to doctor
    $doctor_email_sent = sendEmail($docemail, $doctor_subject, $doctor_body);

    // Patient email content and sending
    $patients_notified = 0;
    $patient_email_success = true;
    while ($patient = $patient_query->fetch_assoc()) {
        $pemail = $patient['pemail'];
        $pname = $patient['pname'];

        $patient_subject = "Updated Google Meet Link for Your Scheduled Appointment";
        $patient_body = "
            <p>Dear $pname,</p>
            <p>We are writing to inform you that the Google Meet link for your upcoming appointment has been updated. Below are the details:</p>
            <ul>
                <li><strong>Session Title:</strong> $title</li>
                <li><strong>Doctor:</strong> Dr. $docname</li>
                <li><strong>Date:</strong> $scheduledate</li>
                <li><strong>Time:</strong> $start_time - $end_time</li>
                <li><strong>Google Meet Link:</strong> <a href='$gmeet_link'>$gmeet_link</a></li>
            </ul>
            <p>Please ensure you join the session at the scheduled time. If you have any questions, feel free to contact us.</p>
            <p>Best regards,<br>Ginhawa Administration Team</p>
        ";

        if (sendEmail($pemail, $patient_subject, $patient_body)) {
            $patients_notified++;
        } else {
            $patient_email_success = false;
        }
    }

    // Determine success message
    if ($doctor_email_sent && $patient_email_success) {
        header("Location: schedule.php?success=Google Meet link updated and notifications sent successfully to doctor and $patients_notified patient(s)");
    } elseif ($doctor_email_sent) {
        header("Location: schedule.php?success=Google Meet link updated and doctor notified, but some patient notifications failed");
    } else {
        header("Location: schedule.php?action=edit&id=$scheduleid&error=Google Meet link updated, but email notifications failed");
    }
} else {
    header("Location: schedule.php?action=edit&id=$scheduleid&error=Failed to update Google Meet link: " . $stmt->error);
}

$stmt->close();
$database->close();
?>