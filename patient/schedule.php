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

    <title>Sessions</title>
    <style>
        .popup {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
        }
        /* Style for disabled buttons */
        .btn-primary-soft:disabled,
        .btn-primary-soft[disabled] {
            background-color: #357960; /* Teal color for disabled state */
            color: #ffffff; /* White text for contrast */
            cursor: not-allowed; /* Disabled cursor */
            opacity: 0.7; /* Slightly faded */
        }
        /* Prevent hover effects on disabled buttons */
        .btn-primary-soft:disabled:hover,
        .btn-primary-soft[disabled]:hover {
            background-color: #357960; /* Maintain same background */
            color: #ffffff; /* Maintain same text color */
        }
    </style>
</head>
<body>
    <?php
    session_start();

    if(isset($_SESSION["user"])) {
        if(($_SESSION["user"])=="" || $_SESSION['usertype']!='p') {
            header("location: ../login.php");
        } else {
            $useremail = $_SESSION["user"];
        }
    } else {
        header("location: ../login.php");
    }

    include("../connection.php");
    $sqlmain = "SELECT * FROM patient WHERE pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $result = $stmt->get_result();
    $userfetch = $result->fetch_assoc();
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];

    date_default_timezone_set('Asia/Kolkata');
    $today = date('Y-m-d');
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
                    <td class="menu-btn menu-icon-home">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Scheduled Sessions</p></div></a>
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
        <?php
        $sqlmain = "SELECT * FROM schedule INNER JOIN doctor ON schedule.docid = doctor.docid WHERE schedule.scheduledate >= ? ORDER BY schedule.scheduledate ASC";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();

        $sqlpt1 = "";
        $insertkey = "";
        $q = '';
        $searchtype = "All";
        if($_POST && !empty($_POST["search"])) {
            $keyword = $_POST["search"];
            $sqlmain = "SELECT * FROM schedule INNER JOIN doctor ON schedule.docid = doctor.docid WHERE schedule.scheduledate >= ? AND (doctor.docname LIKE ? OR schedule.title LIKE ? OR schedule.scheduledate LIKE ?) ORDER BY schedule.scheduledate ASC";
            $stmt = $database->prepare($sqlmain);
            $likeKeyword = "%$keyword%";
            $stmt->bind_param("ssss", $today, $likeKeyword, $likeKeyword, $likeKeyword);
            $stmt->execute();
            $result = $stmt->get_result();
            $insertkey = $keyword;
            $searchtype = "Search Result : ";
            $q = '"';
        }
        ?>
        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0; margin:0; padding:0; margin-top:25px;">
                <tr>
                    <td width="13%">
                        <a href="schedule.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <form action="" method="post" class="header-search">
                            <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name or Email or Date (YYYY-MM-DD)" list="doctors" value="<?php echo $insertkey ?>">  
                            <?php
                            echo '<datalist id="doctors">';
                            $list11 = $database->query("SELECT DISTINCT * FROM doctor;");
                            $list12 = $database->query("SELECT DISTINCT * FROM schedule GROUP BY title;");
                            for ($y = 0; $y < $list11->num_rows; $y++) {
                                $row00 = $list11->fetch_assoc();
                                $d = $row00["docname"];
                                echo "<option value='$d'><br/>";
                            }
                            for ($y = 0; $y < $list12->num_rows; $y++) {
                                $row00 = $list12->fetch_assoc();
                                $d = $row00["title"];
                                echo "<option value='$d'><br/>";
                            }
                            echo '</datalist>';
                            ?>
                            <input type="Submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                        </form>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php echo $today; ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)"><?php echo $searchtype . " Sessions (" . $result->num_rows . ")"; ?></p>
                        <p class="heading-main12" style="margin-left: 45px;font-size:22px;color:rgb(49, 49, 49)"><?php echo $q . $insertkey . $q; ?></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <center>
                            <div class="abc scroll">
                                <table width="100%" class="sub-table scrolldown" border="0" style="padding: 50px;border:none">
                                    <tbody>
                                        <?php
                                        if($result->num_rows == 0) {
                                            echo '<tr>
                                                <td colspan="4">
                                                    <br><br><br><br>
                                                    <center>
                                                        <img src="../img/notfound.svg" width="25%">
                                                        <br>
                                                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords !</p>
                                                        <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">  Show all Sessions  </font></button></a>
                                                    </center>
                                                    <br><br><br><br>
                                                </td>
                                            </tr>';
                                        } else {
                                            for ($x = 0; $x < $result->num_rows; $x++) {
                                                echo "<tr>";
                                                for($q = 0; $q < 3; $q++) {
                                                    $row = $result->fetch_assoc();
                                                    if (!isset($row)) {
                                                        break;
                                                    }
                                                    $scheduleid = $row["scheduleid"];
                                                    $title = $row["title"];
                                                    $docname = $row["docname"];
                                                    $docid = $row["docid"];
                                                    $scheduledate = $row["scheduledate"];
                                                    $start_time = $row["start_time"];

                                                    if($scheduleid == "") {
                                                        break;
                                                    }

                                                    // Convert start_time to 12-hour format
                                                    $start_time_12hr = date('h:i A', strtotime($start_time));

                                                    // Check if the user has already booked this specific session
                                                    $booking_check_sql = "SELECT COUNT(*) as count FROM appointment WHERE pid = ? AND scheduleid = ?";
                                                    $stmt_check = $database->prepare($booking_check_sql);
                                                    $stmt_check->bind_param("ii", $userid, $scheduleid);
                                                    $stmt_check->execute();
                                                    $booking_result = $stmt_check->get_result();
                                                    $booking_row = $booking_result->fetch_assoc();
                                                    $already_booked_session = $booking_row['count'] > 0;

                                                    // Check if the user has already booked any session on this date
                                                    $date_booking_check_sql = "SELECT COUNT(*) as count FROM appointment INNER JOIN schedule ON appointment.scheduleid = schedule.scheduleid WHERE appointment.pid = ? AND schedule.scheduledate = ?";
                                                    $stmt_date_check = $database->prepare($date_booking_check_sql);
                                                    $stmt_date_check->bind_param("is", $userid, $scheduledate);
                                                    $stmt_date_check->execute();
                                                    $date_booking_result = $stmt_date_check->get_result();
                                                    $date_booking_row = $date_booking_result->fetch_assoc();
                                                    $already_booked_date = $date_booking_row['count'] > 0;

                                                    // Check if another patient has booked this doctor at the same date and time
                                                    $other_booking_check_sql = "SELECT COUNT(*) as count FROM appointment INNER JOIN schedule ON appointment.scheduleid = schedule.scheduleid WHERE schedule.docid = ? AND schedule.scheduledate = ? AND schedule.start_time = ? AND appointment.pid != ?";
                                                    $stmt_other_check = $database->prepare($other_booking_check_sql);
                                                    $stmt_other_check->bind_param("issi", $docid, $scheduledate, $start_time, $userid);
                                                    $stmt_other_check->execute();
                                                    $other_booking_result = $stmt_other_check->get_result();
                                                    $other_booking_row = $other_booking_result->fetch_assoc();
                                                    $other_patient_booked = $other_booking_row['count'] > 0;

                                                    // Determine button state
                                                    if ($already_booked_session) {
                                                        $button_text = "Already Booked";
                                                        $button_disabled = "disabled";
                                                        $button_link = "#";
                                                    } elseif ($already_booked_date) {
                                                        $button_text = "Date Occupied";
                                                        $button_disabled = "disabled";
                                                        $button_link = "#";
                                                    } elseif ($other_patient_booked) {
                                                        $button_text = "Booked by Another Client";
                                                        $button_disabled = "disabled";
                                                        $button_link = "#";
                                                    } else {
                                                        $button_text = "Book Now";
                                                        $button_disabled = "";
                                                        $button_link = "booking.php?id=" . $scheduleid;
                                                    }

                                                    echo '
                                                    <td style="width: 25%;">
                                                        <div class="dashboard-items search-items">
                                                            <div style="width:100%">
                                                                <div class="h1-search">
                                                                    ' . substr($title, 0, 21) . '
                                                                </div><br>
                                                                <div class="h3-search">
                                                                    ' . substr($docname, 0, 30) . '
                                                                </div>
                                                                <div class="h4-search">
                                                                    ' . $scheduledate . '<br>Starts: <b>' . $start_time_12hr . '</b>
                                                                </div>
                                                                <br>
                                                                <a href="' . $button_link . '"><button class="login-btn btn-primary-soft btn ' . $button_disabled . '" style="padding-top:11px;padding-bottom:11px;width:100%" ' . ($button_disabled ? 'disabled' : '') . '><font class="tn-in-text">' . $button_text . '</font></button></a>
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
</body>
</html>