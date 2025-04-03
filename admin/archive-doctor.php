<?php
session_start();

// Check session and redirect if necessary
if (isset($_SESSION["user"])) {
    if (empty($_SESSION["user"]) || $_SESSION['usertype'] != 'a') {
        header("Location: ../login.php");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}

include("../connection.php");

// Check if required GET parameters are set
if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: doctors.php?error=missing_params");
    exit;
}

$id = $_GET['id'];
$status = $_GET['status'];

// Validate inputs
if (!is_numeric($id) || !in_array($status, ['0', '1'])) {
    header("Location: doctors.php?error=invalid_params");
    exit;
}

// Prepare and execute the update query
$sql = "UPDATE doctor SET archived = ? WHERE docid = ?";
$stmt = $database->prepare($sql);
if ($stmt === false) {
    header("Location: doctors.php?error=db_prepare_failed");
    exit;
}

$stmt->bind_param("ii", $status, $id);
$success = $stmt->execute();

if ($success) {
    $stmt->close();
    $database->close();
    header("Location: doctors.php?success=archive_updated");
} else {
    $stmt->close();
    $database->close();
    header("Location: doctors.php?error=archive_failed");
}
exit;
?>