<?php
include("../connection.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $session_date = $_POST['session_date'];

    // Fetch appointment details including session time and doctor info
    $sql = "SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, 
            schedule.scheduledate, schedule.start_time, patient.pemail, patient.pname 
            FROM appointment 
            INNER JOIN schedule ON appointment.scheduleid = schedule.scheduleid 
            INNER JOIN patient ON appointment.pid = patient.pid 
            INNER JOIN doctor ON schedule.docid = doctor.docid 
            WHERE appointment.pid = ? AND schedule.scheduledate = ?";
    $stmt = $database->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Database prepare error: ' . $database->error]);
        exit;
    }
    $stmt->bind_param("is", $patient_id, $session_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $appointments = [];
        date_default_timezone_set('Asia/Kolkata');
        $current_datetime = date('Y-m-d H:i');

        while ($row = $result->fetch_assoc()) {
            $session_datetime = $row['scheduledate'] . ' ' . substr($row['start_time'], 0, 5);
            $appointments[] = [
                'appoid' => $row['appoid'],
                'title' => $row['title'],
                'docname' => $row['docname'],
                'scheduledate' => $row['scheduledate'],
                'start_time' => $row['start_time'],
                'patient_email' => $row['pemail'],
                'patient_name' => $row['pname'],
                'is_now' => ($current_datetime === $session_datetime)
            ];
        }
        echo json_encode(['hasAppointment' => true, 'appointments' => $appointments]);
    } else {
        echo json_encode(['hasAppointment' => false]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>