<?php
session_start();
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
require_once '../vendor/autoload.php'; // Load Google API Client Library

$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];

if ($_POST && isset($_POST["booknow"])) {
    $apponum = $_POST["apponum"];
    $scheduleid = $_POST["scheduleid"];
    $date = $_POST["date"];
    $scheduledate = $_POST["scheduledate"];
    $start_time = $_POST["start_time"];
    $title = $_POST["title"];
    $docname = $_POST["docname"];
    $patient_email = $_POST["patient_email"];
    $patient_name = $_POST["patient_name"];

    // Insert appointment into database
    $sql2 = "INSERT INTO appointment (pid, apponum, scheduleid, appodate, scheduledate, start_time, title, docname, patient_email, patient_name) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $database->prepare($sql2);
    $stmt->bind_param("iiisssssss", $userid, $apponum, $scheduleid, $date, $scheduledate, $start_time, $title, $docname, $patient_email, $patient_name);
    $stmt->execute();

    // Google Calendar API setup
    $client = new Google_Client();
    $client->setAuthConfig('/path/to/service-account-key.json'); // Replace with your JSON key file path
    $client->addScope(Google_Service_Calendar::CALENDAR);
    $client->setSubject($patient_email); // Impersonate the patient (requires domain-wide delegation in Workspace)

    $service = new Google_Service_Calendar($client);

    // Create event with Google Meet link
    $start_datetime = new DateTime("$scheduledate $start_time", new DateTimeZone('Asia/Kolkata'));
    $end_datetime = clone $start_datetime;
    $end_datetime->modify('+1 hour'); // Adjust duration as needed

    $event = new Google_Service_Calendar_Event([
        'summary' => $title,
        'description' => "Session with Dr. $docname",
        'start' => [
            'dateTime' => $start_datetime->format(DateTime::RFC3339),
            'timeZone' => 'Asia/Kolkata',
        ],
        'end' => [
            'dateTime' => $end_datetime->format(DateTime::RFC3339),
            'timeZone' => 'Asia/Kolkata',
        ],
        'attendees' => [
            ['email' => $patient_email],
            // Doctor email can be added later when extending to doctors
        ],
        'conferenceData' => [
            'createRequest' => [
                'requestId' => uniqid(),
                'conferenceSolutionKey' => [
                    'type' => 'hangoutsMeet'
                ]
            ]
        ]
    ]);

    $calendarId = 'primary'; // Patient's primary calendar
    $event = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);
    $gmeet_link = $event->getHangoutLink();

    // Store the Meet link in the database
    $sql_update = "UPDATE appointment SET gmeet_link = ? WHERE pid = ? AND scheduleid = ? AND apponum = ?";
    $stmt = $database->prepare($sql_update);
    $stmt->bind_param("siii", $gmeet_link, $userid, $scheduleid, $apponum);
    $stmt->execute();

    header("location: appointment.php?action=booking-added&id=" . $apponum . "&titleget=none");
    exit;
}
?>