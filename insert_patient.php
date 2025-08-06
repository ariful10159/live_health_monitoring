<?php
// ডাটাবেজ কানেকশন সেটআপ
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "health_monitor";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $patientName = $_POST['patientName'];
    $height = $_POST['height'];
    $bloodPressure = $_POST['bloodPressure'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];

    // Insert data into the patients table
    $sql = "INSERT INTO patients (patientName, height, bloodPressure, age, gender) 
            VALUES ('$patientName', '$height', '$bloodPressure', '$age', '$gender')";

    if ($conn->query($sql) === TRUE) {
        $message = "New patient record created successfully.";
        $messageClass = "success";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
        $messageClass = "error";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Patient Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="patient_form_style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <i class="fas fa-heartbeat"></i>
            <span>IoT Health Monitoring</span>
        </div>
        <div class="navbar-links">
            <a href="dashboard.html" class="nav-link">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="sensor_data_entry.php" class="nav-link">
                <i class="fas fa-database"></i> Manually add
            </a>
            <a href="patient_details.php" class="nav-link">
                <i class="fas fa-user"></i> Patient Details
            </a>
            <a href="insert_patient.php" class="nav-link active">
                <i class="fas fa-plus"></i> Add Patient details
            </a>
            <a href="export_csv.php" class="nav-link" id="export-csv">
                <i class="fas fa-file-export"></i> Export CSV
            </a>
        </div>
    </nav>

    <div class="form-container">
        <h1>Add Patient Details</h1>

        <?php if (isset($message)): ?>
            <div class="message <?php echo $messageClass; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="patientName">Patient Name:</label>
                <input type="text" id="patientName" name="patientName" required>
            </div>
            
            <div class="form-group">
                <label for="height">Height (cm):</label>
                <input type="number" id="height" name="height" required>
            </div>
            
            <div class="form-group">
                <label for="bloodPressure">Blood Pressure:</label>
                <input type="text" id="bloodPressure" name="bloodPressure" required>
            </div>

            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="">-- Select Gender --</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
