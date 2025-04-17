<?php
date_default_timezone_set("Asia/Kolkata");

$conn = new mysqli("localhost", "root", "", "realtime_tracker");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$sql = "
    SELECT t.trip_id, t.route_id, v.vehicle_number, v.type,
           t.start_time, t.end_time,
           (SELECT latitude FROM location l WHERE l.trip_id = t.trip_id ORDER BY l.timestamp DESC LIMIT 1) AS latitude,
           (SELECT longitude FROM location l WHERE l.trip_id = t.trip_id ORDER BY l.timestamp DESC LIMIT 1) AS longitude
    FROM trip t
    JOIN vehicle v ON t.vehicle_id = v.vehicle_id
    WHERE t.start_time <= NOW() 
      AND (t.end_time IS NULL OR t.end_time > NOW())
    ORDER BY t.start_time ASC
";

$res = $conn->query($sql);

if (!$res) {
    die(json_encode(["error" => "Query failed: " . $conn->error]));
}

$trips = [];

while ($row = $res->fetch_assoc()) {
    $row['route_name'] = "Route #" . $row['route_id'];
    $trips[] = $row;
}

header('Content-Type: application/json');
echo json_encode($trips);
?>