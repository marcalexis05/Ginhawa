<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['usertype'] != 'p') {
    header("Location: login.php");
    exit;
}

include("connection.php");

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['user'];
    $code = $_POST['verification_code'];

    $stmt = $database->prepare("SELECT * FROM patient WHERE pemail = ? AND verification_code = ? AND code_expiry > NOW()");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $stmt = $database->prepare("UPDATE patient SET verification_code = NULL, code_expiry = NULL WHERE pemail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        header("Location: patient/index.php");
        exit;
    } else {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Invalid or expired verification code.</label>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/signup.css">
    <link rel="icon" href="../Images/G-icon.png">
    <title>Verify Your Account</title>
</head>
<body>
<center>
    <div class="container">
        <table border="0" style="width: 69%;">
            <tr>
                <td colspan="2">
                    <p class="header-text">Verify Your Account</p>
                    <p class="sub-text">Enter the verification code sent to your email</p>
                </td>
            </tr>
            <tr>
                <form action="" method="POST">
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="verification_code" class="form-label">Verification Code: </label>
                        <input type="text" name="verification_code" class="input-text" placeholder="Enter 6-digit code" required maxlength="6">
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo $error; ?></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" value="Verify" class="login-btn btn-primary btn">
                    </td>
                </tr>
                </form>
            </tr>
        </table>
    </div>
</center>
</body>
</html>