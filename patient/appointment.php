<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../Images/G-icon.png">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <title>Appointments</title>
    <style>
        .popup { animation: transitionIn-Y-bottom 0.5s; }
        .sub-table { animation: transitionIn-Y-bottom 0.5s; }
    </style>
</head>
<body>
    <?php
    session_start();

    if (!isset($_SESSION["user"]) || empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit;
    }

    $useremail = $_SESSION["user"];
    include("../connection.php");

    // Fetch patient details
    $sqlmain = "SELECT * FROM patient WHERE pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];

    // Main query for appointments with updated schema
    $sqlmain = "SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, patient.pname, 
                schedule.scheduledate, schedule.start_time, schedule.end_time, appointment.apponum, appointment.appodate 
                FROM schedule 
                INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
                INNER JOIN patient ON patient.pid = appointment.pid 
                INNER JOIN doctor ON schedule.docid = doctor.docid 
                WHERE patient.pid = $userid";

    if ($_POST && !empty($_POST["sheduledate"])) {
        $sheduledate = $_POST["sheduledate"];
        $sqlmain .= " AND schedule.scheduledate = '$sheduledate'";
    }

    $sqlmain .= " ORDER BY appointment.appodate ASC";
    $result = $database->query($sqlmain);

    // Check if query failed
    if ($result === false) {
        die("Query failed: " . $database->error);
    }
    ?>

    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                        <tr>
                                <td width="30%" style="padding-left:20px">
                                    <img src="<?php echo isset($_SESSION['google_picture']) ? $_SESSION['google_picture'] : '../img/user.png'; ?>" 
                                         alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($username, 0, 13) ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail, 0, 22) ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-home"><a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></div></a></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor"><a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></div></a></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session"><a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Scheduled Sessions</p></div></a></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment menu-active menu-icon-appoinment-active"><a href="appointment.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">My Bookings</p></div></a></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings"><a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a></td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0; margin: 0; padding: 0; margin-top: 25px;">
                <tr>
                    <td width="13%"><a href="appointment.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding: 11px; margin-left: 20px; width: 125px"><font class="tn-in-text">Back</font></button></a></td>
                    <td><p style="font-size: 23px; padding-left: 12px; font-weight: 600;">My Bookings History</p></td>
                    <td width="15%">
                        <p style="font-size: 14px; color: rgb(119, 119, 119); padding: 0; margin: 0; text-align: right;">Today's Date</p>
                        <p class="heading-sub12" style="padding: 0; margin: 0;">
                            <?php 
                            date_default_timezone_set('Asia/Manila');
                            $today = date('Y-m-d');
                            echo $today;
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex; justify-content: center; align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <?php
                // Display error message if booking failed with a retry option (kept for compatibility, though redirected to schedule.php now)
                if (isset($_GET['action']) && $_GET['action'] == 'booking-failed' && isset($_GET['error'])) {
                    echo '<tr>
                        <td colspan="4">
                            <p style="color: red; text-align: center; font-size: 16px; padding: 10px;">' . htmlspecialchars(urldecode($_GET['error'])) . '</p>
                            <p style="text-align: center;"><a href="schedule.php" class="non-style-link"><button class="btn-primary btn" style="padding: 10px;">Retry Booking</button></a></p>
                        </td>
                    </tr>';
                }
                ?>
                <tr>
                    <td colspan="4" style="padding-top: 10px; width: 100%;">
                        <p class="heading-main12" style="margin-left: 45px; font-size: 18px; color: rgb(49, 49, 49)">My Bookings (<?php echo $result->num_rows; ?>)</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top: 0px; width: 100%;">
                        <center>
                            <table class="filter-container" border="0">
                                <tr>
                                    <td width="10%"></td>
                                    <td width="5%" style="text-align: center;">Date:</td>
                                    <td width="30%">
                                        <form action="" method="post">
                                            <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="margin: 0; width: 95%;" value="<?php echo isset($_POST['sheduledate']) ? $_POST['sheduledate'] : ''; ?>">
                                    </td>
                                    <td width="12%">
                                        <input type="submit" name="filter" value="Filter" class="btn-primary-soft btn button-icon btn-filter" style="padding: 15px; margin: 0; width: 100%;">
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <center>
                            <div class="abc scroll">
                                <table width="93%" class="sub-table scrolldown" border="0" style="border: none;">
                                    <tbody>
                                        <?php
                                        if ($result->num_rows == 0) {
                                            echo '<tr>
                                                <td colspan="7">
                                                    <br><br><br><br>
                                                    <center>
                                                        <img src="../img/notfound.svg" width="25%">
                                                        <br>
                                                        <p class="heading-main12" style="margin-left: 45px; font-size: 20px; color: rgb(49, 49, 49)">We couldn\'t find anything related to your keywords!</p>
                                                        <a class="non-style-link" href="appointment.php"><button class="login-btn btn-primary-soft btn" style="display: flex; justify-content: center; align-items: center; margin-left: 20px;"> Show all Appointments </button></a>
                                                    </center>
                                                    <br><br><br><br>
                                                </td>
                                            </tr>';
                                        } else {
                                            for ($x = 0; $x < $result->num_rows; $x++) {
                                                echo "<tr>";
                                                for ($q = 0; $q < 3; $q++) {
                                                    $row = $result->fetch_assoc();
                                                    if (!isset($row)) {
                                                        break;
                                                    }
                                                    $scheduleid = $row["scheduleid"];
                                                    $title = $row["title"];
                                                    $docname = $row["docname"];
                                                    $scheduledate = $row["scheduledate"];
                                                    $start_time = date('h:i A', strtotime($row["start_time"]));
                                                    $end_time = date('h:i A', strtotime($row["end_time"]));
                                                    $apponum = $row["apponum"];
                                                    $appodate = $row["appodate"];
                                                    $appoid = $row["appoid"];

                                                    if (empty($scheduleid)) {
                                                        break;
                                                    }

                                                    echo '
                                                        <td style="width: 25%;">
                                                            <div class="dashboard-items search-items">
                                                                <div style="width: 100%;">
                                                                    <div class="h3-search">
                                                                        Booking Date: ' . substr($appodate, 0, 30) . '<br>
                                                                        Reference Number: OC-000-' . $appoid . '
                                                                    </div>
                                                                    <div class="h1-search">
                                                                        ' . substr($title, 0, 21) . '<br>
                                                                    </div>
                                                                    <div class="h3-search">
                                                                        Appointment Number: <div class="h1-search">' . sprintf("%02d", $apponum) . '</div>
                                                                    </div>
                                                                    <div class="h3-search">
                                                                        ' . substr($docname, 0, 30) . '
                                                                    </div>
                                                                    <div class="h4-search">
                                                                        Scheduled Date: ' . $scheduledate . '<br>Time: <b>' . $start_time . ' - ' . $end_time . '</b>
                                                                    </div>
                                                                    <br>
                                                                    <a href="?action=drop&id=' . $appoid . '&title=' . urlencode($title) . '&doc=' . urlencode($docname) . '"><button class="login-btn btn-primary-soft btn" style="padding: 11px; width: 100%"><font class="tn-in-text">Cancel Booking</font></button></a>
                                                                </div>
                                                            </div>
                                                        </td>';
                                                }
                                                echo "</tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </center>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <?php
    if (!empty($_GET) && isset($_GET["action"])) {
        $action = $_GET["action"];
        
        if ($action == 'booking-added' || $action == 'drop') {
            if (!isset($_GET["id"]) || empty($_GET["id"])) {
                header("location: appointment.php");
                exit;
            }
            $id = $_GET["id"];

            if ($action == 'booking-added') {
                echo '
                <div id="popup1" class="overlay">
                    <div class="popup">
                        <center>
                            <br><br>
                            <h2>Booking Successfully.</h2>
                            <a class="close" href="appointment.php">×</a>
                            <div class="content">
                                Your Appointment number is ' . htmlspecialchars($id) . '.<br><br>
                            </div>
                            <div style="display: flex; justify-content: center;">
                                <a href="appointment.php" class="non-style-link"><button class="btn-primary btn" style="display: flex; justify-content: center; align-items: center; margin: 10px; padding: 10px;"><font class="tn-in-text">  OK  </font></button></a>
                                <br><br><br><br>
                            </div>
                        </center>
                    </div>
                </div>';
            } elseif ($action == 'drop') {
                $title = isset($_GET["title"]) ? urldecode($_GET["title"]) : 'Unknown Session';
                $docname = isset($_GET["doc"]) ? urldecode($_GET["doc"]) : 'Unknown Doctor';
                echo '
                <div id="popup1" class="overlay">
                    <div class="popup">
                        <center>
                            <h2>Are you sure?</h2>
                            <a class="close" href="appointment.php">×</a>
                            <div class="content">
                                You want to Cancel this Appointment?<br><br>
                                Session Name:  <b>' . htmlspecialchars(substr($title, 0, 40)) . '</b><br>
                                Doctor name : <b>' . htmlspecialchars(substr($docname, 0, 40)) . '</b><br><br>
                            </div>
                            <div style="display: flex; justify-content: center;">
                                <a href="delete-appointment.php?id=' . htmlspecialchars($id) . '" class="non-style-link"><button class="btn-primary btn" style="display: flex; justify-content: center; align-items: center; margin: 10px; padding: 10px;"><font class="tn-in-text"> Yes </font></button></a>   
                                <a href="appointment.php" class="non-style-link"><button class="btn-primary btn" style="display: flex; justify-content: center; align-items: center; margin: 10px; padding: 10px;"><font class="tn-in-text">  No  </font></button></a>
                            </div>
                        </center>
                    </div>
                </div>';
            }
        }
    }
    ?>
    <script src="scrollAnimation.js"></script>
</body>
</html>