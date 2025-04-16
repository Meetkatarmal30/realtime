<?php
// Set timezone
date_default_timezone_set("Asia/Kolkata");

// Connect to the database
$conn = new mysqli("localhost", "root", "", "realtime_tracker");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the trip_id from the URL query string
$trip_id = isset($_GET['trip_id']) ? (int) $_GET['trip_id'] : 0;

// If no trip_id is provided, return an empty response
if ($trip_id <= 0) {
    echo json_encode([]);
    exit;
}

// Debugging: Log the trip_id to check its value
error_log("Requested trip_id: " . $trip_id);

// Query to get the trip details by trip_id
$sql = "
    SELECT t.trip_id, t.route_name, t.vehicle_id, v.vehicle_number, v.type,
           t.start_time, t.end_time,
           (SELECT latitude FROM location l WHERE l.trip_id = t.trip_id ORDER BY l.timestamp DESC LIMIT 1) AS latitude,
           (SELECT longitude FROM location l WHERE l.trip_id = t.trip_id ORDER BY l.timestamp DESC LIMIT 1) AS longitude
    FROM trip t
    JOIN vehicle v ON t.vehicle_id = v.vehicle_id
    WHERE t.trip_id = $trip_id
";

// Execute the query
$res = $conn->query($sql);

// Debugging: Check if the query was successful
if (!$res) {
    error_log("Query failed: " . $conn->error);
    die("Query failed: " . $conn->error);
}

$trip = null;

if ($res && $res->num_rows > 0) {
    $trip = $res->fetch_assoc();

    // Debugging: Log the fetched trip data
    error_log("Fetched trip: " . print_r($trip, true));

    // Fetch the route points
    $routeQuery = "SELECT latitude, longitude FROM route_point WHERE trip_id = $trip_id ORDER BY id ASC";
    $routeRes = $conn->query($routeQuery);

    if (!$routeRes) {
        error_log("Route Query failed: " . $conn->error);
        die("Route Query failed: " . $conn->error);
    }

    $route = [];
    if ($routeRes && $routeRes->num_rows > 0) {
        while ($point = $routeRes->fetch_assoc()) {
            $route[] = [$point['latitude'], $point['longitude']];
        }
    }

    // Add route to the trip
    $trip['route'] = $route;
}

// Debugging: Log final trip data before returning
error_log("Final trip data: " . print_r($trip, true));

// Return the trip data or an empty array if no trip found
header('Content-Type: application/json');
echo json_encode($trip ? $trip : []);
?>
