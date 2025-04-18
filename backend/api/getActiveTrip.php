<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set("Asia/Kolkata");

$conn = new mysqli("localhost", "root", "", "realtime_tracker");

if ($conn->connect_error) {
    echo json_encode(["error" => "DB connection failed"]);
    exit;
}

$trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : 0;
if ($trip_id <= 0) {
    echo json_encode(["error" => "Invalid trip ID"]);
    exit;
}

// Fetch trip + vehicle + route info
$sql = "
SELECT t.trip_id, t.start_time, t.end_time, v.vehicle_number, v.type,
       r.source_lat, r.source_lng, r.destination_lat, r.destination_lng,
       (SELECT latitude FROM location l WHERE l.trip_id = t.trip_id ORDER BY l.timestamp DESC LIMIT 1) AS latitude,
       (SELECT longitude FROM location l WHERE l.trip_id = t.trip_id ORDER BY l.timestamp DESC LIMIT 1) AS longitude
FROM trip t
JOIN vehicle v ON t.vehicle_id = v.vehicle_id
JOIN route r ON t.route_id = r.route_id
WHERE t.trip_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trip_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Trip not found"]);
    exit;
}

$trip = $result->fetch_assoc();

// Add the route name using latitude and longitude
$trip['route_name'] = $trip['source_lat'] . ", " . $trip['source_lng'] . " to " . $trip['destination_lat'] . ", " . $trip['destination_lng'];

// Fetch route points
$route_sql = "
SELECT latitude, longitude FROM route_point
WHERE trip_id = ? ORDER BY point_order ASC
";

$route_stmt = $conn->prepare($route_sql);
$route_stmt->bind_param("i", $trip_id);
$route_stmt->execute();
$route_result = $route_stmt->get_result();

$route = [];
while ($row = $route_result->fetch_assoc()) {
    $route[] = [floatval($row['latitude']), floatval($row['longitude'])];
}

$trip['route'] = $route;

echo json_encode($trip);
?>
