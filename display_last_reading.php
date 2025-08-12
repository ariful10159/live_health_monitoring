<?php
header("Access-Control-Allow-Origin: *");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "health_monitor";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get last 50 sensor records (excluding GSR)
$sql_sensor_data = "SELECT id, recorded_at, temp, hum, ds18, bpm, ecg, weight FROM sensor_data ORDER BY id DESC LIMIT 50";
$result_sensor_data = $conn->query($sql_sensor_data);

$sensor_data = [];
if ($result_sensor_data->num_rows > 0) {
    while ($row = $result_sensor_data->fetch_assoc()) {
        $sensor_data[] = [
            'recorded_at' => $row['recorded_at'] ?? "No data",
            'temp' => $row['temp'] ?? "No data",
            'hum' => $row['hum'] ?? "No data",
            'ds18' => $row['ds18'] ?? "No data",
            'bpm' => $row['bpm'] ?? "No data",
            'ecg' => $row['ecg'] ?? "No data",
            'weight' => $row['weight'] ?? "No data",
            'gsr' => "No data" // Initialize with no data
        ];
    }
} else {
    $sensor_data[] = [
        'recorded_at' => "No data",
        'temp' => "No data",
        'hum' => "No data",
        'ds18' => "No data",
        'bpm' => "No data",
        'ecg' => "No data",
        'weight' => "No data",
        'gsr' => "No data",
    ];
}

// Get GSR values from mental_health_wearable_data table - FIXED QUERY
$sql_gsr_data = "SELECT GSR_Values as gsr_value, Timestamp 
                 FROM mental_health_wearable_data 
                 ORDER BY Timestamp DESC 
                 LIMIT 50";

$result_gsr_data = $conn->query($sql_gsr_data);

if (!$result_gsr_data) {
    die("GSR Query failed: " . $conn->error);
}

$gsr_values = [];
if ($result_gsr_data->num_rows > 0) {
    while ($row = $result_gsr_data->fetch_assoc()) {
        $gsr_values[] = [
            'timestamp' => $row['Timestamp'] ?? "No data",
            'gsr_value' => $row['gsr_value'] ?? "No data"  // Using the alias
        ];
    }
}

// If we have GSR data, merge it with sensor data
if (!empty($gsr_values)) {
    // We'll match by array index since we're getting the same number of records (50)
    foreach ($sensor_data as $key => &$data) {
        if (isset($gsr_values[$key])) {
            $data['gsr'] = $gsr_values[$key]['gsr_value'];
        }
    }
}

// Get ECG values
$sql_ecg_data = "SELECT ecg_value FROM ecg_values LIMIT 4000";
$result_ecg_data = $conn->query($sql_ecg_data);

$ecg_values = [];
if ($result_ecg_data->num_rows > 0) {
    while ($row = $result_ecg_data->fetch_assoc()) {
        $ecg_values[] = $row['ecg_value'] ?? 0;
    }
} else {
    $ecg_values = array_fill(0, 300, 0);
}

$conn->close();

$response = [
    'sensor_data' => $sensor_data,
    'ecg_values' => $ecg_values
];

echo json_encode($response);
?>