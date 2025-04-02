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
    <title>Register Admin</title>
</head>
<body>
    <?php
    // Start session
    session_start();

    // Import database connection
    include("connection.php");

    $message = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = filter_var($_POST['adminemail'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['adminpassword'];

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Invalid email format</label>';
        } else {
            // Check if email already exists in webuser
            $check_stmt = $database->prepare("SELECT * FROM webuser WHERE email = ?");
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $message = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Email already registered</label>';
            } else {
                // Hash the password using bcrypt
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                // For demonstration, let's show what a hashed "ginhawa123" might look like:
                // $2y$10$5Qz5Qz5Qz5Qz5Qz5Qz5QzuhbL7X8z5Qz5Qz5Qz5Qz5Qz5Qz5Qz5Qz

                // Insert into webuser table
                $stmt_webuser = $database->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'a')");
                $stmt_webuser->bind_param("s", $email);
                
                // Insert into admin table with hashed password
                $stmt_admin = $database->prepare("INSERT INTO admin (aemail, apassword) VALUES (?, ?)");
                $stmt_admin->bind_param("ss", $email, $hashed_password);

                // Execute both queries
                $webuser_success = $stmt_webuser->execute();
                $admin_success = $stmt_admin->execute();

                if ($webuser_success && $admin_success) {
                    $message = '<label class="form-label" style="color:green;text-align:center;">Admin registered successfully! <a href="login.php">Login here</a></label>';
                } else {
                    $message = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Registration failed</label>';
                }

                $stmt_webuser->close();
                $stmt_admin->close();
            }
            $check_stmt->close();
        }
    }
    ?>

    <center>
    <div class="container">
        <table border="0" style="margin: 0;padding: 0;width: 60%;">
            <tr>
                <td>
                    <p class="header-text">Register Admin</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="sub-text">Create a new admin account</p>
                </td>
            </tr>
            <tr>
                <td>
                    <form action="" method="POST">
                        <table class="form-body">
                            <tr>
                                <td class="label-td">
                                    <label for="adminemail" class="form-label">Email: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td">
                                    <input type="email" name="adminemail" class="input-text" placeholder="Email Address" required>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td">
                                    <label for="adminpassword" class="form-label">Password: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td">
                                    <input type="password" name="adminpassword" class="input-text" placeholder="Password" required>
                                </td>
                            </tr>
                            <tr>
                                <td><br>
                                    <?php echo $message; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="submit" value="Register" class="login-btn btn-primary btn">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <br>
                                    <label class="sub-text" style="font-weight: 280;">Already have an account? </label>
                                    <a href="login.php" class="hover-link1 non-style-link">Login</a>
                                    <br><br>
                                </td>
                            </tr>
                        </table>
                    </form>
                </td>
            </tr>
        </table>
    </div>
    </center>
</body>
</html>