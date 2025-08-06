<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "health_monitor";

// DB connect
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="health_monitor_data.csv"');

$output = fopen('php://output', 'w');

// --- Export sensor_data table ---

// Write a title for sensor_data section
fputcsv($output, ['Sensor Data Table']);
fputcsv($output, ['Recorded At', 'Temp', 'Humidity', 'DS18 Temp', 'BPM', 'ECG', 'Weight', 'Blood Glucose']);

$sql_sensor = "SELECT recorded_at, temp, hum, ds18, bpm, ecg, weight, blood_glucose FROM sensor_data ORDER BY recorded_at DESC";
$result_sensor = $conn->query($sql_sensor);

if ($result_sensor->num_rows > 0) {
    while ($row = $result_sensor->fetch_assoc()) {
        fputcsv($output, [
            $row['recorded_at'],
            $row['temp'],
            $row['hum'],
            $row['ds18'],
            $row['bpm'],
            $row['ecg'],
            $row['weight'],
            $row['blood_glucose'],
        ]);
    }
} else {
    fputcsv($output, ['No sensor data found']);
}

// Empty line to separate tables
fputcsv($output, []);

// --- Export patients table ---

// Write a title for patients section
fputcsv($output, ['Patients Table']);
fputcsv($output, ['Patient Name', 'Height', 'Blood Pressure', 'Recorded At']);

$sql_patients = "SELECT patientName, height, bloodPressure, recorded_at FROM patients ORDER BY userID DESC";
$result_patients = $conn->query($sql_patients);

if ($result_patients->num_rows > 0) {
    while ($row = $result_patients->fetch_assoc()) {
        fputcsv($output, [
            $row['patientName'],
            $row['height'],
            $row['bloodPressure'],
            $row['recorded_at'],
        ]);
    }
} else {
    fputcsv($output, ['No patient data found']);
}

fclose($output);
$conn->close();
?>
