<!DOCTYPE  html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Breakdown Dashboard</title>
    <meta name="description" content="Built with jdoodle.ai - Comprehensive breakdown history and reporting system for hotel housekeeping, room management, and inventory">
    <meta property="og:title" content="Breakdown Dashboard">
    <meta property="og:description" content="Built with jdoodle.ai - Comprehensive breakdown history and reporting system for hotel housekeeping, room management, and inventory">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Breakdown Dashboard">
    <meta name="twitter:description" content="Built with jdoodle.ai - Comprehensive breakdown history and reporting system for hotel housekeeping, room management, and inventory">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🏨</text></svg>">
    <style>
      * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  height: 100%;
  background: url('hotel_room.jpg') no-repeat center center fixed;
  background-size: cover;
        }
        .overlay {
  background: rgba(0, 0, 0, 0.65);
  background-size:cover;
  min-height: 100vh;


}

        .container {
            max-width: 1200px;
            margin: 0 auto;
  background: transparent;
            border-radius: 20px;
              border: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }
        .header { background: linear-gradient(135deg, #0f172a, #1e293b); color: white; padding: 30px 20px; text-align: center; margin-bottom: 30px; border-radius: 12px; }
        .hero-img { width: 100%; max-width: 600px; height: 200px; object-fit: cover; border-radius: 8px; margin: 20px 0; }
        .nav-tabs { display: flex; gap: 2px; background: white; border-radius: 8px; padding: 4px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .tab { flex: 1; padding: 12px 20px; text-align: center; border-radius: 6px; cursor: pointer; font-weight: 500; transition: all 0.2s; }
        .tab.active { background: #3b82f6; color: white; }
        .tab:not(.active) { color: #6b7280; }
        .tab:not(.active):hover { background: #f3f4f6; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-left: 4px solid #ef4444; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #dc2626; }
        .stat-label { color: #6b7280; font-size: 0.9rem; margin-top: 5px; }
        .section { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .issue-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .issue-table th, .issue-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .issue-table th { background: #f9fafb; font-weight: 600; }
        .status { padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 500; }
        .status-urgent { background: #fef2f2; color: #dc2626; }
        .status-high { background: #fff7ed; color: #ea580c; }
        .status-medium { background: #fefce8; color: #ca8a04; }
        .status-resolved { background: #f0fdf4; color: #16a34a; }
        .status-pending { background: #f0f9ff; color: #0284c7; }
        .room-img { width: 50px; height: 35px; object-fit: cover; border-radius: 4px; margin-right: 8px; }
        .filter-bar { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
        .filter-input { padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px; background: white; }
        .btn { padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 500; }
        .btn:hover { background: #2563eb; }
        .btn-secondary { background: #6b7280; }
        .btn-secondary:hover { background: #4b5563; }
        .priority-indicator { width: 12px; height: 12px; border-radius: 50%; display: inline-block; margin-right: 8px; }
        .priority-critical { background: #dc2626; }
        .priority-high { background: #ea580c; }
        .priority-medium { background: #ca8a04; }
        .priority-low { background: #16a34a; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .chart-bars { margin-top: 15px; }
        .chart-bar { height: 25px; background: linear-gradient(90deg, #3b82f6, #1d4ed8); margin: 8px 0; border-radius: 4px; display: flex; align-items: center; padding: 0 10px; color: white; font-size: 0.9rem; font-weight: 500; }
          @media (max-width: 768px) { 
            .stats-grid { grid-template-columns: 1fr 1fr; } 
            .filter-bar { flex-direction: column; }
            .nav-tabs { flex-direction: column; }
        }
        .footer {
  position: bottom;
  bottom: 0;
  left: 0;
  width: 100%;
  padding: 15px;
  background: #111827;
  color: #f9fafb;
  font-size: 10px;
  border-top: 1px solid #374151;

  display: bottom;
  justify-content: center; /* centers horizontally */
  align-items: center;     /* centers vertically */
  text-align: center;
}

    </style>
</head>
<body>
      <div class="overlay">
    <div class="container">
    <div class="header">
    <h1>
        <a href="maintenance.php" style="text-decoration: none; color: white;">
             Hotel Maintenance Dashboard
        </a>
    </h1>
    <p>Breakdown History & Reporting </p>
</div>

    <div class="container">
        <nav class="nav-tabs">
            <div class="tab active" onclick="switchTab('housekeeping')">🧹 Housekeeping</div>
            <div class="tab" onclick="switchTab('rooms')">🛏️ Room Management</div>
            <div class="tab" onclick="switchTab('inventory')">📦 Inventory</div>
            <div class="tab" onclick="switchTab('reports')">📊 Reports</div>
        </nav>

        <!-- Housekeeping Tab -->
        <div id="housekeeping" class="tab-content active">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">23</div>
                    <div class="stat-label">Open Issues (Today)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Urgent Repairs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">156</div>
                    <div class="stat-label">Resolved (This Week)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">92%</div>
                    <div class="stat-label">Resolution Rate</div>
                </div>
            </div>

            <div class="section">
                <h3>Recent Housekeeping Issues</h3>
                <div class="filter-bar">
                    <select class="filter-input">
                        <option>All Floors</option>
                        <option>Floor 1-5</option>
                        <option>Floor 6-10</option>
                        <option>Floor 11-15</option>
                    </select>
                    <select class="filter-input">
                        <option>All Priorities</option>
                        <option>Urgent</option>
                        <option>High</option>
                        <option>Medium</option>
                    </select>
                    <button class="btn">Filter</button>
                     <!-- NEW Search Bar -->
    <input type="text" id="searchInput" class="filter-input" placeholder="🔍 Search by Room, Issue, or Staff">

                </div>
                <table class="issue-table">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Issue</th>
                            <th>Priority</th>
                            <th>Reported</th>
                            <th>Staff</th>
                            <th>Status</th>
                            <th>ETA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <img src="https://images.unsplash.com/photo-1590761044169-b9ad903fca4d?ixid=M3w3MjUzNDh8MHwxfHNlYXJjaHwxfHxob3RlbCUyMGhvdXNla2VlcGluZyUyMG1haW50ZW5hbmNlfGVufDB8fHx8MTc1Njk3Mjk2N3ww&ixlib=rb-4.1.0&fit=fillmax&h=100&w=150" alt="Room" class="room-img">
                                <strong>Room 415</strong>
                            </td>
                            <td>Bathroom leak under sink</td>
                            <td><span class="priority-indicator priority-critical"></span>Urgent</td>
                            <td>Jan 15, 14:30</td>
                            <td>Maria Santos</td>
                            <td><span class="status status-pending">In Progress</span></td>
                            <td>2 hours</td>
                        </tr>
                        <tr>
                            <td>
                                <img src="https://images.unsplash.com/photo-1590761044169-b9ad903fca4d?ixid=M3w3MjUzNDh8MHwxfHNlYXJjaHwxfHxob3RlbCUyMGhvdXNla2VlcGluZyUyMG1haW50ZW5hbmNlfGVufDB8fHx8MTc1Njk3Mjk2N3ww&ixlib=rb-4.1.0&fit=fillmax&h=100&w=150" alt="Room" class="room-img">
                                <strong>Room 312</strong>
                            </td>
                            <td>AC not cooling properly</td>
                            <td><span class="priority-indicator priority-high"></span>High</td>
                            <td>Jan 15, 12:15</td>
                            <td>John Kim</td>
                            <td><span class="status status-urgent">Urgent</span></td>
                            <td>1 hour</td>
                        </tr>
                        <tr>
                            <td>
                                <img src="https://images.unsplash.com/photo-1590761044169-b9ad903fca4d?ixid=M3w3MjUzNDh8MHwxfHNlYXJjaHwxfHxob3RlbCUyMGhvdXNla2VlcGluZyUyMG1haW50ZW5hbmNlfGVufDB8fHx8MTc1Njk3Mjk2N3ww&ixlib=rb-4.1.0&fit=fillmax&h=100&w=150" alt="Room" class="room-img">
                                <strong>Room 208</strong>
                            </td>
                            <td>Light bulb replacement needed</td>
                            <td><span class="priority-indicator priority-medium"></span>Medium</td>
                            <td>Jan 15, 10:45</td>
                            <td>Lisa Park</td>
                            <td><span class="status status-resolved">Completed</span></td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Room Management Tab -->
        <div id="rooms" class="tab-content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">245</div>
                    <div class="stat-label">Total Rooms</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">12</div>
                    <div class="stat-label">Out of Order</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">187</div>
                    <div class="stat-label">Occupied</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">46</div>
                    <div class="stat-label">Available</div>
                </div>
            </div>

            <div class="section">
                <h3>Room Status Overview</h3>
                <table class="issue-table">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Last Maintenance</th>
                            <th>Next Service</th>
                            <th>Issues</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Room 501</strong></td>
                            <td>Suite</td>
                            <td><span class="status status-urgent">Out of Order</span></td>
                            <td>Jan 10, 2024</td>
                            <td>Pending repair</td>
                            <td>Plumbing issue</td>
                            <td><button class="btn btn-secondary">View Details</button></td>
                        </tr>
                        <tr>
                            <td><strong>Room 423</strong></td>
                            <td>Deluxe</td>
                            <td><span class="status status-resolved">Available</span></td>
                            <td>Jan 14, 2024</td>
                            <td>Jan 28, 2024</td>
                            <td>None</td>
                            <td><button class="btn">Schedule Service</button></td>
                        </tr>
                        <tr>
                            <td><strong>Room 318</strong></td>
                            <td>Standard</td>
                            <td><span class="status status-pending">Maintenance Due</span></td>
                            <td>Dec 28, 2023</td>
                            <td>Jan 15, 2024</td>
                            <td>Scheduled cleaning</td>
                            <td><button class="btn">Start Service</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inventory Tab -->
        <div id="inventory" class="tab-content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">1,247</div>
                    <div class="stat-label">Total Items</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">23</div>
                    <div class="stat-label">Low Stock Items</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">5</div>
                    <div class="stat-label">Out of Stock</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">$12.5k</div>
                    <div class="stat-label">Monthly Cost</div>
                </div>
            </div>

            <div class="section">
                <h3>Inventory Status</h3>
                <table class="issue-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Min Required</th>
                            <th>Status</th>
                            <th>Last Ordered</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Toilet Paper</strong></td>
                            <td>Bathroom Supplies</td>
                            <td>45 units</td>
                            <td>100 units</td>
                            <td><span class="status status-urgent">Low Stock</span></td>
                            <td>Jan 10, 2024</td>
                            <td><button class="btn">Reorder</button></td>
                        </tr>
                        <tr>
                            <td><strong>Towels (Bath)</strong></td>
                            <td>Linens</td>
                            <td>0 units</td>
                            <td>50 units</td>
                            <td><span class="status status-urgent">Out of Stock</span></td>
                            <td>Jan 5, 2024</td>
                            <td><button class="btn">Urgent Order</button></td>
                        </tr>
                        <tr>
                            <td><strong>Cleaning Supplies</strong></td>
                            <td>Maintenance</td>
                            <td>156 units</td>
                            <td>80 units</td>
                            <td><span class="status status-resolved">In Stock</span></td>
                            <td>Jan 12, 2024</td>
                            <td><button class="btn btn-secondary">View Details</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reports Tab -->
        <div id="reports" class="tab-content">
            <div class="section">
                <h3>Weekly Breakdown Analysis</h3>
                <div class="chart-bars">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Plumbing Issues</span>
                        <span>34 incidents</span>
                    </div>
                    <div class="chart-bar" style="width: 85%;">Plumbing: 34</div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>HVAC Problems</span>
                        <span>28 incidents</span>
                    </div>
                    <div class="chart-bar" style="width: 70%;">HVAC: 28</div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Electrical Issues</span>
                        <span>19 incidents</span>
                    </div>
                    <div class="chart-bar" style="width: 47%;">Electrical: 19</div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Housekeeping</span>
                        <span>45 requests</span>
                    </div>
                    <div class="chart-bar" style="width: 100%;">Housekeeping: 45</div>
                </div>
            </div>

            <div class="section">
                <h3>Cost Breakdown (Monthly)</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin-top: 20px;">
                    <div style="text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: bold; color: #dc2626;">$8.2k</div>
                        <div style="color: #6b7280;">Materials</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: bold; color: #dc2626;">$15.6k</div>
                        <div style="color: #6b7280;">Labor</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: bold; color: #dc2626;">$4.3k</div>
                        <div style="color: #6b7280;">External Contractors</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: bold; color: #dc2626;">$28.1k</div>
                        <div style="color: #6b7280;">Total</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Suggestion dropdown -->
<ul id="suggestions" class="suggestion-box"></ul>

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(tabName).classList.add('active');
        }
    </script>
    <script>
    const searchInput = document.getElementById("searchInput");
    const suggestionsBox = document.getElementById("suggestions");

    // Example data for suggestions
    const data = ["Room 415", "Room 312", "Room 208", "Maria Santos", "John Kim", "Lisa Park", "Bathroom leak", "AC not cooling"];

    searchInput.addEventListener("input", () => {
        const query = searchInput.value.toLowerCase();
        suggestionsBox.innerHTML = "";
        
        if (query) {
            const matches = data.filter(item => item.toLowerCase().includes(query));
            if (matches.length > 0) {
                suggestionsBox.style.display = "block";
                matches.forEach(match => {
                    const li = document.createElement("li");
                    li.textContent = match;
                    li.onclick = () => {
                        searchInput.value = match;
                        suggestionsBox.style.display = "none";
                    };
                    suggestionsBox.appendChild(li);
                });
            } else {
                suggestionsBox.style.display = "none";
            }
        } else {
            suggestionsBox.style.display = "none";
        }
    });

    document.addEventListener("click", (e) => {
        if (e.target !== searchInput) {
            suggestionsBox.style.display = "none";
        }
    });
</script>
<!-- FOOTER -->
<footer class="footer">
  <p>© 2025 Hotel Maintenance and Engineering | All Rights Reserved</p>
</footer>

</body>
</html>
 