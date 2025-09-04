<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reporting & Analytics</title>
  <link rel="stylesheet" href="index.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="overlay">
    <div class="container">
      <header>
        <h1>Reporting & Analytics</h1>
        <p>Visualize your hotel performance with detailed analytics and reports.</p>
      </header>

      <div class="grid">
        <a href="fetch_analytics.php" class="module">
          <i class="fas fa-bed"></i>
          <span>Occupancy Report</span>
        </a>
        <a href="sales_revenue.php" class="module">
          <i class="fas fa-dollar-sign"></i>
          <span>Sales & Revenue</span>
        </a>
        <a href="staff_performance.php" class="module">
          <i class="fas fa-user-check"></i>
          <span>Staff Performance</span>
        </a>
        <a href="guest_feedback.php" class="module">
          <i class="fas fa-comment-dots"></i>
          <span>Guest Feedback</span>
        </a>
        <a href="inventory_costs.php" class="module">
          <i class="fas fa-boxes"></i>
          <span>Inventory & Costs</span>
        </a>
      </div>

      <br>
      <a href="index.php" class="module" style="display:inline-block; background:rgba(255,255,255,0.15);">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Main Dashboard</span>
      </a>
    </div>
  </div>
</body>
</html>
