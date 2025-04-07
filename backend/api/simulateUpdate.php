<?php
// Connect to DB
$conn = new mysqli("localhost", "root", "", "realtime_tracker");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Simulate moving location
$vehicle_id = 1;

// Get current location
$query = "SELECT latitude, longitude FROM vehicle_location WHERE vehicle_id = $vehicle_id";
$result = $conn->query($query);
$row = $result->fetch_assoc();

$lat = $row['latitude'];
$lng = $row['longitude'];

// Simulate new position
$lat += 0.0005;
$lng += 0.0005;

// Update DB
$update = "UPDATE vehicle_location SET latitude = $lat, longitude = $lng WHERE vehicle_id = $vehicle_id";
$conn->query($update);

echo json_encode(["status" => "updated", "lat" => $lat, "lng" => $lng]);
?>
