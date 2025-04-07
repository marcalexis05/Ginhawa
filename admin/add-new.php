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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Doctor</title>
    <style>
        .popup { animation: transitionIn-Y-bottom 0.5s; }
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
include("../email_helper.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $tele = filter_input(INPUT_POST, 'full_tele', FILTER_SANITIZE_STRING);
    $spec = filter_input(INPUT_POST, 'spec', FILTER_SANITIZE_NUMBER_INT);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    error_log("Received POST data: " . print_r($_POST, true));

    if (empty($name) || empty($email) || empty($tele) || empty($spec) || empty($password) || empty($cpassword)) {
        error_log("Missing required fields for $email");
        header("location: doctors.php?action=add&error=7");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Invalid email format: $email");
        header("location: doctors.php?action=add&error=8");
        exit;
    }

    // Check email existence silently
    $stmt = $database->prepare("SELECT * FROM doctor WHERE docemail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        error_log("Email already registered in doctor table: $email");
        header("location: doctors.php?action=add&error=1"); // Silent redirect
        exit;
    }
    $stmt->close();

    $stmt = $database->prepare("SELECT * FROM webuser WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        error_log("Email already registered in webuser table: $email");
        header("location: doctors.php?action=add&error=9"); // Silent redirect
        exit;
    }
    $stmt->close();

    if ($password !== $cpassword) {
        error_log("Password confirmation failed for $email");
        header("location: doctors.php?action=add&error=2");
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    if ($hashed_password === false) {
        error_log("Password hashing failed for $email");
        header("location: doctors.php?action=add&error=6");
        exit;
    }

    $result = $database->query("SELECT COUNT(*) as total FROM doctor");
    if (!$result) {
        error_log("Failed to count doctors: " . $database->error);
        header("location: doctors.php?action=add&error=5");
        exit;
    }
    $row = $result->fetch_assoc();
    $next_id = $row['total'] + 1;
    $ptid = "PT" . str_pad($next_id, 3, "0", STR_PAD_LEFT);

    $stmt = $database->prepare("INSERT INTO doctor (ptid, docname, docemail, doctel, specialties, docpassword) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $ptid, $name, $email, $tele, $spec, $hashed_password);
    if (!$stmt->execute()) {
        error_log("Insert into doctor failed: " . $stmt->error);
        $stmt->close();
        header("location: doctors.php?action=add&error=5");
        exit;
    }
    $stmt->close();

    $stmt = $database->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'd')");
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        error_log("Insert into webuser failed: " . $stmt->error);
        $database->query("DELETE FROM doctor WHERE docemail='$email'");
        $stmt->close();
        header("location: doctors.php?action=add&error=5");
        exit;
    }
    $stmt->close();

    $stmt = $database->prepare("SELECT sname FROM specialties WHERE id = ?");
    $stmt->bind_param("i", $spec);
    $stmt->execute();
    $spec_result = $stmt->get_result();
    $spec_name = $spec_result->fetch_assoc()['sname'] ?? 'Unknown Specialty';
    $stmt->close();

    $subject = "Welcome to Ginhawa Mental Health - Account Creation";
    $body = "
    <html>
    <body>
        <p>Dear Dr. $name,</p>
        <p>We are pleased to inform you that your account with Ginhawa Mental Health has been successfully created. Below are your account details:</p>
        <ul>
            <li><strong>PT ID:</strong> $ptid</li>
            <li><strong>Email:</strong> $email</li>
            <li><strong>Telephone:</strong> $tele</li>
            <li><strong>Specialty:</strong> $spec_name</li>
            <li><strong>Temporary Password:</strong> $password</li>
        </ul>
        <p>Please log in to the Ginhawa Mental Health portal using the email and temporary password provided above. We recommend changing your password upon your first login for security purposes.</p>
        <p><a href='http://localhost/ginhawa/login.php'>Click here to log in</a></p>
        <p>If you have any questions or require assistance, feel free to contact our support team at <a href='mailto:support@ginhawamentalhealth.com'>support@ginhawamentalhealth.com</a>.</p>
        <p>We look forward to your valuable contribution to our community.</p>
        <p>Best regards,<br>
        The Ginhawa Mental Health Team</p>
    </body>
    </html>";

    if (sendEmail($email, $subject, $body)) {
        error_log("Email notification sent successfully to $email");
        echo "
        <script>
            Swal.fire({
                title: 'Success!',
                text: 'Doctor account created and email notification sent successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'doctors.php';
                }
            });
        </script>";
    } else {
        error_log("Email sending failed for $email");
        header("location: doctors.php?action=add&error=4");
        exit;
    }
} else {
    error_log("No POST data received");
    header("location: doctors.php?action=add&error=3");
    exit;
}
?>
</body>
</html>


