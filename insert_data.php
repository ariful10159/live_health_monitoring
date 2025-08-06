<?php
header("Access-Control-Allow-Origin: *");  // CORS error এড়াতে

// ডাটাবেজ কানেকশন সেটআপ
$servername = "localhost";
$username = "root";
$password = "";    // তোমার XAMPP MySQL পাসওয়ার্ড যদি থাকে
$dbname = "health_monitor";

// MySQL কানেক্ট করা
$conn = new mysqli($servername, $username, $password, $dbname);

// কানেকশন চেক করা
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// POST থেকে ডেটা নেওয়া
$temp = isset($_POST['temp']) ? floatval($_POST['temp']) : null;
$hum = isset($_POST['hum']) ? floatval($_POST['hum']) : null;
$ds18 = isset($_POST['ds18']) ? floatval($_POST['ds18']) : null;
$bpm = isset($_POST['bpm']) ? intval($_POST['bpm']) : null;
$ecg = isset($_POST['ecg']) ? intval($_POST['ecg']) : null;
$weight = isset($_POST['weight']) ? floatval($_POST['weight']) : null;
$gsr = isset($_POST['gsr']) ? intval($_POST['gsr']) : null;

// Validate data (optional)
if ($temp === null || $hum === null || $ds18 === null || $bpm === null || $ecg === null || $weight === null || $gsr === null) {
    echo "Error: Missing parameters";
    $conn->close();
    exit();
}

// Prepare statement for security
$stmt = $conn->prepare("INSERT INTO sensor_data (temp, hum, ds18, bpm, ecg, weight, gsr) VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("dddiiid", $temp, $hum, $ds18, $bpm, $ecg, $gsr, $weight);

if ($stmt->execute()) {
    echo "New patient record created successfully.";
    // Button to redirect to temp.php
    echo '<br><br><a href="temp.php"><button style="padding: 10px 20px; font-size: 16px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">Go Back to Temp Data</button></a>';
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
