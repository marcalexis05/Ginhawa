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
        .notification-bell { 
            position: relative; 
            display: inline-block; 
            cursor: pointer; 
        }
        .notification-count { 
            position: absolute; 
            top: -5px; 
            right: -5px; 
            background-color: red; 
            color: white; 
            border-radius: 50%; 
            padding: 2px 6px; 
            font-size: 12px; 
        }
        .notification-dropdown { 
            display: none; 
            position: absolute; 
            background-color: white; 
            min-width: 350px; 
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); 
            z-index: 1; 
            right: 0; 
            border-radius: 5px; 
            max-height: 300px; 
            overflow-y: auto; 
        }
        .notification-item { 
            padding: 10px; 
            border-bottom: 1px solid #ddd; 
            background-color: #f8d7da; 
            position: relative; 
        }
        .notification-item:last-child { border-bottom: none; }
        .notification-close { 
            position: absolute; 
            top: 5px; 
            right: 5px; 
            cursor: pointer; 
            font-size: 18px; 
            color: #721c24; 
        }
        .bell-icon { width: 30px; height: 30px; fill: #333; }
        .bell-icon:hover { fill: #007bff; }
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

    // Fetch rejected requests
    $rejected_requests_query = "SELECT pr.*, d.docname 
                               FROM patient_requests pr 
                               INNER JOIN doctor d ON pr.doctor_id = d.docid 
                               WHERE pr.patient_id = ? AND pr.status = 'rejected'";
    $stmt = $database->prepare($rejected_requests_query);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $rejected_requests = $stmt->get_result();
    $rejected_count = $rejected_requests->num_rows;
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
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Professionals</p></a></div>
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
                            $sql_today_sessions = "SELECT * FROM schedule 
                                                  INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
                                                  WHERE appointment.pid = ? AND schedule.scheduledate = ?";
                            $stmt_today = $database->prepare($sql_today_sessions);
                            $stmt_today->bind_param("is", $userid, $today);
                            $stmt_today->execute();
                            $schedulerow = $stmt_today->get_result();
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <div class="notification-bell" onclick="toggleNotifications()">
                            <svg class="bell-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                            </svg>
                            <?php if ($rejected_count > 0) { ?>
                                <span class="notification-count"><?php echo $rejected_count; ?></span>
                            <?php } ?>
                            <div class="notification-dropdown" id="notificationDropdown">
                                <?php
                                if ($rejected_requests->num_rows > 0) {
                                    while ($request = $rejected_requests->fetch_assoc()) {
                                        $start_time = DateTime::createFromFormat('H:i:s', $request["start_time"]);
                                        $start_time_display = $start_time ? $start_time->format('h:i A') : 'Invalid Time';
                                        echo '<div class="notification-item" id="notification-' . $request['request_id'] . '">';
                                        echo '<p><strong>Request Rejected</strong><br>';
                                        echo 'Session: ' . htmlspecialchars($request['title']) . '<br>';
                                        echo 'Doctor: ' . htmlspecialchars($request['docname']) . '<br>';
                                        echo 'Date: ' . $request['session_date'] . ' ' . $start_time_display . '<br>';
                                        echo 'Reason: ' . htmlspecialchars($request['rejection_reason']) . '</p>';
                                        echo '<span class="notification-close" onclick="dismissNotification(' . $request['request_id'] . ')">×</span>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<div class="notification-item">No rejected requests</div>';
                                }
                                ?>
                            </div>
                        </div>
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
                                            <a href="doctors.php" class="non-style-link"><b>"All Professionals"</b></a> section or 
                                            <a href="schedule.php" class="non-style-link"><b>"Sessions"</b></a><br>
                                            Track your past and future appointme    nts history.<br>
                                            Also find out the expected available time of your consultant.<br><br>
                                        </p>
                                        <h3>Channel a Professional Here</h3>
                                        <form action="schedule.php" method="post" style="display: flex">
                                            <input type="search" name="search" class="input-text" placeholder="Search Professionals and We will Find The Session Available" list="doctors" style="width:45%;">
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
                        <table border="0" width="100%">
                            <tr>
                                <td width="50%">
                                    <center>
                                        <table class="filter-container" style="border: none;" border="0">
                                            <tr>
                                                <td colspan="3">
                                                    <p style="font-size: 20px;font-weight:600;padding-left: 12px;">Status</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 33.33%;">
                                                    <div class="dashboard-items">
                                                        <div>
                                                            <div class="h1-dashboard"><?php echo $doctorrow->num_rows ?></div>
                                                            <br>
                                                            <div class="h3-dashboard">All Doctors</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                                    </div>
                                                </td>
                                                <td style="width: 33.33%;">
                                                    <div class="dashboard-items">
                                                        <div>
                                                            <div class="h1-dashboard"><?php echo $appointmentrow->num_rows ?></div>
                                                            <br>
                                                            <div class="h3-dashboard">New Booking</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/book-hover.svg');"></div>
                                                    </div>
                                                </td>
                                                <td style="width: 33.33%;">
                                                    <div class="dashboard-items">
                                                        <div>
                                                            <div class="h1-dashboard"><?php echo $schedulerow->num_rows ?></div>
                                                            <br>
                                                            <div class="h3-dashboard" style="font-size: 15px;">Today Sessions</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/session-iceblue.svg');"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </center>
                                </td>
                                <td>
                                    <p style="font-size: 20px;font-weight:600;padding-left: 40px;" class="anime">Your Upcoming Booking</p>
                                    <center>
                                        <div class="abc scroll" style="height: 250px;padding: 0;margin: 0;">
                                            <table width="85%" class="sub-table scrolldown" border="0">
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
                                                            <td colspan="4">
                                                                <br><br><br><br>
                                                                <center>
                                                                    <img src="../img/notfound.svg" width="25%">
                                                                    <br>
                                                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Nothing to show here!</p>
                                                                    <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">Channel a Doctor</button></a>
                                                                </center>
                                                                <br><br><br><br>
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
                                                                <td style="padding:30px;font-size:25px;font-weight:700;">'.$apponum.'</td>
                                                                <td style="padding:20px;">'.substr($title,0,30).'</td>
                                                                <td>'.substr($docname,0,20).'</td>
                                                                <td style="text-align:center;">'.substr($scheduledate,0,10).' '.$start_time.' - '.$end_time.'</td>
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
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <script>
    function toggleNotifications() {
        var dropdown = document.getElementById('notificationDropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    window.onclick = function(event) {
        if (!event.target.closest('.notification-bell')) {
            var dropdown = document.getElementById('notificationDropdown');
            if (dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
            }
        }
    }

    function dismissNotification(requestId) {
        fetch('dismiss_notification.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'request_id=' + requestId
        }).then(response => {
            if (response.ok) {
                document.getElementById('notification-' + requestId).style.display = 'none';
                updateNotificationCount();
            }
        });
    }

    function updateNotificationCount() {
        var items = document.querySelectorAll('.notification-item:not([style*="display: none"])');
        var countSpan = document.querySelector('.notification-count');
        if (items.length > 0) {
            countSpan.textContent = items.length;
            countSpan.style.display = 'block';
        } else {
            countSpan.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateNotificationCount();
    });
    </script>
</body>
</html>