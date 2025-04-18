<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set("Asia/Kolkata");

$conn = new mysqli("localhost", "root", "", "realtime_tracker");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Fetch all active or upcoming trips
$sql = "
    SELECT t.trip_id, t.route_id, t.start_time, t.end_time,
           v.vehicle_number, v.type,
           r.source_lat, r.source_lng,  -- Fetching updated source coordinates
           r.destination_lat, r.destination_lng  -- Fetching updated destination coordinates
    FROM trip t
    JOIN vehicle v ON t.vehicle_id = v.vehicle_id
    LEFT JOIN route r ON t.route_id = r.route_id
    WHERE t.end_time >= NOW()
    ORDER BY t.start_time ASC
";

$res = $conn->query($sql);

if (!$res) {
    error_log("SQL Error: " . $conn->error);
    die(json_encode(["error" => "Query failed: " . $conn->error]));
}

$trips = [];

while ($row = $res->fetch_assoc()) {
    // Fetch stops (route points)
    $trip_id = $row['trip_id'];
    $points = $conn->query("SELECT latitude, longitude FROM route_point WHERE trip_id = $trip_id ORDER BY point_order ASC");

    $stops = [];
    $route = [];

    while ($pt = $points->fetch_assoc()) {
        $lat = floatval($pt['latitude']);
        $lng = floatval($pt['longitude']);
        $stops[] = "($lat, $lng)";
        $route[] = [$lat, $lng];
    }

    // Add source and destination coordinates to the response
    $row['source_coordinates'] = ['lat' => $row['source_lat'], 'lng' => $row['source_lng']];
    $row['destination_coordinates'] = ['lat' => $row['destination_lat'], 'lng' => $row['destination_lng']];

    // Add the stops and route data
    $row['stops'] = $stops;
    $row['route'] = $route;

    // Generate route name (using the updated coordinates)
    $row['route_name'] = $row['source_lat'] . " to " . $row['destination_lat'];

    // Dummy driver name for now
    $row['driver'] = "Driver " . $trip_id;

    $trips[] = $row;
}

header('Content-Type: application/json');
echo json_encode($trips);
?>