<?php
session_start();
$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

// Set the new timezone
date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

// Calculate min and max dates for the datepicker
$maxDate = date('Y-m-d', strtotime('-18 years')); // Minimum age 18
$minDate = date('Y-m-d', strtotime('-100 years')); // Maximum age 100

$error = ""; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate Client ID
    $clientId = "CL" . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    // Calculate age from date of birth
    $dob = $_POST['dob'];
    $birthDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;

    // Validate age server-side
    if ($age < 18 || $age > 100) {
        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Age must be between 18 and 100 years.</label>';
    } else {
        $_SESSION["personal"] = array(
            'fname' => $_POST['fname'],
            'mname' => $_POST['mname'], // Middle name
            'lname' => $_POST['lname'],
            'suffix' => $_POST['suffix'], // Suffix
            'sex' => $_POST['sex'],
            'nic' => $clientId,
            'dob' => $dob,
            'age' => $age // Add age to session
        );

        // Debugging: Print session data (commented out to avoid header issue)
        // print_r($_SESSION["personal"]);
        
        header("Location: create-account.php");
        exit;
    }
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
    <link rel="icon" href="../Images/G-icon.png">
        
    <title>Sign Up</title>
    <style>
        .input-text[readonly] {
            background-color: #f0f0f0; /* Light gray background for readonly field */
            cursor: not-allowed; /* Indicate it's not editable */
        }
    </style>
    <script>
        function calculateAge(dob) {
            const birthDate = new Date(dob);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return age;
        }

        function validateField(field) {
            const value = field.value;
            const fieldName = field.name;
            let errorMessage = "";

            // Clear previous error message
            document.getElementById(fieldName + "Error").innerText = "";

            // Validate first name
            if (fieldName === "fname" && value.trim() === "") {
                errorMessage = "First name is required.";
            }

            // Validate last name
            if (fieldName === "lname" && value.trim() === "") {
                errorMessage = "Last name is required.";
            }

            // Validate sex
            if (fieldName === "sex" && value === "") {
                errorMessage = "Please select your sex.";
            }

            // Validate date of birth and age
            if (fieldName === "dob") {
                if (value === "") {
                    errorMessage = "Date of birth is required.";
                } else {
                    const age = calculateAge(value);
                    document.getElementById("age").value = age; // Display age
                    if (age < 18) {
                        errorMessage = "You must be at least 18 years old.";
                    } else if (age > 100) {
                        errorMessage = "Maximum age allowed is 100 years.";
                    }
                }
            }

            // Display error message if any
            if (errorMessage) {
                document.getElementById(fieldName + "Error").innerText = errorMessage;
            }
        }

        function validateForm() {
            let valid = true;

            // Validate all fields
            const fields = ["fname", "lname", "sex", "dob"];
            fields.forEach(fieldName => {
                const field = document.forms["signupForm"][fieldName];
                validateField(field);
                if (fieldName === "dob") {
                    const age = calculateAge(field.value);
                    if (age < 18 || age > 100) valid = false;
                } else if (field.value.trim() === "" && fieldName !== "sex") {
                    valid = false;
                }
            });

            return valid; // Return true if all validations pass
        }

        // Real-time validation setup
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    validateField(this);
                });
                input.addEventListener('change', function() {
                    validateField(this);
                });
            });
        });
    </script>
</head>
<body>
<center>
    <div class="container">
        <table border="0">
            <tr>
                <td colspan="2">
                    <p class="header-text">Let's Get Started</p>
                    <p class="sub-text">Add Your Personal Details to Continue</p>
                </td>
            </tr>
            <tr>
                <form name="signupForm" action="" method="POST" onsubmit="return validateForm()">
                <td class="label-td" colspan="2">
                    <label for="name" class="form-label">Name: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td">
                    <input type="text" name="fname" class="input-text" placeholder="First Name" required>
                    <span id="fnameError" style="color:red;"></span>
                </td>
                <td class="label-td">
                    <input type="text" name="mname" class="input-text" placeholder="Middle Name (Optional)">
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <input type="text" name="lname" class="input-text" placeholder="Last Name" required>
                    <span id="lnameError" style="color:red;"></span>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <input type="text" name="suffix" class="input-text" placeholder="Suffix (Optional)">
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <label for="sex" class="form-label">Sex: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
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
                    <input type="hidden" name="nic" value="<?php echo isset($clientId) ? $clientId : ''; ?>">
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <label for="dob" class="form-label">Date of Birth: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <input type="date" name="dob" class="input-text" required min="<?php echo $minDate; ?>" max="<?php echo $maxDate; ?>">
                    <span id="dobError" style="color:red;"></span>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <label for="age" class="form-label">Age: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <input type="number" name="age" id="age" class="input-text" readonly>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <?php echo isset($error) ? $error : ''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">
                </td>
                <td>
                    <input type="submit" value="Next" class="login-btn btn-primary btn">
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