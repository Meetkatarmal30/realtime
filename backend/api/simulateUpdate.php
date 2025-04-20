<?php
$conn = new mysqli("localhost", "root", "", "realtime_tracker");

// Step 1: Get trip_id safely
$tripRes = $conn->query("SELECT trip_id FROM trip ORDER BY trip_id DESC LIMIT 1");

if ($tripRes && $tripRes->num_rows > 0) {
    $tripRow = $tripRes->fetch_assoc();
    $trip_id = $tripRow['trip_id'];
} else {
    echo "No trip found!";
    exit();
}

// Step 2: Generate random location
$lat = 12.9716 + (rand(-100, 100) / 10000);
$lng = 77.5946 + (rand(-100, 100) / 10000);

// Step 3: Insert into location table
$sql = "INSERT INTO location (trip_id, latitude, longitude, timestamp)
        VALUES ($trip_id, $lat, $lng, NOW())";

if ($conn->query($sql)) {
    echo "Location updated!";
} else {
    echo "Error: " . $conn->error;
}
?>