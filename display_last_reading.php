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

// ✅ sensor_data টেবিল থেকে সর্বশেষ 8টি রেকর্ড আনা
$sql_sensor_data = "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 8";
$result_sensor_data = $conn->query($sql_sensor_data);

// sensor_data গুলো অ্যারে আকারে রাখা
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
            'gsr' => $row['gsr'] ?? "No data",
        ];
    }
} else {
    // ডিফল্ট 1 রেকর্ড দিয়ে রাখি (array এর মধ্যে)
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

// ✅ শুধুমাত্র ecg এর সর্বশেষ 50টি মান আনা
$sql_ecg_data = "SELECT ecg FROM sensor_data ORDER BY id DESC LIMIT 100";
$result_ecg_data = $conn->query($sql_ecg_data);

$ecg_values = [];
if ($result_ecg_data->num_rows > 0) {
    while ($row = $result_ecg_data->fetch_assoc()) {
        $ecg_values[] = $row['ecg'] ?? 0;
    }
} else {
    // fallback default 0 values
    $ecg_values = array_fill(0, 50, 0);
}

// কানেকশন ক্লোজ
$conn->close();

// ✅ রেসপন্স JSON বানানো
$response = [
    'sensor_data' => $sensor_data,   // সর্বশেষ 8টি রেকর্ড
    'ecg_values' => $ecg_values      // সর্বশেষ 50টি ecg মান
];

// JSON রিটার্ন করা
echo json_encode($response);
?>
