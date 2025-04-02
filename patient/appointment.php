<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
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

    $sqlmain = "SELECT * FROM patient WHERE pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];

    $sqlmain = "SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, patient.pname, 
                schedule.scheduledate, schedule.start_time, schedule.end_time, appointment.apponum, appointment.appodate, appointment.gmeet_link 
                FROM schedule 
                INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
                INNER JOIN patient ON patient.pid = appointment.pid 
                INNER JOIN doctor ON schedule.docid = doctor.docid 
                WHERE patient.pid = ?";

    if ($_POST && !empty($_POST["sheduledate"])) {
        $sheduledate = $_POST["sheduledate"];
        $sqlmain .= " AND schedule.scheduledate = ?";
    }

    $sqlmain .= " ORDER BY appointment.appodate ASC";
    $stmt = $database->prepare($sqlmain);
    if ($_POST && !empty($_POST["sheduledate"])) {
        $stmt->bind_param("is", $userid, $sheduledate);
    } else {
        $stmt->bind_param("i", $userid);
    }
    $stmt->execute();
    $result = $stmt->get_result();

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
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
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
                    <td class="menu-btn menu-icon-home"><a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></a></div></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor"><a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></a></div></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session"><a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Scheduled Sessions</p></div></a></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment menu-active menu-icon-appoinment-active"><a href="appointment.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">My Bookings</p></a></div></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings"><a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div></td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0; margin:0; padding:0; margin-top:25px;">
                <tr>
                    <td width="13%"><a href="appointment.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a></td>
                    <td>
                        <form action="" method="post" class="header-search">
                            <input type="date" name="sheduledate" class="input-text header-searchbar" placeholder="Search by Date (YYYY-MM-DD)" value="<?php echo isset($_POST['sheduledate']) ? $_POST['sheduledate'] : ''; ?>">
                            <input type="submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                        </form>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">Today's Date</p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;"><?php echo date('Y-m-d'); ?></p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">My Bookings (<?php echo $result->num_rows; ?>)</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <center>
                            <div class="abc scroll">
                                <table width="93%" class="sub-table scrolldown" border="0">
                                    <thead>
                                        <tr>
                                            <th class="table-headin">Appointment No</th>
                                            <th class="table-headin">Title</th>
                                            <th class="table-headin">Doctor</th>
                                            <th class="table-headin">Scheduled Date & Time</th>
                                            <th class="table-headin">Google Meet Link</th>
                                            <th class="table-headin">Events</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result->num_rows == 0) {
                                            echo '<tr><td colspan="6"><br><br><br><br><center><img src="../img/notfound.svg" width="25%"><br><p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">No bookings found!</p></center><br><br><br><br></td></tr>';
                                        } else {
                                            while ($row = $result->fetch_assoc()) {
                                                $appoid = $row["appoid"];
                                                $scheduleid = $row["scheduleid"];
                                                $title = $row["title"];
                                                $docname = $row["docname"];
                                                $scheduledate = $row["scheduledate"];
                                                $start_time = date('h:i A', strtotime($row["start_time"]));
                                                $gmeet_link = $row["gmeet_link"] ?? 'Not yet generated';

                                                echo '<tr>
                                                    <td>' . $row["apponum"] . '</td>
                                                    <td>' . substr($title, 0, 30) . '</td>
                                                    <td>' . substr($docname, 0, 20) . '</td>
                                                    <td>' . $scheduledate . ' ' . $start_time . '</td>
                                                    <td><a href="' . $gmeet_link . '" target="_blank">' . substr($gmeet_link, 0, 30) . '...</a></td>
                                                    <td>
                                                        <div style="display:flex;justify-content: center;">
                                                            <a href="?action=drop&id=' . $appoid . '&scheduleid=' . $scheduleid . '" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-cancel" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Cancel</font></button></a>
                                                        </div>
                                                    </td>
                                                </tr>';
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
    if ($_GET) {
        $id = $_GET["id"];
        $action = $_GET["action"];
        if ($action == 'drop') {
            $scheduleid = $_GET["scheduleid"];
            $sql_drop = "DELETE FROM appointment WHERE appoid=?";
            $stmt = $database->prepare($sql_drop);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <a class="close" href="appointment.php">×</a>
                        <div class="content">
                            <p style="padding: 0;margin: 0;text-align: center;font-size: 20px;font-weight: 500;">Booking Cancelled</p>
                        </div>
                        <div style="display: flex;justify-content: center;">
                            <a href="appointment.php"><button class="btn-primary-soft btn">OK</button></a>
                        </div>
                    </center>
                </div>
            </div>';
        } elseif ($action == 'booking-added') {
            $id = $_GET["id"];
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <a class="close" href="appointment.php">×</a>
                        <div class="content">
                            <p style="padding: 0;margin: 0;text-align: center;font-size: 20px;font-weight: 500;">Booking Successful</p>
                        </div>
                        <div style="display: flex;justify-content: center;">
                            <a href="appointment.php"><button class="btn-primary-soft btn">OK</button></a>
                        </div>
                    </center>
                </div>
            </div>';
        }
    }
    ?>
    <script src="scrollAnimation.js"></script>
</body>
</html>