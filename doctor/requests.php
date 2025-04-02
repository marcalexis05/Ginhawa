<?php
session_start();

if (!isset($_SESSION["user"]) || empty($_SESSION["user"]) || $_SESSION['usertype'] != 'd') {
    header("location: ../login.php");
    exit;
}

$useremail = $_SESSION["user"];
include("../connection.php");

// Get doctor info
$userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["docid"];
$username = $userfetch["docname"];

// Handle request actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $request_id = $_GET['id'];
    if ($_GET['action'] == 'accept') {
        $database->query("UPDATE patient_requests SET status='accepted' WHERE request_id='$request_id'");
    } elseif ($_GET['action'] == 'reject') {
        $database->query("UPDATE patient_requests SET status='rejected' WHERE request_id='$request_id'");
    }
    header("location: requests.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <title>Patient Requests</title>
    <style>
        .popup { animation: transitionIn-Y-bottom 0.5s; }
        .sub-table { animation: transitionIn-Y-bottom 0.5s; }
    </style>
</head>
<body>
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
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">My Sessions</p></div></a>
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
        
        <div class="dash-body" style="margin-top:15px;">
            <table border="0" width="100%" style="border-spacing:0;margin:0;padding:0;">
                <tr>
                    <td colspan="4">
                        <p class="heading-main12" style="margin-left:45px;font-size:18px;color:rgb(49,49,49)">Patient Requests</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <center>
                            <div class="abc scroll">
                                <table width="93%" class="sub-table scrolldown" border="0">
                                    <thead>
                                        <tr>
                                            <th class="table-headin">Patient Name</th>
                                            <th class="table-headin">Request Date</th>
                                            <th class="table-headin">Status</th>
                                            <th class="table-headin">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result = $database->query("SELECT pr.*, p.pname 
                                                                   FROM patient_requests pr 
                                                                   JOIN patient p ON pr.patient_id = p.pid 
                                                                   WHERE pr.doctor_id='$userid' 
                                                                   ORDER BY pr.request_date DESC");
                                        if ($result->num_rows == 0) {
                                            echo '<tr><td colspan="4"><br><br><center>No requests found</center><br><br></td></tr>';
                                        } else {
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<tr>
                                                    <td>' . $row['pname'] . '</td>
                                                    <td>' . $row['request_date'] . '</td>
                                                    <td>' . ucfirst($row['status']) . '</td>
                                                    <td>';
                                                if ($row['status'] == 'pending') {
                                                    echo '<a href="?action=accept&id=' . $row['request_id'] . '" class="non-style-link"><button class="btn-primary-soft btn" style="padding:10px 20px;margin:5px;background-color:#28a745;">Accept</button></a>
                                                          <a href="?action=reject&id=' . $row['request_id'] . '" class="non-style-link"><button class="btn-primary-soft btn" style="padding:10px 20px;margin:5px;background-color:#dc3545;">Reject</button></a>';
                                                }
                                                echo '</td></tr>';
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