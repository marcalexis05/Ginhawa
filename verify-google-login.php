<?php
session_start();

include("connection.php");

$error = '';
$success = '';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    if (isset($_SESSION['google_email']) && isset($_SESSION['google_token']) &&
        $_SESSION['google_email'] === $email && $_SESSION['google_token'] === $token) {
        
        $stmt = $database->prepare("SELECT * FROM patient WHERE pemail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $patient = $result->fetch_assoc();
            $_SESSION['user'] = $email;
            $_SESSION['usertype'] = 'p';
            $_SESSION['username'] = explode(" ", $patient['pname'])[0];

            unset($_SESSION['google_email']);
            unset($_SESSION['google_token']);

            $success = "Email verified successfully! Redirecting to your dashboard...";
            header("Refresh: 2; url=patient/index.php");
        } else {
            $error = "No patient account found for this email.";
        }
    } else {
        $error = "Invalid or expired verification link.";
    }
} else {
    $error = "Invalid request. Please use the link from your email.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <title>Verify Google Login</title>
</head>
<body>
    <center>
        <div class="container">
            <h2>Verify Google Sign-In</h2>
            <?php if ($error): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p style="color: green;"><?php echo $success; ?></p>
            <?php endif; ?>
            <?php if (!$success): ?>
                <p>Please check your email for the verification link or <a href="login.php">try logging in again</a>.</p>
            <?php endif; ?>
        </div>
    </center>
</body>
</html>