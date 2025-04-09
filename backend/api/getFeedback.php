<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "tracker_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM feedback ORDER BY submitted_at DESC";
$result = $conn->query($query);

$feedback = array();
while($row = $result->fetch_assoc()) {
    $feedback[] = $row;
}
header('Content-Type: application/json');
echo json_encode($feedback);
$conn->close();
?>
