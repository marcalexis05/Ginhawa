<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/login.css">
    <link rel="icon" href="../Images/G-icon.png">
    <title>Reset Password</title>
    <style>
        .container { animation: transitionIn-X 0.5s; }
    </style>
    <script>
        function validateResetPasswordForm() {
            let newpassword = document.forms["resetPasswordForm"]["newpassword"].value;
            let confirmpassword = document.forms["resetPasswordForm"]["confirmpassword"].value;

            let valid = true;

            document.getElementById("passwordError").innerText = "";
            document.getElementById("cpasswordError").innerText = "";

            if (newpassword === "") {
                document.getElementById("passwordError").innerText = "New password is required.";
                valid = false;
            } else if (newpassword.length < 8) {
                document.getElementById("passwordError").innerText = "Password must be at least 8 characters.";
                valid = false;
            }

            if (confirmpassword === "") {
                document.getElementById("cpasswordError").innerText = "Please confirm your password.";
                valid = false;
            } else if (newpassword !== confirmpassword) {
                document.getElementById("cpasswordError").innerText = "Passwords do not match.";
                valid = false;
            }

            return valid;
        }

        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^A-Za-z0-9]/)) strength++;

            switch(strength) {
                case 0:
                case 1:
                    return { text: "Weak", color: "red" };
                case 2:
                case 3:
                    return { text: "Medium", color: "orange" };
                case 4:
                    return { text: "Strong", color: "green" };
            }
        }

        function realTimeValidation() {
            const passwordInput = document.querySelector('input[name="newpassword"]');
            const cpasswordInput = document.querySelector('input[name="confirmpassword"]');

            passwordInput.addEventListener('input', function() {
                const passwordError = document.getElementById("passwordError");
                const cpasswordError = document.getElementById("cpasswordError");
                
                if (this.value === "") {
                    passwordError.innerText = "New password is required.";
                    passwordError.style.color = "red";
                } else if (this.value.length < 8) {
                    passwordError.innerText = "Password must be at least 8 characters.";
                    passwordError.style.color = "red";
                } else {
                    const strength = checkPasswordStrength(this.value);
                    passwordError.innerText = `Password strength: ${strength.text}`;
                    passwordError.style.color = strength.color;
                }
                
                if (cpasswordInput.value !== "" && this.value !== cpasswordInput.value) {
                    cpasswordError.innerText = "Passwords do not match.";
                } else if (cpasswordInput.value !== "" && this.value === cpasswordInput.value) {
                    cpasswordError.innerText = "";
                }
            });

            cpasswordInput.addEventListener('input', function() {
                const cpasswordError = document.getElementById("cpasswordError");
                
                if (this.value === "") {
                    cpasswordError.innerText = "Please confirm your password.";
                } else if (this.value !== passwordInput.value) {
                    cpasswordError.innerText = "Passwords do not match.";
                } else {
                    cpasswordError.innerText = "";
                }
            });
        }

        function togglePasswords() {
            const newPasswordField = document.getElementById("newpassword");
            const confirmPasswordField = document.getElementById("confirmpassword");
            const checkbox = document.getElementById("showPasswords");
            newPasswordField.type = checkbox.checked ? "text" : "password";
            confirmPasswordField.type = checkbox.checked ? "text" : "password";
        }

        document.addEventListener('DOMContentLoaded', realTimeValidation);
    </script>
</head>
<body>
    <?php
    include("connection.php");

    $error = '<label for="promter" class="form-label"> </label>';
    $token = isset($_GET['token']) ? $_GET['token'] : '';

    if (!$token) {
        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Invalid or missing token.</label>';
    } else {
        // Check if the token is valid and not expired
        $stmt = $database->prepare("SELECT * FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $reset = $result->fetch_assoc();
            $email = $reset['email'];
            $expires = strtotime($reset['expires']);
            $currentTime = strtotime(date('Y-m-d H:i:s'));

            if ($currentTime > $expires) {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">This password reset link has expired.</label>';
            } else {
                if ($_POST) {
                    $newpassword = $_POST['newpassword'];
                    $confirmpassword = $_POST['confirmpassword'];

                    if ($newpassword !== $confirmpassword) {
                        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Passwords do not match.</label>';
                    } elseif (strlen($newpassword) < 8) {
                        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password must be at least 8 characters long.</label>';
                    } else {
                        // Hash the new password
                        $hashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);

                        // Update the password in the appropriate table
                        $stmt = $database->prepare("SELECT * FROM webuser WHERE email = ?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $userResult = $stmt->get_result();
                        $user = $userResult->fetch_assoc();
                        $usertype = $user['usertype'];

                        if ($usertype == 'p') {
                            $stmt = $database->prepare("UPDATE patient SET ppassword = ? WHERE pemail = ?");
                        } elseif ($usertype == 'a') {
                            $stmt = $database->prepare("UPDATE admin SET apassword = ? WHERE aemail = ?");
                        } elseif ($usertype == 'd') {
                            $stmt = $database->prepare("UPDATE doctor SET docpassword = ? WHERE docemail = ?");
                        }

                        $stmt->bind_param("ss", $hashedPassword, $email);
                        $stmt->execute();

                        // Delete the token from the password_resets table
                        $stmt = $database->prepare("DELETE FROM password_resets WHERE token = ?");
                        $stmt->bind_param("s", $token);
                        $stmt->execute();

                        $error = '<label for="promter" class="form-label" style="color:green;text-align:center;">Password reset successfully! <a href="login.php">Login</a></label>';
                    }
                }
            }
        } else {
            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Invalid token.</label>';
        }

        $stmt->close();
    }
    ?>

    <center>
    <div class="container">
        <table border="0" style="margin: 0;padding: 0;width: 60%;">
            <tr>
                <td>
                    <p class="header-text">Reset Password</p>
                </td>
            </tr>
            <div class="form-body">
                <tr>
                    <td>
                        <p class="sub-text">Enter your new password</p>
                    </td>
                </tr>
                <tr>
                    <form name="resetPasswordForm" action="" method="POST" onsubmit="return validateResetPasswordForm()">
                        <td class="label-td">
                            <label for="newpassword" class="form-label">New Password: </label>
                        </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <input type="password" name="newpassword" id="newpassword" class="input-text" placeholder="New Password" required>
                        <br>
                        <span id="passwordError" style="color:red;"></span>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <label for="confirmpassword" class="form-label">Confirm Password: </label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <input type="password" name="confirmpassword" id="confirmpassword" class="input-text" placeholder="Confirm Password" required>
                        <br>
                        <label>
                            <input type="checkbox" id="showPasswords" onclick="togglePasswords()"> Show Passwords
                        </label>
                        <br>
                        <span id="cpasswordError" style="color:red;"></span>
                    </td>
                </tr>
                <tr>
                    <td><br>
                        <?php echo $error ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" value="Reset Password" class="login-btn btn-primary btn">
                    </td>
                </tr>
                <tr>
                    <td>
                        <br>
                        <label for="" class="sub-text" style="font-weight: 280;">Remembered your password? </label>
                        <a href="login.php" class="hover-link1 non-style-link">Login</a>
                        <br><br><br>
                    </td>
                </tr>
            </form>
        </table>
    </div>
    </center>
</body>
</html>