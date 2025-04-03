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
    <title>Doctor Attendance</title>
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
                    <td class="menu-btn menu-icon-dashbord">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div>
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
                    <td class="menu-btn menu-icon-attendance menu-active menu-icon-attendance-active">
                        <a href="attendance.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Attendance</p></div></a>
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
                    <td width="15%">
                        <p style="font-size:14px;color:rgb(119,119,119);padding:0;margin:0;text-align:right;">Today's Date</p>
                        <p class="heading-sub12" style="padding:0;margin:0;">
                            <?php echo $today; ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display:flex;justify-content:center;align-items:center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table width="100%" border="0" class="dashbord-tables">
                            <tr>
                                <td>
                                    <p style="padding:10px;padding-left:48px;padding-bottom:0;font-size:23px;font-weight:700;color:var(--primarycolor);">
                                        Doctor Attendance
                                    </p>
                                    <p style="padding-bottom:19px;padding-left:50px;font-size:15px;font-weight:500;color:#212529e3;line-height:20px;">
                                        View and track doctor attendance records<br>
                                        Real-time updates available below.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td width="100%">
                                    <center>
                                        <table class="filter-container" style="border:none;" border="0">
                                            <tr>
                                                <td style="text-align:right;">
                                                    <form method="GET" id="filterForm">
                                                        <input type="date" name="filter_date" value="<?php echo isset($_GET['filter_date']) ? $_GET['filter_date'] : date('Y-m-d'); ?>" class="input-text" style="margin-right:10px;">
                                                        <input type="submit" value="Filter" class="btn btn-primary" style="padding-left:25px;padding-right:25px;padding-top:10px;padding-bottom:10px;">
                                                    </form>
                                                </td>
                                            </tr>
                                        </table>
                                        <div class="abc scroll" style="height:400px;">
                                            <table width="93%" class="sub-table scrolldown" border="0">
                                                <thead>
                                                    <tr>
                                                        <th class="table-headin">Doctor Name</th>
                                                        <th class="table-headin">Email</th>
                                                        <th class="table-headin">Specialty</th>
                                                        <th class="table-headin">Most Recent Time In</th>
                                                        <th class="table-headin">Most Recent Time Out</th>
                                                        <th class="table-headin">Status</th>
                                                        <th class="table-headin">Hours Worked</th>
                                                        <th class="table-headin">Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="attendanceBody">
                                                    <?php
                                                    $filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : date('Y-m-d');
                                                    
                                                    $query = "SELECT d.docname, d.docemail, s.sname AS specialty_name,
                                                                    da.time_in, da.time_out, da.date
                                                              FROM doctor d
                                                              LEFT JOIN (
                                                                  SELECT doctor_id, time_in, time_out, date
                                                                  FROM doctor_attendance
                                                                  WHERE date = ?
                                                                  ORDER BY time_in DESC
                                                                  LIMIT 1
                                                              ) da ON d.docid = da.doctor_id
                                                              LEFT JOIN specialties s ON d.specialties = s.id
                                                              GROUP BY d.docid, d.docname, d.docemail, s.sname
                                                              ORDER BY d.docname";
                                                    
                                                    $stmt = $database->prepare($query);
                                                    if ($stmt === false) {
                                                        echo "<tr><td colspan='8'>Error preparing query: " . $database->error . "</td></tr>";
                                                    } else {
                                                        $stmt->bind_param("s", $filter_date);
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();

                                                        if ($result->num_rows == 0) {
                                                            echo '<tr><td colspan="8"><br><br><br><br><center><img src="../img/notfound.svg" width="25%"><br><p class="heading-main12" style="margin-left:45px;font-size:20px;color:rgb(49,49,49)">No attendance records found for this date!</p></center><br><br><br><br></td></tr>';
                                                        } else {
                                                            while ($row = $result->fetch_assoc()) {
                                                                $time_in = $row['time_in'] ? date('h:i A', strtotime($row['time_in'])) : '-';
                                                                $time_out = $row['time_out'] ? date('h:i A', strtotime($row['time_out'])) : '-';
                                                                $specialty = $row['specialty_name'] ?? '-';
                                                                
                                                                $status = 'Inactive';
                                                                $status_class = 'status-inactive';
                                                                if ($row['time_in'] && !$row['time_out']) {
                                                                    $status = 'Active';
                                                                    $status_class = 'status-active';
                                                                }

                                                                $hours_worked = '-';
                                                                if ($row['time_in'] && $row['time_out']) {
                                                                    $time_in_obj = new DateTime($row['time_in']);
                                                                    $time_out_obj = new DateTime($row['time_out']);
                                                                    $interval = $time_in_obj->diff($time_out_obj);
                                                                    $hours = $interval->h + ($interval->i / 60) + ($interval->s / 3600);
                                                                    $hours_worked = number_format($hours, 2) . ' hrs';
                                                                }

                                                                echo "<tr>
                                                                    <td style='padding:20px;'>" . htmlspecialchars($row['docname']) . "</td>
                                                                    <td>" . htmlspecialchars($row['docemail']) . "</td>
                                                                    <td>" . htmlspecialchars($specialty) . "</td>
                                                                    <td style='text-align:center;'>{$time_in}</td>
                                                                    <td style='text-align:center;'>{$time_out}</td>
                                                                    <td class='$status_class' style='text-align:center;'>{$status}</td>
                                                                    <td style='text-align:center;'>{$hours_worked}</td>
                                                                    <td style='text-align:center;'>" . date('F j, Y', strtotime($filter_date)) . "</td>
                                                                </tr>";
                                                            }
                                                        }
                                                        $stmt->close();
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
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateClock() {
            const now = new Date();
            const options = { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true };
            document.getElementById('realTimeClock') && (document.getElementById('realTimeClock').textContent = now.toLocaleString('en-US', options));
        }
        setInterval(updateClock, 1000);
        updateClock();

        function fetchAttendance() {
            const filterDate = $('input[name="filter_date"]').val();
            $.ajax({
                url: 'fetch_attendance.php',
                method: 'GET',
                data: { filter_date: filterDate },
                success: function(data) {
                    $('#attendanceBody').html(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching attendance:', error);
                }
            });
        }

        setInterval(fetchAttendance, 5000);
        fetchAttendance();

        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            fetchAttendance();
        });
    </script>
</body>
</html>