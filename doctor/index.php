<?php
session_start();

if (!isset($_SESSION["user"]) || empty($_SESSION["user"]) || $_SESSION['usertype'] != 'd') {
    header("location: ../login.php");
    exit;
}

$useremail = $_SESSION["user"];
include("../connection.php");

$userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["docid"];
$username = $userfetch["docname"];

$_SESSION['doctor_id'] = $userid;

// Today's date
date_default_timezone_set('Asia/Manila');
$today = date('Y-m-d');

// Fetch counts
$patientrow = $database->query("SELECT * FROM patient");
$doctorrow = $database->query("SELECT * FROM doctor");
$appointmentrow = $database->query("SELECT * FROM appointment WHERE appodate>='$today'");
$schedulerow = $database->query("SELECT * FROM schedule WHERE docid='$userid' AND scheduledate='$today'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="icon" href="../Images/G-icon.png">
    <title>Dashboard</title>
    <style>
        .dashbord-tables, .doctor-heade { animation: transitionIn-Y-over 0.5s; }
        .filter-container { animation: transitionIn-Y-bottom 0.5s; }
        .sub-table, #anim { animation: transitionIn-Y-bottom 0.5s; }
        .dashboard-items {
            padding: 20px;
            margin: auto;
            width: 95%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .dashboard-items:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .h1-dashboard {
            font-size: 36px;
            color: #007B62;
            margin-bottom: 5px;
        }
        .h3-dashboard {
            font-size: 16px;
            color: #333;
        }
        .dashboard-icons {
            width: 40px;
            height: 40px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>
</head>
<body>
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
                                    <p class="profile-title"><?php echo substr($username,0,13) ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22) ?></p>
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
                    <td class="menu-btn menu-icon-dashbord menu-active menu-icon-dashbord-active">
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Dashboard</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Appointments</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">My Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">My Patients</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style="border-spacing:0;margin:0;padding:0;">
                <tr>
                    <td colspan="1" class="nav-bar">
                        <p style="font-size:23px;padding-left:12px;font-weight:600;margin-left:20px;">Dashboard</p>
                    </td>
                    <td width="15%">
                        <p style="font-size:14px;color:rgb(119,119,119);padding:0;margin:0;text-align:right;">Today's Date</p>
                        <p class="heading-sub12" style="padding:0;margin:0;"><?php echo $today; ?></p>
                    </td>
                    <td width="10%">
                    <button class="btn-label" style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <center>
                            <table class="filter-container doctor-header" style="border:none;width:95%" border="0">
                                <tr>
                                    <td>
                                        <h3>Welcome!</h3>
                                        <h1><?php echo $username ?>.</h1>
                                        <p>Thanks for joining with us. We are always trying to get you a complete service<br>
                                        You can view your daily schedule, Reach Patients Appointment at home!<br><br>
                                        </p>
                                        <a href="appointment.php" class="non-style-link"><button class="btn-primary btn" style="width:30%">View My Appointments</button></a>
                                        <br><br>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <table border="0" width="100%">
                            <tr>
                                <td width="50%">
                                    <center>
                                        <table class="filter-container" style="border:none;" border="0">
                                            <tr>
                                                <td colspan="3">
                                                    <p style="font-size:20px;font-weight:600;padding-left:12px;">Status</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:33.33%;">
                                                    <div class="dashboard-items">
                                                        <div>
                                                            <div class="h1-dashboard"><?php echo $patientrow ? $patientrow->num_rows : 0 ?></div>
                                                            <br>
                                                            <div class="h3-dashboard">All Patients</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/patients-hover.svg');"></div>
                                                    </div>
                                                </td>
                                                <td style="width:33.33%;">
                                                    <div class="dashboard-items">
                                                        <div>
                                                            <div class="h1-dashboard"><?php echo $appointmentrow ? $appointmentrow->num_rows : 0 ?></div>
                                                            <br>
                                                            <div class="h3-dashboard">New Booking</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/book-hover.svg');"></div>
                                                    </div>
                                                </td>
                                                <td style="width:33.33%;">
                                                    <div class="dashboard-items">
                                                        <div>
                                                            <div class="h1-dashboard"><?php echo $schedulerow ? $schedulerow->num_rows : 0 ?></div>
                                                            <br>
                                                            <div class="h3-dashboard" style="font-size:15px">Today Sessions</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/session-iceblue.svg');"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </center>
                                </td>
                                <td>
                                    <p id="anim" style="font-size:20px;font-weight:600;padding-left:40px;">Your Upcoming Sessions until Next Week</p>
                                    <center>
                                        <div class="abc scroll" style="height:250px;padding:0;margin:0;">
                                            <table width="85%" class="sub-table scrolldown" border="0">
                                                <thead>
                                                    <tr>
                                                        <th class="table-headin">Session Title</th>
                                                        <th class="table-headin">Patient Name</th>
                                                        <th class="table-headin">Scheduled Date</th>
                                                        <th class="table-headin">Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $nextweek = date("Y-m-d", strtotime("+1 week"));
                                                    $sqlmain = "SELECT schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.start_time, schedule.end_time, patient.pname 
                                                                FROM schedule 
                                                                INNER JOIN doctor ON schedule.docid=doctor.docid 
                                                                INNER JOIN appointment ON schedule.scheduleid=appointment.scheduleid 
                                                                INNER JOIN patient ON appointment.pid=patient.pid 
                                                                WHERE schedule.scheduledate>='$today' AND schedule.scheduledate<='$nextweek' 
                                                                AND doctor.docid='$userid' 
                                                                ORDER BY schedule.scheduledate DESC";
                                                    $result = $database->query($sqlmain);

                                                    if ($result === false) {
                                                        echo '<tr><td colspan="4">Query failed: ' . htmlspecialchars($database->error) . '</td></tr>';
                                                    } elseif ($result->num_rows == 0) {
                                                        echo '<tr>
                                                            <td colspan="4">
                                                                <br><br><br><br>
                                                                <center>
                                                                    <img src="../img/notfound.svg" width="25%">
                                                                    <br>
                                                                    <p class="heading-main12" style="margin-left:45px;font-size:20px;color:rgb(49,49,49)">No upcoming sessions found!</p>
                                                                    <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display:flex;justify-content:center;align-items:center;margin-left:20px;">Show all Sessions</button></a>
                                                                </center>
                                                                <br><br><br><br>
                                                            </td>
                                                        </tr>';
                                                    } else {
                                                        for ($x = 0; $x < $result->num_rows; $x++) {
                                                            $row = $result->fetch_assoc();
                                                            $scheduleid = $row["scheduleid"];
                                                            $title = $row["title"];
                                                            $docname = $row["docname"];
                                                            $scheduledate = $row["scheduledate"];
                                                            $start_time = date("h:i A", strtotime($row["start_time"]));
                                                            $end_time = date("h:i A", strtotime($row["end_time"]));
                                                            $pname = $row["pname"];
                                                            echo '<tr>
                                                                <td style="padding:20px;"> ' . substr($title, 0, 30) . '</td>
                                                                <td style="padding:20px;"> ' . substr($pname, 0, 20) . '</td>
                                                                <td style="padding:20px;font-size:13px;">' . substr($scheduledate, 0, 10) . '</td>
                                                                <td style="text-align:center;">' . $start_time . ' - ' . $end_time . '</td>
                                                            </tr>';
                                                        }
                                                    }
                                                    $database->close();
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </center>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>