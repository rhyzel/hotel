<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reporting & Analytics</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body, html {
      height: 100%;
      font-family: 'Outfit', sans-serif;
      background: url('/hotel/homepage/hotel_room.jpg') no-repeat center center fixed;
      background-size: cover;
    }

    .overlay {
      background: rgba(0, 0, 0, 0.65);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center; 
      align-items: center;
      padding: 40px 20px;
    }

    .container {
      max-width: 1100px;
      width: 100%;
      text-align: center;
      color: #fff;
    }

    header h1 {
      font-size: 3rem;
      font-weight: 700;
      letter-spacing: 1px;
      margin-bottom: 10px;
    }

    header p {
      font-size: 1.1rem;
      opacity: 0.85;
      margin-bottom: 50px;
    }

    /* Grid layout: 3 columns, 2 rows */
    .grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr); /* 3 columns */
      gap: 20px;
      max-width: 900px;
      margin: 0 auto;
      padding: 0 20px;
    }

    /* Module Buttons - smaller */
    .module {
      background: rgba(255, 255, 255, 0.08);
      padding: 16px 12px; /* smaller padding */
      border-radius: 12px;
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      text-decoration: none;
      color: #fff;
      border: 1px solid rgba(255, 255, 255, 0.12);
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.25);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .module:hover {
      background: rgba(255, 255, 255, 0.18);
      transform: translateY(-3px) scale(1.02);
    }

    .module i {
      font-size: 2rem; /* smaller icon */
      margin-bottom: 8px;
      display: block;
      color: #ffd700;
    }

    .module span {
      font-size: 0.85rem; /* smaller text */
      font-weight: 600;
      display: block;
    }

    /* Responsive adjustments */
    @media screen and (max-width: 900px) {
      .grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media screen and (max-width: 600px) {
      .grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <div class="overlay">
    <div class="container">
      <header>
        <h1>Reporting & Analytics</h1>
        <p>Visualize your hotel performance with detailed analytics and reports.</p>
      </header>

      <div class="grid">
        <a href="occupancy_dashboard/analytics_page.php" class="module">
          <i class="fas fa-bed"></i>
          <span>Room Occupancy Dashboard</span>
        </a>

        <a href="occupancy_report/occupancy_reports.php" class="module">
          <i class="fas fa-chart-bar"></i>
          <span>Room Occupancy Reports</span>
        </a>

        <a href="sales_and_revenue/sales_revenue.php" class="module">
          <i class="fas fa-dollar-sign"></i>
          <span>Sales & Revenue</span>
        </a>

        <a href="staff_performance.php" class="module">
          <i class="fas fa-user-check"></i>
          <span>Staff Performance</span>
        </a>

        <a href="guest_feedback/guest_feedback.php" class="module">
          <i class="fas fa-comment-dots"></i>
          <span>Guest Feedback</span>
        </a>

        <a href="inventory_reports/inventory_reports.php" class="module">
          <i class="fas fa-boxes"></i>
          <span>Inventory & Costs</span>
        </a>
      </div>

      <br>
      <a href="../homepage/index.php" class="module" style="display:inline-block; background:rgba(255,255,255,0.15);">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Main Dashboard</span>
      </a>
    </div>
  </div>
</body>
</html>
