<?php
session_start();
$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

include("connection.php");
include("email_helper.php");

$error = ""; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_SESSION['personal']['fname'] ?? '';
    $mname = $_SESSION['personal']['mname'] ?? '';
    $lname = $_SESSION['personal']['lname'] ?? '';
    $suffix = $_SESSION['personal']['suffix'] ?? '';
    $sex = $_SESSION['personal']['sex'] ?? '';
    $age = $_SESSION['personal']['age'] ?? 0;

    $nameParts = array_filter([$fname, $mname, $lname, $suffix]);
    $name = implode(" ", $nameParts);
    
    $clientId = "CL" . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    $dob = $_SESSION['personal']['dob'] ?? '';
    $email = $_POST['newemail'] ?? '';
    $tele = $_POST['full_tele'] ?? '';
    $newpassword = $_POST['newpassword'] ?? '';
    $cpassword = $_POST['cpassword'] ?? '';
    
    if ($newpassword == $cpassword) {
        $hashed_password = password_hash($newpassword, PASSWORD_DEFAULT);

        $sqlmain = "SELECT * FROM webuser WHERE email=?";
        $stmt = $database->prepare($sqlmain);
        if ($stmt === false) {
            die("Prepare failed: " . $database->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>';
        } else {
            $verificationCode = sprintf("%06d", mt_rand(100000, 999999));
            $codeExpiry = date('Y-m-d H:i:s', strtotime('+100 seconds'));

            $sql_patient = "INSERT INTO patient (pemail, pname, ppassword, pclientid, pdob, ptel, psex, age, verification_code, code_expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_patient = $database->prepare($sql_patient);
            if ($stmt_patient === false) {
                die("Prepare failed: " . $database->error);
            }
            $stmt_patient->bind_param("ssssssssss", $email, $name, $hashed_password, $clientId, $dob, $tele, $sex, $age, $verificationCode, $codeExpiry);
            if (!$stmt_patient->execute()) {
                die("Execute failed: " . $stmt_patient->error);
            }

            $subject = "Account Verification - Ginhawa Mental Health";
            $body = "
                <h2>Welcome to Ginhawa Mental Health</h2>
                <p>Your verification code is: <strong>$verificationCode</strong></p>
                <p>This code will expire in 100 seconds. Please verify your account promptly.</p>
            ";
            if (sendEmail($email, $subject, $body)) {
                $_SESSION["user"] = $email;
                $_SESSION["usertype"] = "p";
                $_SESSION["username"] = $fname;

                header('Location: verify-account.php');
                exit;
            } else {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Failed to send verification email.</label>';
            }
        }
        $stmt->close();
    } else {
        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Confirmation Error! Reconfirm Password</label>';
    }
} else {
    $error = '<label for="promter" class="form-label"></label>';
}
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <link rel="icon" href="../Images/G-icon.png">
    <title>Create Account</title>
    <style>
        .container { animation: transitionIn-X 0.5s; }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        let iti;

        function validateCreateAccountForm() {
            let email = document.forms["createAccountForm"]["newemail"].value;
            let tele = iti.getNumber();
            let newpassword = document.forms["createAccountForm"]["newpassword"].value;
            let cpassword = document.forms["createAccountForm"]["cpassword"].value;

            let valid = true;

            document.getElementById("emailError").innerText = "";
            document.getElementById("teleError").innerText = "";
            document.getElementById("passwordError").innerText = "";
            document.getElementById("cpasswordError").innerText = "";

            if (email === "") {
                document.getElementById("emailError").innerText = "Email is required.";
                valid = false;
            } else if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
                document.getElementById("emailError").innerText = "Please enter a valid email.";
                valid = false;
            }

            if (!iti.isValidNumber()) {
                document.getElementById("teleError").innerText = "Please enter a valid mobile number.";
                valid = false;
            } else {
                document.getElementById("full_tele").value = tele;
            }

            if (newpassword === "") {
                document.getElementById("passwordError").innerText = "New password is required.";
                valid = false;
            } else if (newpassword.length < 6) {
                document.getElementById("passwordError").innerText = "Password must be at least 6 characters.";
                valid = false;
            }

            if (cpassword === "") {
                document.getElementById("cpasswordError").innerText = "Please confirm your password.";
                valid = false;
            } else if (newpassword !== cpassword) {
                document.getElementById("cpasswordError").innerText = "Passwords do not match.";
                valid = false;
            }

            return valid;
        }

        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 6) strength++;
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
            const emailInput = document.querySelector('input[name="newemail"]');
            const teleInput = document.querySelector('input[name="tele"]');
            const passwordInput = document.querySelector('input[name="newpassword"]');
            const cpasswordInput = document.querySelector('input[name="cpassword"]');

            iti = window.intlTelInput(teleInput, {
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                initialCountry: "ph",
            });

            emailInput.addEventListener('input', function() {
                const emailError = document.getElementById("emailError");
                if (this.value === "") {
                    emailError.innerText = "Email is required.";
                } else if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(this.value)) {
                    emailError.innerText = "Please enter a valid email.";
                } else {
                    emailError.innerText = "";
                }
            });

            teleInput.addEventListener('input', function() {
                const teleError = document.getElementById("teleError");
                if (this.value.trim() === "") {
                    teleError.innerText = "Mobile number is required.";
                } else if (!iti.isValidNumber()) {
                    teleError.innerText = "Please enter a valid mobile number.";
                } else {
                    teleError.innerText = "";
                    document.getElementById("full_tele").value = iti.getNumber();
                }
            });

            passwordInput.addEventListener('input', function() {
                const passwordError = document.getElementById("passwordError");
                const cpasswordError = document.getElementById("cpasswordError");
                
                if (this.value === "") {
                    passwordError.innerText = "New password is required.";
                    passwordError.style.color = "red";
                } else if (this.value.length < 6) {
                    passwordError.innerText = "Password must be at least 6 characters.";
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
            const confirmPasswordField = document.getElementById("cpassword");
            const checkbox = document.getElementById("showPasswords");
            newPasswordField.type = checkbox.checked ? "text" : "password";
            confirmPasswordField.type = checkbox.checked ? "text" : "password";
        }

        document.addEventListener('DOMContentLoaded', realTimeValidation);
    </script>
</head>
<body>
<center>
    <div class="container">
        <table border="0" style="width: 69%;">
            <tr>
                <td colspan="2">
                    <p class="header-text">Let's Get Started</p>
                    <p class="sub-text">It's Okay, Now Create User Account.</p>
                </td>
            </tr>
            <tr>
                <form name="createAccountForm" action="" method="POST" onsubmit="return validateCreateAccountForm()">
                <td class="label-td" colspan="2">
                    <label for="newemail" class="form-label">Email: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <input type="email" name="newemail" class="input-text" placeholder="Email Address" required>
                    <br>
                    <span id="emailError" style="color:red;"></span>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <label for="tele" class="form-label">Mobile Number: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <input type="tel" name="tele" class="input-text" placeholder="Mobile Number" required>
                    <input type="hidden" name="full_tele" id="full_tele">
                    <br>
                    <span id="teleError" style="color:red;"></span>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <label for="newpassword" class="form-label">Create New Password: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <input type="password" name="newpassword" id="newpassword" class="input-text" placeholder="New Password" required>
                    <br>
                    <span id="passwordError" style="color:red;"></span>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <label for="cpassword" class="form-label">Confirm Password: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <input type="password" name="cpassword" id="cpassword" class="input-text" placeholder="Confirm Password" required>
                    <br>
                    <label>
                        <input type="checkbox" id="showPasswords" onclick="togglePasswords()"> Show Passwords
                    </label>
                    <br>
                    <span id="cpasswordError" style="color:red;"></span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php echo $error ?>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">
                </td>
                <td>
                    <input type="submit" value="Sign Up" class="login-btn btn-primary btn">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <br>
                    <label for="" class="sub-text" style="font-weight: 280;">Already have an account? </label>
                    <a href="login.php" class="hover-link1 non-style-link">Login</a>
                    <br><br><br>
                </td>
            </tr>
                </form>
            </tr>
        </table>
    </div>
</center>
</body>
</html>