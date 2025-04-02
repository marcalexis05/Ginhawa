<?php
session_start();

if (isset($_SESSION["user"])) {
    if (($_SESSION["user"] == "") || ($_SESSION['usertype'] != 'a')) {
        header("location: ../login.php");
        exit;
    }
} else {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

if ($_POST) {
    $id = $_POST['id00'];
    $oldemail = $_POST['oldemail'];
    $ptid = $_POST['ptid']; // Preserve existing ptid
    $name = $_POST['name'];
    $email = $_POST['email'];
    $tele = $_POST['Tele'];
    $spec = $_POST['spec'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Check if email is changed and already exists
    if ($email != $oldemail) {
        $result = $database->query("SELECT * FROM doctor WHERE docemail='$email'");
        if ($result->num_rows > 0) {
            header("location: doctors.php?action=edit&id=$id&error=1");
            exit;
        }
    }

    // Verify password confirmation
    if ($password == $cpassword) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update doctor table with existing ptid
        $sql = "UPDATE doctor SET ptid='$ptid', docname='$name', docemail='$email', doctel='$tele', specialties='$spec', docpassword='$hashed_password' WHERE docid='$id'";
        $database->query($sql);

        // Update webuser table if email changed
        if ($email != $oldemail) {
            $sql2 = "UPDATE webuser SET email='$email' WHERE email='$oldemail'";
            $database->query($sql2);
        }

        header("location: doctors.php?action=edit&id=$id&error=4");
        exit;
    } else {
        header("location: doctors.php?action=edit&id=$id&error=2");
        exit;
    }
} else {
    header("location: doctors.php?action=edit&id=$id&error=3");
    exit;
}
?>