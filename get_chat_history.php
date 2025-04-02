<?php
session_start();
include("connection.php");

header('Content-Type: application/json');

$patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : null;
$doctor_id = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : null;

if (!$patient_id || !$doctor_id) {
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

// Ensure the request is from an authorized user
if (($_SESSION['usertype'] === 'p' && $patient_id !== (int)$_SESSION['pid']) ||
    ($_SESSION['usertype'] === 'd' && $doctor_id !== (int)$_SESSION['doctor_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$sql = "SELECT sender_id, receiver_id, message, timestamp, sender_type 
        FROM chat_messages 
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) 
        ORDER BY timestamp ASC";
$stmt = $database->prepare($sql);
$stmt->bind_param("iiii", $patient_id, $doctor_id, $doctor_id, $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'senderId' => (int)$row['sender_id'],
        'receiverId' => (int)$row['receiver_id'],
        'message' => $row['message'],
        'timestamp' => $row['timestamp'],
        'senderType' => $row['sender_type']
    ];
}

echo json_encode($messages);
$stmt->close();
$database->close();
?>