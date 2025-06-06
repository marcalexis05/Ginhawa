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
        .dashbord-tables { animation: transitionIn-Y-over 0.5s; }
        .filter-container { animation: transitionIn-Y-bottom 0.5s; }
        .sub-table { animation: transitionIn-Y-bottom 0.5s; }
        .menu-icon-attendance {
            background-image: url('../img/icons/attendance.svg');
        }
        .menu-icon-attendance-active {
            background-image: url('../img/icons/attendance-hover.svg');
        }
        .notification-bell { 
            position: relative; 
            display: inline-block; 
            margin-right: 20px;
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
            fill: #333;
        }
        .bell-icon:hover { 
            fill: #357960;
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
        .notification-item:last-child { 
            border-bottom: none; 
        }
        .btn-view { 
            background-color: #357960; 
            color: white; 
            border: none; 
            padding: 6px 12px; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 14px; 
            font-weight: 500; 
            transition: background-color 0.3s; 
        }
        .btn-view:hover { 
            background-color: #2a5f4a; 
        }
        .view-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        .view-modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 400px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .view-modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            text-decoration: none;
            color: #333;
        }
        .btn-remove {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        .btn-remove:hover {
            background-color: #cc3333;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'a') {
        header("location: ../login.php");
        exit;
    }
    include("../connection.php");
    
    date_default_timezone_set('Asia/Manila');
    $today = date('Y-m-d');
    
    // Check active doctors
    $active_doctors = $database->query("SELECT COUNT(*) as count FROM doctor_attendance WHERE date = '$today' AND time_in IS NOT NULL AND time_out IS NULL");
    if ($active_doctors === false) {
        echo "Error in active doctors query: " . $database->error;
        $active_count = 0;
    } else {
        $active_count = $active_doctors->fetch_assoc()['count'];
    }

    // Fetch approved session requests
    $notify_query = "SELECT sr.*, d.docname 
                     FROM session_requests sr 
                     INNER JOIN doctor d ON sr.docid = d.docid 
                     WHERE sr.status = 'approved'";
    $notify_result = $database->query($notify_query);
    $notify_count = ($notify_result && $notify_result->num_rows > 0) ? $notify_result->num_rows : 0;

    // Handle remove action
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_session'])) {
        $request_id = $_POST['request_id'];

        // Update session request status to 'processed'
        $update_query = "UPDATE session_requests SET status = 'processed' WHERE request_id = ?";
        $stmt = $database->prepare($update_query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();

        // Redirect to refresh the page
        header("Location: index.php");
        exit;
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
                                    <p class="profile-title">Administrator</p>
                                    <p class="profile-subtitle">admin@ginhawa.com</p>
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
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Dashboard</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Doctors</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-schedule">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
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
        
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style="border-spacing:0;margin:0;padding:0;">
                <tr>
                    <td colspan="2" class="nav-bar">
                        <form action="doctors.php" method="post" class="header-search">
                            <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name or Email" list="doctors">  
                            <?php
                            $list11 = $database->query("select docname,docemail from doctor;");
                            if ($list11 === false) {
                                echo "Error in doctor search query: " . $database->error;
                            } else {
                                echo '<datalist id="doctors">';
                                for ($y = 0; $y < $list11->num_rows; $y++) {
                                    $row00 = $list11->fetch_assoc();
                                    $d = $row00["docname"];
                                    $c = $row00["docemail"];
                                    echo "<option value='$d'><br/>";
                                    echo "<option value='$c'><br/>";
                                }
                                echo '</datalist>';
                            }
                            ?>
                            <input type="Submit" value="Search" class="login-btn btn-primary-soft btn" style="padding-left:25px;padding-right:25px;padding-top:10px;padding-bottom:10px;">
                        </form>
                    </td>
                    <td width="25%">
                        <a href="#" class="non-style-link">
                            <div class="notification-bell" onclick="toggleNotifications()">
                                <svg class="bell-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                                </svg>
                                <?php if ($notify_count > 0) { ?>
                                    <span class="notification-count"><?php echo $notify_count; ?></span>
                                <?php } ?>
                                <div class="notification-dropdown" id="notificationDropdown">
                                    <?php
                                    if ($notify_result && $notify_result->num_rows > 0) {
                                        while ($notify = $notify_result->fetch_assoc()) {
                                            $request_id = $notify['request_id'];
                                            $start_time_12hr = date('h:i A', strtotime($notify['start_time']));
                                            $end_time_12hr = isset($notify['end_time']) ? date('h:i A', strtotime($notify['end_time'])) : 'N/A';
                                            echo '<div class="notification-item">';
                                            echo '<strong>' . htmlspecialchars($notify['docname']) . '</strong><br>';
                                            echo 'Title: ' . htmlspecialchars($notify['title']) . '<br>';
                                            echo 'Date: ' . htmlspecialchars($notify['session_date']) . '<br>';
                                            echo 'Time: ' . $start_time_12hr . '<br>';
                                            echo '<button class="btn-view" onclick="showViewModal(' . $request_id . ', \'' . htmlspecialchars($notify['title']) . '\', \'' . $notify['session_date'] . '\', \'' . $start_time_12hr . '\', \'' . $end_time_12hr . '\')">View</button>';
                                            echo '</div>';
                                        }
                                    } else {
                                        echo '<div class="notification-item">No approved session requests</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </a>
                    </td>
                    <td width="15%">
                        <p style="font-size:14px;color:rgb(119,119,119);padding:0;margin:0;text-align:right;">Today's Date</p>
                        <p class="heading-sub12" style="padding:0;margin:0;">
                            <?php 
                            echo $today;
                            $patientrow = $database->query("select * from patient;");
                            $doctorrow = $database->query("select * from doctor;");
                            $appointmentrow = $database->query("select * from appointment where appodate>='$today';");
                            $schedulerow = $database->query("select * from schedule where scheduledate='$today';");
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display:flex;justify-content:center;align-items:center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <center>
                        <table class="filter-container" style="border:none;" border="0">
                            <tr>
                                <td colspan="4">
                                    <p style="font-size:20px;font-weight:600;padding-left:12px;">Status</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:25%;">
                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display:flex">
                                        <div>
                                            <div class="h1-dashboard"><?php echo ($doctorrow === false) ? 0 : $doctorrow->num_rows ?></div><br>
                                            <div class="h3-dashboard">Doctors</div>
                                        </div>
                                        <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/doctors-hover.svg');"></div>
                                    </div>
                                </td>
                                <td style="width:25%;">
                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display:flex;">
                                        <div>
                                            <div class="h1-dashboard"><?php echo ($patientrow === false) ? 0 : $patientrow->num_rows ?></div><br>
                                            <div class="h3-dashboard">Patients</div>
                                        </div>
                                        <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/patients-hover.svg');"></div>
                                    </div>
                                </td>
                                <td style="width:25%;">
                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display:flex;">
                                        <div>
                                            <div class="h1-dashboard"><?php echo ($appointmentrow === false) ? 0 : $appointmentrow->num_rows ?></div><br>
                                            <div class="h3-dashboard">New Booking</div>
                                        </div>
                                        <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/book-hover.svg');"></div>
                                    </div>
                                </td>
                                <td style="width:25%;">
                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display:flex;padding-top:26px;padding-bottom:26px;">
                                        <div>
                                            <div class="h1-dashboard"><?php echo ($schedulerow === false) ? 0 : $schedulerow->num_rows ?></div><br>
                                            <div class="h3-dashboard" style="font-size:15px">Today Sessions</div>
                                        </div>
                                        <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/session-iceblue.svg');"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:25%;">
                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display:flex;">
                                        <div>
                                            <div class="h1-dashboard"><?php echo $active_count ?></div><br>
                                            <div class="h3-dashboard">Active Doctors</div>
                                        </div>
                                        <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/doctors-hover.svg');"></div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        </center>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table width="100%" border="0" class="dashbord-tables">
                            <tr>
                                <td>
                                    <p style="padding:10px;padding-left:48px;padding-bottom:0;font-size:23px;font-weight:700;color:var(--primarycolor);">
                                        Upcoming Appointments until Next <?php echo date("l", strtotime("+1 week")); ?>
                                    </p>
                                    <p style="padding-bottom:19px;padding-left:50px;font-size:15px;font-weight:500;color:#212529e3;line-height:20px;">
                                        Here's Quick access to Upcoming Appointments until 7 days<br>
                                        More details available in @Appointment section.
                                    </p>
                                </td>
                                <td>
                                    <p style="text-align:right;padding:10px;padding-right:48px;padding-bottom:0;font-size:23px;font-weight:700;color:var(--primarycolor);">
                                        Upcoming Sessions until Next <?php echo date("l", strtotime("+1 week")); ?>
                                    </p>
                                    <p style="padding-bottom:19px;text-align:right;padding-right:50px;font-size:15px;font-weight:500;color:#212529e3;line-height:20px;">
                                        Here's Quick access to Upcoming Sessions that Scheduled until 7 days<br>
                                        Add,Remove and Many features available in @Schedule section.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%">
                                    <center>
                                        <div class="abc scroll" style="height:200px;">
                                        <table width="85%" class="sub-table scrolldown" border="0">
                                        <thead>
                                        <tr>
                                            <th class="table-headin" style="font-size:12px;">Appointment number</th>
                                            <th class="table-headin">Patient name</th>
                                            <th class="table-headin">Doctor</th>
                                            <th class="table-headin">Session</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $nextweek = date("Y-m-d", strtotime("+1 week"));
                                            $sqlmain = "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.start_time,schedule.end_time,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid where schedule.scheduledate>='$today' and schedule.scheduledate<='$nextweek' order by schedule.scheduledate desc";
                                            $result = $database->query($sqlmain);
                                            
                                            if ($result === false) {
                                                echo '<tr><td colspan="4">Error in query: ' . $database->error . '</td></tr>';
                                            } elseif ($result->num_rows == 0) {
                                                echo '<tr><td colspan="4"><br><br><br><br><center><img src="../img/notfound.svg" width="25%"><br><p class="heading-main12" style="margin-left:45px;font-size:20px;color:rgb(49,49,49)">We couldn\'t find anything related to your keywords!</p><a class="non-style-link" href="appointment.php"><button class="login-btn btn-primary-soft btn" style="display:flex;justify-content:center;align-items:center;margin-left:20px;">Show all Appointments</button></a></center><br><br><br><br></td></tr>';
                                            } else {
                                                for ($x = 0; $x < $result->num_rows; $x++) {
                                                    $row = $result->fetch_assoc();
                                                    $appoid = $row["appoid"];
                                                    $scheduleid = $row["scheduleid"];
                                                    $title = $row["title"];
                                                    $docname = $row["docname"];
                                                    $scheduledate = $row["scheduledate"];
                                                    $start_time = date('h:i A', strtotime($row["start_time"]));
                                                    $end_time = date('h:i A', strtotime($row["end_time"]));
                                                    $pname = $row["pname"];
                                                    $apponum = $row["apponum"];
                                                    $appodate = $row["appodate"];
                                                    echo '<tr><td style="text-align:center;font-size:23px;font-weight:500;color:var(--btnnicetext);padding:20px;">' . sprintf("%02d", $apponum) . '</td><td style="font-weight:600;">' . substr($pname, 0, 25) . '</td><td style="font-weight:600;">' . substr($docname, 0, 25) . '</td><td>' . substr($title, 0, 15) . '</td></tr>';
                                                }
                                            }
                                            ?>
                                        </tbody>
                                        </table>
                                        </div>
                                    </center>
                                </td>
                                <td width="50%" style="padding:0;">
                                    <center>
                                        <div class="abc scroll" style="height:200px;padding:0;margin:0;">
                                        <table width="85%" class="sub-table scrolldown" border="0">
                                        <thead>
                                        <tr>
                                            <th class="table-headin">Session Title</th>
                                            <th class="table-headin">Doctor</th>
                                            <th class="table-headin">Scheduled Date & Time</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $nextweek = date("Y-m-d", strtotime("+1 week"));
                                            $sqlmain = "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.start_time,schedule.end_time from schedule inner join doctor on schedule.docid=doctor.docid where schedule.scheduledate>='$today' and schedule.scheduledate<='$nextweek' order by schedule.scheduledate desc";
                                            $result = $database->query($sqlmain);
                                            
                                            if ($result === false) {
                                                echo '<tr><td colspan="3">Error in query: ' . $database->error . '</td></tr>';
                                            } elseif ($result->num_rows == 0) {
                                                echo '<tr><td colspan="3"><br><br><br><br><center><img src="../img/notfound.svg" width="25%"><br><p class="heading-main12" style="margin-left:45px;font-size:20px;color:rgb(49,49,49)">We couldn\'t find anything related to your keywords!</p><a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display:flex;justify-content:center;align-items:center;margin-left:20px;">Show all Sessions</button></a></center><br><br><br><br></td></tr>';
                                            } else {
                                                for ($x = 0; $x < $result->num_rows; $x++) {
                                                    $row = $result->fetch_assoc();
                                                    $scheduleid = $row["scheduleid"];
                                                    $title = $row["title"];
                                                    $docname = $row["docname"];
                                                    $scheduledate = $row["scheduledate"];
                                                    $start_time = date('h:i A', strtotime($row["start_time"]));
                                                    $end_time = date('h:i A', strtotime($row["end_time"]));
                                                    echo '<tr><td style="padding:20px;">' . substr($title, 0, 30) . '</td><td>' . substr($docname, 0, 20) . '</td><td style="text-align:center;">' . substr($scheduledate, 0, 10) . ' ' . $start_time . ' - ' . $end_time . '</td></tr>';
                                                }
                                            }
                                            ?>
                                        </tbody>
                                        </table>
                                        </div>
                                    </center>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <center>
                                        <a href="appointment.php" class="non-style-link"><button class="btn-primary btn" style="width:85%">Show all Appointments</button></a>
                                    </center>
                                </td>
                                <td>
                                    <center>
                                        <a href="schedule.php" class="non-style-link"><button class="btn-primary btn" style="width:85%">Show all Sessions</button></a>
                                    </center>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- View Session Modal -->
    <div id="viewModal" class="view-modal">
        <div class="view-modal-content">
            <a class="view-modal-close" href="#" onclick="hideViewModal()">×</a>
            <center>
                <h2>View Session Request</h2>
                <form method="post" action="">
                    <input type="hidden" name="request_id" id="view_request_id">
                    <table width="100%" border="0">
                        <tr>
                            <td class="label-td">
                                <label class="form-label">Session Title:</label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td">
                                <span id="view_title"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td">
                                <label class="form-label">Session Date:</label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td">
                                <span id="view_session_date"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td">
                                <label class="form-label">Start Time:</label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td">
                                <span id="view_start_time"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td">
                                <label class="form-label">End Time:</label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td">
                                <span id="view_end_time"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" style="text-align: center; padding-top: 20px;">
                                <button type="submit" name="remove_session" class="btn-remove">Remove</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </center>
        </div>
    </div>

    <script>
        function toggleNotifications() {
            var dropdown = document.getElementById('notificationDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        window.onclick = function(event) {
            if (!event.target.closest('.notification-bell') && !event.target.closest('.view-modal')) {
                var dropdown = document.getElementById('notificationDropdown');
                var modal = document.getElementById('viewModal');
                if (dropdown.style.display === 'block') {
                    dropdown.style.display = 'none';
                }
                if (modal.style.display === 'block') {
                    modal.style.display = 'none';
                }
            }
        }

        function showViewModal(requestId, title, sessionDate, startTime, endTime) {
            document.getElementById('view_request_id').value = requestId;
            document.getElementById('view_title').textContent = title;
            document.getElementById('view_session_date').textContent = sessionDate;
            document.getElementById('view_start_time').textContent = startTime;
            document.getElementById('view_end_time').textContent = endTime;
            document.getElementById('viewModal').style.display = 'block';
        }

        function hideViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }
    </script>
</body>
</html>