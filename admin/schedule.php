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
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        
    <title>Schedule</title>
    <style>
        .popup { animation: transitionIn-Y-bottom 0.5s; }
        .sub-table { animation: transitionIn-Y-bottom 0.5s; }
        .notification-bell { 
            position: relative; 
            display: inline-block; 
        }
        .notification-count { 
            position: absolute; 
            top: -5px; 
            right: -5px; 
            background: red; 
            color: white; 
            border-radius: 50%; 
            padding: 2px 6px; 
            font-size: 12px;
        }
        .bell-icon { 
            width: 30px; 
            height: 30px; 
            cursor: pointer; 
            fill: #333; /* Default color */
        }
        .bell-icon:hover { 
            fill: #28a745; /* Hover color */
        }
        .notification-dropdown {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .notification-item:last-child { 
            border-bottom: none; 
        }
        .notification-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .btn-approve {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .btn-reject {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php
    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
            header("location: ../login.php");
        }
    }else{
        header("location: ../login.php");
    }
    
    // Import database
    include("../connection.php");

    // Calculate min and max dates for the date input
    date_default_timezone_set('Asia/Kolkata');
    $today = date('Y-m-d'); // e.g., 2025-04-04
    $oneWeekLater = date('Y-m-d', strtotime('+7 days')); // e.g., 2025-04-11
    ?>
    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px" >
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title">Administrator</p>
                                    <p class="profile-subtitle">admin@edoc.com</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                    </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord" >
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor ">
                        <a href="doctors.php" class="non-style-link-menu "><div><p class="menu-text">Doctors</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-schedule menu-active menu-icon-schedule-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-attendance">
                        <a href="attendance.php" class="non-style-link-menu"><div><p class="menu-text">Attendance</p></div></a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <td width="13%" >
                        <a href="schedule.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;">Schedule Manager</p>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 
                            echo $today;
                            $list110 = $database->query("SELECT * FROM schedule INNER JOIN doctor ON schedule.docid = doctor.docid WHERE doctor.archived = 0;");
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <a href="#" class="non-style-link">
                            <div class="notification-bell" onclick="toggleNotifications()">
                                <svg class="bell-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                                </svg>
                                <?php
                                $request_query = $database->query("SELECT COUNT(*) as count FROM session_requests WHERE status='pending'");
                                $request_count = $request_query->fetch_assoc()['count'];
                                if($request_count > 0) {
                                    echo '<span class="notification-count">'.$request_count.'</span>';
                                }
                                ?>
                                <div id="notificationDropdown" class="notification-dropdown">
                                    <?php
                                    $requests = $database->query("SELECT sr.*, d.docname FROM session_requests sr 
                                                                INNER JOIN doctor d ON sr.docid = d.docid 
                                                                WHERE sr.status='pending' AND d.archived = 0
                                                                ORDER BY sr.request_date DESC");
                                    if($requests->num_rows == 0) {
                                        echo '<div class="notification-item">No pending requests</div>';
                                    } else {
                                        while($request = $requests->fetch_assoc()) {
                                            $request_id = $request['request_id'];
                                            $start_time = date('h:i A', strtotime($request["session_time"]));
                                            $end_time = date('h:i A', strtotime($request["session_time"] . " +30 minutes")); // Assuming 30-minute default duration
                                            echo '<div class="notification-item">';
                                            echo '<p><strong>'.$request['docname'].'</strong></p>';
                                            echo '<p>Title: '.$request['title'].'</p>';
                                            echo '<p>Sessions: '.$request['num_sessions'].'</p>';
                                            echo '<p>Date: '.$request['session_date'].' '.$start_time.' - '.$end_time.'</p>';
                                            echo '<p>Requested: '.$request['request_date'].'</p>';
                                            echo '<div class="notification-actions">';
                                            echo '<a href="handle_request.php?action=approve&id='.$request_id.'"><button class="btn-approve">Approve</button></a>';
                                            echo '<a href="handle_request.php?action=reject&id='.$request_id.'"><button class="btn-reject">Reject</button></a>';
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </a>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display:flex;justify-content:center;align-items:center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>

                </tr>
                <tr>
                    <td colspan="4" >
                        <div style="display: flex;margin-top: 40px;">
                            <div class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49);margin-top: 5px;">Schedule a Session</div>
                            <a href="?action=add-session" class="non-style-link"><button  class="login-btn btn-primary btn button-icon"  style="margin-left:25px;background-image: url('../img/icons/add.svg');">Add a Session</font></button></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;" >
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">All Sessions (<?php echo $list110->num_rows; ?>)</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:0px;width: 100%;" >
                        <center>
                            <table class="filter-container" border="0" >
                                <tr>
                                    <td width="10%"></td> 
                                    <td width="5%" style="text-align: center;">Date:</td>
                                    <td width="30%">
                                        <form action="" method="post">
                                            <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="margin: 0;width: 95%;">
                                    </td>
                                    <td width="5%" style="text-align: center;">Doctor:</td>
                                    <td width="30%">
                                        <select name="docid" id="" class="box filter-container-items" style="width:90% ;height: 37px;margin: 0;" >
                                            <option value="" disabled selected hidden>Choose Doctor Name from the list</option><br/>
                                            <?php 
                                                $list11 = $database->query("SELECT * FROM doctor WHERE archived = 0 ORDER BY docname ASC;");
                                                for ($y=0; $y<$list11->num_rows; $y++){
                                                    $row00=$list11->fetch_assoc();
                                                    $sn=$row00["docname"];
                                                    $id00=$row00["docid"];
                                                    echo "<option value=".$id00.">$sn</option><br/>";
                                                };
                                            ?>
                                        </select>
                                    </td>
                                    <td width="12%">
                                        <input type="submit"  name="filter" value=" Filter" class=" btn-primary-soft btn button-icon btn-filter"  style="padding: 15px; margin :0;width:100%">
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
                <?php
                    if($_POST){
                        $sqlpt1 = "";
                        if(!empty($_POST["sheduledate"])){
                            $sheduledate = $_POST["sheduledate"];
                            $sqlpt1 = " schedule.scheduledate='$sheduledate' ";
                        }
                        $sqlpt2 = "";
                        if(!empty($_POST["docid"])){
                            $docid = $_POST["docid"];
                            $sqlpt2 = " doctor.docid=$docid ";
                        }
                        $sqlmain = "SELECT schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.start_time, schedule.end_time 
                                    FROM schedule 
                                    INNER JOIN doctor ON schedule.docid = doctor.docid 
                                    WHERE doctor.archived = 0";
                        $sqllist = array($sqlpt1, $sqlpt2);
                        $sqlkeywords = array(" WHERE ", " AND ");
                        $key2 = 0;
                        foreach($sqllist as $key){
                            if(!empty($key)){
                                $sqlmain .= $sqlkeywords[$key2] . $key;
                                $key2++;
                            };
                        };
                    } else {
                        $sqlmain = "SELECT schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.start_time, schedule.end_time 
                                    FROM schedule 
                                    INNER JOIN doctor ON schedule.docid = doctor.docid 
                                    WHERE doctor.archived = 0 
                                    ORDER BY schedule.scheduledate DESC";
                    }
                ?>
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                            <table width="93%" class="sub-table scrolldown" border="0">
                                <thead>
                                    <tr>
                                        <th class="table-headin">Session Title</th>
                                        <th class="table-headin">Doctor</th>
                                        <th class="table-headin">Scheduled Date & Time</th>
                                        <th class="table-headin">Events</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = $database->query($sqlmain);
                                        if($result->num_rows == 0){
                                            echo '<tr>
                                                <td colspan="4">
                                                    <br><br><br><br>
                                                    <center>
                                                        <img src="../img/notfound.svg" width="25%">
                                                        <br>
                                                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords!</p>
                                                        <a class="non-style-link" href="schedule.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;"> Show all Sessions </font></button></a>
                                                    </center>
                                                    <br><br><br><br>
                                                </td>
                                            </tr>';
                                        } else {
                                            for ($x = 0; $x < $result->num_rows; $x++){
                                                $row = $result->fetch_assoc();
                                                $scheduleid = $row["scheduleid"];
                                                $title = $row["title"];
                                                $docname = $row["docname"];
                                                $scheduledate = $row["scheduledate"];
                                                $start_time = date('h:i A', strtotime($row["start_time"]));
                                                $end_time = date('h:i A', strtotime($row["end_time"]));
                                                echo '<tr>
                                                    <td> '.substr($title,0,30).'</td>
                                                    <td>'.substr($docname,0,20).'</td>
                                                    <td style="text-align:center;">'.substr($scheduledate,0,10).' '.$start_time.' - '.$end_time.'</td>
                                                    <td>
                                                        <div style="display:flex;justify-content: center;">
                                                            <a href="?action=view&id='.$scheduleid.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a>
                                                            <a href="?action=drop&id='.$scheduleid.'&name='.$title.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Remove</font></button></a>
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
    if($_GET){
        $action = $_GET["action"];
        $id = isset($_GET["id"]) ? $_GET["id"] : null; // Safely check for id

        if($action == 'add-session'){
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <a class="close" href="schedule.php">×</a> 
                        <div style="display: flex;justify-content: center;">
                        <div class="abc">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                            <tr>
                                <td class="label-td" colspan="2"></td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Add New Session</p><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <form action="add-session.php" method="POST" class="add-new-form" onsubmit="return validateForm()">
                                        <label for="title" class="form-label">Session Title: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="text" name="title" id="title" class="input-text" placeholder="Name of this Session" required><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="docid" class="form-label">Select Doctor: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <select name="docid" id="" class="box" required>
                                        <option value="" disabled selected hidden>Choose Doctor Name from the list</option><br/>
                                        ';
                                        $list11 = $database->query("SELECT * FROM doctor WHERE archived = 0 ORDER BY docname ASC;");
                                        for ($y=0; $y<$list11->num_rows; $y++){
                                            $row00=$list11->fetch_assoc();
                                            $sn=$row00["docname"];
                                            $id00=$row00["docid"];
                                            echo "<option value=".$id00.">$sn</option><br/>";
                                        };
                        echo     '       </select><br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="date" class="form-label">Session Date: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="text" name="date" id="session_date" class="input-text" required><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="start_time" class="form-label">Start Time (8:00 AM - 5:30 PM): </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <select name="start_time" id="start_time" class="input-text" required onchange="updateEndTime()">
                                        <option value="">Select Start Time</option>';
                                        for ($h = 8; $h < 18; $h++) {
                                            foreach ([0, 30] as $m) {
                                                if ($h == 12 && $m == 0) continue;
                                                if ($h == 17 && $m == 30) break;
                                                $time = sprintf("%02d:%02d:00", $h, $m);
                                                $ampm = $h >= 12 ? 'PM' : 'AM';
                                                $display_h = $h > 12 ? $h - 12 : ($h == 0 ? 12 : $h);
                                                echo "<option value='$time'>$display_h:" . ($m == 0 ? '00' : '30') . " $ampm</option>";
                                            }
                                        }
                        echo '          </select><br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="duration" class="form-label">Duration: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <select name="duration" id="duration" class="input-text" required onchange="updateEndTime()">
                                        <option value="30">30 minutes</option>
                                        <option value="60">1 hour</option>
                                        <option value="90">1 hour 30 minutes</option>
                                        <option value="120">2 hours</option>
                                    </select><br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="end_time" class="form-label">End Time: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="text" name="end_time" id="end_time" class="input-text" readonly><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="gmeet_link" class="form-label">Google Meet Link (Optional): </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="url" name="gmeet_link" id="gmeet_link" class="input-text" placeholder="e.g., https://meet.google.com/abc-defg-hij"><br>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >    
                                    <input type="submit" value="Place this Session" class="login-btn btn-primary btn" name="shedulesubmit">
                                </td>
                            </tr>
                            </form>
                            </tr>
                        </table>
                        </div>
                        </div>
                    </center>
                    <br><br>
            </div>
            </div>';
        } elseif($action == 'session-added'){
            $titleget = $_GET["title"];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <br><br>
                        <h2>Session Placed</h2>
                        <a class="close" href="schedule.php">×</a>
                        <div class="content">
                            '.substr($titleget,0,40).' was scheduled.<br><br>
                        </div>
                        <div style="display: flex;justify-content: center;">
                            <a href="schedule.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">OK</font></button></a>
                            <br><br><br><br>
                        </div>
                    </center>
            </div>
            </div>';
        } elseif($action == 'add-session-conflict'){
            $titleget = $_GET["title"];
            $dateget = $_GET["date"];
            $start_timeget = $_GET["start_time"];
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <br><br>
                        <h2>Session Conflict</h2>
                        <a class="close" href="schedule.php">×</a>
                        <div class="content">
                            The doctor already has a session scheduled on ' . htmlspecialchars($dateget) . ' at ' . htmlspecialchars(date('h:i A', strtotime($start_timeget))) . '.<br>
                            Cannot schedule "' . htmlspecialchars($titleget) . '". Please choose a different time or date.
                        </div>
                        <div style="display: flex;justify-content: center;">
                            <a href="?action=add-session" class="non-style-link"><button class="btn-primary btn" style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">Try Again</font></button></a>
                            <a href="schedule.php" class="non-style-link"><button class="btn-primary btn" style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">Cancel</font></button></a>
                        </div>
                    </center>
                </div>
            </div>';
        } elseif($action == 'add-session-limit-exceeded'){
            $titleget = $_GET["title"];
            $dateget = $_GET["date"];
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <br><br>
                        <h2>Session Limit Exceeded</h2>
                        <a class="close" href="schedule.php">×</a>
                        <div class="content">
                            The doctor already has 5 sessions scheduled on ' . htmlspecialchars($dateget) . '.<br>
                            Cannot schedule "' . htmlspecialchars($titleget) . '". Maximum limit is 5 sessions per day.
                        </div>
                        <div style="display: flex;justify-content: center;">
                            <a href="?action=add-session" class="non-style-link"><button class="btn-primary btn" style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">Try Again</font></button></a>
                            <a href="schedule.php" class="non-style-link"><button class="btn-primary btn" style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">Cancel</font></button></a>
                        </div>
                    </center>
                </div>
            </div>';
        } elseif($action == 'drop'){
            if ($id === null) {
                echo "Error: No session ID provided for deletion.";
            } else {
                $nameget = $_GET["name"];
                echo '
                <div id="popup1" class="overlay">
                        <div class="popup">
                        <center>
                            <h2>Are you sure?</h2>
                            <a class="close" href="schedule.php">×</a>
                            <div class="content">
                                You want to delete this record<br>('.substr($nameget,0,40).').
                            </div>
                            <div style="display: flex;justify-content: center;">
                                <a href="delete-session.php?id='.$id.'" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">Yes</font></button></a>   
                                <a href="schedule.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">No</font></button></a>
                            </div>
                        </center>
                </div>
                </div>';
            }
        } elseif($action == 'view'){
            if ($id === null) {
                echo "Error: No session ID provided for viewing.";
            } else {
                $sqlmain = "SELECT schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.start_time, schedule.end_time, schedule.gmeet_link 
                            FROM schedule 
                            INNER JOIN doctor ON schedule.docid = doctor.docid 
                            WHERE schedule.scheduleid = $id AND doctor.archived = 0";
                $result = $database->query($sqlmain);
                if ($result->num_rows == 0) {
                    echo "Error: Session not found or doctor is archived.";
                } else {
                    $row = $result->fetch_assoc();
                    $docname = $row["docname"];
                    $scheduleid = $row["scheduleid"];
                    $title = $row["title"];
                    $scheduledate = $row["scheduledate"];
                    $start_time = date('h:i A', strtotime($row["start_time"]));
                    $end_time = date('h:i A', strtotime($row["end_time"]));
                    $gmeet_link = $row["gmeet_link"] ?: "Not provided";
                    $sqlmain12 = "SELECT * 
                                  FROM appointment 
                                  INNER JOIN patient ON patient.pid = appointment.pid 
                                  INNER JOIN schedule ON schedule.scheduleid = appointment.scheduleid 
                                  WHERE schedule.scheduleid = $id;";
                    $result12 = $database->query($sqlmain12);
                    echo '
                    <div id="popup1" class="overlay">
                        <div class="popup" style="width: 70%;">
                            <center>
                                <h2></h2>
                                <a class="close" href="schedule.php">×</a>
                                <div class="content"></div>
                                <div class="abc scroll" style="display: flex;justify-content: center;">
                                    <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                        <tr>
                                            <td>
                                                <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details</p><br><br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-td" colspan="2">
                                                <label for="name" class="form-label">Session Title: </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-td" colspan="2">
                                                '.$title.'<br><br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-td" colspan="2">
                                                <label for="Email" class="form-label">Doctor of this session: </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-td" colspan="2">
                                                '.$docname.'<br><br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-td" colspan="2">
                                                <label for="nic" class="form-label">Scheduled Date: </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-td" colspan="2">
                                                '.$scheduledate.'<br><br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-td" colspan="2">
                                                <label for="Tele" class="form-label">Scheduled Time: </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-td" colspan="2">
                                                '.$start_time.' - '.$end_time.'<br><br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-td" colspan="2">
                                                <label for="gmeet" class="form-label">Google Meet Link: </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-td" colspan="2">
                                                '.($gmeet_link == "Not provided" ? $gmeet_link : '<a href="'.$gmeet_link.'" target="_blank">'.$gmeet_link.'</a>').'<br><br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-td" colspan="2">
                                                <label for="spec" class="form-label"><b>Patients that Already registered for this session:</b> ('.$result12->num_rows.')</label>
                                                <br><br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">
                                                <center>
                                                    <div class="abc scroll">
                                                        <table width="100%" class="sub-table scrolldown" border="0">
                                                            <thead>
                                                                <tr>   
                                                                    <th class="table-headin">Patient ID</th>
                                                                    <th class="table-headin">Patient name</th>
                                                                    <th class="table-headin">Appointment number</th>
                                                                    <th class="table-headin">Patient Telephone</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>';
                                                                $result = $database->query($sqlmain12);
                                                                if($result->num_rows == 0){
                                                                    echo '<tr>
                                                                        <td colspan="7">
                                                                            <br><br><br><br>
                                                                            <center>
                                                                                <img src="../img/notfound.svg" width="25%">
                                                                                <br>
                                                                                <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords!</p>
                                                                                <a class="non-style-link" href="appointment.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;"> Show all Appointments </font></button></a>
                                                                            </center>
                                                                            <br><br><br><br>
                                                                        </td>
                                                                    </tr>';
                                                                } else {
                                                                    for ($x = 0; $x < $result->num_rows; $x++){
                                                                        $row = $result->fetch_assoc();
                                                                        $apponum = $row["apponum"];
                                                                        $pid = $row["pid"];
                                                                        $pname = $row["pname"];
                                                                        $ptel = $row["ptel"];
                                                                        echo '<tr style="text-align:center;">
                                                                            <td>'.substr($pid,0,15).'</td>
                                                                            <td style="font-weight:600;padding:25px">'.substr($pname,0,25).'</td>
                                                                            <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">'.$apponum.'</td>
                                                                            <td>'.substr($ptel,0,25).'</td>
                                                                        </tr>';
                                                                    }
                                                                }
                                                            echo '</tbody>
                                                        </table>
                                                    </div>
                                                </center>
                                            </td> 
                                        </tr>
                                    </table>
                                </div>
                            </center>
                            <br><br>
                    </div>
                    </div>';
                }
            }
        }
    }
    ?>
    </div>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    function toggleNotifications() {
        var dropdown = document.getElementById('notificationDropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    document.addEventListener('click', function(event) {
        var bell = document.querySelector('.notification-bell');
        var dropdown = document.getElementById('notificationDropdown');
        if (!bell.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });

    function updateEndTime() {
        let startTime = document.getElementById('start_time').value;
        let duration = parseInt(document.getElementById('duration').value);
        if (startTime && duration) {
            let [hours, minutes] = startTime.split(':').map(Number);
            let totalMinutes = hours * 60 + minutes + duration;
            let newHours = Math.floor(totalMinutes / 60);
            let newMinutes = totalMinutes % 60;
            let ampm = newHours >= 12 ? 'PM' : 'AM';
            newHours = newHours > 12 ? newHours - 12 : (newHours == 0 ? 12 : newHours);
            let endTime = `${newHours}:${newMinutes < 10 ? '0' + newMinutes : newMinutes} ${ampm}`;
            document.getElementById('end_time').value = endTime;
        }
    }

    // Initialize Flatpickr with Sunday disabled
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr('#session_date', {
            minDate: '<?php echo $today; ?>', // e.g., "2025-04-04"
            maxDate: '<?php echo $oneWeekLater; ?>', // e.g., "2025-04-11"
            disable: [
                function(date) {
                    // Disable Sundays (getDay() === 0)
                    return date.getDay() === 0;
                }
            ],
            dateFormat: 'Y-m-d', // Format for form submission
            onOpen: function(selectedDates, dateStr, instance) {
                instance.redraw(); // Ensure calendar updates correctly
            }
        });
    });

    function validateForm() {
        let sessionDate = document.getElementById('session_date').value;
        let startTime = document.getElementById('start_time').value;
        let duration = document.getElementById('duration').value;
        let today = new Date();
        today.setHours(0, 0, 0, 0);
        let title = document.getElementById('title').value.trim();
        let gmeetLink = document.getElementById('gmeet_link').value.trim();

        if (title === '') {
            alert('Session title cannot be empty');
            return false;
        }
        if (!sessionDate) {
            alert('Please select a session date');
            return false;
        }
        if (new Date(sessionDate) < today) {
            alert('Session date cannot be in the past');
            return false;
        }
        if (!startTime || !duration) {
            alert('Please select start time and duration');
            return false;
        }
        let [hours] = startTime.split(':').map(Number);
        let endMinutes = (hours * 60 + parseInt(duration)) % 1440;
        let endHour = Math.floor(endMinutes / 60);
        if (hours < 8 || endHour > 18 || (hours < 13 && endHour > 12)) {
            alert('Time must be between 8:00 AM - 6:00 PM, excluding 12:00 PM - 1:00 PM');
            return false;
        }
        if (gmeetLink && !gmeetLink.match(/^https:\/\/meet\.google\.com\/[a-z]{3}-[a-z]{4}-[a-z]{3}$/)) {
            alert('Please enter a valid Google Meet link (e.g., https://meet.google.com/abc-defg-hij)');
            return false;
        }
        return true;
    }
    </script>
</body>
</html>