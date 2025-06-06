<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="./Images/G-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/login.css">
    <title>Login</title>
    <style>
        /* Custom styling for the Google Login button */
        .google-login-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 10px 15px;
            background-color: #ffffff;
            border: 1px solid #dadce0;
            border-radius: 4px;
            text-decoration: none;
            color: #3c4043;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            transition: background-color 0.2s, box-shadow 0.2s;
        }

        .google-login-btn:hover {
            background-color: #f8f9fa;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }

        .google-login-btn img {
            width: 18px;
            height: 18px;
            margin-right: 10px;
        }

        /* Styling for the back button */
        .back-btn {
            position: absolute;
            top: 10px; /* Adjusted for top-left corner */
            left: 10px; /* Adjusted for top-left corner */
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            text-decoration: none; /* Remove underline from link */
            color: inherit; /* Inherit text color */
        }

        .back-icon {
            width: 20px;
            height: 20px;
            filter: invert(46%) sepia(68%) saturate(1350%) hue-rotate(90deg) brightness(95%) contrast(90%); /* Makes it green */
            margin-right: 5px; /* Space between icon and text */
        }

        .back-text {
            font-size: 12px; /* Small font size for "Back" */
            color: green; /* Default color, adjust if needed */
        }
    </style>
</head>
<body>
    <?php
    session_start();
    date_default_timezone_set('Asia/Manila'); // Set timezone at the start

    // Google OAuth Integration
    require_once 'vendor/autoload.php';

    use Google\Client as Google_Client;
    use Google\Service\OAuth2 as Google_Service_OAuth2;

    $client = new Google_Client();
    try {
        $client->setAuthConfig('client_secret.json');
    } catch (Exception $e) {
        die("Error loading client_secret.json: " . $e->getMessage());
    }
    $client->addScope('email');
    $client->addScope('profile');
    $client->setRedirectUri('http://localhost/Ginhawa/login.php');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    $error = '';

    // Handle Google callback
    if (isset($_GET['code'])) {
        try {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            if (!is_array($token)) {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Google login failed: Invalid response from server.</label>';
            } elseif (isset($token['error'])) {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Google login failed: ' . htmlspecialchars($token['error_description']) . '</label>';
            } elseif (!isset($token['access_token'])) {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Google login failed: No access token received.</label>';
            } else {
                $client->setAccessToken($token);
                $oauth = new Google_Service_OAuth2($client);
                $userInfo = $oauth->userinfo->get();
                $email = $userInfo->email;
                $name = $userInfo->name;
                $picture = $userInfo->picture;

                include("connection.php");

                $stmt = $database->prepare("SELECT * FROM webuser WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();
                    $utype = $user['usertype'];

                    if ($utype == 'p') {
                        $stmt = $database->prepare("SELECT * FROM patient WHERE pemail = ?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $checker = $stmt->get_result();
                        $patient = $checker->fetch_assoc();

                        if ($checker->num_rows == 1) {
                            if ($patient['archived'] == 1) {
                                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Your account has been archived. Please contact support.</label>';
                            } else {
                                $_SESSION['user'] = $email;
                                $_SESSION['usertype'] = 'p';
                                $_SESSION['username'] = explode(" ", $patient['pname'])[0] ?? $name;
                                $_SESSION['google_picture'] = $picture;

                                if (empty($patient['ptel']) || empty($patient['pdob']) || empty($patient['psex']) || empty($patient['age'])) {
                                    header('Location: complete-profile.php');
                                    exit();
                                } else {
                                    header('Location: patient/index.php');
                                    exit();
                                }
                            }
                        }
                    } else {
                        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Google login is only for patients</label>';
                    }
                } else {
                    $stmt = $database->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'p')");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();

                    $clientId = "CL" . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                    $stmt = $database->prepare("INSERT INTO patient (pemail, pname, pclientid) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $email, $name, $clientId);
                    $stmt->execute();

                    $_SESSION['user'] = $email;
                    $_SESSION['usertype'] = 'p';
                    $_SESSION['username'] = explode(" ", $name)[0];
                    $_SESSION['google_picture'] = $picture;
                    header('Location: complete-profile.php');
                    exit();
                }
                $stmt->close();
                $database->close();
            }
        } catch (Exception $e) {
            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Google login error: ' . htmlspecialchars($e->getMessage()) . '</label>';
        }
    } else {
        $error = '<label for="promter" class="form-label"> </label>';
    }

    $googleLoginUrl = $client->createAuthUrl();

    // Manual login logic
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        include("connection.php");
        $email = $_POST['useremail'] ?? '';
        $password = $_POST['userpassword'] ?? '';

        if (empty($email) || empty($password)) {
            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Email and password are required.</label>';
        } else {
            $stmt = $database->prepare("SELECT * FROM webuser WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                $utype = $user['usertype'];

                if ($utype == 'p') {
                    $stmt = $database->prepare("SELECT * FROM patient WHERE pemail = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $checker = $stmt->get_result();
                    $patient = $checker->fetch_assoc();

                    if ($checker->num_rows == 1) {
                        if ($patient['archived'] == 1) {
                            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Your account has been archived. Please contact support.</label>';
                        } elseif ($patient['verification_code'] !== null) {
                            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Please verify your account first.</label>';
                            header("Location: verify-account.php");
                            exit();
                        } elseif (password_verify($password, $patient['ppassword'])) {
                            $_SESSION['user'] = $email;
                            $_SESSION['usertype'] = 'p';
                            $_SESSION['username'] = explode(" ", $patient['pname'])[0];
                            header('Location: patient/index.php');
                            exit();
                        } else {
                            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Invalid password</label>';
                        }
                    } else {
                        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Patient record not found</label>';
                    }
                } elseif ($utype == 'a') {
                    $stmt = $database->prepare("SELECT * FROM admin WHERE aemail = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $checker = $stmt->get_result();
                    $admin = $checker->fetch_assoc();

                    if ($checker->num_rows == 1 && password_verify($password, $admin['apassword'])) {
                        $_SESSION['user'] = $email;
                        $_SESSION['usertype'] = 'a';
                        header('Location: admin/index.php');
                        exit();
                    } else {
                        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Invalid credentials</label>';
                    }
                } elseif ($utype == 'd') {
                    $stmt = $database->prepare("SELECT * FROM doctor WHERE docemail = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $checker = $stmt->get_result();
                    $doctor = $checker->fetch_assoc();

                    if ($checker->num_rows == 1) {
                        if ($doctor['archived'] == 1) {
                            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Your account has been archived. Please contact support.</label>';
                        } elseif (password_verify($password, $doctor['docpassword'])) {
                            $today = date('Y-m-d');
                            $time_now = date('Y-m-d H:i:s');

                            $stmt_attendance = $database->prepare("INSERT INTO doctor_attendance (doctor_id, docemail, time_in, date) VALUES (?, ?, ?, ?)");
                            $stmt_attendance->bind_param("isss", $doctor['docid'], $email, $time_now, $today);
                            $stmt_attendance->execute();
                            $stmt_attendance->close();

                            $_SESSION['user'] = $email;
                            $_SESSION['usertype'] = 'd';
                            $_SESSION['doctor_id'] = $doctor['docid'];
                            header('Location: doctor/index.php');
                            exit();
                        } else {
                            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Invalid credentials</label>';
                        }
                    } else {
                        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Doctor record not found</label>';
                    }
                }
                $stmt->close();
            } else {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">No account found for this email</label>';
            }
            $database->close();
        }
    }
    ?>

    <center>
    <div class="container" style="position: relative;">
        <!-- Back Button with Text in Top-Left Corner -->
        <a href="landing.html" class="back-btn">
            <img src="https://cdn-icons-png.flaticon.com/512/271/271220.png" alt="Back" class="back-icon">
            <span class="back-text">Back</span>
        </a>
        <table border="0" style="margin: 0; padding: 0; width: 60%;">
            <tr>
                <td>
                    <p class="header-text">Welcome Back!</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="sub-text">Login with your details to continue</p>
                </td>
            </tr>
            <tr>
                <td>
                    <form action="" method="POST">
                        <table border="0" style="width: 100%;">
                            <tr>
                                <td class="label-td">
                                    <label for="useremail" class="form-label">Email: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td">
                                    <input type="email" name="useremail" class="input-text" placeholder="Email Address" required>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td">
                                    <label for="userpassword" class="form-label">Password: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td">
                                    <input type="password" name="userpassword" id="userpassword" class="input-text" placeholder="Password" required>
                                    <br>
                                    <label>
                                        <input type="checkbox" id="showPassword" onclick="togglePassword('userpassword')"> Show Password
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td><br>
                                    <?php echo $error ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="submit" value="Login" class="login-btn btn-primary btn">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="<?php echo $googleLoginUrl; ?>" class="google-login-btn">
                                        <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google Logo">
                                        Sign in with Google
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <br>
                                    <label for="" class="sub-text" style="font-weight: 280;">Forgot your password? </label>
                                    <a href="forgot-password.php" class="hover-link1 non-style-link">Reset Password</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <br>
                                    <label for="" class="sub-text" style="font-weight: 280;">Don't have an account? </label>
                                    <a href="signup.php" class="hover-link1 non-style-link">Sign Up</a>
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

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const checkbox = document.getElementById('showPassword');
            passwordField.type = checkbox.checked ? "text" : "password";
        }
    </script>
</body>
</html>