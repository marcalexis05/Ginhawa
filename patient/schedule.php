<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Sessions</title>
    <style>
        .popup { animation: transitionIn-Y-bottom 0.5s; }
        .sub-table { animation: transitionIn-Y-bottom 0.5s; }
        .disabled-btn {
            background-color: #cccccc;
            cursor: not-allowed;
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (isset($_SESSION["user"])) {
        if (empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
            header("location: ../login.php");
            exit;
        } else {
            $useremail = $_SESSION["user"];
        }
    } else {
        header("location: ../login.php");
        exit;
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

    $sqlmain = "SELECT * FROM schedule INNER JOIN doctor ON schedule.docid = doctor.docid WHERE schedule.scheduledate >= ? ORDER BY schedule.scheduledate ASC";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s", $today);
    $searchtype = "All";
    $insertkey = "";
    $q = '';

    if ($_POST && !empty($_POST["search"])) {
        $keyword = $_POST["search"];
        $sqlmain = "SELECT * FROM schedule INNER JOIN doctor ON schedule.docid = doctor.docid 
                    WHERE schedule.scheduledate >= ? AND (
                        doctor.docname LIKE ? OR doctor.docname LIKE ? OR doctor.docname LIKE ? OR doctor.docname = ? OR 
                        schedule.title LIKE ? OR schedule.title LIKE ? OR schedule.title LIKE ? OR schedule.title = ? OR 
                        schedule.scheduledate LIKE ? OR schedule.scheduledate LIKE ? OR schedule.scheduledate LIKE ? OR schedule.scheduledate = ?
                    ) ORDER BY schedule.scheduledate ASC";
        $stmt = $database->prepare($sqlmain);
        $like1 = "$keyword%";
        $like2 = "%$keyword";
        $like3 = "%$keyword%";
        $stmt->bind_param("sssssssssssss", $today, $like1, $like2, $like3, $keyword, $like1, $like2, $like3, $keyword, $like1, $like2, $like3, $keyword);
        $insertkey = $keyword;
        $searchtype = "Search Result : ";
        $q = '"';
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $appointment_check_sql = "SELECT DISTINCT scheduledate FROM appointment INNER JOIN schedule ON appointment.scheduleid = schedule.scheduleid WHERE appointment.pid = ?";
    $stmt = $database->prepare($appointment_check_sql);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $appointment_dates_result = $stmt->get_result();
    $booked_dates = [];
    while ($row = $appointment_dates_result->fetch_assoc()) {
        $booked_dates[] = $row['scheduledate'];
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
                    <td class="menu-btn menu-icon-session menu-active menu-icon-session-active"><a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Scheduled Sessions</p></div></a></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment"><a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></a></div></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings"><a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div></td>
                </tr>
            </table>
        </div>

<<<<<<< HEAD
=======
        // Check for existing appointments for the patient on each scheduled date
        $appointment_check_sql = "SELECT DISTINCT scheduledate 
                                 FROM appointment 
                                 INNER JOIN schedule ON appointment.scheduleid = schedule.scheduleid 
                                 WHERE appointment.pid = ?";
        $stmt = $database->prepare($appointment_check_sql);
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $appointment_dates_result = $stmt->get_result();
        $booked_dates = [];
        while ($row = $appointment_dates_result->fetch_assoc()) {
            $booked_dates[] = $row['scheduledate'];
        }

        // Check for approved patient requests
        $approved_request_sql = "SELECT doctor_id, session_date, start_time, end_time 
                                 FROM patient_requests 
                                 WHERE patient_id = ? AND status = 'approved'";
        $stmt = $database->prepare($approved_request_sql);
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $approved_requests_result = $stmt->get_result();
        $approved_requests = [];
        while ($row = $approved_requests_result->fetch_assoc()) {
            $approved_requests[] = [
                'doctor_id' => $row['doctor_id'],
                'session_date' => $row['session_date'],
                'start_time' => $row['start_time'],
                'end_time' => $row['end_time']
            ];
        }
        ?>
>>>>>>> b083dc0241311adb5590ed4e03d9f9bbffb2d787
        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0; margin: 0; padding: 0; margin-top: 25px;">
                <tr>
                    <td width="13%"><a href="schedule.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a></td>
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
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">Today's Date</p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;"><?php echo $today; ?></p>
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
                                        if ($result->num_rows == 0) {
                                            echo '<tr><td colspan="4"><br><br><br><br><center><img src="../img/notfound.svg" width="25%"><br><p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords!</p><a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">Show all Sessions</font></button></a></center><br><br><br><br></td></tr>';
                                        } else {
                                            for ($x = 0; $x < $result->num_rows; $x++) {
                                                echo "<tr>";
                                                for ($q = 0; $q < 3; $q++) {
                                                    $row = $result->fetch_assoc();
                                                    if (!isset($row)) {
                                                        break;
                                                    }
                                                    $scheduleid = $row["scheduleid"];
                                                    $title = $row["title"];
                                                    $docid = $row["docid"];
                                                    $docname = $row["docname"];
                                                    $scheduledate = $row["scheduledate"];
                                                    $start_time = date('h:i A', strtotime($row["start_time"]));
                                                    $end_time = date('h:i A', strtotime($row["end_time"]));
                                                    if (empty($scheduleid)) {
                                                        break;
                                                    }

                                                    $booking_check_sql = "SELECT COUNT(*) as count FROM appointment WHERE pid = ? AND scheduleid = ?";
                                                    $stmt = $database->prepare($booking_check_sql);
                                                    $stmt->bind_param("ii", $userid, $scheduleid);
                                                    $stmt->execute();
                                                    $already_booked = $stmt->get_result()->fetch_assoc()['count'] > 0;

                                                    $date_booked = in_array($scheduledate, $booked_dates);

                                                    $schedule_booked_sql = "SELECT COUNT(*) as count FROM appointment WHERE scheduleid = ?";
                                                    $stmt = $database->prepare($schedule_booked_sql);
                                                    $stmt->bind_param("i", $scheduleid);
                                                    $stmt->execute();
                                                    $schedule_booked = $stmt->get_result()->fetch_assoc()['count'] > 0;

<<<<<<< HEAD
                                                    $button_disabled = $already_booked || $date_booked || $schedule_booked;
                                                    $button_class = $button_disabled ? "login-btn btn-primary-soft btn disabled-btn" : "login-btn btn-primary-soft btn";
                                                    $button_text = $already_booked ? "Already Booked" : ($date_booked ? "Date Booked" : ($schedule_booked ? "Slot Taken" : "Book Now"));

                                                    $apponum_sql = "SELECT COUNT(*) as count FROM appointment WHERE scheduleid = ?";
                                                    $stmt = $database->prepare($apponum_sql);
                                                    $stmt->bind_param("i", $scheduleid);
                                                    $stmt->execute();
                                                    $apponum = $stmt->get_result()->fetch_assoc()['count'] + 1;
=======
                                                    // Check if this patient has an approved request for this doctor's session
                                                    $has_approved_request = false;
                                                    foreach ($approved_requests as $request) {
                                                        if ($request['doctor_id'] == $docid && 
                                                            $request['session_date'] == $scheduledate && 
                                                            $request['start_time'] == $row["start_time"] && 
                                                            $request['end_time'] == $row["end_time"]) {
                                                            $has_approved_request = true;
                                                            break;
                                                        }
                                                    }

                                                    // Determine button state
                                                    $button_disabled = $already_booked || $date_booked || $schedule_booked || !$has_approved_request;
                                                    $button_class = $button_disabled ? "login-btn btn-primary-soft btn disabled-btn" : "login-btn btn-primary-soft btn";
                                                    if ($already_booked) {
                                                        $button_text = "Already Booked";
                                                    } elseif ($date_booked) {
                                                        $button_text = "Date Booked";
                                                    } elseif ($schedule_booked) {
                                                        $button_text = "Slot Taken";
                                                    } elseif (!$has_approved_request) {
                                                        $button_text = "Request Pending";
                                                    } else {
                                                        $button_text = "Book Now";
                                                    }
                                                    $button_link = $button_disabled ? "#" : "booking.php?id=$scheduleid";
>>>>>>> b083dc0241311adb5590ed4e03d9f9bbffb2d787

                                                    echo '
                                                    <td style="width: 25%;">
                                                        <div class="dashboard-items search-items">
                                                            <div style="width:100%">
                                                                <div class="h1-search">' . substr($title, 0, 21) . '</div><br>
                                                                <div class="h3-search">' . substr($docname, 0, 30) . '</div>
                                                                <div class="h4-search">' . $scheduledate . '<br>Time: <b>' . $start_time . ' - ' . $end_time . '</b></div>
                                                                <br>
                                                                <form action="booking-complete.php" method="post" style="display:inline;">
                                                                    <input type="hidden" name="scheduleid" value="' . $scheduleid . '">
                                                                    <input type="hidden" name="apponum" value="' . $apponum . '">
                                                                    <input type="hidden" name="date" value="' . $today . '">
                                                                    <input type="hidden" name="scheduledate" value="' . $scheduledate . '">
                                                                    <input type="hidden" name="start_time" value="' . $row["start_time"] . '">
                                                                    <input type="hidden" name="title" value="' . $title . '">
                                                                    <input type="hidden" name="docname" value="' . $docname . '">
                                                                    <input type="hidden" name="patient_email" value="' . $useremail . '">
                                                                    <input type="hidden" name="patient_name" value="' . $username . '">
                                                                    <button type="' . ($button_disabled ? 'button' : 'submit') . '" name="booknow" class="' . $button_class . '" style="padding-top:11px;padding-bottom:11px;width:100%" ' . ($button_disabled ? 'onclick="return false;"' : '') . '><font class="tn-in-text">' . $button_text . '</font></button>
                                                                </form>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
    document.querySelectorAll('.disabled-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            let message;
            switch (this.textContent) {
                case 'Already Booked':
                    message = 'You have already booked this session.';
                    break;
                case 'Date Booked':
                    message = 'You already have an appointment scheduled on this date.';
                    break;
                case 'Slot Taken':
                    message = 'This time slot is already booked by another patient.';
                    break;
                case 'Request Pending':
                    message = 'You need to request this session and get approval from the doctor first.';
                    break;
            }
            Swal.fire({
                icon: 'info',
                title: 'Booking Unavailable',
                text: message
            });
        });
    });
    </script>
</body>
</html>