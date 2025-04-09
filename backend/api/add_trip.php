<?php
$conn = new mysqli("localhost", "root", "", "realtime_tracker");

// Step 1: Get form inputs
$vehicle_number = $_POST['vehicle_number'];
$type = $_POST['type'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$route_points = explode("\n", trim($_POST['route_points']));

// Step 2: Insert or get existing vehicle
$res = $conn->query("SELECT vehicle_id FROM vehicle WHERE vehicle_number = '$vehicle_number'");
if ($res->num_rows > 0) {
    $vehicle_id = $res->fetch_assoc()['vehicle_id'];
} else {
    $conn->query("INSERT INTO vehicle (vehicle_number, type) VALUES ('$vehicle_number', '$type')");
    $vehicle_id = $conn->insert_id;
}

// Step 3: Insert trip
$conn->query("INSERT INTO trip (vehicle_id, start_time, end_time)
              VALUES ($vehicle_id, '$start_time', '$end_time')");
$trip_id = $conn->insert_id;

// Step 4: Insert route points
$order = 1;
foreach ($route_points as $line) {
    $coords = explode(",", trim($line));
    if (count($coords) == 2) {
        $lat = trim($coords[0]);
        $lng = trim($coords[1]);
        $conn->query("INSERT INTO route_point (trip_id, latitude, longitude, point_order)
                      VALUES ($trip_id, $lat, $lng, $order)");
        $order++;
    }
}

// Step 5: Done
echo "✅ Trip and route added successfully. <a href='../../frontend/index.html'>Go to Map</a>";
?>
