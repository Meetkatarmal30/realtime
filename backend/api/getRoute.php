<?php
header('Content-Type: application/json');
include __DIR__ . '/../db_config.php';

if (!isset($_GET['trip_id'])) {
    echo json_encode([]);
    exit;
}

$trip_id = intval($_GET['trip_id']);
$sql = "SELECT latitude, longitude FROM route_point WHERE trip_id = ? ORDER BY point_order ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trip_id);
$stmt->execute();
$result = $stmt->get_result();

$points = [];
while ($row = $result->fetch_assoc()) {
    $points[] = $row;
}
echo json_encode($points);
