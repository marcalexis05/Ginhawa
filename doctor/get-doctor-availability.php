<?php
header('Content-Type: application/json');
include("../connection.php");
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $session_date = $_POST['session_date'];

    // Fetch booked sessions from schedule/appointment only (not patient_requests for doctor side)
    $query_booked = "SELECT s.scheduletime AS start_time, 
                            ADDTIME(s.scheduletime, SEC_TO_TIME(s.duration * 60)) AS end_time
                     FROM schedule s
                     WHERE s.docid = ? AND s.scheduledate = ?";
    $stmt_booked = $database->prepare($query_booked);
    $stmt_booked->bind_param("is", $doctor_id, $session_date);
    $stmt_booked->execute();
    $result_booked = $stmt_booked->get_result();

    $booked_slots = [];
    while ($row = $result_booked->fetch_assoc()) {
        $booked_slots[] = [
            'start_time' => substr($row['start_time'], 0, 5),
            'end_time' => substr($row['end_time'], 0, 5)
        ];
    }

    echo json_encode(['booked_slots' => $booked_slots]);
    $stmt_booked->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}
$database->close();
?>