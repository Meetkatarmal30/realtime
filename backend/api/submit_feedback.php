<?php
// Show errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "realtime_tracker");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if it's a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $trip_id = isset($_POST["trip_id"]) ? intval($_POST["trip_id"]) : 0;
  $message = isset($_POST["message"]) ? trim($_POST["message"]) : '';

  if ($trip_id > 0 && !empty($message)) {
    // Check if trip_id exists in trip table
    $checkTripStmt = $conn->prepare("SELECT trip_id FROM trip WHERE trip_id = ?");
    $checkTripStmt->bind_param("i", $trip_id);
    $checkTripStmt->execute();
    $checkTripResult = $checkTripStmt->get_result();

    if ($checkTripResult && $checkTripResult->num_rows > 0) {
      $stmt = $conn->prepare("INSERT INTO feedback (trip_id, message, timestamp) VALUES (?, ?, NOW())");
      $stmt->bind_param("is", $trip_id, $message);

      if ($stmt->execute()) {
        echo "✅ Feedback submitted successfully! <br><a href='../../frontend/index.html'>Back to Home</a>";
      } else {
        echo "❌ Error submitting feedback: " . $stmt->error;
      }

      $stmt->close();
    } else {
      echo "❌ Invalid trip ID. Feedback not submitted.";
    }

    $checkTripStmt->close();
  } else {
    echo "❌ Please fill in all required fields.";
  }
} else {
  echo "Invalid request.";
}

$conn->close();
?>
