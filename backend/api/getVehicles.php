<?php
$conn = new mysqli("localhost", "root", "", "realtime_tracker");
$res = $conn->query("SELECT * FROM vehicle");
$vehicles = [];
while ($row = $res->fetch_assoc()) {
  $vehicles[] = $row;
}
echo json_encode($vehicles);