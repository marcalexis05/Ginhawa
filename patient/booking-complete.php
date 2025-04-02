<?php
session_start();

require 'C:/xampp/htdocs/Ginhawa/PHPMailer/src/PHPMailer.php';
require 'C:/xampp/htdocs/Ginhawa/PHPMailer/src/SMTP.php';
require 'C:/xampp/htdocs/Ginhawa/PHPMailer/src/Exception.php';
require 'C:/xampp/htdocs/Ginhawa/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

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

    // Set up Google Client
    $client = new Client();
    $client->setApplicationName('Ginhawa Meet');
    $client->setScopes([Calendar::CALENDAR]);
    $client->setAuthConfig('C:/xampp/htdocs/Ginhawa/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    $client->setRedirectUri('http://localhost/Ginhawa/patient/callback.php'); // Must match Google Cloud Console

    // Load previously authorized token, if it exists
    $tokenPath = 'C:/xampp/htdocs/Ginhawa/token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // Check if token is expired or missing
    if ($client->isAccessTokenExpired() || !$client->getAccessToken()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        } else {
            $authUrl = $client->createAuthUrl();
            header("Location: $authUrl");
            exit;
        }
    }

    // Create Google Calendar event with Google Meet link
    $service = new Calendar($client);

    $event = new Event([
        'summary' => $title,
        'description' => "Session with Dr. $docname for $patient_name",
        'start' => [
            'dateTime' => "$scheduledate" . "T$start_time:00+08:00",
            'timeZone' => 'Asia/Manila',
        ],
        'end' => [
            'dateTime' => "$scheduledate" . "T" . date("H:i", strtotime($start_time) + 3600) . ":00+08:00",
            'timeZone' => 'Asia/Manila',
        ],
        'attendees' => [
            ['email' => $patient_email],
            ['email' => 'ginhawamentalhealth@gmail.com'],
        ],
        'conferenceData' => [
            'createRequest' => [
                'requestId' => uniqid(),
                'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
            ],
        ],
    ]);

    $calendarId = 'primary';
    $event = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);
    $gmeet_link = $event->hangoutLink;

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