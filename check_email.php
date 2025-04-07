<?php
include("connection.php");

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    
    $sql = "SELECT * FROM webuser WHERE email = ?";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }
    
    $stmt->close();
}
?>