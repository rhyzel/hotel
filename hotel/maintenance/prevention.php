<!DOCTYPE  html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prevention Maintenance Scheduler</title>
  <meta name="description" content="Built with jdoodle.ai - Comprehensive preventive maintenance scheduling system for hotel housekeeping, room management, and inventory control">
  <meta property="og:title" content="Preventive Maintenance Scheduler">
  <meta property="og:description" content="Built with jdoodle.ai - Comprehensive preventive maintenance scheduling system for hotel housekeeping, room management, and inventory control">
  <meta property="og:image" content="https://imagedelivery.net/None/6bbaef43jd5acj4ffaj95a1jc596ecf62252/public">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Preventive Maintenance Scheduler">
  <meta name="twitter:description" content="Built with jdoodle.ai - Comprehensive preventive maintenance scheduling system for hotel housekeeping, room management, and inventory control">
  <meta name="twitter:image" content="https://imagedelivery.net/None/6bbaef43jd5acj4ffaj95a1jc596ecf62252/public">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><rect x='3' y='4' width='18' height='18' rx='2' ry='2'/><line x1='16' y1='2' x2='16' y2='6'/><line x1='8' y1='2' x2='8' y2='6'/><line x1='3' y1='10' x2='21' y2='10'/></svg>">
  <script src="https://cdn.tailwindcss.com"></script>
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
    .card {
                border: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.25);
      padding: 24px;
      margin-bottom: 20px;
    }
    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 8px;
    }
    .calendar-day {
      aspect-ratio: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      padding: 8px;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      background: white;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .calendar-day:hover {
      background: #f3f4f6;
      border-color: #6366f1;
    }
    .calendar-day.today {
      background: #eef2ff;
      border-color: #6366f1;
      box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
    }
    .calendar-day.has-maintenance {
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      border-color: #f59e0b;
    }
    .calendar-day.overdue {
      background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
      border-color: #ef4444;
    }
    .maintenance-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      margin-top: 2px;
    }
    .priority-high { background: #ef4444; }
    .priority-medium { background: #f59e0b; }
    .priority-low { background: #10b981; }
    .status-badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
    }
    .status-scheduled { background: #dbeafe; color: #1e40af; }
    .status-in-progress { background: #fef3c7; color: #92400e; }
    .status-completed { background: #d1fae5; color: #065f46; }
    .status-overdue { background: #fee2e2; color: #991b1b; }
    .modal {
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(8px);
    }
    .tab-button {
      padding: 8px 16px;
      border-radius: 6px;
      font-weight: 500;
      transition: all 0.2s ease;
    }
    .tab-button.active {
      background: #4f46e5;
      color: white;
    }
    .tab-button:not(.active) {
      background: #f3f4f6;
      color: #374151;
    }
    .maintenance-item {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 16px;
      margin-bottom: 12px;
      transition: all 0.2s ease;
    }
    .maintenance-item:hover {
      background: #f9fafb;
      border-color: #d1d5db;
    }
    @media (max-width: 768px) {
      .calendar-grid {
        gap: 4px;
      }
      .calendar-day {
        padding: 4px;
        font-size: 0.875rem;
      }
      .card {
        padding: 16px;
      }
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

  display: flex;
  justify-content: center; /* centers horizontally */
  align-items: center;     /* centers vertically */
  text-align: center;
}
  </style>
</head>
   <div class="overlay">
    <div class="container">
    <!-- Header -->
    <div class="card mb-6">
      <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-4">

          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              <a href="maintenance.php" style="text-decoration: text align: center  none; color: white; ">
            PREVENTION MAINTENANCE REQUEST</h1>
</a>
            <p class="text-3x1 text-white">Schedule and track maintenance for hotel operations</p>
          </div>
        </div>
        
        <div class="flex gap-3">
          <button onclick="openScheduleModal()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Schedule Task
          </button>
          <button onclick="generateReport()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path d="M9 17h6l3 3v-3h2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2h2v3l3-3z"></path>
            </svg>
            Report
          </button>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Calendar -->
      <div class="lg:col-span-2">
        <div class="card">
          <div class="flex justify-between items-center mb-6 text-white">
            <h2 class="text-xl font-semibold flex items-center gap-2">
              <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <span id="calendarMonth">January 2024</span>
            </h2>
            <div class="flex gap-2">
              <button onclick="changeMonth(-1)" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <polyline points="15,18 9,12 15,6"></polyline>
                </svg>
              </button>
              <button onclick="changeMonth(1)" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <polyline points="9,18 15,12 9,6"></polyline>
                </svg>
              </button>
            </div>
          </div>
          
          <!-- Calendar Header -->
          <div class="calendar-grid mb-2 text-center font-medium text-white text-sm">
            <div>Sun</div>
            <div>Mon</div>
            <div>Tue</div>
            <div>Wed</div>
            <div>Thu</div>
            <div>Fri</div>
            <div>Sat</div>
          </div>
          
          <!-- Calendar Days -->
          <div id="calendarGrid" class="calendar-grid">
            <!-- Days populated by JavaScript -->
          </div>
          
          <!-- Legend -->
          <div class="mt-4 flex flex-wrap gap-4 text-white text-sm">
            <div class="flex items-center gap-2">
              <div class="w-4 h-4 bg-yellow-200 border border-yellow-400 rounded"></div>
              <span>Scheduled</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-4 h-4 bg-red-200 border border-red-400 rounded"></div>
              <span>Overdue</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 bg-green-500 rounded-full"></div>
              <span>Low Priority</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
              <span>Medium Priority</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 bg-red-500 rounded-full"></div>
              <span>High Priority</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Quick Stats -->
        <div class="card">
          <h3 class="text-lg font-semibold mb-4 text-white">Quick Stats</h3>
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-white">Today</span>
              <span class="font-semibold text-white" id="todayCount">0</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-white">This Week</span>
              <span class="font-semibold text-white" id="weekCount">0</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-white">Overdue</span>
              <span class="font-semibold text-white" id="overdueCount">0</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-white">Completed</span>
              <span class="font-semibold text-white" id="completedCount">0</span>
            </div>
          </div>
        </div>

        <!-- Filter -->
        <div class="card">
          <h3 class="text-lg font-semibold mb-4 text-white">Filter</h3>
          <div class="space-y-3">
            <div class="flex gap-2">
              <button onclick="setFilter('all')" class="tab-button active" id="filter-all">All</button>
              <button onclick="setFilter('housekeeping')" class="tab-button" id="filter-housekeeping">Housekeeping</button>
            </div>
            <div class="flex gap-2">
              <button onclick="setFilter('room')" class="tab-button" id="filter-room">Rooms</button>
              <button onclick="setFilter('inventory')" class="tab-button" id="filter-inventory">Inventory</button>
            </div>
          </div>
          
          <div class="mt-4 space-y-2">
            <label class="block text-sm font-medium text-white">Priority</label>
            <select id="priorityFilter" onchange="applyFilters()" class="w-full px-3 py-2 border rounded-lg">
              <option value="">All Priorities</option>
              <option value="high">High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>
            
            <label class="block text-sm font-medium mt-3 text-white">Status</label>
            <select id="statusFilter" onchange="applyFilters()" class="w-full px-3 py-2 border rounded-lg">
              <option value="">All Status</option>
              <option value="scheduled">Scheduled</option>
              <option value="in-progress">In Progress</option>
              <option value="completed">Completed</option>
              <option value="overdue">Overdue</option>
            </select>
          </div>
        </div>

        <!-- Upcoming Tasks -->
        <div class="card">
          <h3 class="text-lg font-semibold mb-4 text-white">Upcoming Tasks</h3>
          <div id="upcomingTasks" class="space-y-3 max-h-64 overflow-y-auto">
            <!-- Populated by JavaScript -->
          </div>
        </div>
      </div>
    </div>

    <!-- Task List -->
    <div class="card mt-6">
      <div class="flex justify-between items-center mb-4 text-white">
        <h2 class="text-xl font-semibold">Maintenance Tasks</h2>
        <input type="text" id="searchTasks" placeholder="Search tasks..." class="px-3 py-2 border rounded-lg w-64">
      </div>
      <div id="tasksList" class="space-y-3">
        <!-- Populated  by JavaScript -->
      </div>
    </div>

    <!-- Schedule Modal -->
    <div id="scheduleModal" class="fixed inset-0 modal hidden flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">Schedule Maintenance Task</h3>
        
        <form id="scheduleForm" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-2">Task Name</label>
              <input type="text" name="name" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-2">Category</label>
              <select name="category" class="w-full px-3 py-2 border rounded-lg" required>
                <option value="housekeeping">Housekeeping</option>
                <option value="room">Room Management</option>
                <option value="inventory">Inventory</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-2">Equipment/Asset</label>
              <input type="text" name="equipment" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-2">Location</label>
              <input type="text" name="location" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-2">Scheduled Date</label>
              <input type="date" name="date" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-2">Time</label>
              <input type="time" name="time" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-2">Priority</label>
              <select name="priority" class="w-full px-3 py-2 border rounded-lg" required>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-2 text-white">Frequency</label>
              <select name="frequency" class="w-full px-3 py-2 border rounded-lg">
                <option value="once">One-time</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="annually">Annually</option>
              </select>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-2">Assigned To</label>
            <input type="text" name="assignedTo" class="w-full px-3 py-2 border rounded-lg" required>
          </div>
          
          <div class="flex justify-end gap-3 pt-4">
            <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
              Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
              Schedule Task
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Task Details Modal -->
    <div id="taskModal" class="fixed inset-0 modal hidden flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4" id="taskModalTitle">Task Details</h3>
        <div id="taskModalContent">
          <!-- Populated by JavaScript -->
        </div>
        <div class="flex justify-end gap-3 pt-4">
          <button onclick="closeTaskModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</div> 
  <script>
    // Sample data
    let maintenanceTasks = [
      {
        id: 1,
        name: 'HVAC Filter Replacement',
        category: 'room',
        equipment: 'Central HVAC Unit',
        location: 'Floor 3',
        date: '2024-01-15',
        time: '09:00',
        priority: 'high',
        frequency: 'monthly',
        status: 'scheduled',
        description: 'Replace air filters in main HVAC system',
        assignedTo: 'Mike Johnson'
      },
      {
        id: 2,
        name: 'Vacuum Cleaner Maintenance',
        category: 'housekeeping',
        equipment: 'Industrial Vacuum',
        location: 'Housekeeping Storage',
        date: '2024-01-18',
        time: '14:00',
        priority: 'medium',
        frequency: 'weekly',
        status: 'scheduled',
        description: 'Check and clean vacuum filters',
        assignedTo: 'Sarah Davis'
      },
      {
        id: 3,
        name: 'Kitchen Equipment Check',
        category: 'inventory',
        equipment: 'Industrial Dishwasher',
        location: 'Main Kitchen',
        date: '2024-01-10',
        time: '10:30',
        priority: 'high',
        frequency: 'daily',
        status: 'overdue',
        description: 'Daily operational check',
        assignedTo: 'Tom Wilson'
      }
    ];

    let currentDate = new Date();
    let currentFilter = 'all';

    function initCalendar() {
      renderCalendar();
      updateStats();
      renderUpcomingTasks();
      renderTasksList();
    }

    function renderCalendar() {
      const year = currentDate.getFullYear();
      const month = currentDate.getMonth();
      
      document.getElementById('calendarMonth').textContent = 
        new Date(year, month).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

      const firstDay = new Date(year, month, 1).getDay();
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      const today = new Date();
      
      const grid = document.getElementById('calendarGrid');
      grid.innerHTML = '';

      // Empty cells for days before month starts
      for (let i = 0; i < firstDay; i++) {
        const emptyDay = document.createElement('div');
        emptyDay.className = 'calendar-day opacity-30';
        grid.appendChild(emptyDay);
      }

      // Days of the month
      for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        const dayDate = new Date(year, month, day);
        const dateStr = dayDate.toISOString().split('T')[0];
        
        dayElement.className = 'calendar-day';
        dayElement.innerHTML = `<span class="font-medium">${day}</span>`;
        
        // Check if today
        if (dayDate.toDateString() === today.toDateString()) {
          dayElement.classList.add('today');
        }

        // Check for scheduled maintenance
        const dayTasks = maintenanceTasks.filter(task => task.date === dateStr);
        if (dayTasks.length > 0) {
          dayElement.classList.add('has-maintenance');
          
          // Add priority dots
          dayTasks.forEach(task => {
            const dot = document.createElement('div');
            dot.className = `maintenance-dot priority-${task.priority}`;
            dayElement.appendChild(dot);
          });

          // Check for overdue tasks
          if (dayTasks.some(task => task.status === 'overdue')) {
            dayElement.classList.add('overdue');
          }
        }

        dayElement.onclick = () => showDayTasks(dateStr);
        grid.appendChild(dayElement);
      }
    }

    function showDayTasks(date) {
      const tasks = maintenanceTasks.filter(task => task.date === date);
      if (tasks.length === 0) return;

      const content = tasks.map(task => `
        <div class="maintenance-item" onclick="showTaskDetails(${task.id})">
          <div class="flex justify-between items-start mb-2">
            <h4 class="font-medium">${task.name}</h4>
            <span class="status-badge status-${task.status}">${task.status}</span>
          </div>
          <p class="text-sm text-gray-600">${task.time} - ${task.equipment}</p>
          <p class="text-sm text-gray-500">${task.location}</p>
        </div>
      `).join('');

      document.getElementById('taskModalTitle').textContent = `Tasks for ${new Date(date).toLocaleDateString()}`;
      document.getElementById('taskModalContent').innerHTML = content;
      document.getElementById('taskModal').classList.remove('hidden');
    }

    function showTaskDetails(taskId) {
      const task = maintenanceTasks.find(t => t.id === taskId);
      if (!task) return;

      const content = `
        <div class="space-y-3">
          <div>
            <span class="font-medium">Equipment:</span> ${task.equipment}
          </div>
          <div>
            <span class="font-medium">Location:</span> ${task.location}
          </div>
          <div>
            <span class="font-medium">Scheduled:</span> ${new Date(task.date + ' ' + task.time).toLocaleString()}
          </div>
          <div>
            <span class="font-medium">Priority:</span> 
            <span class="inline-block w-3 h-3 rounded-full priority-${task.priority} ml-2"></span>
            ${task.priority.charAt(0).toUpperCase() + task.priority.slice(1)}
          </div>
          <div>
            <span class="font-medium">Assigned to:</span> ${task.assignedTo}
          </div>
          <div>
            <span class="font-medium">Description:</span> ${task.description || 'No description'}
          </div>
          <div class="mt-4 flex gap-2">
            <button onclick="updateTaskStatus(${task.id}, 'in-progress')" class="px-3 py-1 bg-yellow-500 text-white text-sm rounded">
              Start
            </button>
            <button onclick="updateTaskStatus(${task.id}, 'completed')" class="px-3 py-1 bg-green-500 text-white text-sm rounded">
              Complete
            </button>
          </div>
        </div>
      `;

      document.getElementById('taskModalTitle').textContent = task.name;
      document.getElementById('taskModalContent').innerHTML = content;
      document.getElementById('taskModal').classList.remove('hidden');
    }

    function updateTaskStatus(taskId, status) {
      const task = maintenanceTasks.find(t => t.id === taskId);
      if (task) {
        task.status = status;
        if (status === 'completed') {
          task.completedDate = new Date().toISOString().split('T')[0];
        }
        initCalendar();
        closeTaskModal();
      }
    }

    function renderUpcomingTasks() {
      const today = new Date();
      const upcoming = maintenanceTasks
        .filter(task => {
          const taskDate = new Date(task.date);
          return taskDate >= today && task.status !== 'completed';
        })
        .sort((a, b) => new Date(a.date) - new Date(b.date))
        .slice(0, 5);

      const container = document.getElementById('upcomingTasks');
      container.innerHTML = upcoming.map(task => `
        <div class="p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100" onclick="showTaskDetails(${task.id})">
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <h4 class="font-medium text-sm">${task.name}</h4>
              <p class="text-xs text-gray-600">${task.equipment}</p>
              <p class="text-xs text-gray-500">${new Date(task.date).toLocaleDateString()}</p>
            </div>
            <div class="w-3 h-3 rounded-full priority-${task.priority}"></div>
          </div>
        </div>
      `).join('');
    }

    function renderTasksList() {
      const filtered = getFilteredTasks();
      const container = document.getElementById('tasksList');
      
      container.innerHTML = filtered.map(task => `
        <div class="maintenance-item cursor-pointer" onclick="showTaskDetails(${task.id})">
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <h3 class="font-medium">${task.name}</h3>
                <span class="status-badge status-${task.status}">${task.status}</span>
                <div class="w-3 h-3 rounded-full priority-${task.priority}"></div>
              </div>
              <div class="text-sm text-gray-600 space-y-1">
                <div><strong>Equipment:</strong> ${task.equipment}</div>
                <div><strong>Location:</strong> ${task.location}</div>
                <div><strong>Scheduled:</strong> ${new Date(task.date + ' ' + task.time).toLocaleString()}</div>
                <div><strong>Assigned to:</strong> ${task.assignedTo}</div>
              </div>
            </div>
          </div>
        </div>
      `).join('');
    }

    function getFilteredTasks() {
      let filtered = maintenanceTasks;
      
      if (currentFilter !== 'all') {
        filtered = filtered.filter(task => task.category === currentFilter);
      }
      
      const priorityFilter = document.getElementById('priorityFilter')?.value;
      if (priorityFilter) {
        filtered = filtered.filter(task => task.priority === priorityFilter);
      }
      
      const statusFilter = document.getElementById('statusFilter')?.value;
      if (statusFilter) {
        filtered = filtered.filter(task => task.status === statusFilter);
      }
      
      const searchTerm = document.getElementById('searchTasks')?.value.toLowerCase();
      if (searchTerm) {
        filtered = filtered.filter(task => 
          task.name.toLowerCase().includes(searchTerm) ||
          task.equipment.toLowerCase().includes(searchTerm) ||
          task.location.toLowerCase().includes(searchTerm)
        );
      }
      
      return filtered;
    }

    function updateStats() {
      const today = new Date().toISOString().split('T')[0];
      const startOfWeek = new Date();
      startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay());
      const endOfWeek = new Date(startOfWeek);
      endOfWeek.setDate(endOfWeek.getDate() + 6);

      const todayTasks = maintenanceTasks.filter(task => task.date === today).length;
      const weekTasks = maintenanceTasks.filter(task => {
        const taskDate = new Date(task.date);
        return taskDate >= startOfWeek && taskDate <= endOfWeek;
      }).length;
      const overdueTasks = maintenanceTasks.filter(task => task.status === 'overdue').length;
      const completedTasks = maintenanceTasks.filter(task => task.status === 'completed').length;

      document.getElementById('todayCount').textContent = todayTasks;
      document.getElementById('weekCount').textContent = weekTasks;
      document.getElementById('overdueCount').textContent = overdueTasks;
      document.getElementById('completedCount').textContent = completedTasks;
    }

    function setFilter(filter) {
      currentFilter = filter;
      
      document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
      document.getElementById(`filter-${filter}`).classList.add('active');
      
      renderTasksList();
    }

    function applyFilters() {
      renderTasksList();
    }

    function changeMonth(direction) {
      currentDate.setMonth(currentDate.getMonth() + direction);
      renderCalendar();
    }

    function openScheduleModal() {
      document.getElementById('scheduleModal').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('scheduleModal').classList.add('hidden');
    }

    function closeTaskModal() {
      document.getElementById('taskModal').classList.add('hidden');
    }

    function generateReport() {
      const stats = {
        total: maintenanceTasks.length,
        completed: maintenanceTasks.filter(t => t.status === 'completed').length,
        overdue: maintenanceTasks.filter(t => t.status === 'overdue').length,
        scheduled: maintenanceTasks.filter(t => t.status === 'scheduled').length
      };
      
      alert(`Maintenance Report:
Total Tasks: ${stats.total}
Completed: ${stats.completed}
Overdue: ${stats.overdue}
Scheduled: ${stats.scheduled}`);
    }

    // Event listeners
    document.getElementById('scheduleForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      
      const newTask = {
        id: Date.now(),
        name: formData.get('name'),
        category: formData.get('category'),
        equipment: formData.get('equipment'),
        location: formData.get('location'),
        date: formData.get('date'),
        time: formData.get('time'),
        priority: formData.get('priority'),
        frequency: formData.get('frequency'),
        status: 'scheduled',
        description: formData.get('description'),
        assignedTo: formData.get('assignedTo')
      };
      
      maintenanceTasks.push(newTask);
      initCalendar();
      closeModal();
      e.target.reset();
    });

    document.getElementById('searchTasks')?.addEventListener('input', renderTasksList);

    // Close modals on outside click
    document.getElementById('scheduleModal').addEventListener('click', function(e) {
      if (e.target === this) closeModal();
    });

    document.getElementById('taskModal').addEventListener('click', function(e) {
      if (e.target === this) closeTaskModal();
    });

    // Initialize
    initCalendar();
  </script>
   <footer class="footer">
  <p>© 2025 Hotel Maintenance and Engineering | All Rights Reserved</p>
</footer>
</body>
</html>