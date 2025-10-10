<?php
// Assuming you're connecting to a MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kleish_collection";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total sales for each branch (example query, adjust as needed)
$manila_total_sales = 0;
$quezon_total_sales = 0;
$altaraza_total_sales = 0;

$manila_sales_query = "SELECT SUM(total_sales) AS total_sales FROM sales WHERE branch = 'Manila'";
$quezon_sales_query = "SELECT SUM(total_sales) AS total_sales FROM sales WHERE branch = 'Quezon City'";
$altaraza_sales_query = "SELECT SUM(total_sales) AS total_sales FROM sales WHERE branch = 'Altaraza'";

// Execute queries and check for errors
$manila_result = $conn->query($manila_sales_query);
if ($manila_result && $manila_result->num_rows > 0) {
    $row = $manila_result->fetch_assoc();
    $manila_total_sales = $row['total_sales'];
} else {
    // Handle query failure
    echo "Error executing Manila sales query: " . $conn->error;
}

$quezon_result = $conn->query($quezon_sales_query);
if ($quezon_result && $quezon_result->num_rows > 0) {
    $row = $quezon_result->fetch_assoc();
    $quezon_total_sales = $row['total_sales'];
} else {
    // Handle query failure
    echo "Error executing Quezon sales query: " . $conn->error;
}

$altaraza_result = $conn->query($altaraza_sales_query);
if ($altaraza_result && $altaraza_result->num_rows > 0) {
    $row = $altaraza_result->fetch_assoc();
    $altaraza_total_sales = $row['total_sales'];
} else {
    // Handle query failure
    echo "Error executing Altaraza sales query: " . $conn->error;
}

// Fetch sales data for each branch to create the charts
$manila_sales_query = "SELECT sale_date, total_sales FROM sales WHERE branch = 'Manila' ORDER BY sale_date";
$quezon_sales_query = "SELECT sale_date, total_sales FROM sales WHERE branch = 'Quezon City' ORDER BY sale_date";
$altaraza_sales_query = "SELECT sale_date, total_sales FROM sales WHERE branch = 'Altaraza' ORDER BY sale_date";

// Execute the queries and check for errors
$manila_sales = $conn->query($manila_sales_query);
if ($manila_sales) {
    $manila_sales = $manila_sales->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error executing Manila sales data query: " . $conn->error;
}

$quezon_sales = $conn->query($quezon_sales_query);
if ($quezon_sales) {
    $quezon_sales = $quezon_sales->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error executing Quezon sales data query: " . $conn->error;
}

$altaraza_sales = $conn->query($altaraza_sales_query);
if ($altaraza_sales) {
    $altaraza_sales = $altaraza_sales->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error executing Altaraza sales data query: " . $conn->error;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="analyticss.css">
    
   
</head>
<body>


    <div class="dashboard-summary">
        <h3>Sales Summary</h3>
        <div class="dashboard-link">
        <a href="https://business.tiktok.com" target="_blank">Go to TikTok Business Dashboard</a>
    </div>
        <div class="summary-box">
            <h4>Manila Branch</h4>
            <p>Total Sales: ₱<?php echo number_format($manila_total_sales, 2); ?></p>
        </div>
        <div class="summary-box">
            <h4>Quezon City Branch</h4>
            <p>Total Sales: ₱<?php echo number_format($quezon_total_sales, 2); ?></p>
        </div>
        <div class="summary-box">
            <h4>Altaraza Branch</h4>
            <p>Total Sales: ₱<?php echo number_format($altaraza_total_sales, 2); ?></p>
        </div>
    </div>

    <!-- Link to TikTok Business Dashboard -->
    
    <!-- Sales Charts -->
    <div class="chart-container">
        <h3>Manila Branch Sales</h3>
        <canvas id="manilaChart"></canvas>
    </div>

    <div class="chart-container">
        <h3>Quezon City Branch Sales</h3>
        <canvas id="quezonChart"></canvas>
    </div>

    <div class="chart-container">
        <h3>Altaraza Branch Sales</h3>
        <canvas id="altarazaChart"></canvas>
    </div>

    <script>
        // Prepare data for Manila Branch chart
        var manilaData = {
            labels: <?php echo json_encode(array_column($manila_sales, 'sale_date')); ?>,
            datasets: [{
                label: 'Sales Amount',
                data: <?php echo json_encode(array_column($manila_sales, 'total_sales')); ?>,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderWidth: 1
            }]
        };

        // Prepare data for Quezon City Branch chart
        var quezonData = {
            labels: <?php echo json_encode(array_column($quezon_sales, 'sale_date')); ?>,
            datasets: [{
                label: 'Sales Amount',
                data: <?php echo json_encode(array_column($quezon_sales, 'total_sales')); ?>,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderWidth: 1
            }]
        };

        // Prepare data for Altaraza Branch chart
        var altarazaData = {
            labels: <?php echo json_encode(array_column($altaraza_sales, 'sale_date')); ?>,
            datasets: [{
                label: 'Sales Amount',
                data: <?php echo json_encode(array_column($altaraza_sales, 'total_sales')); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 1
            }]
        };

        // Create the charts
        new Chart(document.getElementById('manilaChart'), {
            type: 'line',
            data: manilaData,
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'category',
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Amount (₱)'
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('quezonChart'), {
            type: 'line',
            data: quezonData,
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'category',
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Amount (₱)'
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('altarazaChart'), {
            type: 'line',
            data: altarazaData,
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'category',
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Amount (₱)'
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>
