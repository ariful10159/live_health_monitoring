<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IOT Based Smart Health Monitoring System</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        header { background: #f0f0f0; padding: 10px; text-align: center; }
        .tabs { display: flex; justify-content: center; gap: 10px; background: #007BFF; padding: 10px; }
        .tab { color: white; cursor: pointer; padding: 5px 15px; }
        .tab.active { background: #0056b3; }
        .chart-container { width: 45%; display: inline-block; vertical-align: top; padding: 10px; }
        .bar-chart-container { width: 100%; padding: 10px; }
        footer { background: #f0f0f0; text-align: center; padding: 10px; position: fixed; bottom: 0; width: 100%; }
    </style>
</head>
<body>
    <header>
        <h1>IOT Based Smart Health Monitoring System</h1>
        <div class="tabs">
            <div class="tab active" data-tab="ecg">ECG Report</div>
            <div class="tab" data-tab="temp">Temperature</div>
            <div class="tab" data-tab="oxygen">Oxygen Level</div>
            <div class="tab" data-tab="heart">Heart Rate</div>
            <div class="tab" data-tab="export">Export CSV</div>
        </div>
    </header>

    <div class="content">
        <div id="ecgChart" class="chart-container"><canvas id="ecgChartCanvas"></canvas></div>
        <div id="tempChart" class="chart-container" style="display:none;"><canvas id="tempChartCanvas"></canvas></div>
        <div id="oxygenChart" class="chart-container" style="display:none;"><canvas id="oxygenChartCanvas"></canvas></div>
        <div id="heartChart" class="chart-container" style="display:none;"><canvas id="heartChartCanvas"></canvas></div>

        <div class="bar-chart-container">
            <canvas id="barChartCanvas"></canvas>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Patient Monitoring System (PMS)</p>
        <p>Email: PMS.contact@gmail.com | Phone: +88 01521-549873</p>
    </footer>

    <script>
        // Tab switching
        $('.tab').click(function() {
            $('.tab').removeClass('active');
            $(this).addClass('active');
            $('.chart-container').hide();
            $('#' + $(this).data('tab') + 'Chart').show();
        });

        // Fetch data and update charts
        $.ajax({
            url: 'display_last_reading.php',
            type: 'GET',
            success: function(data) {
                console.log(data);
                try {
                    var response = JSON.parse(data);
                    if (response.sensor_data && response.sensor_data.length) {
                        var labels = response.sensor_data.map(d => d.recorded_at);
                        var ecgData = response.sensor_data.map(d => d.ecg);
                        var tempData = response.sensor_data.map(d => d.temp);
                        var bpmData = response.sensor_data.map(d => d.bpm);
                        var spo2Data = response.sensor_data.map(d => d.weight); // Using weight as SPO2 proxy

                        // ECG Chart
                        new Chart(document.getElementById('ecgChartCanvas'), {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'ECG',
                                    data: ecgData,
                                    borderColor: 'orange',
                                    fill: false
                                }]
                            },
                            options: {
                                scales: { y: { beginAtZero: true } }
                            }
                        });
                        // Temperature Chart
                        new Chart(document.getElementById('tempChartCanvas'), {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Temperature',
                                    data: tempData,
                                    borderColor: 'orange',
                                    fill: false
                                }]
                            },
                            options: {
                                scales: { y: { beginAtZero: true } }
                            }
                        });
                        // Oxygen Chart (using weight as proxy for SPO2)
                        new Chart(document.getElementById('oxygenChartCanvas'), {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Oxygen Level',
                                    data: spo2Data,
                                    borderColor: 'orange',
                                    fill: false
                                }]
                            },
                            options: {
                                scales: { y: { beginAtZero: true } }
                            }
                        });
                        // Heart Rate Chart
                        new Chart(document.getElementById('heartChartCanvas'), {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Heart Rate',
                                    data: bpmData,
                                    borderColor: 'orange',
                                    fill: false
                                }]
                            },
                            options: {
                                scales: { y: { beginAtZero: true } }
                            }
                        });

                        // Bar Chart for latest values
                        new Chart(document.getElementById('barChartCanvas'), {
                            type: 'bar',
                            data: {
                                labels: ['Temperature', 'Heart Rate', 'SPO2'],
                                datasets: [{
                                    label: 'GSR',
                                    data: [tempData[tempData.length-1], bpmData[bpmData.length-1], spo2Data[spo2Data.length-1]],
                                    backgroundColor: 'orange'
                                }]
                            },
                            options: {
                                scales: { y: { beginAtZero: true } }
                            }
                        });
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                }
            },
            error: function() {
                alert("Error fetching patient data.");
            }
        });
    </script>
</body>
</html>