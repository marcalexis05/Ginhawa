<?php
session_start();

require 'C:/xampp/htdocs/Ginhawa/PHPMailer/src/PHPMailer.php';
require 'C:/xampp/htdocs/Ginhawa/PHPMailer/src/SMTP.php';
require 'C:/xampp/htdocs/Ginhawa/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_SESSION["user"])) {
    if (empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit;
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

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
    $meetCode = substr(str_shuffle("ooe-hytx-obg"), 0, 3) . "-" . 
                substr(str_shuffle("ghq-tunk-jes"), 0, 4) . "-" . 
                substr(str_shuffle("xjj-tado-vrt"), 0, 3);
    $gmeet_link = "https://meet.google.com/" . $meetCode;

    // Insert appointment into database
    $sql2 = "INSERT INTO appointment (pid, apponum, scheduleid, appodate, scheduledate, start_time, title, docname, patient_email, patient_name, gmeet_link) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $database->prepare($sql2);
    $stmt->bind_param("iiissssssss", $userid, $apponum, $scheduleid, $date, $scheduledate, $start_time, $title, $docname, $patient_email, $patient_name, $gmeet_link);
    $stmt->execute();

    // Send email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ginhawamentalhealth@gmail.com';
        $mail->Password = 'oetk pjfs bcvi uswm';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('ginhawamentalhealth@gmail.com', 'Session Coordinator');
        $mail->addAddress($patient_email);

        $mail->isHTML(true);
        $mail->Subject = "Your Upcoming Session Google Meet Link";
        $mail->Body = "Dear $patient_name,<br><br>You have successfully booked a session '$title' with Dr. $docname.<br><br>Details:<br>- Date: $scheduledate<br>- Time: $start_time<br>- Join here: <a href='$gmeet_link'>$gmeet_link</a><br><br>You will receive a reminder email when the session starts.<br><br>Regards,<br>Session Coordinator";

        $mail->send();
        error_log("Immediate email sent to $patient_email for booking $title");
    } catch (Exception $e) {
        error_log("Failed to send immediate email to $patient_email: " . $mail->ErrorInfo);
    }

    header("location: appointment.php?action=booking-added&id=" . $apponum . "&titleget=none");
    exit;
}
?>