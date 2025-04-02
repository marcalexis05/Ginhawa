<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <title>Doctor Attendance</title>
    <style>
        .clock { font-size: 24px; margin: 15px 0; color: #2c3e50; font-weight: 600; background: #ecf0f1; padding: 10px 20px; border-radius: 8px; display: inline-block; }
        .attendance-table { width: 100%; margin: 20px 0; background: #fff; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; }
        .sub-table th { background: #3498db; color: white; padding: 15px; font-weight: 600; }
        .sub-table td { padding: 12px; border-bottom: 1px solid #eee; }
        .sub-table tr:hover { background: #f8f9fa; }
        .filter-container { background: #fff; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .status-active { color: #2ecc71; font-weight: bold; }
        .status-inactive { color: #e74c3c; font-weight: bold; }
        .error-message { color: #e74c3c; padding: 10px; background: #ffebee; border-radius: 5px; margin: 10px 0; }
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
                    <td class="menu-btn menu-icon-dashbord"><a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor"><a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Doctors</p></a></div></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-schedule"><a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment"><a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></a></div></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient"><a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-attendance menu-active menu-icon-attendance-active"><a href="attendance.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Attendance</p></div></a></td>
                </tr>
            </table>
        </div>

        <div class="dash-body">
            <div class="clock" id="realTimeClock"></div>
            
            <div class="filter-container">
                <table border="0" width="100%">
                    <tr>
                        <td><p class="heading-main12">Doctor Attendance</p></td>
                        <td style="text-align: right;">
                            <form method="GET" id="filterForm">
                                <input type="date" name="filter_date" value="<?php echo isset($_GET['filter_date']) ? $_GET['filter_date'] : date('Y-m-d'); ?>" style="padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                                <input type="submit" value="Filter" class="btn-primary btn" style="padding: 8px 20px;">
                            </form>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="attendance-table">
                <table class="sub-table">
                    <thead>
                        <tr>
                            <th>Doctor Name</th>
                            <th>Email</th>
                            <th>Specialty</th>
                            <th>Most Recent Time In</th>
                            <th>Most Recent Time Out</th>
                            <th>Status</th>
                            <th>Hours Worked</th>
                            <th>Date</th>
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
                            echo "<div class='error-message'>Error preparing query: " . $database->error . "</div>";
                        } else {
                            $stmt->bind_param("s", $filter_date);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows == 0) {
                                echo "<tr><td colspan='8' style='text-align:center;'>No attendance records found for this date</td></tr>";
                            } else {
                                while ($row = $result->fetch_assoc()) {
                                    $time_in = $row['time_in'] ? date('h:i A', strtotime($row['time_in'])) : '-'; // e.g., "3:16 PM"
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
                                        <td>" . htmlspecialchars($row['docname']) . "</td>
                                        <td>" . htmlspecialchars($row['docemail']) . "</td>
                                        <td>" . htmlspecialchars($specialty) . "</td>
                                        <td>{$time_in}</td>
                                        <td>{$time_out}</td>
                                        <td class='$status_class'>{$status}</td>
                                        <td>{$hours_worked}</td>
                                        <td>" . date('F j, Y', strtotime($filter_date)) . "</td>
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
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateClock() {
            const now = new Date();
            const options = { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true };
            document.getElementById('realTimeClock').textContent = now.toLocaleString('en-US', options);
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