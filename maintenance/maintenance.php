<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hotel La Vista - Maintenance</title>
  <link rel="stylesheet" href="../homepage/index.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="overlay">
    <div class="container">
      <header>
        <h1>Maintenance Module</h1>
        <p>Manage hotel maintenance tasks efficiently.</p>
      </header>
      <div class="grid">
        <a href="requests/maintenance_requests.php" class="module">
          <i class="fas fa-wrench"></i>
          <span>Maintenance Requests</span>
        </a>
        <a href="maintenance_schedule.php" class="module">
          <i class="fas fa-calendar-alt"></i>
          <span>Maintenance Schedule</span>
        </a>
        <a href="maintenance_logs.php" class="module">
          <i class="fas fa-file-alt"></i>
          <span>Maintenance Logs</span>
        </a>
        <a href="maintenance_inventory.php" class="module">
          <i class="fas fa-box-open"></i>
          <span>Equipment & Inventory</span>
        </a>
        <a href="/hotel/homepage/index.php" class="module back">
          <i class="fas fa-arrow-left"></i>
          <span>Back to Main Dashboard</span>
        </a>
      </div>
    </div>
  </div>
</body>
</html>
