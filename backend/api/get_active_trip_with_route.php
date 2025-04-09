<?php
$host = "localhost";
$db = "your_database_name";
$user = "root";
$pass = ""; // or your MySQL password

$conn = new mysqli($host, $user, $pass, $db);

// 1. Get current active trip
$now = date("Y-m-d H:i:s");
$trip_sql = "SELECT * FROM trip WHERE start_time <= '$now' AND end_time >= '$now' LIMIT 1";
$trip_result = $conn->query($trip_sql);

if ($trip_result->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "No active trip"]);
    exit;
}

$trip = $trip_result->fetch_assoc();
$trip_id = $trip['id'];

// 2. Fetch route for this trip
$route_sql = "SELECT latitude, longitude FROM route_point WHERE trip_id = $trip_id ORDER BY point_order ASC";
$route_result = $conn->query($route_sql);

$route = [];
while ($row = $route_result->fetch_assoc()) {
    $route[] = ["lat" => $row["latitude"], "lng" => $row["longitude"]];
}

if (count($route) == 0) {
    echo json_encode(["status" => "error", "message" => "No route data"]);
    exit;
}

echo json_encode([
    "status" => "success",
    "trip_id" => $trip_id,
    "vehicle_type" => $trip['vehicle_type'],
    "vehicle_id" => $trip['vehicle_id'],
    "start_time" => $trip['start_time'],
    "end_time" => $trip['end_time'],
    "route" => $route
]);
?>
