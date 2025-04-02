<?php
session_start();
include("connection.php");
include("email_helper.php");

if (!isset($_SESSION['user'])) {
    header("Location: signup.php");
    exit;
}

$error = "";
$success = "";
$email = $_SESSION['user'];

// Track failed attempts
if (!isset($_SESSION['failed_attempts'])) {
    $_SESSION['failed_attempts'] = 0;
}

// Check if this is the first load (no previous code sent) and send initial code
if (!isset($_SESSION['code_sent'])) {
    $verificationCode = sprintf("%06d", mt_rand(100000, 999999));
    $codeExpiry = date('Y-m-d H:i:s', strtotime('+100 seconds'));

    $sql_update = "UPDATE patient SET verification_code=?, code_expiry=? WHERE pemail=?";
    $stmt_update = $database->prepare($sql_update);
    $stmt_update->bind_param("sss", $verificationCode, $codeExpiry, $email);
    $stmt_update->execute();

    $subject = "Account Verification - Ginhawa Mental Health";
    $body = "
        <h2>Welcome to Ginhawa Mental Health</h2>
        <p>Your verification code is: <strong>$verificationCode</strong></p>
        <p>This code will expire in 100 seconds. Please verify your account promptly.</p>
    ";
    sendEmail($email, $subject, $body);
    $_SESSION['code_sent'] = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_POST['verify'])) {
        $enteredCode = trim($_POST['verification_code']);
        
        $sql = "SELECT verification_code, code_expiry FROM patient WHERE pemail=?";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $patient = $result->fetch_assoc();

        if ($patient) {
            $currentTime = new DateTime();
            $expiryTime = new DateTime($patient['code_expiry']);
            
            if ($currentTime > $expiryTime) {
                echo json_encode(['success' => false, 'message' => 'Verification code has expired. Please request a new one.']);
            } elseif ($enteredCode === $patient['verification_code']) {
                $sql_update = "UPDATE patient SET verification_code=NULL, code_expiry=NULL WHERE pemail=?";
                $stmt_update = $database->prepare($sql_update);
                $stmt_update->bind_param("s", $email);
                $stmt_update->execute();

                $sql_webuser = "INSERT INTO webuser (email, usertype) VALUES (?, 'p')";
                $stmt_webuser = $database->prepare($sql_webuser);
                $stmt_webuser->bind_param("s", $email);
                $stmt_webuser->execute();

                echo json_encode(['success' => true, 'message' => 'Account verified successfully!']);
                unset($_SESSION['code_sent']);
                unset($_SESSION['failed_attempts']);
            } else {
                $_SESSION['failed_attempts']++;
                echo json_encode(['success' => false, 'message' => "Invalid verification code. Attempt {$_SESSION['failed_attempts']} of 3."]);
            }
        }
        exit;
    } elseif (isset($_POST['resend'])) {
        $sql = "SELECT code_expiry FROM patient WHERE pemail=?";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $patient = $result->fetch_assoc();

        $currentTime = new DateTime();
        $expiryTime = new DateTime($patient['code_expiry']);
        
        if ($currentTime <= $expiryTime && $_SESSION['failed_attempts'] < 3) {
            echo json_encode(['success' => false, 'message' => 'Please wait until the current code expires or reach 3 failed attempts.']);
        } else {
            $verificationCode = sprintf("%06d", mt_rand(100000, 999999));
            $codeExpiry = date('Y-m-d H:i:s', strtotime('+100 seconds'));

            $sql_update = "UPDATE patient SET verification_code=?, code_expiry=? WHERE pemail=?";
            $stmt_update = $database->prepare($sql_update);
            $stmt_update->bind_param("sss", $verificationCode, $codeExpiry, $email);
            $stmt_update->execute();

            $subject = "Account Verification - Ginhawa Mental Health (New Code)";
            $body = "
                <h2>Welcome to Ginhawa Mental Health</h2>
                <p>Your verification code is: <strong>$verificationCode</strong></p>
                <p>This code will expire in 100 seconds. Please verify your account promptly.</p>
            ";
            sendEmail($email, $subject, $body);
            $_SESSION['failed_attempts'] = 0;
            echo json_encode(['success' => true, 'message' => 'New verification code sent successfully!']);
        }
        exit;
    }
}

// Get current code expiry for initial page load
$sql = "SELECT code_expiry FROM patient WHERE pemail=?";
$stmt = $database->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$initialTimeLeft = $patient && $patient['code_expiry'] ? (strtotime($patient['code_expiry']) - time()) : 100;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/signup.css">
    <link rel="icon" href="../Images/G-icon.png">
    <title>Verify Account</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .countdown { font-size: 1.2em; color: #ff3e3e; margin-top: 10px; }
        .resend-btn:disabled { background-color: #cccccc; cursor: not-allowed; }
    </style>
</head>
<body>
<center>
    <div class="container">
        <table border="0" style="width: 69%;">
            <tr>
                <td colspan="2">
                    <p class="header-text">Verify Your Account</p>
                    <p class="sub-text">Enter the 6-digit code sent to your email</p>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <label for="verification_code" class="form-label">Verification Code: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <input type="text" id="verification_code" class="input-text" placeholder="Enter 6-digit code" maxlength="6" required>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <div id="countdown" class="countdown"></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div id="message"></div>
                </td>
            </tr>
            <tr>
                <td>
                    <button id="verifyBtn" class="login-btn btn-primary btn">Verify</button>
                </td>
                <td>
                    <button id="resendBtn" class="login-btn btn-primary-soft btn resend-btn">Resend Code</button>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <br>
                    <a href="signup.php" class="hover-link1 non-style-link">Back to Sign Up</a>
                    <br><br><br>
                </td>
            </tr>
        </table>
    </div>
</center>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let timeLeft = <?php echo $initialTimeLeft > 0 ? $initialTimeLeft : 0; ?>;
    const countdownElement = document.getElementById('countdown');
    const resendBtn = document.getElementById('resendBtn');
    const verifyBtn = document.getElementById('verifyBtn');
    const messageElement = document.getElementById('message');
    let failedAttempts = <?php echo $_SESSION['failed_attempts']; ?>;

    function updateCountdown() {
        if (timeLeft > 0) {
            countdownElement.textContent = `Code expires in ${timeLeft} seconds`;
            resendBtn.disabled = failedAttempts < 3;
            timeLeft--;
        } else {
            countdownElement.textContent = 'Code has expired';
            resendBtn.disabled = false;
        }
    }
    
    updateCountdown();
    const timer = setInterval(updateCountdown, 1000);

    verifyBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        const code = document.getElementById('verification_code').value;

        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `verify=1&verification_code=${code}`
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'Okay'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'login.php';
                    }
                });
            } else {
                failedAttempts++;
                messageElement.innerHTML = `<label class='form-label' style='color:rgb(255, 62, 62);text-align:center;'>${data.message}</label>`;
                updateCountdown();
            }
        } catch (error) {
            messageElement.innerHTML = `<label class='form-label' style='color:rgb(255, 62, 62);text-align:center;'>An error occurred. Please try again.</label>`;
        }
    });

    resendBtn.addEventListener('click', async function(e) {
        e.preventDefault();

        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `resend=1`
            });

            const data = await response.json();

            if (data.success) {
                messageElement.innerHTML = `<label class='form-label' style='color:green;text-align:center;'>${data.message}</label>`;
                // Reload the page to stay on the same page
                window.location.reload();
            } else {
                messageElement.innerHTML = `<label class='form-label' style='color:rgb(255, 62, 62);text-align:center;'>${data.message}</label>`;
            }
        } catch (error) {
            messageElement.innerHTML = `<label class='form-label' style='color:rgb(255, 62, 62);text-align:center;'>An error occurred. Please try again.</label>`;
        }
    });

    <?php if ($error): ?>
        messageElement.innerHTML = `<label class='form-label' style='color:rgb(255, 62, 62);text-align:center;'><?php echo $error; ?></label>`;
    <?php endif; ?>
    <?php if ($success && $success !== "Account verified successfully!"): ?>
        messageElement.innerHTML = `<label class='form-label' style='color:green;text-align:center;'><?php echo $success; ?></label>`;
    <?php endif; ?>
});
</script>
</body>
</html>