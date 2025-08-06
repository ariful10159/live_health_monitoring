<?php
// ডাটাবেজ কানেকশন সেটআপ
$servername = "localhost";
$username = "root";
$password = "";    
$dbname = "health_monitor";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        $message = "Connection failed: " . $conn->connect_error;
    } else {
        // POST data collect
        $temp = $_POST['temp'];
        $hum = $_POST['hum'];
        $ds18 = $_POST['ds18'];
        $bpm = $_POST['bpm'];
        $ecg = $_POST['ecg'];
        $weight = $_POST['weight'];
        $gsr = $_POST['gsr'];
        $blood_glucose = $_POST['blood_glucose'];

        // SQL query to insert data
        $sql = "INSERT INTO sensor_data (temp, hum, ds18, bpm, ecg, weight, gsr, blood_glucose) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dddddddi", $temp, $hum, $ds18, $bpm, $ecg, $weight, $gsr, $blood_glucose);

        if ($stmt->execute()) {
            $message = "Data inserted successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Health Monitoring System</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="sensor_data_style.css">
</head>
<body>
   <nav class="navbar">
        <div class="navbar-brand">
            <i class="fas fa-heartbeat"></i>
            <span>IoT Health Monitoring</span>
        </div>
        <div class="navbar-links">
            <a href="dashboard.html" class="nav-link"><i class="fas fa-home"></i> Home</a>
            <a href="sensor_data_entry.php" class="nav-link active"><i class="fas fa-plus"></i> Manually add</a>
            <a href="patient_details.php" class="nav-link"><i class="fas fa-user"></i> Patient Details</a>
            <a href="insert_patient.php" class="nav-link"><i class="fas fa-plus"></i> Add Patient</a>
            <a href="export_csv.php" class="nav-link" id="export-csv"><i class="fas fa-file-export"></i> Export CSV</a>
        </div>
    </nav>

    <div class="form-container">
        <h2>Enter Sensor Data</h2>
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="temp">Temperature (°C):</label>
            <input type="number" step="0.1" name="temp" required>

            <label for="hum">Humidity (%):</label>
            <input type="number" step="0.1" name="hum" required>

            <label for="ds18">DS18 Temperature (°C):</label>
            <input type="number" step="0.1" name="ds18" required>

            <label for="bpm">Heart Rate (bpm):</label>
            <input type="number" name="bpm" required>

            <label for="ecg">ECG Value:</label>
            <input type="number" name="ecg" required>

            <label for="weight">Weight (g):</label>
            <input type="number" name="weight" required>

            <label for="gsr">GSR Value:</label>
            <input type="number" name="gsr" required>

            <label for="blood_glucose">Blood Glucose (mg/dL):</label>
            <input type="number" name="blood_glucose" required>

            <button type="submit">Submit Data</button>
        </form>
    </div>
</body>
</html>
