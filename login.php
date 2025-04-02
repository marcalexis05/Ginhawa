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
    <title>Login</title>
</head>
<body>
    <?php
    session_start();
    date_default_timezone_set('Asia/Manila'); // Set timezone at the start

    // Google OAuth Integration
    require_once 'vendor/autoload.php';

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
            if (isset($token['error'])) {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Google login failed: ' . htmlspecialchars($token['error_description']) . '</label>';
            } elseif (!$token || !isset($token['access_token'])) {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Google login failed: Invalid token received.</label>';
            } else {
                $client->setAccessToken($token);
                $oauth = new Google_Service_Oauth2($client);
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
                                $_SESSION['username'] = explode(" ", $patient['pname'])[0];
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
        $email = $_POST['useremail'];
        $password = $_POST['userpassword'];

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
                    } elseif ($patient['verification_code'] !== NULL) {
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

                if ($checker->num_rows == 1 && password_verify($password, $doctor['docpassword'])) {
                    $today = date('Y-m-d');
                    $time_now = date('Y-m-d H:i:s'); // e.g., "2025-04-01 15:16:00"

                    $stmt_attendance = $database->prepare("INSERT INTO doctor_attendance (doctor_id, docemail, time_in, date) VALUES (?, ?, ?, ?)");
                    $stmt_attendance->bind_param("isss", $doctor['docid'], $email, $time_now, $today);
                    $stmt_attendance->execute();
                    $stmt_attendance->close();

                    $_SESSION['user'] = $email;
                    $_SESSION['usertype'] = 'd';
                    $_SESSION['doctor_id'] = $doctor['docid']; // Store doctor_id in session
                    header('Location: doctor/index.php');
                    exit();
                } else {
                    $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Invalid credentials</label>';
                }
            }
        } else {
            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">No account found for this email</label>';
        }

        $stmt->close();
        $database->close();
    }
    ?>

    <center>
    <div class="container">
        <table border="0" style="margin: 0;padding: 0;width: 60%;">
            <tr>
                <td>
                    <p class="header-text">Welcome Back!</p>
                </td>
            </tr>
            <div class="form-body">
                <tr>
                    <td>
                        <p class="sub-text">Login with your details to continue</p>
                    </td>
                </tr>
                <tr>
                    <form action="" method="POST">
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
                        <br>
                        <a href="<?php echo $googleLoginUrl; ?>" class="login-btn btn-primary btn" style="display:block;text-align:center;">Login with Google</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <br>
                        <label for="" class="sub-text" style="font-weight: 280;">Forgot your password? </label>
                        <a href="forgot-password.php" class="hover-link1 non-style-link">Reset Password</a>
                    </td>
                </tr>
            </div>
            <tr>
                <td>
                    <br>
                    <label for="" class="sub-text" style="font-weight: 280;">Don't have an account? </label>
                    <a href="signup.php" class="hover-link1 non-style-link">Sign Up</a>
                    <br><br><br>
                </td>
            </tr>
            </form>
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