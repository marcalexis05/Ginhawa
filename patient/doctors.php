<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../Images/G-icon.png">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Professionals</title>
    <style>
        .popup { animation: transitionIn-Y-bottom 0.5s; }
        .sub-table { animation: transitionIn-Y-bottom 0.5s; }
        .request-btn {
            background-color: var(--primarycolor);
            color: white;
            border: 1px solid var(--primarycolor);
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            font-family: 'Inter', sans-serif;
        }
        .request-btn:hover {
            background-color: var(--primarycolorhover);
            box-shadow: 0 3px 5px 0 rgba(123,174,55,0.3);
        }
        .header-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 45px 10px 45px;
        }
        .symptom-btn {
            background-color: var(--primarycolor);
            color: white;
            border: 1px solid var(--primarycolor);
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }
        .symptom-btn:hover {
            background-color: var(--primarycolorhover);
            box-shadow: 0 3px 5px 0 rgba(123,174,55,0.3);
        }
        #symptomsPopup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            max-width: 800px;
            background: white;
            border-radius: 5px;
            padding: 30px;
            z-index: 1000;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        .symptom-list {
            max-height: 400px;
            overflow-y: auto;
            margin: 15px 0;
        }
        .symptom-list label {
            display: block;
            margin: 8px 0;
            padding: 8px;
            font-size: 16px;
        }
        .suggestion-box {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ebebeb;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        #symptomsPopupContent {
            text-align: center;
        }
        #symptomsPopupContent h2 {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION["user"]) || empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit;
    }
    $useremail = $_SESSION["user"];
    include("../connection.php");
    $userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];
    date_default_timezone_set('Asia/Kolkata');
    $today = date('Y-m-d');
    $oneWeekLater = date('Y-m-d', strtotime('+7 days'));

    // Fetch all doctors for recommendations
    $doctors_query = $database->query("SELECT d.docid, d.docname, s.sname as specialty 
                                     FROM doctor d 
                                     JOIN specialties s ON d.specialties = s.id");
    $doctors = [];
    while ($row = $doctors_query->fetch_assoc()) {
        $doctors[] = [
            'id' => $row['docid'],
            'name' => $row['docname'],
            'specialty' => $row['specialty']
        ];
    }
    $doctors_json = json_encode($doctors);
    ?>

    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px">
                                    <img src="<?php echo isset($_SESSION['google_picture']) ? $_SESSION['google_picture'] : '../img/user.png'; ?>" 
                                         alt="" width="100%" style="border-radius:50%">
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
                    <td class="menu-btn menu-icon-home"><a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></div></a></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor menu-active menu-icon-doctor-active"><a href="doctors.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">All Professionals</p></div></a></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session"><a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Scheduled Sessions</p></div></a></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment"><a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></div></a></td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings"><a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a></td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing:0;margin:0;padding:0;margin-top:25px;">
                <tr>
                    <td width="13%"><a href="doctors.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a></td>
                    <td>
                        <form action="" method="post" class="header-search">
                            <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Professional name or Email" list="doctors">  
                            <?php
                            echo '<datalist id="doctors">';
                            $list11 = $database->query("SELECT docname,docemail FROM doctor");
                            for ($y = 0; $y < $list11->num_rows; $y++) {
                                $row00 = $list11->fetch_assoc();
                                $d = $row00["docname"];
                                $c = $row00["docemail"];
                                echo "<option value='$d'><br/>";
                                echo "<option value='$c'><br/>";
                            }
                            echo '</datalist>';
                            ?>
                            <input type="submit" value="Search" class="login-btn btn-primary btn" style="padding:10px 25px;">
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
                    <td colspan="4" style="padding-top:10px;">
                        <div class="header-section">
                            <p class="heading-main12" style="font-size:18px;color:rgb(49,49,49)">All Professionals (<?php echo $list11->num_rows; ?>)</p>
                            <button class="symptom-btn" onclick="openSymptomsPopup(0)">Select Symptoms</button>
                        </div>
                    </td>
                </tr>
                <?php
                if ($_POST) {
                    $keyword = $_POST["search"];
                    $sqlmain = "SELECT * FROM doctor WHERE docemail='$keyword' OR docname='$keyword' OR docname LIKE '$keyword%' OR docname LIKE '%$keyword' OR docname LIKE '%$keyword%'";
                } else {
                    $sqlmain = "SELECT * FROM doctor ORDER BY docid DESC";
                }
                ?>
                <tr>
                    <td colspan="4">
                        <center>
                            <div class="abc scroll">
                                <table width="93%" class="sub-table scrolldown" border="0">
                                    <thead>
                                        <tr>
                                            <th class="table-headin">Professionals Name</th>
                                            <th class="table-headin">Email</th>
                                            <th class="table-headin">Specialties</th>
                                            <th class="table-headin">Events</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result = $database->query($sqlmain);
                                        if ($result->num_rows == 0) {
                                            echo '<tr><td colspan="4"><center><img src="../img/notfound.svg" width="25%"><p class="heading-main12" style="margin-left:45px;font-size:20px;color:rgb(49,49,49)">We couldn\'t find anything related to your keywords!</p><a class="non-style-link" href="doctors.php"><button class="login-btn btn-primary-soft btn" style="display:flex;justify-content:center;align-items:center;margin-left:20px;"> Show all Doctors </button></a></center></td></tr>';
                                        } else {
                                            for ($x = 0; $x < $result->num_rows; $x++) {
                                                $row = $result->fetch_assoc();
                                                $docid = $row["docid"];
                                                $name = $row["docname"];
                                                $email = $row["docemail"];
                                                $spe = $row["specialties"];
                                                $spcil_res = $database->query("SELECT sname FROM specialties WHERE id='$spe'");
                                                $spcil_array = $spcil_res->fetch_assoc();
                                                $spcil_name = $spcil_array["sname"];
                                                echo '<tr>
                                                    <td>' . substr($name, 0, 30) . '</td>
                                                    <td>' . substr($email, 0, 20) . '</td>
                                                    <td>' . substr($spcil_name, 0, 20) . '</td>
                                                    <td>
                                                        <div style="display:flex;justify-content:center;">
                                                            <a href="?action=view&id=' . $docid . '" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-view" style="padding:12px 40px;margin-top:10px;"><font class="tn-in-text">View</font></button></a>  
                                                            <a href="?action=session&id=' . $docid . '&name=' . $name . '" class="non-style-link"><button class="btn-primary-soft btn button-icon menu-icon-session-active" style="padding:12px 40px;margin-top:10px;"><font class="tn-in-text">Sessions</font></button></a>  
                                                            <button class="request-btn" onclick="document.getElementById(\'requestPopup' . $docid . '\').style.display=\'block\'"><font class="tn-in-text">Request</font></button>
                                                        </div>
                                                    </td>
                                                </tr>';
                                                echo '
                                                <div id="requestPopup' . $docid . '" class="overlay" style="display:none;">
                                                    <div class="popup">
                                                        <center>
                                                            <h2>Request a Session</h2>
                                                            <a class="close" onclick="document.getElementById(\'requestPopup' . $docid . '\').style.display=\'none\'">×</a>
                                                            <div class="content">
                                                                <form action="submit-patient-request.php" method="post" onsubmit="return validateRequestForm(' . $docid . ', event)">
                                                                    <label for="description' . $docid . '" style="display:block;margin:10px 0 5px;">Session Description:</label>
                                                                    <textarea name="description" id="description' . $docid . '" class="input-text" placeholder="Describe how you’re feeling or your symptoms" required style="height:100px;"></textarea>
                                                                    <div id="suggestions' . $docid . '" class="suggestion-box" style="display:none;"></div>
                                                                    <label for="session_date' . $docid . '" style="display:block;margin:10px 0 5px;">Preferred Date:</label>
                                                                    <input type="text" name="session_date" id="session_date' . $docid . '" class="input-text" required>
                                                                    <label for="start_time' . $docid . '" style="display:block;margin:10px 0 5px;">Start Time (8:00 AM - 5:00 PM):</label>
                                                                    <select name="start_time" id="start_time' . $docid . '" class="input-text" required>
                                                                        <option value="">Select Start Time</option>';
                                                                        for ($h = 8; $h < 18; $h++) {
                                                                            foreach ([0, 30] as $m) {
                                                                                if ($h == 12 || ($h == 17 && $m == 30)) continue;
                                                                                $time = sprintf("%02d:%02d:00", $h, $m);
                                                                                $ampm = $h >= 12 ? 'PM' : 'AM';
                                                                                $display_h = $h > 12 ? $h - 12 : ($h == 12 ? 12 : $h);
                                                                                $display_time = sprintf("%d:%02d %s", $display_h, $m, $ampm);
                                                                                echo "<option value='$time'>$display_time</option>";
                                                                            }
                                                                        }
                                                echo '          </select>
                                                                    <label for="duration' . $docid . '" style="display:block;margin:10px 0 5px;">Duration:</label>
                                                                    <select name="duration" id="duration' . $docid . '" class="input-text" required>
                                                                        <option value="30">30 minutes</option>
                                                                        <option value="60">1 hour</option>
                                                                        <option value="90">1 hour 30 minutes</option>
                                                                        <option value="120">2 hours</option>
                                                                    </select>
                                                                    <label for="gmeet_request' . $docid . '" style="display:block;margin:10px 0 5px;">Request Google Meet Link:</label>
                                                                    <input type="checkbox" name="gmeet_request" id="gmeet_request' . $docid . '" value="1">
                                                                    <p><b>Note:</b> Opening: 8:00 AM, Break: 12:00 PM - 1:00 PM, Closing:                                                                    6:00 PM</p>
                                                                    <input type="hidden" name="patient_id" value="' . $userid . '">
                                                                    <input type="hidden" name="doctor_id" value="' . $docid . '">
                                                                    <div style="display: flex; justify-content: center; margin-top: 20px;">
                                                                        <button type="submit" class="btn-primary btn">Submit Request</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </center>
                                                    </div>
                                                </div>';
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

    <!-- Symptoms Popup -->
    <div id="symptomsPopup" style="display:none;">
        <div id="symptomsPopupContent">
            <a class="close" onclick="document.getElementById('symptomsPopup').style.display='none'" style="position: absolute; top: 20px; right: 30px; font-size: 24px; text-decoration: none; color: #333;">×</a>
            <h2>Select Symptoms</h2>
            <div class="content symptom-list">
                <form id="symptomsForm">
                    <label><input type="checkbox" name="symptoms[]" value="difficulty_sleeping"> Difficulty Sleeping</label>
                    <label><input type="checkbox" name="symptoms[]" value="feeling_tired"> Feeling Tired or Exhausted</label>
                    <label><input type="checkbox" name="symptoms[]" value="low_energy"> Low Energy</label>
                    <label><input type="checkbox" name="symptoms[]" value="poor_concentration"> Poor Concentration</label>
                    <label><input type="checkbox" name="symptoms[]" value="appetite_changes"> Appetite Changes</label>
                    <label><input type="checkbox" name="symptoms[]" value="restlessness"> Restlessness</label>
                    <label><input type="checkbox" name="symptoms[]" value="irritability"> Irritability</label>
                </form>
            </div>
            <button class="btn-primary btn" onclick="submitSymptoms()">Submit</button>
        </div>
    </div>

    <?php 
    if ($_GET) {
        $id = $_GET["id"];
        $action = $_GET["action"];
        if ($action == 'view') {
            $sqlmain = "SELECT * FROM doctor WHERE docid=?";
            $stmt = $database->prepare($sqlmain);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $name = $row["docname"];
            $email = $row["docemail"];
            $spe = $row["specialties"];
            $stmt = $database->prepare("SELECT sname FROM specialties WHERE id=?");
            $stmt->bind_param("s", $spe);
            $stmt->execute();
            $spcil_res = $stmt->get_result();
            $spcil_array = $spcil_res->fetch_assoc();
            $spcil_name = $spcil_array["sname"];
            $tele = $row['doctel'];
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <a class="close" href="doctors.php">×</a>
                        <div class="content">eDoc Web App</div>
                        <div style="display:flex;justify-content:center;">
                            <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                <tr><td><p style="padding:0;margin:0;text-align:left;font-size:25px;font-weight:500;">View Details.</p></td></tr>
                                <tr><td class="label-td" colspan="2"><label for="name" class="form-label">Name: </label></td></tr>
                                <tr><td class="label-td" colspan="2">' . htmlspecialchars($name) . '</td></tr>
                                <tr><td class="label-td" colspan="2"><label for="Email" class="form-label">Email: </label></td></tr>
                                <tr><td class="label-td" colspan="2">' . htmlspecialchars($email) . '</td></tr>
                                <tr><td class="label-td" colspan="2"><label for="Tele" class="form-label">Telephone: </label></td></tr>
                                <tr><td class="label-td" colspan="2">' . htmlspecialchars($tele) . '</td></tr>
                                <tr><td class="label-td" colspan="2"><label for="spec" class="form-label">Specialties: </label></td></tr>
                                <tr><td class="label-td" colspan="2">' . htmlspecialchars($spcil_name) . '</td></tr>
                                <tr><td colspan="2"><a href="doctors.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn"></a></td></tr>
                            </table>
                        </div>
                    </center>
                </div>
            </div>';
        } elseif ($action == 'session') {
            $name = $_GET["name"];
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <h2>Redirect to Doctor\'s Sessions?</h2>
                        <a class="close" href="doctors.php">×</a>
                        <div class="content">You want to view All sessions by (' . htmlspecialchars(substr($name, 0, 40)) . ').</div>
                        <form action="schedule.php" method="post" style="display:flex">
                            <input type="hidden" name="search" value="' . htmlspecialchars($name) . '">
                            <div style="display:flex;justify-content:center;margin-left:45%;margin-top:6%;margin-bottom:6%;">
                                <input type="submit" value="Yes" class="btn-primary btn">
                            </div>
                        </form>
                    </center>
                </div>
            </div>';
        }
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
    let currentDocId = null;
    const allDoctors = <?php echo $doctors_json; ?>;

    document.addEventListener('DOMContentLoaded', function() {
        <?php
        $result = $database->query($sqlmain);
        for ($x = 0; $x < $result->num_rows; $x++) {
            $row = $result->fetch_assoc();
            $docid = $row["docid"];
            echo "
            flatpickr('#session_date$docid', {
                minDate: '$today',
                maxDate: '$oneWeekLater',
                disable: [
                    function(date) {
                        return date.getDay() === 0;
                    }
                ],
                dateFormat: 'Y-m-d',
                onOpen: function(selectedDates, dateStr, instance) {
                    instance.redraw();
                }
            });
            ";
        }
        ?>
    });

    function validateRequestForm(docid, event) {
        event.preventDefault();
        let description = document.getElementById('description' + docid).value.trim();
        let sessionDateStr = document.getElementById('session_date' + docid).value;
        let sessionDate = new Date(sessionDateStr);
        let startTime = document.getElementById('start_time' + docid).value;
        let duration = parseInt(document.getElementById('duration' + docid).value);

        let currentTime = new Date().toLocaleString("en-US", { timeZone: "Asia/Kolkata" });
        currentTime = new Date(currentTime);
        let today = new Date(currentTime);
        today.setHours(0, 0, 0, 0);

        let isToday = sessionDate.getTime() === today.getTime();
        if (isToday && startTime) {
            let [startHours, startMinutes] = startTime.split(':').map(Number);
            let currentHours = currentTime.getHours();
            let currentMinutes = currentTime.getMinutes();
            let startTotalMinutes = startHours * 60 + startMinutes;
            let currentTotalMinutes = currentHours * 60 + currentMinutes;

            if (startTotalMinutes <= currentTotalMinutes) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Cannot request a session for a time that has already passed or is now on today\'s date!'
                });
                return false;
            }
        }

        if (description === '') {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Session description cannot be empty!' });
            return false;
        }
        if (sessionDate < today) {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Session date cannot be in the past!' });
            return false;
        }
        if (!startTime || !duration) {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Please select start time and duration!' });
            return false;
        }

        let [startHours, startMinutes] = startTime.split(':').map(Number);
        let totalMinutes = startHours * 60 + startMinutes + duration;
        let breakStart = 12 * 60;
        let breakEnd = 13 * 60;
        if (totalMinutes > breakStart && (startHours * 60 + startMinutes) < breakEnd) {
            totalMinutes += (breakEnd - breakStart);
        }
        let endHours = Math.floor(totalMinutes / 60);

        if (startHours < 8 || endHours > 18) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Time must be between 8:00 AM - 6:00 PM, excluding 12:00 PM - 1:00 PM!'
            });
            return false;
        }

        let patientId = <?php echo $userid; ?>;
        fetch('check_appointments.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'patient_id=' + patientId + '&session_date=' + sessionDateStr
        })
        .then(response => response.json())
        .then(data => {
            if (data.hasAppointment) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'You already have an appointment scheduled on this date!'
                });
            } else {
                document.querySelector('#requestPopup' + docid + ' form').submit();
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while checking your appointments. Please try again.'
            });
            console.error('Error:', error);
        });

        return false;
    }

    function openSymptomsPopup(docid) {
        currentDocId = docid;
        document.getElementById('symptomsPopup').style.display = 'block';
        document.getElementById('symptomsForm').reset();
    }

    function submitSymptoms() {
        let selectedSymptoms = Array.from(document.querySelectorAll('#symptomsForm input[name="symptoms[]"]:checked'))
            .map(checkbox => checkbox.value);

        if (selectedSymptoms.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Selection',
                text: 'Please select at least one symptom!'
            });
            return;
        }

        // Symptom-to-advice mapping based on sleep-related issues
        const symptomAdvice = {
            'difficulty_sleeping': 'Establish a regular sleep schedule and avoid screens before bed.',
            'feeling_tired': 'Ensure adequate rest and check your nutrition and hydration.',
            'low_energy': 'Incorporate light exercise and maintain consistent sleep hours.',
            'poor_concentration': 'Take regular breaks and practice relaxation techniques.',
            'appetite_changes': 'Maintain a balanced diet and regular meal times.',
            'restlessness': 'Try relaxation exercises before bed.',
            'irritability': 'Practice stress management and ensure sufficient rest.'
        };

        // Generate advice based on selected symptoms
        let advice = selectedSymptoms.map(symptom => symptomAdvice[symptom] || 'Consult a professional for personalized advice.').join(' ');

        // Recommend doctors (always at least one)
        let recommendedDoctors = [];
        if (selectedSymptoms.includes('difficulty_sleeping') || selectedSymptoms.includes('feeling_tired') || selectedSymptoms.includes('low_energy')) {
            recommendedDoctors = allDoctors.filter(doc => 
                doc.specialty.toLowerCase().includes('sleep') || 
                doc.specialty.toLowerCase().includes('general')
            );
        } else if (selectedSymptoms.includes('poor_concentration') || selectedSymptoms.includes('irritability')) {
            recommendedDoctors = allDoctors.filter(doc => 
                doc.specialty.toLowerCase().includes('psych') || 
                doc.specialty.toLowerCase().includes('neuro')
            );
        } else if (selectedSymptoms.includes('appetite_changes') || selectedSymptoms.includes('restlessness')) {
            recommendedDoctors = allDoctors.filter(doc => 
                doc.specialty.toLowerCase().includes('general') || 
                doc.specialty.toLowerCase().includes('psych')
            );
        }

        // If no specific match or empty, select at least one random doctor
        if (recommendedDoctors.length === 0) {
            const randomIndex = Math.floor(Math.random() * allDoctors.length);
            recommendedDoctors = [allDoctors[randomIndex]];
        }

        if (currentDocId === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Suggestions',
                html: `
                    <div style="text-align: left;">
                        <h3>Advice:</h3>
                        <p>${advice}</p>
                        <h3>Recommended Doctors:</h3>
                        <ul>${recommendedDoctors.map(doc => `<li>${doc.name} - ${doc.specialty}</li>`).join('')}</ul>
                    </div>
                `
            });
            document.getElementById('symptomsPopup').style.display = 'none';
        } else {
            let suggestionsDiv = document.getElementById('suggestions' + currentDocId);
            suggestionsDiv.style.display = 'block';
            suggestionsDiv.innerHTML = `
                <h3>Suggestions</h3>
                <p><strong>Advice:</strong> ${advice}</p>
                <p><strong>Recommended Doctors:</strong></p>
                <ul>${recommendedDoctors.map(doc => `<li>${doc.name} - ${doc.specialty}</li>`).join('')}</ul>
            `;
            document.getElementById('symptomsPopup').style.display = 'none';
        }
    }

    <?php
    if (isset($_GET['success'])) {
        echo "Swal.fire({ icon: 'success', title: 'Success!', text: '" . addslashes($_GET['success']) . "', showConfirmButton: true }).then(() => { window.location = 'doctors.php'; });";
    }
    if (isset($_GET['error'])) {
        echo "Swal.fire({ icon: 'error', title: 'Error!', text: '" . addslashes($_GET['error']) . "', showConfirmButton: true }).then(() => { window.location = 'doctors.php'; });";
    }
    ?>
    </script>
</body>
</html>