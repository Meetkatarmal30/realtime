<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "realtime_tracker";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM feedback";  // Removed ORDER BY clause
$result = $conn->query($query);

$feedback = array();
while($row = $result->fetch_assoc()) {
    $feedback[] = $row;
}

header('Content-Type: application/json');
echo json_encode($feedback);

$conn->close();
?>
