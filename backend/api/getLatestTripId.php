<?php
$conn = new mysqli("localhost", "root", "", "realtime_tracker");

$res = $conn->query("SELECT trip_id FROM trip ORDER BY trip_id DESC LIMIT 1");
$row = $res->fetch_assoc();

echo json_encode(['trip_id' => $row['trip_id'] ?? null]);
?>
