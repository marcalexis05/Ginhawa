<?php
session_start();

// Check if the user is logged in and is a patient
if (!isset($_SESSION["user"]) || empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
    header("location: ../login.php");
    exit;
}

if ($_GET && isset($_GET["id"])) {
    // Import database connection
    include("../connection.php");
    
    $id = $_GET["id"];
    
    // Prepare and execute the delete query
    $sqlmain = "DELETE FROM appointment WHERE appoid = ?";
    $stmt = $database->prepare($sqlmain);
    if ($stmt === false) {
        die("Prepare failed: " . $database->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        // Redirect back to appointment.php with a success message
        header("location: appointment.php?action=deleted&id=$id");
    } else {
        // Redirect back with an error message if deletion failed
        header("location: appointment.php?action=error");
    }
    
    $stmt->close();
    $database->close();
} else {
    // Redirect if no ID is provided
    header("location: appointment.php");
}
?>