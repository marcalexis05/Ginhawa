<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'a') {
    header("location: ../login.php");
    exit;
}
include("../connection.php");

$request_id = $_GET['request_id'] ?? '';
if (!$request_id) {
    header("location: index.php?error=Invalid request");
    exit;
}

// Fetch request details
$request_query = $database->query("SELECT pr.*, s.scheduleid FROM patient_requests pr 
                                  LEFT JOIN schedule s ON pr.title = s.title AND pr.session_date = s.scheduledate 
                                  AND pr.start_time = s.start_time AND pr.end_time = s.end_time 
                                  WHERE pr.request_id='$request_id'");
if ($request_query && $request_query->num_rows > 0) {
    $request = $request_query->fetch_assoc();
} else {
    header("location: index.php?error=Request not found");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scheduleid = $request['scheduleid'];
    $title = $database->real_escape_string($_POST['title']);
    $scheduledate = $database->real_escape_string($_POST['scheduledate']);
    $start_time = $database->real_escape_string($_POST['start_time']);
    $end_time = $database->real_escape_string($_POST['end_time']);
    $duration = $database->real_escape_string($_POST['duration']);

    $update_query = "UPDATE schedule SET title='$title', scheduledate='$scheduledate', start_time='$start_time', 
                     end_time='$end_time', duration='$duration' WHERE scheduleid='$scheduleid'";
    if ($database->query($update_query)) {
        // Mark notification as processed
        $database->query("UPDATE admin_notifications SET status='processed' WHERE request_id='$request_id'");
        header("location: index.php?success=Schedule updated");
    } else {
        header("location: edit_schedule.php?request_id=$request_id&error=Update failed: " . $database->error);
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Schedule</title>
    <link rel="stylesheet" href="../css/main.css">
</head>
<body>
    <div class="container">
        <h2>Edit Schedule</h2>
        <?php if (isset($_GET['error'])) echo "<p style='color:red'>" . $_GET['error'] . "</p>"; ?>
        <form method="POST">
            <label>Title:</label><br>
            <input type="text" name="title" value="<?php echo htmlspecialchars($request['title']); ?>" required><br>
            <label>Date:</label><br>
            <input type="date" name="scheduledate" value="<?php echo $request['session_date']; ?>" required><br>
            <label>Start Time:</label><br>
            <input type="time" name="start_time" value="<?php echo $request['start_time']; ?>" required><br>
            <label>End Time:</label><br>
            <input type="time" name="end_time" value="<?php echo $request['end_time']; ?>" required><br>
            <label>Duration:</label><br>
            <input type="text" name="duration" value="<?php echo $request['duration']; ?>" required><br>
            <input type="submit" value="Save" class="btn-primary btn">
            <a href="index.php" class="btn-primary-soft btn">Cancel</a>
        </form>
    </div>
</body>
</html>
<?php $database->close(); ?>