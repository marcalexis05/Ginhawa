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
    $tele = $_POST['full_tele'];
    $dob = $_POST['dob'];
    $sex = $_POST['sex'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $terms = isset($_POST['terms']) ? true : false;

    if (!$terms) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">You must agree to the Terms and Conditions.</label>';
    } elseif ($password !== $confirm_password) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Passwords do not match.</label>';
    } else {
        $birthDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;

        if ($age < 18 || $age > 100) {
            $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Age must be between 18 and 100 years.</label>';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $database->prepare("UPDATE patient SET ptel = ?, pdob = ?, psex = ?, age = ?, ppassword = ? WHERE pemail = ?");
            $stmt->bind_param("sssiss", $tele, $dob, $sex, $age, $hashed_password, $email);
            $stmt->execute();
            $stmt->close();

            header("Location: patient/index.php");
            exit;
        }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <link rel="icon" href="../Images/G-icon.png">
    <title>Complete Your Profile</title>
    <style>
        .container { animation: transitionIn-X 0.5s; }
        .terms-checkbox {
            display: block;
            margin: 10px 0;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #ffffff;
            margin: 10% auto;
            padding: 25px;
            border: 1px solid #e0e0e0;
            width: 90%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            font-family: Arial, sans-serif;
        }
        .modal-content h2 {
            color: #006400;
            font-size: 24px;
            margin-bottom: 20px;
            border-bottom: 2px solid green;
            padding-bottom: 10px;
            text-align: left;
        }
        .modal-content p {
            color: #000000;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
            text-align: left;
        }
        .modal-content strong {
            color: green;
        }
        .close {
            color: #000000;
            float: right;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s;
        }
        .close:hover,
        .close:focus {
            color: rgb(255, 62, 62);
        }
        .terms-link {
            color: green;
            text-decoration: none;
        }
        .terms-link:hover {
            text-decoration: underline;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        let iti;

        function validateForm() {
            let tele = iti.getNumber();
            let dob = document.forms["completeProfileForm"]["dob"].value;
            let sex = document.forms["completeProfileForm"]["sex"].value;
            let password = document.forms["completeProfileForm"]["password"].value;
            let confirmPassword = document.forms["completeProfileForm"]["confirm_password"].value;
            let terms = document.getElementById("termsCheckbox").checked;

            let valid = true;
            document.getElementById("teleError").innerText = "";
            document.getElementById("dobError").innerText = "";
            document.getElementById("sexError").innerText = "";
            document.getElementById("passwordError").innerText = "";
            document.getElementById("confirmPasswordError").innerText = "";
            document.getElementById("termsError").innerText = "";

            if (!iti.isValidNumber()) {
                document.getElementById("teleError").innerText = "Please enter a valid mobile number.";
                valid = false;
            } else {
                document.getElementById("full_tele").value = tele;
            }

            if (dob === "") {
                document.getElementById("dobError").innerText = "Date of birth is required.";
                valid = false;
            }

            if (sex === "") {
                document.getElementById("sexError").innerText = "Please select your sex.";
                valid = false;
            }

            if (password === "") {
                document.getElementById("passwordError").innerText = "Password is required.";
                valid = false;
            } else if (password.length < 8) {
                document.getElementById("passwordError").innerText = "Password must be at least 8 characters long.";
                valid = false;
            }

            if (confirmPassword === "") {
                document.getElementById("confirmPasswordError").innerText = "Please confirm your password.";
                valid = false;
            } else if (password !== confirmPassword) {
                document.getElementById("confirmPasswordError").innerText = "Passwords do not match.";
                valid = false;
            }

            if (!terms) {
                document.getElementById("termsError").innerText = "You must agree to the Terms and Conditions.";
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

        function togglePasswords() {
            const passwordField = document.getElementById("password");
            const confirmPasswordField = document.getElementById("confirm_password");
            const checkbox = document.getElementById("showPasswords");
            passwordField.type = checkbox.checked ? "text" : "password";
            confirmPasswordField.type = checkbox.checked ? "text" : "password";
        }

        document.addEventListener('DOMContentLoaded', function() {
            const teleInput = document.querySelector('input[name="tele"]');
            const dobInput = document.querySelector('input[name="dob"]');
            const sexInput = document.querySelector('select[name="sex"]');
            const passwordInput = document.querySelector('input[name="password"]');
            const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
            const termsCheckbox = document.getElementById("termsCheckbox");

            iti = window.intlTelInput(teleInput, {
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                initialCountry: "ph",
            });

            teleInput.addEventListener('input', function() {
                const teleError = document.getElementById("teleError");
                if (this.value.trim() === "") {
                    teleError.innerText = "Mobile number is required.";
                    teleError.style.color = "red";
                } else if (!iti.isValidNumber()) {
                    teleError.innerText = "Please enter a valid mobile number.";
                    teleError.style.color = "red";
                } else {
                    teleError.innerText = "";
                    document.getElementById("full_tele").value = iti.getNumber();
                }
            });

            dobInput.addEventListener('input', function() {
                const dobError = document.getElementById("dobError");
                if (this.value === "") {
                    dobError.innerText = "Date of birth is required.";
                    dobError.style.color = "red";
                } else {
                    const birthDate = new Date(this.value);
                    const today = new Date();
                    const age = today.getFullYear() - birthDate.getFullYear();
                    const m = today.getMonth() - birthDate.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
                    if (age < 18 || age > 100) {
                        dobError.innerText = "Age must be between 18 and 100 years.";
                        dobError.style.color = "red";
                    } else {
                        dobError.innerText = "";
                    }
                }
            });

            sexInput.addEventListener('change', function() {
                const sexError = document.getElementById("sexError");
                if (this.value === "") {
                    sexError.innerText = "Please select your sex.";
                    sexError.style.color = "red";
                } else {
                    sexError.innerText = "";
                }
            });

            passwordInput.addEventListener('input', function() {
                const passwordError = document.getElementById("passwordError");
                const confirmPasswordError = document.getElementById("confirmPasswordError");

                if (this.value === "") {
                    passwordError.innerText = "Password is required.";
                    passwordError.style.color = "red";
                } else if (this.value.length < 8) {
                    passwordError.innerText = "Password must be at least 8 characters long.";
                    passwordError.style.color = "red";
                } else {
                    const strength = checkPasswordStrength(this.value);
                    passwordError.innerText = `Password strength: ${strength.text}`;
                    passwordError.style.color = strength.color;
                }

                if (confirmPasswordInput.value !== "" && this.value !== confirmPasswordInput.value) {
                    confirmPasswordError.innerText = "Passwords do not match.";
                    confirmPasswordError.style.color = "red";
                } else if (confirmPasswordInput.value !== "" && this.value === confirmPasswordInput.value) {
                    confirmPasswordError.innerText = "";
                }
            });

            confirmPasswordInput.addEventListener('input', function() {
                const confirmPasswordError = document.getElementById("confirmPasswordError");
                if (this.value === "") {
                    confirmPasswordError.innerText = "Please confirm your password.";
                    confirmPasswordError.style.color = "red";
                } else if (this.value !== passwordInput.value) {
                    confirmPasswordError.innerText = "Passwords do not match.";
                    confirmPasswordError.style.color = "red";
                } else {
                    confirmPasswordError.innerText = "";
                }
            });

            termsCheckbox.addEventListener('change', function() {
                const termsError = document.getElementById("termsError");
                if (!this.checked) {
                    termsError.innerText = "You must agree to the Terms and Conditions.";
                    termsError.style.color = "red";
                } else {
                    termsError.innerText = "";
                }
            });

            const modal = document.getElementById("termsModal");
            const termsLink = document.getElementById("termsLink");
            const closeBtn = document.getElementsByClassName("close")[0];

            termsLink.onclick = function(e) {
                e.preventDefault();
                modal.style.display = "block";
            }

            closeBtn.onclick = function() {
                modal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        });
    </script>
</head>
<body>
<center>
    <div class="container">
        <table border="0" style="width: 69%;">
            <tr>
                <td colspan="2">
                    <p class="header-text">Complete Your Profile</p>
                    <p class="sub-text">Please provide the following details</p>
                </td>
            </tr>
            <tr>
                <form name="completeProfileForm" action="" method="POST" onsubmit="return validateForm()">
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="tele" class="form-label">Mobile Number: </label>
                        <input type="tel" name="tele" class="input-text" placeholder="Mobile Number" required>
                        <input type="hidden" name="full_tele" id="full_tele">
                        <br><span id="teleError" style="color:red;"></span>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="dob" class="form-label">Date of Birth: </label>
                        <input type="date" name="dob" class="input-text" required min="<?php echo date('Y-m-d', strtotime('-100 years')); ?>" max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>">
                        <br><span id="dobError" style="color:red;"></span>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="sex" class="form-label">Sex: </label>
                        <select name="sex" class="input-text" required>
                            <option value="">Select Sex</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <br><span id="sexError" style="color:red;"></span>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="password" class="form-label">Password: </label>
                        <input type="password" name="password" id="password" class="input-text" placeholder="Password" required>
                        <br><span id="passwordError" style="color:red;"></span>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="confirm_password" class="form-label">Confirm Password: </label>
                        <input type="password" name="confirm_password" id="confirm_password" class="input-text" placeholder="Confirm Password" required>
                        <br><span id="confirmPasswordError" style="color:red;"></span>
                        <br>
                        <label>
                            <input type="checkbox" id="showPasswords" onclick="togglePasswords()"> Show Passwords
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label class="terms-checkbox">
                            <input type="checkbox" name="terms" id="termsCheckbox">
                            I agree to the <a href="#" id="termsLink" class="terms-link">Terms and Conditions</a>
                        </label>
                        <br><span id="termsError" style="color:red;"></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo $error; ?></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" value="Save Profile" class="login-btn btn-primary btn">
                    </td>
                </tr>
                </form>
            </tr>
        </table>
        <div id="termsModal" class="modal">
            <div class="modal-content">
                <span class="close">×</span>
                <h2>Terms and Conditions</h2>
                <div id="termsContent">
                    <p><strong>1. Acceptance of Terms</strong><br>
                    By registering an account with Ginhawa Mental Health, you agree to abide by these Terms and Conditions.</p>
                    <p><strong>2. Account Registration</strong><br>
                    You must provide accurate and complete information during registration process.</p>
                    <p><strong>3. Privacy</strong><br>
                    Your personal information (email, phone, etc.) will be handled in accordance with our Privacy Policy and stored securely in our database.</p>
                    <p><strong>4. Account Security</strong><br>
                    You are responsible for maintaining the confidentiality of your password.</p>
                    <p><strong>5. Use of Service</strong><br>
                    Appointments and schedules must be used responsibly and not for any unlawful purpose.</p>
                    <p><strong>6. Termination</strong><br>
                    We reserve the right to terminate your account (marked as archived) if these terms are violated.</p>
                    <p><strong>7. Changes to Terms</strong><br>
                    We may update these terms, and continued use of the service constitutes acceptance of the new terms.</p>
                </div>
            </div>
        </div>
    </div>
</center>
</body>
</html>