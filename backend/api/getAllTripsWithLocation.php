<?php
header('Content-Type: application/json');
date_default_timezone_set("Asia/Kolkata");

$conn = new mysqli("localhost", "root", "", "realtime_tracker");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed"]));
}

$sql = "SELECT t.*, v.vehicle_number, v.type,
        (SELECT latitude FROM location l WHERE l.trip_id = t.trip_id ORDER BY l.timestamp DESC LIMIT 1) AS latitude,
        (SELECT longitude FROM location l WHERE l.trip_id = t.trip_id ORDER BY l.timestamp DESC LIMIT 1) AS longitude
        FROM trip t
        JOIN vehicle v ON t.vehicle_id = v.vehicle_id
        ORDER BY t.start_time DESC";

$res = $conn->query($sql);

if (!$res) {
    die(json_encode(["error" => "Query failed: " . $conn->error]));
}

$trips = [];
while ($row = $res->fetch_assoc()) {
    $now = date('Y-m-d H:i:s');
    if ($now < $row['start_time']) $row['status'] = 'Not Started';
    elseif ($now > $row['end_time']) $row['status'] = 'Completed';
    else $row['status'] = 'Running';
    
    $trips[] = $row;
}

echo json_encode($trips);
?>