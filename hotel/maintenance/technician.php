<!DOCTYPE  html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Maintenance System - Built with jdoodle.ai</title>
  <meta name="description" content="Built with jdoodle.ai - Complete hotel maintenance technician assignment system for housekeeping, room management, and inventory tracking">
  <meta property="og:title" content="Hotel Maintenance System - Built with jdoodle.ai">
  <meta property="og:description" content="Built with jdoodle.ai - Complete hotel maintenance technician assignment system for housekeeping, room management, and inventory tracking">
  <meta property="og:image" content="https://imagedelivery.net/None/628c0037j2b14j4fd9j9785j14163e98b3e2/public">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Hotel Maintenance System - Built with jdoodle.ai">
  <meta name="twitter:description" content="Built with jdoodle.ai - Complete hotel maintenance technician assignment system for housekeeping, room management, and inventory tracking">
  <meta name="twitter:image" content="https://imagedelivery.net/None/628c0037j2b14j4fd9j9785j14163e98b3e2/public">
  <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔧</text></svg>">
  <style>
      body {
        height: 100%;
  font-family: 'Outfit', sans-serif;
  background: url('hotel_room.jpg') no-repeat center center fixed;
  background-size: cover;
    }
            .overlay {
  background: rgba(0, 0, 0, 0.65);
  background-size:cover;
  min-height: 100vh;}
  
          .container {
            max-width: 1200px;
            margin: 0 auto;
        }
    .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; }
    .nav { display: flex; gap: 15px; margin-top: 15px; }
    .nav button { background: rgba(255,255,255,0.2); border: none; color: white; padding: 10px 20px; border-radius: 8px; cursor: pointer; transition: all 0.3s; }
    .nav button:hover, .nav button.active { background: rgba(255,255,255,0.3); }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; }
    .card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.07); border: 1px solid #e2e8f0; }
    .card h3 { color: #2d3748; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
    .icon { width: 20px; height: 20px; }
    .status { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .status.pending { background: #fef3c7; color: #92400e; }
    .status.progress { background: #dbeafe; color: #1e40af; }
    .status.completed { background: #d1fae5; color: #065f46; }
    .status.urgent { background: #fee2e2; color: #991b1b; }
    .form { display: grid; gap: 15px; }
    .form input, .form select, .form textarea { padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; }
    .form button { background: #4f46e5; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
    .form button:hover { background: #4338ca; }
    .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
    .table th { background: #f9fafb; font-weight: 600; color: #374151; }
    .badge { display: inline-block; padding: 4px 8px; background: #f3f4f6; border-radius: 4px; font-size: 12px; margin: 2px; }
    .hidden { display: none; }
    .priority-high { border-left: 4px solid #ef4444; }
    .priority-medium { border-left: 4px solid #f59e0b; }
    .priority-low { border-left: 4px solid #10b981; }
    .hero { background: url('https://images.unsplash.com/photo-1585406666850-82f7532fdae3') center/cover; height: 200px; border-radius: 12px; position: relative; overflow: hidden; }
    .hero::before { content: ''; position: absolute; inset: 0; background: linear-gradient(45deg, rgba(79,70,229,0.8), rgba(124,75,162,0.8)); }
    .hero-content { position: relative; z-index: 1; height: 100%; display: flex; align-items: center; justify-content: center; color: white; text-align: center; }
         .footer {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  padding: 15px;
  background: #111827;
  color: #f9fafb;
  font-size: 10px;
  border-top: 1px solid #374151;

  display: flex;
  justify-content: center; /* centers horizontally */
  align-items: center;     /* centers vertically */
  text-align: center;
}
 </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="hero">
        <div class="hero-content">
          <div>
            <h1>
                 <a href="maintenance.php" style="text-decoration: none; color: white;">
                 TECHNICIAN ASSIGNMENT DASHBOARD
                </a>    
            </h1>
            <p>Technician Assignment & Management Dashboard</p>
          </div>
        </div>
      </div>
      <div class="nav">
        <button onclick="showTab('dashboard')" class="active" id="tab-dashboard">Dashboard</button>
        <button onclick="showTab('assignments')" id="tab-assignments">Assignments</button>
        <button onclick="showTab('rooms')" id="tab-rooms">Room Status</button>
        <button onclick="showTab('inventory')" id="tab-inventory">Inventory</button>
        <button onclick="showTab('technicians')" id="tab-technicians">Technicians</button>
      </div>
    </div>

    <!-- Dashboard Tab -->
    <div id="dashboard-tab">
      <div class="grid">
        <div class="card">
          <h3>📊 Today's Overview</h3>
          <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-top: 15px;">
            <div style="text-align: center; padding: 15px; background: #f8fafc; border-radius: 8px;">
              <div style="font-size: 24px; font-weight: bold; color: #4f46e5;">12</div>
              <div style="font-size: 14px; color: #6b7280;">Active Tasks</div>
            </div>
            <div style="text-align: center; padding: 15px; background: #f8fafc; border-radius: 8px;">
              <div style="font-size: 24px; font-weight: bold; color: #059669;">8</div>
              <div style="font-size: 14px; color: #6b7280;">Completed</div>
            </div>
            <div style="text-align: center; padding: 15px; background: #f8fafc; border-radius: 8px;">
              <div style="font-size: 24px; font-weight: bold; color: #dc2626;">3</div>
              <div style="font-size: 14px; color: #6b7280;">Urgent</div>
            </div>
            <div style="text-align: center; padding: 15px; background: #f8fafc; border-radius: 8px;">
              <div style="font-size: 24px; font-weight: bold; color: #7c2d12;">5</div>
              <div style="font-size: 14px; color: #6b7280;">Technicians</div>
            </div>
          </div>
        </div>

        <div class="card">
          <h3>⚡ Urgent Tasks</h3>
          <div style="space-y: 10px;">
            <div class="priority-high" style="padding: 12px; background: #fef2f2; border-radius: 8px; margin-bottom: 10px;">
              <div style="font-weight: 600;">Room 301 - AC Not Working</div>
              <div style="font-size: 14px; color: #6b7280;">Assigned to: Mike Johnson</div>
            </div>
            <div class="priority-high" style="padding: 12px; background: #fef2f2; border-radius: 8px; margin-bottom: 10px;">
              <div style="font-weight: 600;">Lobby - Plumbing Issue</div>
              <div style="font-size: 14px; color: #6b7280;">Assigned to: Sarah Wilson</div>
            </div>
          </div>
        </div>

        <div class="card">
          <h3>🏠 Room Status Summary</h3>
          <table class="table">
            <tr>
              <td>Available Rooms</td>
              <td><span class="status completed">45</span></td>
            </tr>
            <tr>
              <td>Under Maintenance</td>
              <td><span class="status progress">8</span></td>
            </tr>
            <tr>
              <td>Out of Order</td>
              <td><span class="status urgent">2</span></td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <!-- Assignments Tab -->
    <div id="assignments-tab" class="hidden">
      <div class="grid">
        <div class="card">
          <h3>➕ New Assignment</h3>
          <form class="form" onsubmit="addAssignment(event)">
            <select required>
              <option value="">Select Technician</option>
              <option value="mike">Mike Johnson</option>
              <option value="sarah">Sarah Wilson</option>
              <option value="david">David Chen</option>
              <option value="lisa">Lisa Rodriguez</option>
            </select>
            <input type="text" placeholder="Task Title" required>
            <select required>
              <option value="">Priority Level</option>
              <option value="urgent">Urgent</option>
              <option value="high">High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>
            <select required>
              <option value="">Category</option>
              <option value="plumbing">Plumbing</option>
              <option value="electrical">Electrical</option>
              <option value="hvac">HVAC</option>
              <option value="housekeeping">Housekeeping</option>
              <option value="general">General Maintenance</option>
            </select>
            <input type="text" placeholder="Room/Location">
            <textarea placeholder="Description" rows="3"></textarea>
            <button type="submit">Create Assignment</button>
          </form>
        </div>

        <div class="card">
          <h3>📋 Active Assignments</h3>
          <table class="table">
            <thead>
              <tr>
                <th>Task</th>
                <th>Technician</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Due</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>AC Repair - Room 301</td>
                <td>Mike Johnson</td>
                <td><span class="status urgent">Urgent</span></td>
                <td><span class="status progress">In Progress</span></td>
                <td>Today 2PM</td>
              </tr>
              <tr>
                <td>Toilet Fix - Room 205</td>
                <td>Sarah Wilson</td>
                <td><span class="status pending">High</span></td>
                <td><span class="status pending">Pending</span></td>
                <td>Today 4PM</td>
              </tr>
              <tr>
                <td>Light Bulb Replacement</td>
                <td>David Chen</td>
                <td><span class="status completed">Low</span></td>
                <td><span class="status progress">In Progress</span></td>
                <td>Tomorrow</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Room Status Tab -->
    <div id="rooms-tab" class="hidden">
      <div class="grid">
        <div class="card">
          <h3>🏠 Room Management</h3>
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; margin-top: 15px;">
            <div class="room-grid">
              <div class="room available" onclick="selectRoom(101)">101</div>
              <div class="room available" onclick="selectRoom(102)">102</div>
              <div class="room maintenance" onclick="selectRoom(103)">103</div>
              <div class="room available" onclick="selectRoom(104)">104</div>
              <div class="room occupied" onclick="selectRoom(105)">105</div>
              <div class="room available" onclick="selectRoom(201)">201</div>
              <div class="room maintenance" onclick="selectRoom(202)">202</div>
              <div class="room available" onclick="selectRoom(203)">203</div>
              <div class="room ooo" onclick="selectRoom(204)">204</div>
              <div class="room available" onclick="selectRoom(205)">205</div>
              <div class="room available" onclick="selectRoom(301)">301</div>
              <div class="room maintenance" onclick="selectRoom(302)">302</div>
            </div>
          </div>
          <div style="margin-top: 20px; display: flex; gap: 15px; font-size: 14px;">
            <div><span style="display: inline-block; width: 12px; height: 12px; background: #10b981; border-radius: 2px; margin-right: 5px;"></span>Available</div>
            <div><span style="display: inline-block; width: 12px; height: 12px; background: #f59e0b; border-radius: 2px; margin-right: 5px;"></span>Maintenance</div>
            <div><span style="display: inline-block; width: 12px; height: 12px; background: #ef4444; border-radius: 2px; margin-right: 5px;"></span>Out of Order</div>
            <div><span style="display: inline-block; width: 12px; height: 12px; background: #6b7280; border-radius: 2px; margin-right: 5px;"></span>Occupied</div>
          </div>
        </div>

        <div class="card">
          <h3>🔧 Room Details</h3>
          <div id="room-details">
            <p>Select a room to view details</p>
          </div>
        </div>

        <div class="card">
          <h3>📝 Housekeeping Tasks</h3>
          <table class="table">
            <thead>
              <tr>
                <th>Room</th>
                <th>Task</th>
                <th>Status</th>
                <th>Staff</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>101</td>
                <td>Deep Cleaning</td>
                <td><span class="status completed">Complete</span></td>
                <td>Maria Garcia</td>
              </tr>
              <tr>
                <td>205</td>
                <td>Maintenance Check</td>
                <td><span class="status progress">In Progress</span></td>
                <td>Sarah Wilson</td>
              </tr>
              <tr>
                <td>301</td>
                <td>AC Repair</td>
                <td><span class="status urgent">Urgent</span></td>
                <td>Mike Johnson</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Inventory Tab -->
    <div id="inventory-tab" class="hidden">
      <div class="grid">
        <div class="card">
          <h3>📦 Inventory Overview</h3>
          <img src="https://images.unsplash.com/photo-1683115097173-f24516d000c6" alt="Maintenance tools" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 15px;">
          <table class="table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Last Updated</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>AC Filters</td>
                <td>25</td>
                <td><span class="status completed">Good</span></td>
                <td>Today</td>
              </tr>
              <tr>
                <td>Light Bulbs (LED)</td>
                <td>8</td>
                <td><span class="status pending">Low</span></td>
                <td>Yesterday</td>
              </tr>
              <tr>
                <td>Plumbing Parts</td>
                <td>15</td>
                <td><span class="status completed">Good</span></td>
                <td>2 days ago</td>
              </tr>
              <tr>
                <td>Cleaning Supplies</td>
                <td>3</td>
                <td><span class="status urgent">Critical</span></td>
                <td>Today</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="card">
          <h3>➕ Add Inventory Item</h3>
          <form class="form" onsubmit="addInventory(event)">
            <input type="text" placeholder="Item Name" required>
            <input type="number" placeholder="Quantity" required>
            <select required>
              <option value="">Category</option>
              <option value="plumbing">Plumbing</option>
              <option value="electrical">Electrical</option>
              <option value="hvac">HVAC</option>
              <option value="cleaning">Cleaning</option>
              <option value="tools">Tools</option>
            </select>
            <input type="text" placeholder="Supplier">
            <input type="number" placeholder="Unit Cost ($)" step="0.01">
            <button type="submit">Add Item</button>
          </form>
        </div>

        <div class="card">
          <h3>📊 Usage Analytics</h3>
          <div style="margin-top: 15px;">
            <div style="margin-bottom: 10px;">
              <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 5px;">
                <span>AC Filters</span>
                <span>68%</span>
              </div>
              <div style="background: #f3f4f6; height: 8px; border-radius: 4px;">
                <div style="background: #4f46e5; height: 100%; width: 68%; border-radius: 4px;"></div>
              </div>
            </div>
            <div style="margin-bottom: 10px;">
              <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 5px;">
                <span>Light Bulbs</span>
                <span>23%</span>
              </div>
              <div style="background: #f3f4f6; height: 8px; border-radius: 4px;">
                <div style="background: #f59e0b; height: 100%; width: 23%; border-radius: 4px;"></div>
              </div>
            </div>
            <div style="margin-bottom: 10px;">
              <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 5px;">
                <span>Cleaning Supplies</span>
                <span>12%</span>
              </div>
              <div style="background: #f3f4f6; height: 8px; border-radius: 4px;">
                <div style="background: #ef4444; height: 100%; width: 12%; border-radius: 4px;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Technicians Tab -->
    <div id="technicians-tab" class="hidden">
      <div class="grid">
        <div class="card">
          <h3>👷 Technician Roster</h3>
          <div style="display: grid; gap: 15px; margin-top: 15px;">
            <div style="display: flex; align-items: center; padding: 15px; background: #f8fafc; border-radius: 8px; gap: 15px;">
              <div style="width: 50px; height: 50px; background: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">MJ</div>
              <div style="flex: 1;">
                <div style="font-weight: 600;">Mike Johnson</div>
                <div style="font-size: 14px; color: #6b7280;">HVAC Specialist</div>
                <div style="margin-top: 5px;">
                  <span class="badge">Plumbing</span>
                  <span class="badge">HVAC</span>
                  <span class="badge">Electrical</span>
                </div>
              </div>
              <div style="text-align: right;">
                <div style="font-weight: 600; color: #059669;">Active</div>
                <div style="font-size: 14px; color: #6b7280;">2 tasks</div>
              </div>
            </div>

            <div style="display: flex; align-items: center; padding: 15px; background: #f8fafc; border-radius: 8px; gap: 15px;">
              <div style="width: 50px; height: 50px; background: #ec4899; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">SW</div>
              <div style="flex: 1;">
                <div style="font-weight: 600;">Sarah Wilson</div>
                <div style="font-size: 14px; color: #6b7280;">General Maintenance</div>
                <div style="margin-top: 5px;">
                  <span class="badge">Plumbing</span>
                  <span class="badge">General</span>
                </div>
              </div>
              <div style="text-align: right;">
                <div style="font-weight: 600; color: #059669;">Active</div>
                <div style="font-size: 14px; color: #6b7280;">3 tasks</div>
              </div>
            </div>

            <div style="display: flex; align-items: center; padding: 15px; background: #f8fafc; border-radius: 8px; gap: 15px;">
              <div style="width: 50px; height: 50px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">DC</div>
              <div style="flex: 1;">
                <div style="font-weight: 600;">David Chen</div>
                <div style="font-size: 14px; color: #6b7280;">Electrical Technician</div>
                <div style="margin-top: 5px;">
                  <span class="badge">Electrical</span>
                  <span class="badge">Electronics</span>
                </div>
              </div>
              <div style="text-align: right;">
                <div style="font-weight: 600; color: #f59e0b;">On Break</div>
                <div style="font-size: 14px; color: #6b7280;">1 task</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <h3>📋 Performance Metrics</h3>
          <img src="https://images.unsplash.com/photo-1583955746149-71a61419d759" alt="Maintenance wrench" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; margin-bottom: 15px;">
          <table class="table">
            <thead>
              <tr>
                <th>Technician</th>
                <th>Tasks Done</th>
                <th>Avg Time</th>
                <th>Rating</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Mike Johnson</td>
                <td>23</td>
                <td>2.1h</td>
                <td>⭐⭐⭐⭐⭐</td>
              </tr>
              <tr>
                <td>Sarah Wilson</td>
                <td>19</td>
                <td>1.8h</td>
                <td>⭐⭐⭐⭐⭐</td>
              </tr>
              <tr>
                <td>David Chen</td>
                <td>15</td>
                <td>2.5h</td>
                <td>⭐⭐⭐⭐</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="card">
          <h3>🆕 Add Technician</h3>
          <form class="form" onsubmit="addTechnician(event)">
            <input type="text" placeholder="Full Name" required>
            <input type="email" placeholder="Email" required>
            <input type="tel" placeholder="Phone Number" required>
            <select required>
              <option value="">Specialization</option>
              <option value="hvac">HVAC</option>
              <option value="plumbing">Plumbing</option>
              <option value="electrical">Electrical</option>
              <option value="general">General Maintenance</option>
              <option value="housekeeping">Housekeeping</option>
            </select>
            <select required>
              <option value="">Shift</option>
              <option value="morning">Morning (6AM-2PM)</option>
              <option value="afternoon">Afternoon (2PM-10PM)</option>
              <option value="night">Night (10PM-6AM)</option>
            </select>
            <button type="submit">Add Technician</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
  <style>
    .room-grid {
      display: contents;
    }
    .room {
      aspect-ratio: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s;
    }
    .room:hover {
      transform: scale(1.05);
    }
    .room.available {
      background: #d1fae5;
      color: #065f46;
      border: 2px solid #10b981;
    }
    .room.maintenance {
      background: #fef3c7;
      color: #92400e;
      border: 2px solid #f59e0b;
    }
    .room.ooo {
      background: #fee2e2;
      color: #991b1b;
      border: 2px solid #ef4444;
    }
    .room.occupied {
      background: #f3f4f6;
      color: #374151;
      border: 2px solid #6b7280;
    }
  </style>

  <script>
    function showTab(tabName) {
      // Hide all tabs
      document.querySelectorAll('[id$="-tab"]').forEach(tab => {
        tab.classList.add('hidden');
      });
      
      // Show selected tab
      document.getElementById(tabName + '-tab').classList.remove('hidden');
      
      // Update nav buttons
      document.querySelectorAll('.nav button').forEach(btn => {
        btn.classList.remove('active');
      });
      document.getElementById('tab-' + tabName).classList.add('active');
    }

    function selectRoom(roomNumber) {
      const details = document.getElementById('room-details');
      const roomData = {
        101: { status: 'Available', lastCleaned: '2 hours ago', nextGuest: 'Check-in at 3PM' },
        102: { status: 'Available', lastCleaned: '1 hour ago', nextGuest: 'No reservation' },
        103: { status: 'Under Maintenance', issue: 'AC repair needed', technician: 'Mike Johnson' },
        104: { status: 'Available', lastCleaned: '30 minutes ago', nextGuest: 'Check-in at 5PM' },
        105: { status: 'Occupied', guest: 'John Smith', checkOut: 'Tomorrow 11AM' },
        201: { status: 'Available', lastCleaned: '3 hours ago', nextGuest: 'Check-in at 4PM' },
        202: { status: 'Under Maintenance', issue: 'Plumbing repair', technician: 'Sarah Wilson' },
        203: { status: 'Available', lastCleaned: '1 hour ago', nextGuest: 'No reservation' },
        204: { status: 'Out of Order', issue: 'Water damage', estimated: '3 days repair' },
        205: { status: 'Available', lastCleaned: '2 hours ago', nextGuest: 'Check-in at 6PM' },
        301: { status: 'Under Maintenance', issue: 'HVAC system', technician: 'Mike Johnson' },
        302: { status: 'Under Maintenance', issue: 'Electrical work', technician: 'David Chen' }
      };

      const room = roomData[roomNumber];
      if (room) {
        let html = `<h4>Room ${roomNumber}</h4><div style="margin-top: 10px;">`;
        html += `<p><strong>Status:</strong> ${room.status}</p>`;
        if (room.issue) html += `<p><strong>Issue:</strong> ${room.issue}</p>`;
        if (room.technician) html += `<p><strong>Technician:</strong> ${room.technician}</p>`;
        if (room.lastCleaned) html += `<p><strong>Last Cleaned:</strong> ${room.lastCleaned}</p>`;
        if (room.nextGuest) html += `<p><strong>Next Guest:</strong> ${room.nextGuest}</p>`;
        if (room.guest) html += `<p><strong>Current Guest:</strong> ${room.guest}</p>`;
        if (room.checkOut) html += `<p><strong>Check Out:</strong> ${room.checkOut}</p>`;
        if (room.estimated) html += `<p><strong>Estimated Repair:</strong> ${room.estimated}</p>`;
        html += '</div>';
        details.innerHTML = html;
      }
    }

    function addAssignment(event) {
      event.preventDefault();
      alert('Assignment created successfully!');
      event.target.reset();
    }

    function addInventory(event) {
      event.preventDefault();
      alert('Inventory item added successfully!');
      event.target.reset();
    }

    function addTechnician(event) {
      event.preventDefault();
      alert('Technician added successfully!');
      event.target.reset();
    }

    // Initialize dashboard on load
    showTab('dashboard');
  </script>
  <footer class="footer">
  <p>© 2025 Hotel Maintenance and Engineering | All Rights Reserved</p>
</footer>

</body>
</html>
 