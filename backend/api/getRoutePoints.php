<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "realtime_tracker");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

if (!isset($_GET['trip_id'])) {
    echo json_encode(["error" => "Missing trip_id"]);
    exit;
}

$trip_id = intval($_GET['trip_id']);
$sql = "SELECT latitude, longitude FROM route_point WHERE trip_id = ? ORDER BY point_order ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trip_id);
$stmt->execute();

$res = $stmt->get_result();

$points = [];
while ($row = $res->fetch_assoc()) {
    $points[] = $row;
}

echo json_encode($points);
?>
