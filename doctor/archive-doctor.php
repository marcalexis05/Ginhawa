<?php
session_start();

if (!isset($_SESSION["user"]) || ($_SESSION['usertype'] != 'a' && $_SESSION['usertype'] != 'd')) {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $database->real_escape_string($_GET['id']);
    $status = $database->real_escape_string($_GET['status']);

    if (!in_array($status, ['0', '1'])) {
        header("location: doctors.php?error=invalid_params");
        exit;
    }

    $query = "UPDATE doctor SET archived = ? WHERE docid = ?";
    $stmt = $database->prepare($query);
    if ($stmt === false) {
        header("location: doctors.php?error=db_prepare_failed");
        exit;
    }

    $stmt->bind_param("ii", $status, $id);
    if ($stmt->execute()) {
        if ($_SESSION['usertype'] == 'd') {
            // If doctor archives their own account, log them out
            session_destroy();
            header("location: ../login.php?success=account_archived");
        } else {
            // Admin action
            header("location: doctors.php?success=archive_updated");
        }
    } else {
        header("location: doctors.php?error=archive_failed");
    }

    $stmt->close();
} else {
    header("location: doctors.php?error=missing_params");
}

$database->close();
?>