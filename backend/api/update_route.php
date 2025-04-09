<?php
$conn = new mysqli("localhost", "root", "", "realtime_tracker");
$trip_id = $_POST['trip_id'];
$route_points = explode("\n", trim($_POST['route_points']));

// Remove existing route points
$conn->query("DELETE FROM route_point WHERE trip_id = $trip_id");

$order = 1;
foreach ($route_points as $point) {
  $coords = explode(",", trim($point));
  if (count($coords) == 2) {
    $lat = floatval($coords[0]);
    $lng = floatval($coords[1]);
    $conn->query("INSERT INTO route_point (trip_id, latitude, longitude, point_order)
                  VALUES ($trip_id, $lat, $lng, $order)");
    $order++;
  }
}

echo "<p>✅ Route updated successfully!</p><a href='../../frontend/edit_route.html'>Edit Another</a>";