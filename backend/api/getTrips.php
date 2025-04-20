<?php
$conn = new mysqli("localhost", "root", "", "realtime_tracker");
$sql = "
  SELECT t.*, v.vehicle_number, v.type 
  FROM trip t 
  JOIN vehicle v ON t.vehicle_id = v.vehicle_id
  ORDER BY t.start_time DESC
";
$res = $conn->query($sql);
$trips = [];
while ($row = $res->fetch_assoc()) {
  $trips[] = $row;
}
echo json_encode($trips);