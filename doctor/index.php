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

// Notification logic from schedule.php
$request_count_query = "SELECT COUNT(*) as pending_count FROM patient_requests WHERE doctor_id = $userid AND status = 'pending'";
$request_count_result = $database->query($request_count_query);
$pending_count = $request_count_result->fetch_assoc()['pending_count'];

$requests_query = "SELECT pr.*, p.pname FROM patient_requests pr 
                  INNER JOIN patient p ON pr.patient_id = p.pid 
                  WHERE pr.doctor_id = $userid AND pr.status = 'pending'";
$requests_result = $database->query($requests_query);
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
    <!-- SweetAlert2 CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
        /* Notification styles from schedule.php */
        .notification-bell { position: relative; display: inline-block; cursor: pointer; }
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
        }
        .notification-item:last-child { border-bottom: none; }
        .notification-actions { 
            display: flex; 
            justify-content: space-between; 
            margin-top: 10px; 
        }
        .btn-approve { background-color: #28a745; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; }
        .btn-reject { background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; }
        .bell-icon { width: 30px; height: 30px; fill: #333; }
        .bell-icon:hover { fill: #007bff; }
        .gmeet-link { color: #007bff; text-decoration: underline; cursor: pointer; }
        .gmeet-link:hover { color: #0056b3; }
        .rejection-modal { 
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background-color: rgba(0,0,0,0.5); 
        }
        .modal-content { 
            background-color: white; 
            margin: 15% auto; 
            padding: 20px; 
            border-radius: 5px; 
            width: 300px; 
            text-align: center; 
        }
        .modal-content select { 
            width: 100%; 
            padding: 5px; 
            margin: 10px 0; 
        }
        .modal-content button { 
            padding: 5px 10px; 
            margin: 5px; 
            border: none; 
            border-radius: 3px; 
            cursor: pointer; 
        }
        .btn-submit { background-color: #dc3545; color: white; }
        .btn-cancel { background-color: #6c757d; color: white; }
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
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">My Cases</p></div></a>
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
                        <!-- Notification bell from schedule.php -->
                        <div class="notification-bell" onclick="toggleNotifications()">
                            <svg class="bell-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                            </svg>
                            <?php if ($pending_count > 0) { ?>
                                <span class="notification-count"><?php echo $pending_count; ?></span>
                            <?php } ?>
                            <div class="notification-dropdown" id="notificationDropdown" style="display: none;">
                                <?php
                                if ($requests_result->num_rows > 0) {
                                    while ($request = $requests_result->fetch_assoc()) {
                                        $request_id = $request['request_id'];
                                        $start_time = DateTime::createFromFormat('H:i:s', $request["start_time"]);
                                        $start_time_display = $start_time ? $start_time->format('h:i A') : 'Invalid Time';
                                        $gmeet_request = $request['gmeet_request'] ? '<br><strong>Google Meet Requested</strong>' : '';
                                        echo '<div class="notification-item">';
                                        echo '<strong>' . htmlspecialchars($request['pname']) . '</strong><br>';
                                        echo 'Title: ' . htmlspecialchars($request['title']) . '<br>';
                                        echo 'Date: ' . $request['session_date'] . ' ' . $start_time_display . $gmeet_request . '<br>';
                                        echo '<div class="notification-actions">';
                                        echo '<a href="handle_patient_request.php?action=approve&id=' . $request_id . '"><button class="btn-approve">Approve</button></a>';
                                        echo '<button class="btn-reject" onclick="showRejectionModal(' . $request_id . ')">Reject</button>';
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<div class="notification-item">No pending requests</div>';
                                }
                                ?>
                            </div>
                        </div>
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
                                        You can view your daily schedule, Reach Cases Appointment at home!<br><br>
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
                                                            <div class="h3-dashboard">All Cases</div>
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
                                                        <th class="table-headin">Case Name</th>
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
    <!-- Rejection Modal from schedule.php -->
    <div id="rejectionModal" class="rejection-modal">
        <div class="modal-content">
            <h3>Reason for Rejection</h3>
            <form id="rejectionForm" method="POST" action="handle_patient_request.php">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="id" id="rejectRequestId">
                <select name="rejection_reason" required>
                    <option value="">Select a reason</option>
                    <option value="Schedule Conflict">Schedule Conflict</option>
                    <option value="Insufficient Information">Insufficient Information</option>
                    <option value="Not Available">Not Available</option>
                    <option value="Other">Other</option>
                </select>
                <div>
                    <button type="submit" class="btn-submit">Reject</button>
                    <button type="button" class="btn-cancel" onclick="closeRejectionModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <!-- JavaScript from schedule.php -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function toggleNotifications() {
        var dropdown = document.getElementById('notificationDropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    window.onclick = function(event) {
        if (!event.target.closest('.notification-bell') && !event.target.closest('#rejectionModal')) {
            var dropdown = document.getElementById('notificationDropdown');
            if (dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
            }
        }
    }

    function showRejectionModal(requestId) {
        document.getElementById('rejectRequestId').value = requestId;
        document.getElementById('rejectionModal').style.display = 'block';
    }

    function closeRejectionModal() {
        document.getElementById('rejectionModal').style.display = 'none';
    }
    </script>
</body>
</html>