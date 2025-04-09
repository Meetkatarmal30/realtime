<?php
$tripId = isset($_GET['trip_id']) ? $_GET['trip_id'] : 0;

$conn = new mysqli("localhost", "root", "", "tracker_db");
if ($conn->connect_error) {
    die("Connection failed");
}

$sql = "SELECT lat, lng FROM route_points WHERE trip_id = $tripId ORDER BY sequence ASC";
$result = $conn->query($sql);

$points = [];
while ($row = $result->fetch_assoc()) {
    $points[] = $row;
}

header('Content-Type: application/json');
echo json_encode($points);
$conn->close();
?>
