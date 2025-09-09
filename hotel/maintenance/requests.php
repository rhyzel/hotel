<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Request System</title>
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

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { transform: translateX(-100%) translateY(-100%) rotate(0deg); }
            50% { transform: translateX(-10%) translateY(-10%) rotate(45deg); }
        }

        .header h1 {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .nav-tabs {
            display: flex;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .nav-tab {
            flex: 1;
            padding: 20px;
            background: none;
            border: none;
            font-size: 1.1em;
            font-weight: 600;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-tab::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.3s ease;
        }

        .nav-tab:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #495057;
        }

        .nav-tab.active {
            color: #667eea;
            background: white;
        }

        .nav-tab.active::before {
            width: 100%;
        }

        .tab-content {
            display: none;
            padding: 40px;
            animation: fadeIn 0.5s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .form-group {
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: white;
            font-size: 0.95em;
            
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: #fff;
            
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .priority-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .priority-btn {
            padding: 12px 24px;
            border: 2px solid #e9ecef;
            background: white;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            flex: 1;
            min-width: 120px;
        }

        .priority-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .priority-btn.low {
            border-color: #28a745;
            color: #28a745;
        }

        .priority-btn.low.active,
        .priority-btn.low:hover {
            background: #28a745;
            color: white;
        }

        .priority-btn.medium {
            border-color: #ffc107;
            color: #ffc107;
        }

        .priority-btn.medium.active,
        .priority-btn.medium:hover {
            background: #ffc107;
            color: white;
        }

        .priority-btn.high {
            border-color: #dc3545;
            color: #dc3545;
        }

        .priority-btn.high.active,
        .priority-btn.high:hover {
            background: #dc3545;
            color: white;
        }

        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.2em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .requests-list {
            display: grid;
            gap: 20px;
        }

        .request-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .request-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(50%, -50%);
        }

        .request-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.12);
        }

        .request-card.priority-low {
            border-left-color: #28a745;
        }

        .request-card.priority-medium {
            border-left-color: #ffc107;
        }

        .request-card.priority-high {
            border-left-color: #dc3545;
        }

        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .request-title {
            font-size: 1.3em;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .request-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9em;
            color: #6c757d;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-in-progress {
            background: #cce5ff;
            color: #0066cc;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .priority-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-low-badge {
            background: #d4edda;
            color: #155724;
        }

        .priority-medium-badge {
            background: #fff3cd;
            color: #856404;
        }

        .priority-high-badge {
            background: #f8d7da;
            color: #721c24;
        }

        .request-description {
            color: #495057;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .request-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 0.9em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-btn.update {
            background: #007bff;
            color: white;
        }

        .action-btn.delete {
            background: #dc3545;
            color: white;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.2);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.1) 0%, transparent 50%);
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }

        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            z-index: 1;
        }

        .empty-state {
            text-align: center;
            padding: 60px;
            color: #6c757d;
        }

        .empty-state h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 2em;
            }

            .tab-content {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .request-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
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
<body>
      <div class="overlay">
    <div class="container">
        <div class="header">
       <h1>
        <a href="maintenance.php" style="text-decoration: none; color: white;">
           MAINTENANCE HUB
        </a>
    </h1>
            <p>Streamlined facility maintenance request management</p>
        </div>

        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showTab('new-request')">New Request</button>
            <button class="nav-tab" onclick="showTab('view-requests')">View Requests</button>
        </div>

        <div id="new-request" class="tab-content active">
            <form id="maintenanceForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="requesterName">Requester Name *</label>
                        <input type="text" id="requesterName" name="requesterName" required>
                    </div>

                    <div class="form-group">
                        <label for="requesterEmail">Email Address *</label>
                        <input type="email" id="requesterEmail" name="requesterEmail" required>
                    </div>

                    <div class="form-group">
                        <label for="department">Department</label>
                        <select id="department" name="department">
                            <option value="">Select Department</option>
                            <option value="IT">Information Technology</option>
                            <option value="HR">Human Resources</option>
                            <option value="Finance">Finance</option>
                            <option value="Operations">Operations</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Facilities">Facilities</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Location *</label>
                        <input type="text" id="location" name="location" placeholder="Building, Floor, Room Number" required>
                    </div>

                    <div class="form-group">
                        <label for="category">Issue Category *</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Electrical">Electrical</option>
                            <option value="Plumbing">Plumbing</option>
                            <option value="HVAC">HVAC (Heating/Cooling)</option>
                            <option value="Cleaning">Cleaning</option>
                            <option value="Security">Security</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Structural">Structural</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="requestedDate">Requested Completion Date</label>
                        <input type="date" id="requestedDate" name="requestedDate">
                    </div>

                    <div class="form-group full-width">
                        <label for="issueTitle">Issue Title *</label>
                        <input type="text" id="issueTitle" name="issueTitle" placeholder="Brief description of the issue" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="description">Detailed Description *</label>
                        <textarea id="description" name="description" placeholder="Please provide a detailed description of the maintenance issue, including any relevant details that might help our team address it quickly and effectively." required></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label>Priority Level *</label>
                        <div class="priority-buttons">
                            <button type="button" class="priority-btn low" onclick="setPriority('low')">Low</button>
                            <button type="button" class="priority-btn medium" onclick="setPriority('medium')">Medium</button>
                            <button type="button" class="priority-btn high" onclick="setPriority('high')">High</button>
                        </div>
                        <input type="hidden" id="priority" name="priority" required>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Submit Maintenance Request</button>
            </form>
        </div>

        <div id="view-requests" class="tab-content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="totalRequests">0</div>
                    <div class="stat-label">Total Requests</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="pendingRequests">0</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="inProgressRequests">0</div>
                    <div class="stat-label">In Progress</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="completedRequests">0</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>

            <div id="requestsList" class="requests-list">
                <div class="empty-state">
                    <h3>No Maintenance Requests Yet</h3>
                    <p>Submit your first maintenance request to get started!</p>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        let requests = [];
        let currentPriority = '';

        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all nav tabs
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked nav tab
            event.target.classList.add('active');

            // Refresh requests display when viewing requests tab
            if (tabName === 'view-requests') {
                displayRequests();
                updateStats();
            }
        }

        function setPriority(priority) {
            currentPriority = priority;
            document.getElementById('priority').value = priority;
            
            // Remove active class from all priority buttons
            document.querySelectorAll('.priority-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to selected button
            document.querySelector(`.priority-btn.${priority}`).classList.add('active');
        }

        function generateRequestId() {
            return 'REQ-' + Date.now().toString(36).toUpperCase();
        }

        document.getElementById('maintenanceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentPriority) {
                alert('Please select a priority level');
                return;
            }

            const formData = new FormData(this);
            const request = {
                id: generateRequestId(),
                requesterName: formData.get('requesterName'),
                requesterEmail: formData.get('requesterEmail'),
                department: formData.get('department') || 'Not specified',
                location: formData.get('location'),
                category: formData.get('category'),
                issueTitle: formData.get('issueTitle'),
                description: formData.get('description'),
                priority: currentPriority,
                requestedDate: formData.get('requestedDate') || 'No specific date',
                submittedDate: new Date().toLocaleDateString(),
                status: 'pending'
            };

            requests.push(request);
            
            // Reset form
            this.reset();
            currentPriority = '';
            document.querySelectorAll('.priority-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            alert('Maintenance request submitted successfully! Request ID: ' + request.id);
            
            // Switch to view requests tab
            showTab('view-requests');
            document.querySelector('.nav-tab:nth-child(2)').click();
        });

        function displayRequests() {
            const requestsList = document.getElementById('requestsList');
            
            if (requests.length === 0) {
                requestsList.innerHTML = `
                    <div class="empty-state">
                        <h3>No Maintenance Requests Yet</h3>
                        <p>Submit your first maintenance request to get started!</p>
                    </div>
                `;
                return;
            }

            requestsList.innerHTML = requests.map(request => `
                <div class="request-card priority-${request.priority}">
                    <div class="request-header">
                        <h3 class="request-title">${request.issueTitle}</h3>
                        <div>
                            <span class="status-badge status-${request.status}">${request.status.replace('-', ' ')}</span>
                            <span class="priority-badge priority-${request.priority}-badge">${request.priority} Priority</span>
                        </div>
                    </div>
                    
                    <div class="request-meta">
                        <div class="meta-item">
                            <strong>ID:</strong> ${request.id}
                        </div>
                        <div class="meta-item">
                            <strong>Category:</strong> ${request.category}
                        </div>
                        <div class="meta-item">
                            <strong>Location:</strong> ${request.location}
                        </div>
                        <div class="meta-item">
                            <strong>Submitted:</strong> ${request.submittedDate}
                        </div>
                    </div>
                    
                    <p class="request-description">${request.description}</p>
                    
                    <div class="request-meta">
                        <div class="meta-item">
                            <strong>Requester:</strong> ${request.requesterName} (${request.department})
                        </div>
                        <div class="meta-item">
                            <strong>Email:</strong> ${request.requesterEmail}
                        </div>
                        <div class="meta-item">
                            <strong>Requested Date:</strong> ${request.requestedDate}
                        </div>
                    </div>
                    
                    <div class="request-actions">
                        <button class="action-btn update" onclick="updateStatus('${request.id}')">Update Status</button>
                        <button class="action-btn delete" onclick="deleteRequest('${request.id}')">Delete</button>
                    </div>
                </div>
            `).join('');
        }

        function updateStats() {
            const total = requests.length;
            const pending = requests.filter(r => r.status === 'pending').length;
            const inProgress = requests.filter(r => r.status === 'in-progress').length;
            const completed = requests.filter(r => r.status === 'completed').length;

            document.getElementById('totalRequests').textContent = total;
            document.getElementById('pendingRequests').textContent = pending;
            document.getElementById('inProgressRequests').textContent = inProgress;
            document.getElementById('completedRequests').textContent = completed;
        }

        function updateStatus(requestId) {
            const request = requests.find(r => r.id === requestId);
            if (!request) return;

            const statuses = ['pending', 'in-progress', 'completed'];
            const currentIndex = statuses.indexOf(request.status);
            const nextIndex = (currentIndex + 1) % statuses.length;
            
            request.status = statuses[nextIndex];
            
            displayRequests();
            updateStats();
        }

        function deleteRequest(requestId) {
            if (confirm('Are you sure you want to delete this maintenance request?')) {
                requests = requests.filter(r => r.id !== requestId);
                displayRequests();
                updateStats();
            }
        }

        // Set minimum date to today
        document.getElementById('requestedDate').min = new Date().toISOString().split('T')[0];
    </script>
      <footer class="footer">
  <p>© 2025 Hotel Maintenance and Engineering | All Rights Reserved</p>
</footer>
</body>
</html>