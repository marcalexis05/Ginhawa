<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_SESSION["user"])) {
    if (empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit;
    } else {
        $useremail = $_SESSION["user"]; // Logged-in patient's Gmail
    }
} else {
    header("location: ../login.php");
    exit;
}

include("../connection.php");
require_once '../vendor/autoload.php'; // Only PHPMailer is needed

$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$patient_name = $userfetch["pname"];

if ($_POST && isset($_POST["booknow"])) {
    $apponum = $_POST["apponum"];
    $scheduleid = $_POST["scheduleid"];
    $date = $_POST["date"];
    $scheduledate = $_POST["scheduledate"];
    $start_time = $_POST["start_time"];
    $title = $_POST["title"];
    $docname = $_POST["docname"];
    $patient_email = $useremail;

    // Generate a simple Google Meet link
    $meetCode = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 3) . "-" . 
                substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 4) . "-" . 
                substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 3); // e.g., "abc-defg-hij"
    $gmeet_link = "https://meet.google.com/" . $meetCode;

    // Insert appointment into database with the Meet link
    $sql2 = "INSERT INTO appointment (pid, apponum, scheduleid, appodate, scheduledate, start_time, title, docname, patient_email, patient_name, gmeet_link) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $database->prepare($sql2);
    $stmt->bind_param("iiissssssss", $userid, $apponum, $scheduleid, $date, $scheduledate, $start_time, $title, $docname, $patient_email, $patient_name, $gmeet_link);
    $stmt->execute();

    // Send immediate email to patient's Gmail using PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com'; // Replace with your Gmail
        $mail->Password = 'your_app_password'; // Replace with your App Password
        $mail->SMTPSecure = 'tls';
        $mail->setFrom('your_email@gmail.com', 'Session Coordinator');
        $mail->addAddress($patient_email);
        $mail->Subject = "Your Upcoming Session Google Meet Link";
        $mail->Body = "Dear $patient_name,\n\nYou have successfully booked a session '$title' with Dr. $docname.\n\nDetails:\n- Date: $scheduledate\n- Time: $start_time\n- Join here: $gmeet_link\n\nYou will receive a reminder email when the session starts.\n\nRegards,\nSession Coordinator";

        $mail->send();
        error_log("Immediate email sent to $patient_email for booking $title");
    } catch (Exception $e) {
        error_log("Failed to send immediate email to $patient_email: " . $mail->ErrorInfo);
    }

    header("location: appointment.php?action=booking-added&id=" . $apponum . "&titleget=none");
    exit;
}
?>