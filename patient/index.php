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
    <title>Dashboard</title>
    <style>
        .dashbord-tables { animation: transitionIn-Y-over 0.5s; }
        .filter-container { animation: transitionIn-Y-bottom 0.5s; }
        .sub-table, .anime { animation: transitionIn-Y-bottom 0.5s; }
        
        /* Status Section Styling */
        .status-container {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        .status-container .dashboard-items {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .status-container .dashboard-items:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .status-container .h1-dashboard {
            font-size: 36px;
            color: #007B62;
            margin-bottom: 5px;
        }
        .status-container .h3-dashboard {
            font-size: 16px;
            color: #333;
        }
        
        /* Upcoming Booking Section Styling */
        .booking-container {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        .booking-container .sub-table {
            width: 90%;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .booking-container .table-headin {
            background-color: #007B62;
            color: #ffffff;
            padding: 12px;
            font-weight: 600;
            text-align: center;
        }
        .booking-container tbody tr {
            transition: background-color 0.2s;
        }
        .booking-container tbody tr:hover {
            background-color: #f1faff;
        }
        .booking-container td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        .booking-container .no-data {
            padding: 20px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    include("../connection.php");

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
            exit;
        }else{
            $useremail=$_SESSION["user"];
        }
    }else{
        header("location: ../login.php");
        exit;
    }

    $sqlmain = "select * from patient where pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch = $userrow->fetch_assoc();

    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];
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
                                    <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
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
                    <td class="menu-btn menu-icon-home menu-active menu-icon-home-active">
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Home</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Scheduled Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                <tr>
                    <td colspan="1" class="nav-bar">
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;margin-left:20px;">Home</p>
                    </td>
                    <td width="25%"></td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">Today's Date</p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 
                            date_default_timezone_set('Asia/Manila');
                            $today = date('Y-m-d');
                            echo $today;

                            $patientrow = $database->query("select * from patient;");
                            $doctorrow = $database->query("select * from doctor;");
                            $appointmentrow = $database->query("select * from appointment where appodate>='$today';");
                            $schedulerow = $database->query("select * from schedule where scheduledate='$today';");
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;">
                            <img src="../img/calendar.svg" width="100%">
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <center>
                            <table class="filter-container doctor-header patient-header" style="border: none;width:95%" border="0">
                                <tr>
                                    <td>
                                        <h3>Welcome!</h3>
                                        <h1><?php echo $username; ?>.</h1>
                                        <p>Haven't any idea about doctors? no problem let's jumping to 
                                            <a href="doctors.php" class="non-style-link"><b>"All Doctors"</b></a> section or 
                                            <a href="schedule.php" class="non-style-link"><b>"Sessions"</b></a><br>
                                            Track your past and future appointments history.<br>
                                            Also find out the expected arrival time of your doctor or medical consultant.<br><br>
                                        </p>
                                        <h3>Channel a Doctor Here</h3>
                                        <form action="schedule.php" method="post" style="display: flex">
                                            <input type="search" name="search" class="input-text" placeholder="Search Doctor and We will Find The Session Available" list="doctors" style="width:45%;">
                                            <?php
                                            echo '<datalist id="doctors">';
                                            $list11 = $database->query("select docname,docemail from doctor;");
                                            for ($y=0; $y<$list11->num_rows; $y++){
                                                $row00=$list11->fetch_assoc();
                                                $d=$row00["docname"];
                                                echo "<option value='$d'><br/>";
                                            }
                                            echo '</datalist>';
                                            ?>
                                            <input type="Submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                                        </form>
                                        <br><br>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div class="status-container">
                            <center>
                                <table class="filter-container" style="border: none; width: 100%; text-align: center;" border="0">
                                    <tr>
                                        <td colspan="3">
                                            <p style="font-size: 20px; font-weight: 600; padding: 12px 0; margin: 0 auto;">Status</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 33.33%;">
                                            <div class="dashboard-items" style="padding: 20px; margin: 0 auto; width: 95%; max-width: 300px; display: flex; justify-content: center; align-items: center;">
                                                <div style="text-align: center;">
                                                    <div class="h1-dashboard"><?php echo $doctorrow->num_rows ?></div>
                                                    <br>
                                                    <div class="h3-dashboard">All Doctors</div>
                                                </div>
                                                <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/doctors-hover.svg'); margin-left: 15px;"></div>
                                            </div>
                                        </td>
                                        <td style="width: 33.33%;">
                                            <div class="dashboard-items" style="padding: 20px; margin: 0 auto; width: 95%; max-width: 300px; display: flex; justify-content: center; align-items: center;">
                                                <div style="text-align: center;">
                                                    <div class="h1-dashboard"><?php echo $appointmentrow->num_rows ?></div>
                                                    <br>
                                                    <div class="h3-dashboard">New Booking</div>
                                                </div>
                                                <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/book-hover.svg'); margin-left: 15px;"></div>
                                            </div>
                                        </td>
                                        <td style="width: 33.33%;">
                                            <div class="dashboard-items" style="padding: 20px; margin: 0 auto; width: 95%; max-width: 300px; display: flex; justify-content: center; align-items: center;">
                                                <div style="text-align: center;">
                                                    <div class="h1-dashboard"><?php echo $schedulerow->num_rows ?></div>
                                                    <br>
                                                    <div class="h3-dashboard" style="font-size: 15px;">Today Sessions</div>
                                                </div>
                                                <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/session-iceblue.svg'); margin-left: 15px;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </center>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div class="booking-container">
                            <p style="font-size: 20px; font-weight: 600; padding: 12px 0; margin: 0 auto; text-align: center;" class="anime">Your Upcoming Bookings</p>
                            <center>
                                <div class="abc scroll" style="height: 250px; padding: 0; margin: 0;">
                                    <table width="90%" class="sub-table scrolldown" border="0">
                                        <thead>
                                            <tr>
                                                <th class="table-headin">Appoint. Number</th>
                                                <th class="table-headin">Session Title</th>
                                                <th class="table-headin">Doctor</th>
                                                <th class="table-headin">Scheduled Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $nextweek = date("Y-m-d", strtotime("+1 week"));
                                            $sqlmain = "select schedule.scheduleid, schedule.title, appointment.apponum, doctor.docname, schedule.scheduledate, schedule.start_time, schedule.end_time 
                                                        from schedule 
                                                        inner join appointment on schedule.scheduleid=appointment.scheduleid 
                                                        inner join patient on patient.pid=appointment.pid 
                                                        inner join doctor on schedule.docid=doctor.docid 
                                                        where patient.pid=? and schedule.scheduledate>='$today' 
                                                        order by schedule.scheduledate asc";
                                            $stmt = $database->prepare($sqlmain);
                                            $stmt->bind_param("i", $userid);
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            if($result->num_rows==0){
                                                echo '<tr>
                                                    <td colspan="4" class="no-data">
                                                        <br><br>
                                                        <img src="../img/notfound.svg" width="25%">
                                                        <br>
                                                        <p class="heading-main12" style="font-size:20px;color:rgb(49, 49, 49)">Nothing to show here!</p>
                                                        <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin:20px auto;">Channel a Doctor</button></a>
                                                        <br><br>
                                                    </td>
                                                </tr>';
                                            } else {
                                                while($row = $result->fetch_assoc()){
                                                    $scheduleid = $row["scheduleid"];
                                                    $title = $row["title"];
                                                    $apponum = $row["apponum"];
                                                    $docname = $row["docname"];
                                                    $scheduledate = $row["scheduledate"];
                                                    $start_time = date("h:i A", strtotime($row["start_time"]));
                                                    $end_time = date("h:i A", strtotime($row["end_time"]));
                                                    echo '<tr>
                                                        <td style="font-size: 25px; font-weight: 700;">'.$apponum.'</td>
                                                        <td>'.substr($title,0,30).'</td>
                                                        <td>'.substr($docname,0,20).'</td>
                                                        <td>'.substr($scheduledate,0,10).' '.$start_time.' - '.$end_time.'</td>
                                                    </tr>';
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </center>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>