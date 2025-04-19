<?php
$conn = new mysqli("localhost", "root", "", "realtime_tracker");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate input
if (!isset($_POST['trip_id']) || !isset($_POST['lat']) || !isset($_POST['lng'])) {
    die("Missing parameters.");
}

$trip_id = intval($_POST['trip_id']);
$lat = floatval($_POST['lat']);
$lng = floatval($_POST['lng']);

// Insert into database
$sql = "INSERT INTO location (trip_id, latitude, longitude, timestamp)
        VALUES (?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("idd", $trip_id, $lat, $lng);

if ($stmt->execute()) {
    echo "Location inserted via RMI!";
} else {
    echo "DB Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
