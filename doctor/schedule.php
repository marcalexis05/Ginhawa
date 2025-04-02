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
    <title>Schedule</title>
    <style>
        .popup { animation: transitionIn-Y-bottom 0.5s; }
        .sub-table { animation: transitionIn-Y-bottom 0.5s; }
        .request-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
        }
        .request-btn:hover { background-color: #218838; }
        .error-msg { color: red; text-align: center; margin: 10px; }
        .success-msg { color: green; text-align: center; margin: 10px; }
        .form-label { display: block; margin: 10px 0 5px; }
        .notification-bell {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
        .notification-bell img {
            width: 30px;
            height: 30px;
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
        .notification-bell {
    position: relative;
    display: inline-block;
    cursor: pointer;
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
    width: 30px; /* Ensure the width is set */
    height: 30px; /* Ensure the height is set */
    fill: #333; /* Default color */
}

.bell-icon:hover {
    fill: #007bff; /* Hover color */
}
    </style>
</head>
<body>
    <?php
    session_start();
    if(!isset($_SESSION["user"]) || $_SESSION['usertype']!='d'){
        header("location: ../login.php");
        exit();
    }
    
    $useremail = $_SESSION["user"];
    include("../connection.php");
    $userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["docid"];
    $username = $userfetch["docname"];

    // Fetch pending requests count
    $request_count_query = "SELECT COUNT(*) as pending_count FROM patient_requests WHERE doctor_id = $userid AND status = 'pending'";
    $request_count_result = $database->query($request_count_query);
    $pending_count = $request_count_result->fetch_assoc()['pending_count'];

    // Fetch pending requests
    $requests_query = "SELECT pr.*, p.pname FROM patient_requests pr 
                      INNER JOIN patient p ON pr.patient_id = p.pid 
                      WHERE pr.doctor_id = $userid AND pr.status = 'pending'";
    $requests_result = $database->query($requests_query);
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
                    <td class="menu-btn menu-icon-dashbord">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Appointments</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">My Sessions</p></div></a>
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

        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;margin-top:25px;">
                <tr>
                    <td width="13%">
                        <a href="schedule.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;">My Sessions</p>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">Today's Date</p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 
                            date_default_timezone_set('Asia/Kolkata');
                            $today = date('Y-m-d');
                            echo $today;
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                    <div class="notification-bell" onclick="toggleNotifications()">
    <svg class="bell-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 30px; height: 30px;">
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
                echo '<div class="notification-item">';
                echo '<strong>' . htmlspecialchars($request['pname']) . '</strong><br>';
                echo 'Title: ' . htmlspecialchars($request['title']) . '<br>';
                echo 'Date: ' . $request['session_date'] . ' ' . $request['session_time'] . '<br>';
                echo '<div class="notification-actions">';
                echo '<a href="handle_patient_request.php?action=approve&id=' . $request_id . '"><button class="btn-approve">Approve</button></a>';
                echo '<a href="handle_patient_request.php?action=reject&id=' . $request_id . '"><button class="btn-reject">Reject</button></a>';
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
                    <td colspan="4" style="padding-top:10px;width: 100%;">
                        <?php
                        $sqlmain = "SELECT schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop 
                                  FROM schedule INNER JOIN doctor ON schedule.docid=doctor.docid 
                                  WHERE doctor.docid=$userid";
                        $result = $database->query($sqlmain);
                        ?>
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">My Sessions (<?php echo $result->num_rows; ?>)</p>
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
                if($_POST && !empty($_POST["sheduledate"])){
                    $sheduledate = $_POST["sheduledate"];
                    $sqlmain .= " AND schedule.scheduledate='$sheduledate'";
                }
                $result = $database->query($sqlmain);
                
                if(isset($_GET['success'])) {
                    echo '<p class="success-msg">'.$_GET['success'].'</p>';
                }
                if(isset($_GET['error'])) {
                    echo '<p class="error-msg">'.$_GET['error'].'</p>';
                }
                ?>
                <tr>
                    <td colspan="4">
                        <center>
                            <button class="request-btn" onclick="document.getElementById('requestPopup').style.display='block'">Request New Session</button>
                            <div class="abc scroll">
                                <table width="93%" class="sub-table scrolldown" border="0">
                                    <thead>
                                        <tr>
                                            <th class="table-headin">Session Title</th>
                                            <th class="table-headin">Scheduled Date & Time</th>
                                            <th class="table-headin">Max Bookings</th>
                                            <th class="table-headin">Events</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if($result->num_rows == 0){
                                            echo '<tr>
                                                <td colspan="4">
                                                    <center>
                                                        <img src="../img/notfound.svg" width="25%">
                                                        <br>
                                                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">No sessions found!</p>
                                                    </center>
                                                </td>
                                            </tr>';
                                        } else {
                                            while($row = $result->fetch_assoc()){
                                                $scheduleid = $row["scheduleid"];
                                                $title = $row["title"];
                                                $scheduledate = $row["scheduledate"];
                                                $scheduletime = $row["scheduletime"];
                                                $nop = $row["nop"];
                                                echo '<tr>
                                                    <td>'.substr($title,0,30).'</td>
                                                    <td style="text-align:center;">'.substr($scheduledate,0,10).' '.substr($scheduletime,0,5).'</td>
                                                    <td style="text-align:center;">'.$nop.'</td>
                                                    <td>
                                                        <div style="display:flex;justify-content: center;">
                                                            <a href="?action=view&id='.$scheduleid.'" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-view" style="padding:12px 40px;margin:10px;"><font class="tn-in-text">View</font></button></a>
                                                            <a href="?action=drop&id='.$scheduleid.'&name='.$title.'" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-delete" style="padding:12px 40px;margin:10px;"><font class="tn-in-text">Cancel</font></button></a>
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

    <div id="requestPopup" class="overlay" style="display:none;">
        <div class="popup">
            <center>
                <h2>Request New Session</h2>
                <a class="close" onclick="document.getElementById('requestPopup').style.display='none'">×</a>
                <div class="content">
                    <form action="submit-request.php" method="post" onsubmit="return validateForm()">
                        <label for="title" class="form-label">Session Title:</label>
                        <input type="text" name="title" id="title" class="input-text" placeholder="Enter session title" required>
                        <label for="num_sessions" class="form-label">Number of Sessions (1-5):</label>
                        <input type="number" name="num_sessions" id="num_sessions" min="1" max="5" class="input-text" required>
                        <label for="session_date" class="form-label">Session Date:</label>
                        <input type="date" name="session_date" id="session_date" class="input-text" required>
                        <label for="session_time" class="form-label">Session Time:</label>
                        <input type="time" name="session_time" id="session_time" class="input-text" required>
                        <input type="hidden" name="docid" value="<?php echo $userid; ?>">
                        <div style="display: flex; justify-content: center; margin-top: 20px;">
                            <button type="submit" class="btn-primary btn">Submit Request</button>
                        </div>
                    </form>
                </div>
            </center>
        </div>
    </div>

    <?php
    if(!empty($_GET) && isset($_GET["action"]) && isset($_GET["id"])){
        $id = $_GET["id"];
        $action = $_GET["action"];
        
        if($action == 'drop'){
            $nameget = isset($_GET["name"]) ? $_GET["name"] : '';
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
                            <a href="delete-session.php?id='.$id.'" class="non-style-link"><button class="btn-primary btn" style="margin:10px;padding:10px;"><font class="tn-in-text">Yes</font></button></a>
                            <a href="schedule.php" class="non-style-link"><button class="btn-primary btn" style="margin:10px;padding:10px;"><font class="tn-in-text">No</font></button></a>
                        </div>
                    </center>
                </div>
            </div>';
        } elseif($action == 'view'){
            $sqlmain = "SELECT schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop 
                       FROM schedule INNER JOIN doctor ON schedule.docid=doctor.docid 
                       WHERE schedule.scheduleid=$id";
            $result = $database->query($sqlmain);
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $docname = $row["docname"];
                $scheduleid = $row["scheduleid"];
                $title = $row["title"];
                $scheduledate = $row["scheduledate"];
                $scheduletime = $row["scheduletime"];
                $nop = $row['nop'];

                $sqlmain12 = "SELECT * FROM appointment 
                             INNER JOIN patient ON patient.pid=appointment.pid 
                             INNER JOIN schedule ON schedule.scheduleid=appointment.scheduleid 
                             WHERE schedule.scheduleid=$id";
                $result12 = $database->query($sqlmain12);
                
                echo '
                <div id="popup1" class="overlay">
                    <div class="popup" style="width: 70%;">
                        <center>
                            <a class="close" href="schedule.php">×</a>
                            <div class="content">
                                <p style="text-align: left;font-size: 25px;font-weight: 500;">View Details</p>
                                <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label class="form-label">Session Title: </label>
                                            '.$title.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label class="form-label">Doctor: </label>
                                            '.$docname.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label class="form-label">Scheduled Date: </label>
                                            '.$scheduledate.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label class="form-label">Scheduled Time: </label>
                                            '.$scheduletime.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label class="form-label"><b>Registered Patients:</b> ('.$result12->num_rows.'/'.$nop.')</label>
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
                                                                <th class="table-headin">Patient Name</th>
                                                                <th class="table-headin">Appointment Number</th>
                                                                <th class="table-headin">Patient Telephone</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>';
                                                        if($result12->num_rows == 0){
                                                            echo '<tr>
                                                                <td colspan="4">
                                                                    <center>
                                                                        <img src="../img/notfound.svg" width="25%">
                                                                        <p class="heading-main12" style="font-size:20px;color:rgb(49, 49, 49)">No registrations found!</p>
                                                                    </center>
                                                                </td>
                                                            </tr>';
                                                        } else {
                                                            while($row = $result12->fetch_assoc()){
                                                                echo '<tr style="text-align:center;">
                                                                    <td>'.substr($row["pid"],0,15).'</td>
                                                                    <td style="font-weight:600;padding:25px">'.substr($row["pname"],0,25).'</td>
                                                                    <td style="font-size:23px;font-weight:500;color: var(--btnnicetext);">'.$row["apponum"].'</td>
                                                                    <td>'.substr($row["ptel"],0,25).'</td>
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
                    </div>
                </div>';
            }
        }
    }
    ?>

    <script>
    function validateForm() {
        let numSessions = document.getElementById('num_sessions').value;
        let sessionDate = new Date(document.getElementById('session_date').value);
        let today = new Date();
        today.setHours(0,0,0,0);
        let title = document.getElementById('title').value.trim();

        if(title === '') {
            alert('Session title cannot be empty');
            return false;
        }
        if(numSessions < 1 || numSessions > 5) {
            alert('Number of sessions must be between 1 and 5');
            return false;
        }
        if(sessionDate < today) {
            alert('Session date cannot be in the past');
            return false;
        }
        return true;
    }

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
    </script>
</body>
</html>