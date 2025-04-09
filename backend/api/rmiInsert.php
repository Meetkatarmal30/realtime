<?php
$conn = new mysqli("localhost", "root", "", "realtime_tracker");

$trip_id = 1; // Or fetch latest active trip_id dynamically

$lat = $_POST['lat'];
$lng = $_POST['lng'];

$sql = "INSERT INTO location (trip_id, latitude, longitude, timestamp)
        VALUES ($trip_id, $lat, $lng, NOW())";

if ($conn->query($sql)) {
    echo "Location inserted via RMI!";
} else {
    echo "DB Error: " . $conn->error;
}
?>
