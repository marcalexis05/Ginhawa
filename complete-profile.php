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

    // Validate password match
    if ($password !== $confirm_password) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Passwords do not match.</label>';
    } else {
        $birthDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;

        if ($age < 18 || $age > 100) {
            $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Age must be between 18 and 100 years.</label>';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Update patient details (fixed bind_param with 6 parameters)
            $stmt = $database->prepare("UPDATE patient SET ptel = ?, pdob = ?, psex = ?, age = ?, ppassword = ? WHERE pemail = ?");
            $stmt->bind_param("sssiss", $tele, $dob, $sex, $age, $hashed_password, $email); // Corrected type string
            $stmt->execute();
            $stmt->close();

            // Redirect to patient dashboard
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        let iti;
        function validateForm() {
            let tele = iti.getNumber();
            let dob = document.forms["completeProfileForm"]["dob"].value;
            let sex = document.forms["completeProfileForm"]["sex"].value;
            let password = document.forms["completeProfileForm"]["password"].value;
            let confirmPassword = document.forms["completeProfileForm"]["confirm_password"].value;

            let valid = true;
            document.getElementById("teleError").innerText = "";
            document.getElementById("dobError").innerText = "";
            document.getElementById("sexError").innerText = "";
            document.getElementById("passwordError").innerText = "";
            document.getElementById("confirmPasswordError").innerText = "";

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

            return valid;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const teleInput = document.querySelector('input[name="tele"]');
            iti = window.intlTelInput(teleInput, {
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                initialCountry: "ph",
            });

            teleInput.addEventListener('input', function() {
                const teleError = document.getElementById("teleError");
                if (!iti.isValidNumber()) teleError.innerText = "Please enter a valid mobile number.";
                else {
                    teleError.innerText = "";
                    document.getElementById("full_tele").value = iti.getNumber();
                }
            });
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
                        <span id="teleError" style="color:red;"></span>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="dob" class="form-label">Date of Birth: </label>
                        <input type="date" name="dob" class="input-text" required min="<?php echo date('Y-m-d', strtotime('-100 years')); ?>" max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>">
                        <span id="dobError" style="color:red;"></span>
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
                        <span id="sexError" style="color:red;"></span>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="password" class="form-label">Password: </label>
                        <input type="password" name="password" id="password" class="input-text" placeholder="Password" required>
                        <span id="passwordError" style="color:red;"></span>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="confirm_password" class="form-label">Confirm Password: </label>
                        <input type="password" name="confirm_password" id="confirm_password" class="input-text" placeholder="Confirm Password" required>
                        <span id="confirmPasswordError" style="color:red;"></span>
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
    </div>
</center>
</body>
</html>