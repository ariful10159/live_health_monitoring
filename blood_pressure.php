<?php
// Database connection
$servername = "";
$username = "root";
$password = "";
$dbname = "health_monitor";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch latest blood pressure value
$sql = "SELECT bloodPressure FROM patients ORDER BY USERid DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo $row['bloodPressure'];
} else {
    echo "--";
}

$conn->close();
?>
