<?php
include("../connection.php");
date_default_timezone_set('Asia/Manila');

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
$stmt->bind_param("s", $filter_date);
$stmt->execute();
$result = $stmt->get_result();

$output = '';
if ($result->num_rows == 0) {
    $output .= "<tr><td colspan='8' style='text-align:center;'>No attendance records found for this date</td></tr>";
} else {
    while ($row = $result->fetch_assoc()) {
        $time_in = $row['time_in'] ? date('h:i A', strtotime($row['time_in'])) : '-'; // e.g., "03:16 PM"
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

        $output .= "<tr>
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
$database->close();
echo $output;
?>