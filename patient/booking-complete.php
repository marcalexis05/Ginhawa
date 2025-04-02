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

// Fetch patient data
$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
if ($stmt === false) {
    die("Error preparing patient query: " . $database->error);
}
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$patient_name = $userfetch["pname"];
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["booknow"])) {
    // Define required fields based on appointment table
    $required_fields = ["apponum", "scheduleid", "date"];
    $missing_fields = [];

    // Check for missing fields
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        // Redirect back to schedule.php with error and scheduleid (if available)
        $error_message = "Missing required fields: " . implode(", ", $missing_fields);
        $redirect = "location: schedule.php?action=booking-failed&error=" . urlencode($error_message);
        if (isset($_POST['scheduleid'])) {
            $redirect .= "&scheduleid=" . urlencode($_POST['scheduleid']);
        }
        header($redirect);
        exit;
    }

    // Assign POST data
    $apponum = $_POST["apponum"];
    $scheduleid = $_POST["scheduleid"];
    $date = $_POST["date"]; // This is appodate

    // Insert appointment into database
    $sql2 = "INSERT INTO appointment (pid, apponum, scheduleid, appodate) 
             VALUES (?, ?, ?, ?)";
    $stmt = $database->prepare($sql2);
    if ($stmt === false) {
        die("Error preparing appointment query: " . $database->error);
    }
    $stmt->bind_param("iiis", $userid, $apponum, $scheduleid, $date);
    if (!$stmt->execute()) {
        die("Error executing appointment query: " . $stmt->error);
    }
    $stmt->close();

    header("location: appointment.php?action=booking-added&id=" . $apponum . "&titleget=none");
    exit;
} else {
    header("location: schedule.php?action=invalid-request");
    exit;
}
?>