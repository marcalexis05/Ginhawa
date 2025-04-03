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
    <title>Session</title>
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
    if(isset($_SESSION["user"])) {
        if(($_SESSION["user"])=="" || $_SESSION['usertype']!='p') {
            header("location: ../login.php");
        } else {
            $useremail=$_SESSION["user"];
        }
    } else {
        header("location: ../login.php");
    }
    include("../connection.php");
    $sqlmain= "select * from patient where pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s",$useremail);
    $stmt->execute();
    $result = $stmt->get_result();
    $userfetch=$result->fetch_assoc();
    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];
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
                                <td width="30%" style="padding-left:20px" >
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
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
                    <td class="menu-btn menu-icon-home " ><a href="index.php" class="non-style-link-menu "><div><p class="menu-text">Home</p></a></div></a></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor"><a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></a></div></td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session menu-active menu-icon-session-active"><a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Scheduled Sessions</p></div></a></td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment"><a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></a></div></td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings"><a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div></td>
                </tr>
            </table>
        </div>
        <?php
        // Base query for all schedules (we'll filter later)
        $sqlmain= "select * from schedule inner join doctor on schedule.docid=doctor.docid where schedule.scheduledate>='$today' order by schedule.scheduledate asc";
        $sqlpt1="";
        $insertkey="";
        $q='';
        $searchtype="All";
        if($_POST){
            if(!empty($_POST["search"])){
                $keyword=$_POST["search"];
                $sqlmain= "select * from schedule inner join doctor on schedule.docid=doctor.docid where schedule.scheduledate>='$today' and (doctor.docname='$keyword' or doctor.docname like '$keyword%' or doctor.docname like '%$keyword' or doctor.docname like '%$keyword%' or schedule.title='$keyword' or schedule.title like '$keyword%' or schedule.title like '%$keyword' or schedule.title like '%$keyword%' or schedule.scheduledate like '$keyword%' or schedule.scheduledate like '%$keyword' or schedule.scheduledate like '%$keyword%' or schedule.scheduledate='$keyword' ) order by schedule.scheduledate asc";
                $insertkey=$keyword;
                $searchtype="Search Result : ";
                $q='"';
            }
        }
        $result= $database->query($sqlmain);

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

        // Check for approved patient requests for this patient
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
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <td width="13%" ><a href="schedule.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a></td>
                    <td >
                        <form action="" method="post" class="header-search">
                            <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name or Email or Date (YYYY-MM-DD)" list="doctors" value="<?php  echo $insertkey ?>">  
                            <?php
                            echo '<datalist id="doctors">';
                            $list11 = $database->query("select DISTINCT * from doctor;");
                            $list12 = $database->query("select DISTINCT * from schedule GROUP BY title;");
                            for ($y=0;$y<$list11->num_rows;$y++){
                                $row00=$list11->fetch_assoc();
                                $d=$row00["docname"];
                                echo "<option value='$d'><br/>";
                            };
                            for ($y=0;$y<$list12->num_rows;$y++){
                                $row00=$list12->fetch_assoc();
                                $d=$row00["title"];
                                echo "<option value='$d'><br/>";
                            };
                            echo ' </datalist>';
                            ?>
                            <input type="Submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                        </form>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">Today's Date</p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;"><?php echo $today; ?></p>
                    </td>
                    <td width="10%">
                        <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;" >
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)"><?php echo $searchtype." Sessions"."(".$result->num_rows.")"; ?> </p>
                        <p class="heading-main12" style="margin-left: 45px;font-size:22px;color:rgb(49, 49, 49)"><?php echo $q.$insertkey.$q ; ?> </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <center>
                            <div class="abc scroll">
                                <table width="100%" class="sub-table scrolldown" border="0" style="padding: 50px;border:none">
                                    <tbody>
                                        <?php
                                        if($result->num_rows==0){
                                            echo '<tr><td colspan="4"><br><br><br><br><center><img src="../img/notfound.svg" width="25%"><br><p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords !</p><a class="non-style-link" href="schedule.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">  Show all Sessions  </font></button></a></center><br><br><br><br></td></tr>';
                                        } else {
                                            $displayed_count = 0; // Counter for displayed sessions
                                            for ($x=0; $x<($result->num_rows);$x++){
                                                $row=$result->fetch_assoc();
                                                $scheduleid=$row["scheduleid"];
                                                $title=$row["title"];
                                                $docid=$row["docid"];
                                                $docname=$row["docname"];
                                                $scheduledate=$row["scheduledate"];
                                                $start_time = date('h:i A', strtotime($row["start_time"]));
                                                $end_time = date('h:i A', strtotime($row["end_time"]));

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

                                                // Skip this session if the patient doesn't have an approved request
                                                if (!$has_approved_request) {
                                                    continue;
                                                }

                                                $displayed_count++; // Increment only if session is displayed
                                                if ($displayed_count % 3 == 1) {
                                                    echo "<tr>"; // Start a new row every 3 items
                                                }

                                                // Check if patient already has an appointment with this doctor on this schedule
                                                $booking_check_sql = "SELECT COUNT(*) as count 
                                                                     FROM appointment 
                                                                     WHERE pid = ? AND scheduleid = ?";
                                                $stmt = $database->prepare($booking_check_sql);
                                                $stmt->bind_param("ii", $userid, $scheduleid);
                                                $stmt->execute();
                                                $booking_result = $stmt->get_result();
                                                $booking_row = $booking_result->fetch_assoc();
                                                $already_booked = $booking_row['count'] > 0;

                                                // Check if patient has an appointment on this date
                                                $date_booked = in_array($scheduledate, $booked_dates);

                                                // Check if this schedule is already booked by ANY patient
                                                $schedule_booked_sql = "SELECT COUNT(*) as count 
                                                                       FROM appointment 
                                                                       WHERE scheduleid = ?";
                                                $stmt = $database->prepare($schedule_booked_sql);
                                                $stmt->bind_param("i", $scheduleid);
                                                $stmt->execute();
                                                $schedule_result = $stmt->get_result();
                                                $schedule_row = $schedule_result->fetch_assoc();
                                                $schedule_booked = $schedule_row['count'] > 0;

                                                // Determine button state
                                                $button_disabled = $already_booked || $date_booked || $schedule_booked;
                                                $button_class = $button_disabled ? "login-btn btn-primary-soft btn disabled-btn" : "login-btn btn-primary-soft btn";
                                                if ($already_booked) {
                                                    $button_text = "Already Booked";
                                                } elseif ($date_booked) {
                                                    $button_text = "Date Booked";
                                                } elseif ($schedule_booked) {
                                                    $button_text = "Slot Taken";
                                                } else {
                                                    $button_text = "Book Now";
                                                }
                                                $button_link = $button_disabled ? "#" : "booking.php?id=$scheduleid";

                                                echo '
                                                <td style="width: 25%;">
                                                    <div  class="dashboard-items search-items"  >
                                                        <div style="width:100%">
                                                            <div class="h1-search">'.substr($title,0,21).'</div><br>
                                                            <div class="h3-search">'.substr($docname,0,30).'</div>
                                                            <div class="h4-search">'.$scheduledate.'<br>Time: <b>'.$start_time.' - '.$end_time.'</b></div>
                                                            <br>
                                                            <a href="'.$button_link.'" '.($button_disabled ? 'onclick="return false;"' : '').'><button class="'.$button_class.'" style="padding-top:11px;padding-bottom:11px;width:100%"><font class="tn-in-text">'.$button_text.'</font></button></a>
                                                        </div>
                                                    </div>
                                                </td>';

                                                if ($displayed_count % 3 == 0 || $x == $result->num_rows - 1) {
                                                    echo "</tr>"; // Close row after 3 items or at the end
                                                }
                                            }

                                            // Update the session count to reflect only displayed sessions
                                            echo '<script>document.querySelector(".heading-main12").innerHTML = "'.$searchtype.' Sessions ('.$displayed_count.')"</script>';
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