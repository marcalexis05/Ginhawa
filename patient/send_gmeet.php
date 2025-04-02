<?php
include("../connection.php");
date_default_timezone_set('Asia/Kolkata');
$current_datetime = date('Y-m-d H:i');

$sql = "SELECT patient_email, patient_name, scheduledate, start_time, title, docname, gmeet_link 
        FROM appointment 
        WHERE CONCAT(scheduledate, ' ', LEFT(start_time, 5)) = ? AND gmeet_link IS NOT NULL";
$stmt = $database->prepare($sql);
$stmt->bind_param("s", $current_datetime);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $patient_email = $row["patient_email"];
    $patient_name = $row["patient_name"];
    $scheduledate = $row["scheduledate"];
    $start_time = $row["start_time"];
    $title = $row["title"];
    $docname = $row["docname"];
    $gmeet_link = $row["gmeet_link"];

    $subject = "Your Session Google Meet Link";
    $body = "Dear $patient_name,\n\nYour session '$title' with Dr. $docname is starting now on $scheduledate at $start_time.\nJoin here: $gmeet_link\n\nRegards,\nSession Coordinator";
    $headers = "From: your_email@gmail.com\r\nContent-Type: text/plain; charset=UTF-8\r\n";

    if (mail($patient_email, $subject, $body, $headers)) {
        error_log("Email sent to $patient_email for session $title at $current_datetime");
    } else {
        error_log("Failed to send email to $patient_email for session $title at $current_datetime");
    }
}
?>