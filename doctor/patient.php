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
    <title>Cases</title>
    <!-- Add SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .popup { animation: transitionIn-Y-bottom 0.5s; }
        .sub-table { animation: transitionIn-Y-bottom 0.5s; }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #ffffff;
            margin: 5% auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 700px;
            font-family: Arial, sans-serif;
        }
        .modal-header {
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .modal-header h2 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .close {
            color: #666;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
        }
        .modal-body label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #444;
        }
        .modal-body input[type="text"],
        .modal-body textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .modal-body textarea {
            height: 200px;
            resize: vertical;
        }
        .modal-footer {
            text-align: right;
            padding-top: 10px;
            border-top: 1px solid #e0e0e0;
        }
        .modal-footer input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .modal-footer input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <?php
    session_start();

    if(isset($_SESSION["user"])) {
        if(($_SESSION["user"]) == "" || $_SESSION['usertype'] != 'd') {
            header("location: ../login.php");
        } else {
            $useremail = $_SESSION["user"];
        }
    } else {
        header("location: ../login.php");
    }

    // Import database and email helper
    include("../connection.php");
    include("../email_helper.php");

    $userrow = $database->query("select * from doctor where docemail='$useremail'");
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["docid"];
    $username = $userfetch["docname"];

    // Handle email sending with SweetAlert
    if(isset($_POST['send_recommendation'])) {
        $patient_id = $_POST['patient_id'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];

        // Fetch patient details
        $patient_query = $database->query("SELECT pemail, pname FROM patient WHERE pid='$patient_id'");
        $patient = $patient_query->fetch_assoc();
        $patient_email = $patient['pemail'];
        $patient_name = $patient['pname'];

        // Professional email content
        $emailBody = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 5px; }
                    .header { background-color: #f5f5f5; padding: 10px; text-align: center; border-bottom: 1px solid #e0e0e0; }
                    .header h1 { margin: 0; font-size: 24px; color: #2c3e50; }
                    .content { padding: 20px; }
                    .footer { border-top: 1px solid #e0e0e0; padding: 10px; text-align: center; font-size: 12px; color: #777; }
                    .signature { margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Ginhawa Mental Health</h1>
                    </div>
                    <div class='content'>
                        <p>Dear $patient_name,</p>
                        <p>I hope this message finds you well. Below are my recommendations and advice tailored to your recent consultation:</p>
                        <div style='margin: 20px 0; padding-left: 20px; border-left: 3px solid #4CAF50;'>
                            " . nl2br(htmlspecialchars($message)) . "
                        </div>
                        <p>Please feel free to reach out if you have any questions or require further clarification. You can reply to this email or schedule a follow-up appointment through the Ginhawa platform.</p>
                        <div class='signature'>
                            <p>Best regards,</p>
                            <p><strong>Dr. $username</strong><br>
                            Ginhawa Mental Health<br>
                            Email: $useremail<br>
                            Phone: +63 907 515 1412</p>
                        </div>
                    </div>
                    <div class='footer'>
                        <p>This is an automated message from Ginhawa Mental Health. Please do not reply directly to this email unless instructed otherwise.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        // Send email using email_helper.php
        if(sendEmail($patient_email, $subject, $emailBody)) {
            // Store in database
            $database->query("INSERT INTO doctor_recommendations (doctor_id, patient_id, subject, message, sent_date) 
                            VALUES ('$userid', '$patient_id', '$subject', '$message', NOW())");
            // Echo SweetAlert success message
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Recommendation sent successfully!',
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'patient.php'; // Redirect after clicking OK
                    }
                });
            </script>";
        } else {
            // Echo SweetAlert error message
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Failed to send recommendation. Please try again.',
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                });
            </script>";
        }
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
                    <td class="menu-btn menu-icon-dashbord">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Appointments</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">My Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient menu-active menu-icon-patient-active">
                        <a href="patient.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">My Cases</p></a></div>
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
        $selecttype = "My";
        $current = "My Cases Only";
        if($_POST) {
            if(isset($_POST["search"])) {
                $keyword = $_POST["search12"];
                $sqlmain = "select * from patient where pemail='$keyword' or pname='$keyword' or pname like '$keyword%' or pname like '%$keyword' or pname like '%$keyword%' ";
                $selecttype = "my";
            }
            if(isset($_POST["filter"])) {
                if(isset($_POST["showonly"]) && $_POST["showonly"] == 'all') {
                    $sqlmain = "select * from patient";
                    $selecttype = "All";
                    $current = "All patients";
                } else {
                    $sqlmain = "select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.docid=$userid;";
                    $selecttype = "My";
                    $current = "My Cases Only";
                }
            }
        } else {
            $sqlmain = "select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.docid=$userid;";
            $selecttype = "My";
        }
        ?>

        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;margin-top:25px;">
                <tr>
                    <td width="13%">
                        <a href="patient.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <form action="" method="post" class="header-search">
                            <input type="search" name="search12" class="input-text header-searchbar" placeholder="Search Case name or Email" list="patient">  
                            <?php
                            echo '<datalist id="patient">';
                            $list11 = $database->query($sqlmain);
                            for ($y = 0; $y < $list11->num_rows; $y++) {
                                $row00 = $list11->fetch_assoc();
                                $d = $row00["pname"];
                                $c = $row00["pemail"];
                                echo "<option value='$d'><br/>";
                                echo "<option value='$c'><br/>";
                            };
                            echo '</datalist>';
                            ?>
                            <input type="Submit" value="Search" name="search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                        </form>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 
                            date_default_timezone_set('Asia/Kolkata');
                            $date = date('Y-m-d');
                            echo $date;
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)"><?php echo $selecttype." Cases (".$list11->num_rows.")"; ?></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:0px;width: 100%;">
                        <center>
                            <table class="filter-container" border="0">
                                <form action="" method="post">
                                    <td style="text-align: right;">
                                        Show Details About :  
                                    </td>
                                    <td width="30%">
                                        <select name="showonly" id="" class="box filter-container-items" style="width:90%;height: 37px;margin: 0;">
                                            <option value="" disabled selected hidden><?php echo $current ?></option><br/>
                                            <option value="my">My Cases Only</option><br/>
                                            <option value="all">All Cases</option><br/>
                                        </select>
                                    </td>
                                    <td width="12%">
                                        <input type="submit" name="filter" value="Filter" class="btn-primary-soft btn button-icon btn-filter" style="padding: 15px; margin: 0;width:100%">
                                    </td>
                                </form>
                            </table>
                        </center>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <center>
                            <div class="abc scroll">
                                <table width="93%" class="sub-table scrolldown" style="border-spacing:0;">
                                    <thead>
                                        <tr>
                                            <th class="table-headin">Name</th>
                                            <th class="table-headin">CaseID</th>
                                            <th class="table-headin">Mobile Number</th>
                                            <th class="table-headin">Email</th>
                                            <th class="table-headin">Date of Birth</th>
                                            <th class="table-headin">Events</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result = $database->query($sqlmain);
                                        if($result->num_rows == 0) {
                                            echo '<tr>
                                                <td colspan="6">
                                                    <br><br><br><br>
                                                    <center>
                                                        <img src="../img/notfound.svg" width="25%">
                                                        <br>
                                                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn’t find anything related to your keywords!</p>
                                                        <a class="non-style-link" href="patient.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">  Show all Cases  </button></a>
                                                    </center>
                                                    <br><br><br><br>
                                                </td>
                                            </tr>';
                                        } else {
                                            for ($x = 0; $x < $result->num_rows; $x++) {
                                                $row = $result->fetch_assoc();
                                                $pid = $row["pid"];
                                                $name = $row["pname"];
                                                $email = $row["pemail"];
                                                $clientid = $row["pclientid"];
                                                $dob = $row["pdob"];
                                                $tel = $row["ptel"];
                                                echo '<tr>
                                                    <td> '.substr($name, 0, 35).'</td>
                                                    <td>'.substr($clientid, 0, 12).'</td>
                                                    <td>'.substr($tel, 0, 10).'</td>
                                                    <td>'.substr($email, 0, 20).'</td>
                                                    <td>'.substr($dob, 0, 10).'</td>
                                                    <td>
                                                        <div style="display:flex;justify-content: center;gap: 10px;">
                                                            <a href="?action=view&id='.$pid.'" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-view" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a>
                                                            <button class="btn-primary-soft btn button-icon btn-edit" onclick="openRecommendationModal('.$pid.')" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Recommend</font></button>
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

    <!-- Recommendation Modal -->
    <div id="recommendationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Send Professional Recommendation</h2>
                <span class="close" onclick="closeRecommendationModal()">×</span>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <input type="hidden" name="patient_id" id="modal_patient_id">
                    <div>
                        <label for="subject">Subject</label>
                        <input type="text" name="subject" id="subject" required placeholder="e.g., Post-Consultation Recommendations">
                    </div>
                    <div>
                        <label for="message">Message</label>
                        <textarea name="message" id="message" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" name="send_recommendation" value="Send Recommendation">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php 
    if($_GET) {
        $id = $_GET["id"];
        $action = $_GET["action"];
        $sqlmain = "select * from patient where pid='$id'";
        $result = $database->query($sqlmain);
        $row = $result->fetch_assoc();
        $name = $row["pname"];
        $email = $row["pemail"];
        $clientid = $row["pclientid"];
        $dob = $row["pdob"];
        $tele = $row["ptel"];
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <a class="close" href="patient.php">×</a>
                    <div class="content"></div>
                    <div style="display: flex;justify-content: center;">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                            <tr>
                                <td>
                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details.</p><br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Case ID: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    P-'.$id.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Name: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    '.$name.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Email" class="form-label">Email: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    '.$email.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="clientid" class="form-label">ClientID: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    '.$clientid.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Tele" class="form-label">Mobile Number: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    '.$tele.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Date of Birth: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    '.$dob.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="patient.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </center>
                <br><br>
            </div>
        </div>';
    }
    ?>

    <script>
        function openRecommendationModal(patientId) {
            document.getElementById('recommendationModal').style.display = 'block';
            document.getElementById('modal_patient_id').value = patientId;

            // Fetch patient name via AJAX or pre-populate if available
            fetchPatientName(patientId).then(patientName => {
                const defaultMessage = `Dear ${patientName},

I hope this message finds you well. Below are my recommendations and advice tailored to your recent consultation:

- Practice mindfulness for 10 minutes daily.
- Schedule a follow-up in two weeks to review progress.

Please feel free to reach out if you have any questions or require further clarification. You can reply to this email or schedule a follow-up appointment through the Ginhawa platform.

Best regards,
Dr. <?php echo htmlspecialchars($username); ?>
Ginhawa Mental Health
Email: <?php echo htmlspecialchars($useremail); ?>
Phone: +63 907 515 1412

---

This is an automated message from Ginhawa Mental Health. Please do not reply directly to this email unless instructed otherwise.`;
                
                document.getElementById('message').value = defaultMessage;
            });
        }

        function closeRecommendationModal() {
            document.getElementById('recommendationModal').style.display = 'none';
        }

        window.onclick = function(event) {
            var modal = document.getElementById('recommendationModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Function to fetch patient name dynamically
        function fetchPatientName(patientId) {
            return fetch('get_patient_name.php?pid=' + patientId)
                .then(response => response.text())
                .catch(error => {
                    console.error('Error fetching patient name:', error);
                    return 'Patient'; // Fallback name
                });
        }
    </script>
</body>
</html>