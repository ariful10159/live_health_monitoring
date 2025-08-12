<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "health_monitor";

try {
    // Create database connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get latest 20 GSR values in ascending order
    $sql = "SELECT id, value FROM gsr_values ORDER BY id DESC LIMIT 20";
    $result = $conn->query($sql);

    $gsr_data = [];
    if ($result && $result->num_rows > 0) {
        // Process results in reverse to maintain chronological order
        $temp_data = [];
        while ($row = $result->fetch_assoc()) {
            $temp_data[] = [
                'timestamp' => $row['id'],
                'gsr_value' => (float)$row['value']
            ];
        }
        // Reverse the array to get ascending order
        $gsr_data = array_reverse($temp_data);
    } else {
        // Return empty array if no data
        $gsr_data = [];
    }

    // Close connection
    $conn->close();

    // Return JSON response
    echo json_encode([
        'success' => true,
        'data' => $gsr_data,
        'count' => count($gsr_data),
        'message' => count($gsr_data) ? 'Data retrieved successfully' : 'No GSR data available'
    ]);

} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'data' => []
    ]);
}
?>