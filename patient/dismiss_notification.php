<?php
session_start();
include("../connection.php");

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
    header("location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $useremail = $_SESSION["user"];

    $sql = "SELECT pid FROM patient WHERE pemail = ?";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $userrow = $stmt->get_result()->fetch_assoc();
    $userid = $userrow["pid"];

    $delete_query = "DELETE FROM patient_requests WHERE request_id = ? AND patient_id = ?";
    $stmt = $database->prepare($delete_query);
    $stmt->bind_param("ii", $request_id, $userid);
    $stmt->execute();

    echo "Success";
} else {
    http_response_code(400);
    echo "Invalid request";
}
?>