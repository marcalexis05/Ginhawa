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
            fill: #333;
        }
        .bell-icon:hover { 
            fill: #28a745;
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
        .btn-edit {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            background-image: none;
        }
        .notification-actions .btn-edit {
        background-image: none; /* Ensure no icon appears */
        }

        #addSessionPopup {
            display: none;
        }
        .notification-section {
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .notification-section h4 {
            margin: 10px 0 5px 10px;
            font-size: 16px;
            color: #333;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION['usertype'] != 'a') {
        header("location: ../login.php");
        exit();
    }

    include("../connection.php");

    date_default_timezone_set('Asia/Kolkata');
    $today = date('Y-m-d');
    $oneWeekLater = date('Y-m-d', strtotime('+7 days'));

    // Count pending session requests
    $request_query = $database->query("SELECT COUNT(*) as count FROM session_requests WHERE status='pending'");
    $request_count = $request_query->fetch_assoc()['count'];

    // Count sessions needing a Google Meet link
    $gmeet_query = $database->query("SELECT COUNT(*) as count FROM schedule 
                                     INNER JOIN doctor ON schedule.docid = doctor.docid 
                                     WHERE schedule.gmeet_link = '' AND doctor.archived = 0");
    $gmeet_count = $gmeet_query->fetch_assoc()['count'];

    // Total notification count
    $total_notification_count = $request_count + $gmeet_count;
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
                                    <p class="profile-title">Administrator</p>
                                    <p class="profile-subtitle">admin@edoc.com</p>
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
                    <td class="menu-btn menu-icon-dashbord"><a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor"><a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Doctors</p></a></div></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-schedule menu-active menu-icon-schedule-active"><a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Schedule</p></div></a></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment"><a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></a></div></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient"><a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-attendance"><a href="attendance.php" class="non-style-link-menu"><div><p class="menu-text">Attendance</p></div></a></td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;margin-top:25px;">
                <tr>
                    <td width="13%"><a href="schedule.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a></td>
                    <td><p style="font-size: 23px;padding-left:12px;font-weight: 600;">Schedule Manager</p></td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">Today's Date</p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;"><?php echo $today; ?></p>
                    </td>
                    <td width="10%">
                        <div class="notification-bell" onclick="toggleNotifications()">
                            <svg class="bell-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                            </svg>
                            <?php if ($total_notification_count > 0) { ?>
                                <span class="notification-count"><?php echo $total_notification_count; ?></span>
                            <?php } ?>
                            <div id="notificationDropdown" class="notification-dropdown">
                                <!-- Pending Session Requests -->
                                <div class="notification-section">
                                    <h4>Pending Session Requests (<?php echo $request_count; ?>)</h4>
                                    <?php
                                    $requests = $database->query("SELECT sr.*, d.docname FROM session_requests sr 
                                                                INNER JOIN doctor d ON sr.docid = d.docid 
                                                                WHERE sr.status='pending' AND d.archived = 0
                                                                ORDER BY sr.request_date DESC");
                                    if ($requests->num_rows == 0) {
                                        echo '<div class="notification-item">No pending session requests</div>';
                                    } else {
                                        while ($request = $requests->fetch_assoc()) {
                                            $request_id = $request['request_id'];
                                            $start_time = date('h:i A', strtotime($request["start_time"]));
                                            $end_time = date('h:i A', strtotime($request["end_time"]));
                                            $gmeet_request = isset($request['gmeet_request']) && $request['gmeet_request'] ? '<br><strong>Google Meet Requested</strong>' : '';
                                            echo '<div class="notification-item">';
                                            echo '<p><strong>' . htmlspecialchars($request['docname']) . '</strong></p>';
                                            echo '<p>Description: ' . htmlspecialchars($request['description']) . '</p>';
                                            echo '<p>Duration: ' . htmlspecialchars($request['duration']) . ' minutes</p>';
                                            echo '<p>Date: ' . htmlspecialchars($request['session_date']) . ' ' . $start_time . ' - ' . $end_time . $gmeet_request . '</p>';
                                            echo '<p>Requested: ' . htmlspecialchars($request['request_date']) . '</p>';
                                            echo '<div class="notification-actions">';
                                            echo '<a href="handle_request.php?action=reject&id=' . $request_id . '"><button class="btn-reject">Remove</button></a>';
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                </div>
                                <!-- Sessions Needing Google Meet Link -->
                                <div class="notification-section">
                                    <h4>Sessions Needing Google Meet Link (<?php echo $gmeet_count; ?>)</h4>
                                    <?php
                                    $gmeet_sessions = $database->query("SELECT s.*, d.docname FROM schedule s 
                                                                       INNER JOIN doctor d ON s.docid = d.docid 
                                                                       WHERE s.gmeet_link = '' AND d.archived = 0
                                                                       ORDER BY s.scheduledate DESC");
                                    if ($gmeet_sessions->num_rows == 0) {
                                        echo '<div class="notification-item">No sessions need a Google Meet link</div>';
                                    } else {
                                        while ($session = $gmeet_sessions->fetch_assoc()) {
                                            $schedule_id = $session['scheduleid'];
                                            $start_time = date('h:i A', strtotime($session["start_time"]));
                                            $end_time = date('h:i A', strtotime($session["end_time"]));
                                            echo '<div class="notification-item">';
                                            echo '<p><strong>' . htmlspecialchars($session['title']) . '</strong></p>';
                                            echo '<p>Doctor: ' . htmlspecialchars($session['docname']) . '</p>';
                                            echo '<p>Date: ' . htmlspecialchars($session['scheduledate']) . ' ' . $start_time . ' - ' . $end_time . '</p>';
                                            echo '<div class="notification-actions">';
                                            echo '<button class="btn-edit" onclick="openEditModal(' . $schedule_id . ')">Add GMeet Link</button>';
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display:flex;justify-content:center;align-items:center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div style="display: flex;margin-top: 40px;">
                            <div class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49);margin-top: 5px;">Schedule a Session</div>
                            <button onclick="showAddSessionPopup()" class="login-btn btn-primary btn button-icon" style="margin-left:25px;background-image: url('../img/icons/add.svg');">Add a Session</button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;">
                        <?php $list110 = $database->query("SELECT * FROM schedule INNER JOIN doctor ON schedule.docid = doctor.docid WHERE doctor.archived = 0;"); ?>
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">All Sessions (<?php echo $list110->num_rows; ?>)</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:0px;width: 100%;">
                        <center>
                            <table class="filter-container" border="0">
                                <tr>
                                    <td width="10%"></td> 
                                    <td width="5%" style="text-align: center;">Date:</td>
                                    <td width="30%">
                                        <form action="" method="post">
                                            <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="margin: 0;width: 95%;">
                                    </td>
                                    <td width="5%" style="text-align: center;">Doctor:</td>
                                    <td width="30%">
                                        <select name="docid" id="" class="box filter-container-items" style="width:90%;height: 37px;margin: 0;">
                                            <option value="" disabled selected hidden>Choose Doctor Name from the list</option>
                                            <?php 
                                            $list11 = $database->query("SELECT * FROM doctor WHERE archived = 0 ORDER BY docname ASC;");
                                            for ($y = 0; $y < $list11->num_rows; $y++) {
                                                $row00 = $list11->fetch_assoc();
                                                $sn = $row00["docname"];
                                                $id00 = $row00["docid"];
                                                echo "<option value=" . $id00 . ">$sn</option><br/>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td width="12%">
                                        <input type="submit" name="filter" value="Filter" class="btn-primary-soft btn button-icon btn-filter" style="padding: 15px; margin:0;width:100%">
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
                <?php
                if ($_POST && isset($_POST['filter'])) {
                    $conditions = [];
                    if (!empty($_POST["sheduledate"])) {
                        $sheduledate = $database->real_escape_string($_POST["sheduledate"]);
                        $conditions[] = "schedule.scheduledate='$sheduledate'";
                    }
                    if (!empty($_POST["docid"])) {
                        $docid = $database->real_escape_string($_POST["docid"]);
                        $conditions[] = "doctor.docid='$docid'";
                    }

                    $sqlmain = "SELECT schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.start_time, schedule.end_time 
                                FROM schedule 
                                INNER JOIN doctor ON schedule.docid = doctor.docid 
                                WHERE doctor.archived = 0";
                    
                    if (!empty($conditions)) {
                        $sqlmain .= " AND " . implode(" AND ", $conditions);
                    }
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
                                        if ($result->num_rows == 0) {
                                            echo '<tr><td colspan="4"><center><img src="../img/notfound.svg" width="25%"><br><p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything!</p></center></td></tr>';
                                        } else {
                                            for ($x = 0; $x < $result->num_rows; $x++) {
                                                $row = $result->fetch_assoc();
                                                $scheduleid = $row["scheduleid"];
                                                $title = $row["title"];
                                                $docname = $row["docname"];
                                                $scheduledate = $row["scheduledate"];
                                                $start_time = date('h:i A', strtotime($row["start_time"]));
                                                $end_time = date('h:i A', strtotime($row["end_time"]));
                                                echo '<tr>
                                                    <td>' . htmlspecialchars(substr($title, 0, 30)) . '</td>
                                                    <td>' . htmlspecialchars(substr($docname, 0, 20)) . '</td>
                                                    <td style="text-align:center;">' . htmlspecialchars($scheduledate) . ' ' . $start_time . ' - ' . $end_time . '</td>
                                                    <td>
                                                        <div style="display:flex;justify-content: center;">
                                                            <a href="?action=view&id=' . $scheduleid . '" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-view" style="padding:12px 40px;margin:10px;"><font class="tn-in-text">View</font></button></a>
                                                            <a href="?action=drop&id=' . $scheduleid . '&name=' . urlencode($title) . '" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-delete" style="padding:12px 40px;margin:10px;"><font class="tn-in-text">Remove</font></button></a>
                                                            <a href="?action=edit&id=' . $scheduleid . '" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-edit" style="padding:12px 40px;margin:10px;"><font class="tn-in-text">Edit GMeet</font></button></a>
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
    <div id="addSessionPopup" class="overlay">
        <div class="popup">
            <center>
                <a class="close" onclick="hideAddSessionPopup()">×</a>
                <div class="content">
                    <p style="text-align: left;font-size: 25px;font-weight: 500;">Add New Session</p>
                </div>
                <div style="display: flex;justify-content: center;">
                    <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        <tr><td><form action="add-session.php" method="POST" class="add-new-form">
                            <label for="title" class="form-label">Session Title: </label></td></tr>
                        <tr><td class="label-td" colspan="2">
                            <input type="text" name="title" class="input-text" placeholder="Title of the Session" required></td></tr>
                        <tr><td><label for="docid" class="form-label">Select Doctor: </label></td></tr>
                        <tr><td class="label-td" colspan="2">
                            <select name="docid" class="input-text" required>
                                <option value="" disabled selected>Choose Doctor</option>
                                <?php
                                $list11 = $database->query("SELECT * FROM doctor WHERE archived = 0 ORDER BY docname ASC;");
                                for ($y = 0; $y < $list11->num_rows; $y++) {
                                    $row00 = $list11->fetch_assoc();
                                    $sn = $row00["docname"];
                                    $id00 = $row00["docid"];
                                    echo "<option value='$id00'>$sn</option>";
                                }
                                ?>
                            </select></td></tr>
                        <tr><td><label for="scheduledate" class="form-label">Session Date: </label></td></tr>
                        <tr><td class="label-td" colspan="2">
                            <input type="text" name="scheduledate" id="scheduledate" class="input-text" required></td></tr>
                        <tr><td><label for="start_time" class="form-label">Start Time: </label></td></tr>
                        <tr><td class="label-td" colspan="2">
                            <select name="start_time" class="input-text" required>
                                <option value="">Select Start Time</option>
                                <?php
                                for ($h = 8; $h < 18; $h++) {
                                    foreach ([0, 30] as $m) {
                                        if ($h == 12 && $m == 0) continue; // Skip 12:00 PM (lunch break)
                                        if ($h == 17 && $m == 30) break;   // Stop before 5:30 PM
                                        $time = sprintf("%02d:%02d:00", $h, $m);
                                        $ampm = $h >= 12 ? 'PM' : 'AM';
                                        $display_h = $h > 12 ? $h - 12 : ($h == 0 ? 12 : $h);
                                        echo "<option value='$time'>$display_h:" . ($m == 0 ? '00' : '30') . " $ampm</option>";
                                    }
                                }
                                ?>
                            </select></td></tr>
                        <tr><td><label for="duration" class="form-label">Duration: </label></td></tr>
                        <tr><td class="label-td" colspan="2">
                            <select name="duration" id="duration" class="input-text" required>
                                <option value="30">30 minutes</option>
                                <option value="60">1 hour</option>
                                <option value="90">1 hour 30 minutes</option>
                                <option value="120">2 hours</option>
                            </select></td></tr>
                        <tr><td colspan="2">
                            <input type="submit" value="Add Session" class="login-btn btn-primary btn"></td></tr>
                        </form></td></tr>
                    </table>
                </div>
            </center>
        </div>
    </div>
    <?php
    if (!empty($_GET) && isset($_GET["action"])) {
        $action = $_GET["action"];
        if (in_array($action, ['drop', 'view', 'edit']) && !isset($_GET["id"])) {
            echo '<div id="popup1" class="overlay">
                    <div class="popup">
                        <center>
                            <h2>Error</h2>
                            <a class="close" href="schedule.php">×</a>
                            <div class="content">Invalid request: Session ID is missing.</div>
                            <div style="display: flex;justify-content: center;">
                                <a href="schedule.php" class="non-style-link"><button class="btn-primary btn" style="margin:10px;padding:10px;"><font class="tn-in-text">OK</font></button></a>
                            </div>
                        </center>
                    </div>
                  </div>';
        } elseif (isset($_GET["id"])) {
            $id = $database->real_escape_string($_GET["id"]);
            if ($action == 'drop') {
                $nameget = $_GET["name"] ?? 'Unknown';
                echo '
                <div id="popup1" class="overlay">
                    <div class="popup">
                        <center>
                            <h2>Are you sure?</h2>
                            <a class="close" href="schedule.php">×</a>
                            <div class="content">You want to delete this record<br>(' . htmlspecialchars(substr($nameget, 0, 40)) . ').</div>
                            <div style="display: flex;justify-content: center;">
                                <a href="delete-session.php?id=' . $id . '" class="non-style-link"><button class="btn-primary btn" style="margin:10px;padding:10px;"><font class="tn-in-text">Yes</font></button></a>
                                <a href="schedule.php" class="non-style-link"><button class="btn-primary btn" style="margin:10px;padding:10px;"><font class="tn-in-text">No</font></button></a>
                            </div>
                        </center>
                    </div>
                </div>';
            } elseif ($action == 'view') {
                $sqlmain = "SELECT schedule.scheduleid, schedule.title, schedule.scheduledate, schedule.start_time, schedule.end_time, schedule.gmeet_link, doctor.docname 
                            FROM schedule 
                            INNER JOIN doctor ON schedule.docid = doctor.docid 
                            WHERE schedule.scheduleid='$id'";
                $result = $database->query($sqlmain);
                if ($result->num_rows == 0) {
                    echo '<div id="popup1" class="overlay">
                            <div class="popup">
                                <center>
                                    <h2>Error</h2>
                                    <a class="close" href="schedule.php">×</a>
                                    <div class="content">Session not found.</div>
                                    <div style="display: flex;justify-content: center;">
                                        <a href="schedule.php" class="non-style-link"><button class="btn-primary btn" style="margin:10px;padding:10px;"><font class="tn-in-text">OK</font></button></a>
                                    </div>
                                </center>
                            </div>
                          </div>';
                } else {
                    $row = $result->fetch_assoc();
                    $docname = $row["docname"];
                    $scheduleid = $row["scheduleid"];
                    $title = $row["title"];
                    $scheduledate = $row["scheduledate"];
                    $start_time = date('h:i A', strtotime($row["start_time"]));
                    $end_time = date('h:i A', strtotime($row["end_time"]));
                    $gmeet_link = $row["gmeet_link"] ? '<a href="' . htmlspecialchars($row["gmeet_link"]) . '" target="_blank">' . htmlspecialchars($row["gmeet_link"]) . '</a>' : 'Not set';

                    $sqlmain12 = "SELECT * FROM appointment 
                                  INNER JOIN patient ON patient.pid=appointment.pid 
                                  INNER JOIN schedule ON schedule.scheduleid=appointment.scheduleid 
                                  WHERE schedule.scheduleid='$id'";
                    $result12 = $database->query($sqlmain12);

                    echo '
                    <div id="popup1" class="overlay">
                        <div class="popup" style="width: 70%;">
                            <center>
                                <a class="close" href="schedule.php">×</a>
                                <div class="content">
                                    <p style="text-align: left;font-size: 25px;font-weight: 500;">View Session Details</p>
                                    <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                        <tr><td class="label-td" colspan="2"><label class="form-label">Session Title: </label>' . htmlspecialchars($title) . '</td></tr>
                                        <tr><td class="label-td" colspan="2"><label class="form-label">Doctor: </label>' . htmlspecialchars($docname) . '</td></tr>
                                        <tr><td class="label-td" colspan="2"><label class="form-label">Scheduled Date: </label>' . htmlspecialchars($scheduledate) . '</td></tr>
                                        <tr><td class="label-td" colspan="2"><label class="form-label">Scheduled Time: </label>' . $start_time . ' - ' . $end_time . '</td></tr>
                                        <tr><td class="label-td" colspan="2"><label class="form-label">Google Meet Link: </label>' . $gmeet_link . '</td></tr>
                                        <tr><td class="label-td" colspan="2"><label class="form-label"><b>Registered Patients:</b> (' . $result12->num_rows . ')</label></td></tr>
                                        <tr><td colspan="4">
                                            <center>
                                                <div class="abc scroll">
                                                    <table width="100%" class="sub-table scrolldown" border="0">
                                                        <thead>
                                                            <tr>
                                                                <th class="table-headin">Patient ID</th>
                                                                <th class="table-headin">Patient Name</th>
                                                                <th class="table-headin">Appointment Number</th>
                                                                <th class="table-headin">Patient Telephone</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>';
                    if ($result12->num_rows == 0) {
                        echo '<tr><td colspan="4"><center><img src="../img/notfound.svg" width="25%"><p class="heading-main12" style="font-size:20px;color:rgb(49, 49, 49)">No registrations found!</p></center></td></tr>';
                    } else {
                        while ($row12 = $result12->fetch_assoc()) {
                            echo '<tr style="text-align:center;">
                                    <td>' . htmlspecialchars(substr($row12["pid"], 0, 15)) . '</td>
                                    <td style="font-weight:600;padding:25px">' . htmlspecialchars(substr($row12["pname"], 0, 25)) . '</td>
                                    <td style="font-size:23px;font-weight:500;color: var(--btnnicetext);">' . $row12["apponum"] . '</td>
                                    <td>' . htmlspecialchars(substr($row12["ptel"], 0, 25)) . '</td>
                                  </tr>';
                        }
                    }
                    echo '
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </center>
                                        </td></tr>
                                    </table>
                                </div>
                            </center>
                        </div>
                    </div>';
                }
            } elseif ($action == 'edit') {
                $sqlmain = "SELECT schedule.scheduleid, schedule.title, schedule.gmeet_link 
                            FROM schedule 
                            WHERE schedule.scheduleid = '$id'";
                $result = $database->query($sqlmain);
                if ($result->num_rows == 0) {
                    echo '<div id="popup1" class="overlay">
                            <div class="popup">
                                <center>
                                    <h2>Error</h2>
                                    <a class="close" href="schedule.php">×</a>
                                    <div class="content">Session not found.</div>
                                    <div style="display: flex;justify-content: center;">
                                        <a href="schedule.php" class="non-style-link"><button class="btn-primary btn" style="margin:10px;padding:10px;"><font class="tn-in-text">OK</font></button></a>
                                    </div>
                                </center>
                            </div>
                          </div>';
                } else {
                    $row = $result->fetch_assoc();
                    $scheduleid = $row["scheduleid"];
                    $title = $row["title"];
                    $gmeet_link = $row["gmeet_link"] ?: "";
                    echo '
                    <div id="popup1" class="overlay">
                        <div class="popup">
                            <center>
                                <a class="close" href="schedule.php">×</a>
                                <div style="display: flex;justify-content: center;">
                                    <div class="abc">
                                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                            <tr>
                                                <td>
                                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Edit Google Meet Link</p><br>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-td" colspan="2">
                                                    <form action="update-gmeet.php" method="POST" class="add-new-form">
                                                        <label for="title" class="form-label">Session Title: </label>
                                                        <input type="text" value="' . htmlspecialchars($title) . '" class="input-text" disabled><br>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-td" colspan="2">
                                                    <label for="gmeet_link" class="form-label">Google Meet Link: </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-td" colspan="2">
                                                    <input type="url" name="gmeet_link" id="gmeet_link" class="input-text" value="' . htmlspecialchars($gmeet_link) . '" placeholder="e.g., https://meet.google.com/abc-defg-hij"><br>
                                                    <input type="hidden" name="scheduleid" value="' . $scheduleid . '">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <input type="submit" value="Update" class="login-btn btn-primary btn">
                                                </td>
                                            </tr>
                                            </form>
                                        </table>
                                    </div>
                                </div>
                            </center>
                        </div>
                    </div>';
                }
            }
        }
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    flatpickr('#date', {
        minDate: '<?php echo $today; ?>',
        maxDate: '<?php echo $oneWeekLater; ?>',
        dateFormat: 'Y-m-d'
    });

    let scheduledatePicker;
    function showAddSessionPopup() {
        const popup = document.getElementById('addSessionPopup');
        popup.style.display = 'block';
        if (!scheduledatePicker) {
            scheduledatePicker = flatpickr('#scheduledate', {
                minDate: '<?php echo $today; ?>',
                maxDate: '<?php echo $oneWeekLater; ?>',
                disable: [function(date) { return date.getDay() === 0; }],
                dateFormat: 'Y-m-d'
            });
        }
    }

    function hideAddSessionPopup() {
        const popup = document.getElementById('addSessionPopup');
        popup.style.display = 'none';
    }

    function toggleNotifications() {
        var dropdown = document.getElementById('notificationDropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    function openEditModal(scheduleId) {
        window.location.href = '?action=edit&id=' + scheduleId;
    }

    window.onclick = function(event) {
        if (!event.target.closest('.notification-bell') && !event.target.closest('#addSessionPopup') && !event.target.closest('.btn-primary')) {
            var dropdown = document.getElementById('notificationDropdown');
            if (dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
            }
            var addPopup = document.getElementById('addSessionPopup');
            if (addPopup.style.display === 'block' && !event.target.closest('.popup')) {
                addPopup.style.display = 'none';
            }
        }
    }
    </script>
</body>
</html>