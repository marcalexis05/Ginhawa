<?php
session_start();
include("../connection.php");

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'd' || !isset($_GET['id'])) {
    header("location: ../login.php");
    exit();
}

if (!$database) {
    header("Location: schedule.php?error=Database connection failed");
    exit();
}

$scheduleid = (int)$_GET['id'];
$docid = $_SESSION["docid"];

if (!$docid) {
    header("Location: schedule.php?error=Doctor ID not found in session");
    exit();
}

// Check if the session exists and belongs to the doctor
$check_sql = "SELECT * FROM schedule WHERE scheduleid = ? AND docid = ?";
$check_stmt = $database->prepare($check_sql);
$check_stmt->bind_param("ii", $scheduleid, $docid);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {
    header("Location: schedule.php?error=Session not found or you do not have permission to delete it");
    $check_stmt->close();
    $database->close();
    exit();
}
$check_stmt->close();

// Delete the session
$sql = "DELETE FROM schedule WHERE scheduleid = ? AND docid = ?";
$stmt = $database->prepare($sql);
$stmt->bind_param("ii", $scheduleid, $docid);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header("Location: schedule.php?success=Session cancelled successfully");
    } else {
        header("Location: schedule.php?error=No session was deleted");
    }
} else {
    header("Location: schedule.php?error=Failed to cancel session: " . $stmt->error);
}

$stmt->close();
$database->close();
?>