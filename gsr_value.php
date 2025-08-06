<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "health_monitor";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to get last 20 gsr and ecg values
$sql = "SELECT ecg, gsr FROM sensor_data ORDER BY id DESC LIMIT 20";
$result = $conn->query($sql);

// Prepare response
$data = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = [
            "ecg" => intval($row['ecg']),
            "gsr" => intval($row['gsr'])
        ];
    }
    echo json_encode($data);
} else {
    echo json_encode(["message" => "No data found."]);
}

$conn->close();
?>
