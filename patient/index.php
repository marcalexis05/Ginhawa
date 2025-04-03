<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <title>Dashboard</title>
    <style>
        .dashbord-tables{animation: transitionIn-Y-over 0.5s;}
        .filter-container{animation: transitionIn-Y-bottom 0.5s;}
        .sub-table,.anime{animation: transitionIn-Y-bottom 0.5s;}
        #chat-container {position: fixed; bottom: 20px; right: 20px; width: 300px; height: 400px; background: white; border: 1px solid #ccc; display: none; flex-direction: column;}
        #chat-header {padding: 10px; background: #007bff; color: white; display: flex; justify-content: space-between;}
        #chat-messages {flex: 1; overflow-y: auto; padding: 10px;}
        #chat-input {width: 100%; padding: 10px; border: none; border-top: 1px solid #ccc;}
    </style>
</head>
<body>
    <?php
    session_start();
    include("../connection.php");

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
            exit;
        }else{
            $useremail=$_SESSION["user"];
        }
    }else{
        header("location: ../login.php");
        exit;
    }

    $sqlmain = "select * from patient where pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch = $userrow->fetch_assoc();

    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];
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
                                    <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
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
                    <td class="menu-btn menu-icon-home menu-active menu-icon-home-active">
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Home</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Scheduled Sessions</p></div></a>
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
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                <tr>
                    <td colspan="1" class="nav-bar">
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;margin-left:20px;">Home</p>
                    </td>
                    <td width="25%"></td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">Today's Date</p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 
                            date_default_timezone_set('Asia/Manila');
                            $today = date('Y-m-d');
                            echo $today;

                            $patientrow = $database->query("select * from patient;");
                            $doctorrow = $database->query("select * from doctor;");
                            $appointmentrow = $database->query("select * from appointment where appodate>='$today';");
                            $schedulerow = $database->query("select * from schedule where scheduledate='$today';");
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;">
                            <img src="../img/calendar.svg" width="100%">
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <center>
                            <table class="filter-container doctor-header patient-header" style="border: none;width:95%" border="0">
                                <tr>
                                    <td>
                                        <h3>Welcome!</h3>
                                        <h1><?php echo $username; ?>.</h1>
                                        <p>Haven't any idea about doctors? no problem let's jumping to 
                                            <a href="doctors.php" class="non-style-link"><b>"All Doctors"</b></a> section or 
                                            <a href="schedule.php" class="non-style-link"><b>"Sessions"</b></a><br>
                                            Track your past and future appointments history.<br>
                                            Also find out the expected arrival time of your doctor or medical consultant.<br><br>
                                        </p>
                                        <h3>Channel a Doctor Here</h3>
                                        <form action="schedule.php" method="post" style="display: flex">
                                            <input type="search" name="search" class="input-text" placeholder="Search Doctor and We will Find The Session Available" list="doctors" style="width:45%;">
                                            <?php
                                            echo '<datalist id="doctors">';
                                            $list11 = $database->query("select docname,docemail from doctor;");
                                            for ($y=0; $y<$list11->num_rows; $y++){
                                                $row00=$list11->fetch_assoc();
                                                $d=$row00["docname"];
                                                echo "<option value='$d'><br/>";
                                            }
                                            echo '</datalist>';
                                            ?>
                                            <input type="Submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                                        </form>
                                        <br><br>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table border="0" width="100%">
                            <tr>
                                <td width="50%">
                                    <center>
                                        <table class="filter-container" style="border: none;" border="0">
                                            <tr>
                                                <td colspan="4">
                                                    <p style="font-size: 20px;font-weight:600;padding-left: 12px;">Status</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex">
                                                        <div>
                                                            <div class="h1-dashboard"><?php echo $doctorrow->num_rows ?></div><br>
                                                            <div class="h3-dashboard">All Doctors</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                                    </div>
                                                </td>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex;">
                                                        <div>
                                                            <div class="h1-dashboard"><?php echo $patientrow->num_rows ?></div><br>
                                                            <div class="h3-dashboard">All Patients</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/patients-hover.svg');"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex;">
                                                        <div>
                                                            <div class="h1-dashboard"><?php echo $appointmentrow->num_rows ?></div><br>
                                                            <div class="h3-dashboard">New Booking</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/book-hover.svg');"></div>
                                                    </div>
                                                </td>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex;padding-top:21px;padding-bottom:21px;">
                                                        <div>
                                                            <div class="h1-dashboard"><?php echo $schedulerow->num_rows ?></div><br>
                                                            <div class="h3-dashboard" style="font-size: 15px">Today Sessions</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/session-iceblue.svg');"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </center>
                                </td>
                                <td>
                                    <p style="font-size: 20px;font-weight:600;padding-left: 40px;" class="anime">Your Upcoming Booking</p>
                                    <center>
                                        <div class="abc scroll" style="height: 250px;padding: 0;margin: 0;">
                                            <table width="85%" class="sub-table scrolldown" border="0">
                                                <thead>
                                                    <tr>
                                                        <th class="table-headin">Appoint. Number</th>
                                                        <th class="table-headin">Session Title</th>
                                                        <th class="table-headin">Doctor</th>
                                                        <th class="table-headin">Scheduled Date & Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $nextweek = date("Y-m-d", strtotime("+1 week"));
                                                    $sqlmain = "select schedule.scheduleid, schedule.title, appointment.apponum, doctor.docname, schedule.scheduledate, schedule.start_time, schedule.end_time 
                                                                from schedule 
                                                                inner join appointment on schedule.scheduleid=appointment.scheduleid 
                                                                inner join patient on patient.pid=appointment.pid 
                                                                inner join doctor on schedule.docid=doctor.docid 
                                                                where patient.pid=? and schedule.scheduledate>='$today' 
                                                                order by schedule.scheduledate asc";
                                                    $stmt = $database->prepare($sqlmain);
                                                    $stmt->bind_param("i", $userid);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();

                                                    if($result->num_rows==0){
                                                        echo '<tr>
                                                            <td colspan="4">
                                                                <br><br><br><br>
                                                                <center>
                                                                    <img src="../img/notfound.svg" width="25%">
                                                                    <br>
                                                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Nothing to show here!</p>
                                                                    <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">Channel a Doctor</button></a>
                                                                </center>
                                                                <br><br><br><br>
                                                            </td>
                                                        </tr>';
                                                    } else {
                                                        while($row = $result->fetch_assoc()){
                                                            $scheduleid = $row["scheduleid"];
                                                            $title = $row["title"];
                                                            $apponum = $row["apponum"];
                                                            $docname = $row["docname"];
                                                            $scheduledate = $row["scheduledate"];
                                                            $start_time = date("h:i A", strtotime($row["start_time"]));
                                                            $end_time = date("h:i A", strtotime($row["end_time"]));
                                                            echo '<tr>
                                                                <td style="padding:30px;font-size:25px;font-weight:700;">'.$apponum.'</td>
                                                                <td style="padding:20px;">'.substr($title,0,30).'</td>
                                                                <td>'.substr($docname,0,20).'</td>
                                                                <td style="text-align:center;">'.substr($scheduledate,0,10).' '.$start_time.' - '.$end_time.'</td>
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
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Chat Interface -->
    <div id="chat-container">
        <div id="chat-header">
            <span id="chat-with"></span>
            <button onclick="toggleChat()" style="background: none; border: none; color: white; cursor: pointer;">X</button>
        </div>
        <div id="chat-messages"></div>
        <input id="chat-input" type="text" placeholder="Type a message..." onkeypress="if(event.key === 'Enter') sendMessage();">
    </div>
    <button onclick="toggleChat()" style="position: fixed; bottom: 20px; right: 20px;">Chat</button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.5.0/socket.io.js"></script>
    <script>
        const socket = io('http://localhost:3000');
        const userId = '<?php echo $userid; ?>';
        let selectedUserId = null;

        socket.emit('join', userId);

        function toggleChat() {
            const chatContainer = document.getElementById('chat-container');
            chatContainer.style.display = chatContainer.style.display === 'none' ? 'flex' : 'none';
            if (chatContainer.style.display === 'flex' && !selectedUserId) {
                loadUserList();
            }
        }

        async function loadUserList() {
            const response = await fetch('http://localhost:3000/api/doctors');
            const doctors = await response.json();
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.innerHTML = '<h3>Select a Doctor:</h3>';
            doctors.forEach(doctor => {
                chatMessages.innerHTML += `<p style="cursor: pointer;" onclick="selectUser('${doctor.docid}', '${doctor.docname}')">${doctor.docname}</p>`;
            });
        }

        async function selectUser(id, name) {
            selectedUserId = id;
            document.getElementById('chat-with').textContent = `Chatting with: ${name}`;
            const response = await fetch(`http://localhost:3000/api/chat/${userId}/${selectedUserId}`);
            const messages = await response.json();
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.innerHTML = '';
            messages.forEach(msg => {
                chatMessages.innerHTML += `<p><b>${msg.sender_id === userId ? 'You' : name}:</b> ${msg.message}</p>`;
            });
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function sendMessage() {
            const input = document.getElementById('chat-input');
            const message = input.value.trim();
            if (message && selectedUserId) {
                socket.emit('sendMessage', { senderId: userId, receiverId: selectedUserId, message });
                input.value = '';
            }
        }

        socket.on('receiveMessage', (msg) => {
            if ((msg.sender_id === userId && msg.receiver_id === selectedUserId) || (msg.sender_id === selectedUserId && msg.receiver_id === userId)) {
                const chatMessages = document.getElementById('chat-messages');
                chatMessages.innerHTML += `<p><b>${msg.sender_id === userId ? 'You' : document.getElementById('chat-with').textContent.split(': ')[1]}:</b> ${msg.message}</p>`;
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });
    </script>
</body>
</html>