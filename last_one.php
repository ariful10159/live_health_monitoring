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

// ✅ sensor_data টেবিল থেকে সর্বশেষ 1টি রেকর্ড আনা
$sql_sensor_data = "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 1";
$result_sensor_data = $conn->query($sql_sensor_data);

// sensor_data গুলো অ্যারে আকারে রাখা
$sensor_data = [];
if ($result_sensor_data->num_rows > 0) {
    while ($row = $result_sensor_data->fetch_assoc()) {
        $sensor_data[] = [
            'recorded_at' => $row['recorded_at'] ?? "No data",

            'hum' => $row['hum'] ?? "No data",
            'ds18' => $row['ds18'] ?? "No data",
            'bpm' => $row['bpm'] ?? "No data",
            'ecg' => $row['ecg'] ?? "No data",
            'weight' => $row['weight'] ?? "No data",
            'gsr' => $row['gsr'] ?? "No data",
            'blood_glucose' => $row['blood_glucose'] ?? "No data",
            'spo2' => $row['spo2'] ?? "No data" // ✅ নতুন spo2 ফিল্ড
        ];
    }
} else {
    // ডিফল্ট 1 রেকর্ড দিয়ে রাখি (array এর মধ্যে)
    $sensor_data[] = [
        'recorded_at' => "No data",

        'hum' => "No data",
        'ds18' => "No data",
        'bpm' => "No data",
        'ecg' => "No data",
        'weight' => "No data",
        'gsr' => "No data",
        'blood_glucose' => "No data",
        'spo2' => "No data" // ✅ spo2 ডিফল্ট
    ];
}

// কানেকশন ক্লোজ
$conn->close();

// ✅ রেসপন্স JSON বানানো (শুধু sensor_data)
$response = [
    'sensor_data' => $sensor_data
];

// JSON রিটার্ন করা
echo json_encode($response);
?>
