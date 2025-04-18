<?php
$conn = new mysqli("localhost", "root", "", "realtime_tracker");

// Step 1: Check if form data is sent via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 2: Get form inputs
    if (isset($_POST['vehicle_number']) && isset($_POST['type']) &&
        isset($_POST['start_time']) && isset($_POST['end_time']) &&
        isset($_POST['route_points'])) {

        $vehicle_number = $_POST['vehicle_number'];
        $type = $_POST['type'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $route_points = explode("\n", trim($_POST['route_points']));

        // Step 3: Insert or get existing vehicle
        $res = $conn->query("SELECT vehicle_id FROM vehicle WHERE vehicle_number = '$vehicle_number'");
        if ($res->num_rows > 0) {
            $vehicle_id = $res->fetch_assoc()['vehicle_id'];
        } else {
            $conn->query("INSERT INTO vehicle (vehicle_number, type) VALUES ('$vehicle_number', '$type')");
            $vehicle_id = $conn->insert_id;
        }

        // 🔥 Step 4: Insert route with source and destination coordinates
        $first_point = explode(",", trim($route_points[0]));
        $last_point = explode(",", trim(end($route_points)));

        $source_lat = trim($first_point[0]);
        $source_lng = trim($first_point[1]);
        $destination_lat = trim($last_point[0]);
        $destination_lng = trim($last_point[1]);

        $conn->query("INSERT INTO route (source_lat, source_lng, destination_lat, destination_lng) 
                      VALUES ('$source_lat', '$source_lng', '$destination_lat', '$destination_lng')");
        $route_id = $conn->insert_id;

        // ✅ Step 5: Insert trip with route_id
        $conn->query("INSERT INTO trip (vehicle_id, route_id, start_time, end_time)
                      VALUES ($vehicle_id, $route_id, '$start_time', '$end_time')");
        $trip_id = $conn->insert_id;

        // ✅ Step 6: Insert route points
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

        // ✅ Step 7: Return success message
        echo "✅ Trip and route added successfully. <a href='../../frontend/index.html'>Go to Map</a>";
    } else {
        echo "❌ Error: Missing required fields. Please fill all fields.";
    }
} else {
    echo "❌ Error: Form not submitted correctly.";
}
?>
