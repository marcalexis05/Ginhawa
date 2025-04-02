<?php
session_start();
include("../connection.php");

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'd' || !isset($_GET["id"])) {
    header("location: ../login.php");
    exit();
}

$id = $_GET["id"];
$userid = $database->query("SELECT docid FROM doctor WHERE docemail='".$_SESSION["user"]."'")->fetch_assoc()["docid"];

$sql = $database->query("DELETE FROM schedule WHERE scheduleid='$id' AND docid='$userid'");
if ($sql) {
    header("location: schedule.php?success=Session deleted successfully");
} else {
    header("location: schedule.php?error=Failed to delete session: " . $database->error);
}
?>