<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="icon" href="../Images/G-icon.png">
        
    <title>Doctor</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
</style>
</head>
<body>
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
    $name = $_POST['name'];
    $email = $_POST['email'];
    $tele = $_POST['full_tele'];
    $spec = $_POST['spec'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Check if email already exists
    $result = $database->query("SELECT * FROM doctor WHERE docemail='$email'");
    if ($result->num_rows > 0) {
        header("location: doctors.php?action=add&error=1");
        exit;
    } else {
        // Verify password confirmation
        if ($password == $cpassword) {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Generate sequential ptid (e.g., PT001, PT002, etc.)
            $result = $database->query("SELECT COUNT(*) as total FROM doctor");
            $row = $result->fetch_assoc();
            $next_id = $row['total'] + 1;
            $ptid = "PT" . str_pad($next_id, 3, "0", STR_PAD_LEFT);

            // Insert into doctor table with ptid
            $sql1 = "INSERT INTO doctor (ptid, docname, docemail, doctel, specialties, docpassword) 
                     VALUES ('$ptid', '$name', '$email', '$tele', '$spec', '$hashed_password')";
            $database->query($sql1);

            // Insert into webuser table
            $sql2 = "INSERT INTO webuser (email, usertype) VALUES ('$email', 'd')";
            $database->query($sql2);

            header("location: doctors.php?action=add&error=4");
            exit;
        } else {
            header("location: doctors.php?action=add&error=2");
            exit;
        }
    }
} else {
    header("location: doctors.php?action=add&error=3");
    exit;
}
?>
   

</body>
</html>