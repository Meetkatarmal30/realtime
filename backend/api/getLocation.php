<?php
include('../config/db.php');
$trip_id = $_GET['trip_id'];

$query = "SELECT * FROM Location WHERE trip_id = $trip_id ORDER BY timestamp DESC LIMIT 1";
$result = mysqli_query($conn, $query);
echo json_encode(mysqli_fetch_assoc($result));
?>