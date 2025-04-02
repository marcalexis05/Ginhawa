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
    <title>Patients</title>
    <style>
        .popup { animation: transitionIn-Y-bottom 0.5s; }
        .sub-table { animation: transitionIn-Y-bottom 0.5s; }
        .dropdown-container {
            float: right;
            margin-right: 45px;
            margin-top: 10px;
        }
        .dropdown-select {
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #fff;
            cursor: pointer;
        }
        .btn-archive {
            padding: 10px 15px;
            margin-top: 10px;
            background: transparent;
            color: #ff4444;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .btn-unarchive {
            padding: 10px 15px;
            margin-top: 10px;
            background: transparent;
            color: #44ff44;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        /* Remove hover effects */
        .btn-archive:hover, .btn-unarchive:hover {
            background: transparent;
            color: #ff4444;
        }
        .btn-unarchive:hover {
            color: #44ff44;
        }
    </style>
</head>
<body>
    <?php
    session_start();

    // Check if user session exists
    if (isset($_SESSION["user"])) {
        // Include database connection
        include("../connection.php");

        // Get the user's identifier (assuming it's an email)
        $user = $_SESSION["user"];

        // Check if this user is a patient and archived
        $check_user = $database->prepare("SELECT archived FROM patient WHERE pemail = ?");
        $check_user->bind_param("s", $user);
        $check_user->execute();
        $result = $check_user->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['archived'] == 1) {
                // If user is archived, destroy session and redirect
                session_destroy();
                header("location: ../login.php?error=archived");
                exit();
            }
        }

        // Check if user is not an admin
        if (empty($_SESSION["user"]) || $_SESSION['usertype'] != 'a') {
            header("location: ../login.php");
            exit();
        }
    } else {
        header("location: ../login.php");
        exit();
    }

    // Ensure all patients have a client_id
    $check_clientid = $database->query("SELECT * FROM patient WHERE pclientid IS NULL");
    while ($row = $check_clientid->fetch_assoc()) {
        $pid = $row['pid'];
        $clientid = "CL" . str_pad($pid, 3, '0', STR_PAD_LEFT);
        $update_stmt = $database->prepare("UPDATE patient SET pclientid = ? WHERE pid = ?");
        $update_stmt->bind_param("si", $clientid, $pid);
        $update_stmt->execute();
    }

    // Add archived column if it doesn't exist
    $database->query("ALTER TABLE patient ADD COLUMN IF NOT EXISTS archived TINYINT(1) DEFAULT 0");

    // Handle archive/unarchive actions
    if (isset($_GET['archive_id'])) {
        $archive_id = $_GET['archive_id'];
        $database->query("UPDATE patient SET archived = 1 WHERE pid = '$archive_id'");
        header("location: patient.php");
        exit();
    }
    if (isset($_GET['unarchive_id'])) {
        $unarchive_id = $_GET['unarchive_id'];
        $database->query("UPDATE patient SET archived = 0 WHERE pid = '$unarchive_id'");
        header("location: patient.php");
        exit();
    }

    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'active'; // Default to active patients
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
                                    <p class="profile-subtitle">admin@edoc.com</p>
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
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Doctors</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-schedule">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient menu-active menu-icon-patient-active">
                        <a href="patient.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Patients</p></div></a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing:0;margin:0;padding:0;margin-top:25px;">
                <tr>
                    <td width="13%">
                        <a href="patient.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <form action="" method="post" class="header-search">
                            <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Patient name or Email" list="patient">  
                            <?php
                            echo '<datalist id="patient">';
                            $list11 = $database->query("SELECT pname, pemail FROM patient WHERE archived = 0;");
                            for ($y = 0; $y < $list11->num_rows; $y++) {
                                $row00 = $list11->fetch_assoc();
                                $d = $row00["pname"];
                                $c = $row00["pemail"];
                                echo "<option value='$d'><br/>";
                                echo "<option value='$c'><br/>";
                            }
                            echo '</datalist>';
                            ?>
                            <input type="submit" value="Search" class="login-btn btn-primary btn" style="padding-left:25px;padding-right:25px;padding-top:10px;padding-bottom:10px;">
                        </form>
                    </td>
                    <td width="15%">
                        <p style="font-size:14px;color:rgb(119,119,119);padding:0;margin:0;text-align:right;">Today's Date</p>
                        <p class="heading-sub12" style="padding:0;margin:0;">
                            <?php 
                            date_default_timezone_set('Asia/Kolkata');
                            $date = date('Y-m-d');
                            echo $date;
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display:flex;justify-content:center;align-items:center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <div class="dropdown-container">
                            <select class="dropdown-select" onchange="window.location.href='?filter='+this.value">
                                <option value="active" <?php echo $filter == 'active' ? 'selected' : ''; ?>>Active Patients</option>
                                <option value="archived" <?php echo $filter == 'archived' ? 'selected' : ''; ?>>Archived Patients</option>
                            </select>
                        </div>
                    </td>
                </tr>
                <?php
                if ($_POST) {
                    $keyword = $_POST["search"];
                    $sqlmain = "SELECT * FROM patient WHERE (pemail='$keyword' OR pname='$keyword' OR pname LIKE '$keyword%' OR pname LIKE '%$keyword' OR pname LIKE '%$keyword%') AND archived=" . ($filter == 'archived' ? 1 : 0);
                } else {
                    $sqlmain = "SELECT * FROM patient WHERE archived=" . ($filter == 'archived' ? 1 : 0) . " ORDER BY pid DESC";
                }
                $result = $database->query($sqlmain);
                ?>
                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left:45px;font-size:18px;color:rgb(49,49,49)">
                            <?php echo $filter == 'archived' ? 'Archived' : 'Active'; ?> Patients (<?php echo $result->num_rows; ?>)
                        </p>
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
                                            <th class="table-headin">Client ID</th>
                                            <th class="table-headin">Mobile Number</th>
                                            <th class="table-headin">Email</th>
                                            <th class="table-headin">Date of Birth</th>
                                            <th class="table-headin">Age</th>
                                            <th class="table-headin">Sex</th>
                                            <th class="table-headin">Events</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result->num_rows == 0) {
                                            echo '<tr>
                                                <td colspan="8">
                                                    <br><br><br><br>
                                                    <center>
                                                        <img src="../img/notfound.svg" width="25%">
                                                        <br>
                                                        <p class="heading-main12" style="margin-left:45px;font-size:20px;color:rgb(49,49,49)">No ' . ($filter == 'archived' ? 'archived' : 'active') . ' patients found!</p>
                                                        <a class="non-style-link" href="patient.php"><button class="login-btn btn-primary-soft btn" style="display:flex;justify-content:center;align-items:center;margin-left:20px;"> Show all Patients </button></a>
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
                                                $dob = $row["pdob"] ?? 'N/A';
                                                $tel = $row["ptel"] ?? 'N/A';
                                                $age = $row["age"] ?? 'N/A';
                                                $sex = $row["psex"] ?? 'N/A';
                                                $archived = $row["archived"];
                                                
                                                echo '<tr>
                                                    <td>' . substr($name, 0, 35) . '</td>
                                                    <td>' . substr($clientid, 0, 12) . '</td>
                                                    <td>' . substr($tel, 0, 15) . '</td>
                                                    <td>' . substr($email, 0, 20) . '</td>
                                                    <td>' . substr($dob, 0, 10) . '</td>
                                                    <td>' . $age . '</td>
                                                    <td>' . $sex . '</td>
                                                    <td>
                                                        <div style="display:flex;justify-content:center;gap:10px;">
                                                            <a href="?action=view&id=' . $pid . '" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-view" style="padding-left:40px;padding-top:12px;padding-bottom:12px;margin-top:10px;"><font class="tn-in-text">View</font></button></a>';
                                                if ($archived == 0) {
                                                    echo '<a href="?archive_id=' . $pid . '" class="non-style-link"><button class="btn-archive btn button-icon"><span style="font-size:24px;">📥</span></button></a>';
                                                } else {
                                                    echo '<a href="?unarchive_id=' . $pid . '" class="non-style-link"><button class="btn-unarchive btn button-icon"><span style="font-size:24px;">📤</span></button></a>';
                                                }
                                                echo '</div>
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

    <?php 
    if ($_GET && isset($_GET["action"]) && $_GET["action"] == "view") {
        $id = $_GET["id"];
        $sqlmain = "SELECT * FROM patient WHERE pid='$id'";
        $result = $database->query($sqlmain);
        $row = $result->fetch_assoc();
        $name = $row["pname"];
        $email = $row["pemail"];
        $clientid = $row["pclientid"];
        $dob = $row["pdob"] ?? 'N/A';
        $tele = $row["ptel"] ?? 'N/A';
        $age = $row["age"] ?? 'N/A';
        $sex = $row["psex"] ?? 'N/A';

        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <a class="close" href="patient.php">×</a>
                    <div class="content"></div>
                    <div style="display:flex;justify-content:center;">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                            <tr>
                                <td>
                                    <p style="padding:0;margin:0;text-align:left;font-size:25px;font-weight:500;">View Details.</p><br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Patient ID: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    P-' . $id . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Name: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    ' . $name . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Email" class="form-label">Email: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    ' . $email . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="clientid" class="form-label">Client ID: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    ' . $clientid . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Tele" class="form-label">Mobile Number: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    ' . $tele . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="dob" class="form-label">Date of Birth: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    ' . $dob . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="age" class="form-label">Age: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    ' . $age . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="sex" class="form-label">Sex: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    ' . $sex . '<br><br>
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
</div>

</body>
</html>