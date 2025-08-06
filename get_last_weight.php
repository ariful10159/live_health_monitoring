<?php
header('Content-Type: application/json');

// Database connection info
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "health_monitor";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get last weight value from sensor_data
$sql_weight = "SELECT weight FROM sensor_data ORDER BY id DESC LIMIT 1";
$result_weight = $conn->query($sql_weight);

// Get last height, age, and gender from patients table
$sql_patient = "SELECT height, age, gender FROM patients ORDER BY userID DESC LIMIT 1";
$result_patient = $conn->query($sql_patient);

// Output results as JSON
if ($result_weight->num_rows > 0 && $result_patient->num_rows > 0) {
    $row_weight = $result_weight->fetch_assoc();
    $row_patient = $result_patient->fetch_assoc();

    // Get values
    $weight = floatval($row_weight['weight']);
    $height_cm = floatval($row_patient['height']);
    $height_m = $height_cm / 100;
    $age = intval($row_patient['age']);
    $gender = strtolower($row_patient['gender']);

    // Calculate BMI
    $bmi = $height_m > 0 ? round($weight / ($height_m * $height_m), 2) : 0;

    // Calculate BMR
    if ($gender == "male") {
        $bmr = round((10 * $weight) + (6.25 * $height_cm) - (5 * $age) + 5);
    } elseif ($gender == "female") {
        $bmr = round((10 * $weight) + (6.25 * $height_cm) - (5 * $age) - 161);
    } else {
        $bmr = null;
    }

    // Medical Suggestion based on BMR
    if ($bmr !== null) {
        if ($bmr < 1200) {
            $suggestion = "Very low metabolism. Consult a doctor or improve your diet.";
        } elseif ($bmr < 1500) {
            $suggestion = "Low metabolism. Moderate physical activity is recommended.";
        } elseif ($bmr < 1800) {
            $suggestion = "Average metabolism. Maintain healthy diet and activity.";
        } else {
            $suggestion = "Good metabolism. Maintain your lifestyle with hydration and balance.";
        }
    } else {
        $suggestion = "BMR could not be calculated due to unknown gender.";
    }

    echo json_encode([
        'weight' => $weight,
        'height' => $height_cm,
        'age' => $age,
        'gender' => ucfirst($gender),
        'bmi' => $bmi,
        'bmr' => $bmr,
        'medical_suggestion' => $suggestion
    ]);
} else {
    echo json_encode(['error' => 'No data found for weight, height, age, or gender.']);
}

$conn->close();
?>
