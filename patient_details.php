<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "health_monitor";
// Initialize message and data variables
$message = "";
$sensor_data = null;
$patient_data = null;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $message = "Connection failed: " . $conn->connect_error;
} else {
    // Fetch latest sensor data (bpm and ecg)
    $sensor_sql = "SELECT bpm, ecg FROM sensor_data ORDER BY id DESC LIMIT 1";
    $sensor_result = $conn->query($sensor_sql);

    if ($sensor_result->num_rows > 0) {
        $sensor_data = $sensor_result->fetch_assoc();
    } else {
        $message .= "No sensor data found. ";
    }

    // Fetch latest patient data (patientName, height, bloodPressure)
    $patient_sql = "SELECT patientName, height, bloodPressure FROM patients ORDER BY USERid DESC LIMIT 1";
    $patient_result = $conn->query($patient_sql);

    if ($patient_result->num_rows > 0) {
        $patient_data = $patient_result->fetch_assoc();
    } else {
        $message .= "No patient data found.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest Sensor and Patient Data</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="patient_details_style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <i class="fas fa-heartbeat"></i>
            <span>IoT Health Monitoring</span>
        </div>
        <div class="navbar-links">
            <a href="dashboard.html" class="nav-link ">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="sensor_data_entry.php" class="nav-link">
                <i class="fas fa-home"></i> Manually add
            </a>
            <a href="patient_details.php" class="nav-link active">
                <i class="fas fa-user"></i> Patient Details
            </a>
            <a href="insert_patient.php" class="nav-link">
                <i class="fas fa-plus"></i> Add Patient details
            </a>
            <a href="export_csv.php" class="nav-link" id="export-csv">
                <i class="fas fa-file-export"></i> Export CSV
            </a>
        </div>
    </nav>
    <div class="container">
        <h2>Latest Data</h2>
        <?php if ($message): ?>
            <div class="message error">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($sensor_data || $patient_data): ?>
            <?php if ($sensor_data): ?>
                <div class="data-row">
                    <span class="label">Latest BPM:</span> <?php echo isset($sensor_data['bpm']) ? $sensor_data['bpm'] : 'N/A'; ?> bpm<br>
                    <span class="label">Latest ECG:</span> <?php echo isset($sensor_data['ecg']) ? $sensor_data['ecg'] : 'N/A'; ?>
                </div>
            <?php endif; ?>

            <?php if ($patient_data): ?>
                <div class="data-row">
                    <span class="label">Patient Name:</span> <?php echo isset($patient_data['patientName']) ? htmlspecialchars($patient_data['patientName']) : 'N/A'; ?><br>
                    <span class="label">Height:</span> <?php echo isset($patient_data['height']) ? $patient_data['height'] : 'N/A'; ?> cm<br>
                    <span class="label">Blood Pressure:</span> <?php echo isset($patient_data['bloodPressure']) ? htmlspecialchars($patient_data['bloodPressure']) : 'N/A'; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="message error">
                No data available.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>