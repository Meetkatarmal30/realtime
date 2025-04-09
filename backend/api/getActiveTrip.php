<?php
header('Content-Type: application/json');
include __DIR__ . '/../db_config.php';

$now = date('Y-m-d H:i:s');
$sql = "SELECT * FROM trip WHERE start_time <= ? AND end_time >= ? ORDER BY start_time DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $now, $now);
$stmt->execute();

$result = $stmt->get_result();
$trip = $result->fetch_assoc();

echo json_encode($trip ?: []);
