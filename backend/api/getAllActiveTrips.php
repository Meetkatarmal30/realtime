<?php
date_default_timezone_set("Asia/Kolkata");

$conn = new mysqli("localhost", "root", "", "realtime_tracker");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
    SELECT t.trip_id, t.route_id, v.vehicle_number, v.type,
           t.start_time, t.end_time
    FROM trip t
    JOIN vehicle v ON t.vehicle_id = v.vehicle_id
    WHERE NOW() BETWEEN t.start_time AND t.end_time
    ORDER BY t.start_time ASC
";

$res = $conn->query($sql);

$trips = [];

while ($row = $res->fetch_assoc()) {
    // Build route name like "Route #<id>" or fetch real name if you want from a `route` table
    $row['route_name'] = "Route #" . $row['route_id'];
    $trips[] = $row;
}

header('Content-Type: application/json');
echo json_encode($trips);
?>
