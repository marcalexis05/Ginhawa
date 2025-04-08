<?php
include("../connection.php");

if(isset($_GET['pid'])) {
    $patient_id = $_GET['pid'];
    $patient_query = $database->query("SELECT pname FROM patient WHERE pid='$patient_id'");
    $patient = $patient_query->fetch_assoc();
    echo $patient['pname'] ?? 'Patient';
} else {
    echo 'Patient';
}
?>