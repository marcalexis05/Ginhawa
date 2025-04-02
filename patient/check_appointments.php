<?php
include("../connection.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $session_date = $_POST['session_date'];

    // Check if the patient has an appointment on the given date
    $sql = "SELECT COUNT(*) as count 
            FROM appointment 
            INNER JOIN schedule ON appointment.scheduleid = schedule.scheduleid 
            WHERE appointment.pid = ? AND schedule.scheduledate = ?";
    $stmt = $database->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Database prepare error: ' . $database->error]);
        exit;
    }
    $stmt->bind_param("is", $patient_id, $session_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo json_encode(['hasAppointment' => $row['count'] > 0]);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>