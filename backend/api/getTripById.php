<?php
// Set timezone
date_default_timezone_set("Asia/Kolkata");

// Enable error logging but don't display errors
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Connect to the database
$conn = new mysqli("localhost", "root", "", "realtime_tracker");

if ($conn->connect_error) {
    echo json_encode(['error' => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Get the trip_id from the URL query string
$trip_id = isset($_GET['trip_id']) ? (int) $_GET['trip_id'] : 0;

// If no trip_id is provided, return an error response
if ($trip_id <= 0) {
    echo json_encode(['error' => 'Invalid trip ID']);
    exit;
}

try {
    // Basic query without joins to minimize errors
    $sql = "SELECT * FROM trip WHERE trip_id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $trip_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Query failed: " . $stmt->error);
    }
    
    if ($result->num_rows > 0) {
        $trip = $result->fetch_assoc();
        
        // Add status
        $now = date('Y-m-d H:i:s');
        if ($now < $trip['start_time']) {
            $trip['status'] = 'Not Started';
        } elseif ($now > $trip['end_time']) {
            $trip['status'] = 'Completed';
        } else {
            $trip['status'] = 'Running';
        }
        
        // Get vehicle info separately
        if (isset($trip['vehicle_id'])) {
            $vSql = "SELECT vehicle_number, type FROM vehicle WHERE vehicle_id = ?";
            $vStmt = $conn->prepare($vSql);
            if ($vStmt) {
                $vStmt->bind_param("i", $trip['vehicle_id']);
                $vStmt->execute();
                $vResult = $vStmt->get_result();
                if ($vResult && $vResult->num_rows > 0) {
                    $vehicle = $vResult->fetch_assoc();
                    $trip['vehicle_number'] = $vehicle['vehicle_number'];
                    $trip['type'] = $vehicle['type'];
                }
                $vStmt->close();
            }
        }
        
        // Get route points separately
        $routeSql = "SELECT latitude, longitude FROM route_point WHERE trip_id = ? ORDER BY id ASC";
        $routeStmt = $conn->prepare($routeSql);
        if ($routeStmt) {
            $routeStmt->bind_param("i", $trip_id);
            $routeStmt->execute();
            $routeResult = $routeStmt->get_result();
            
            $route = [];
            if ($routeResult && $routeResult->num_rows > 0) {
                while ($point = $routeResult->fetch_assoc()) {
                    $route[] = [(float)$point['latitude'], (float)$point['longitude']];
                }
            }
            
            $trip['route'] = $route;
            $routeStmt->close();
        }
        
        echo json_encode($trip);
    } else {
        echo json_encode(['error' => "Trip not found"]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("getTripById error: " . $e->getMessage());
    echo json_encode(['error' => "Database error: " . $e->getMessage()]);
}

$conn->close();
?>