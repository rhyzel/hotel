<?php
include __DIR__ . '/../db.php';
date_default_timezone_set('Asia/Manila');

$cutoff_minutes_assigned = 3; 
$cutoff_minutes_in_progress = 30;
$cutoff_time_assigned = date('Y-m-d H:i:s', strtotime("-$cutoff_minutes_assigned minutes"));

$autoUpdateQuery = $conn->prepare("
    UPDATE housekeeping_tasks
    SET task_status = 'in progress', start_time = assigned_at
    WHERE task_status = 'assigned' AND assigned_at <= ?
");
$autoUpdateQuery->bind_param("s", $cutoff_time_assigned);
$autoUpdateQuery->execute();
$autoUpdateQuery->close();

$status_filter = $_GET['status'] ?? 'all';
$search_cleaner = $_GET['cleaner'] ?? '';

$base_select_sql = "
    SELECT h.task_id, h.staff_id, h.room_id, h.assigned_to, h.assigned_by, h.task_status, h.assigned_at, h.start_time, h.end_time, r.room_number
    FROM housekeeping_tasks h
    LEFT JOIN rooms r ON h.room_id = r.room_number
";

if($status_filter === 'all' && empty($search_cleaner)){
    $tasksQuery = $conn->prepare($base_select_sql . "ORDER BY h.task_status ASC, r.room_number ASC");
} else if(empty($search_cleaner)){
    $tasksQuery = $conn->prepare($base_select_sql . "WHERE h.task_status = ? ORDER BY r.room_number ASC");
    $tasksQuery->bind_param("s", $status_filter);
} else if($status_filter === 'all'){
    $tasksQuery = $conn->prepare($base_select_sql . "WHERE h.assigned_to LIKE ? ORDER BY h.task_status ASC, r.room_number ASC");
    $like_search = "%$search_cleaner%";
    $tasksQuery->bind_param("s", $like_search);
} else {
    $tasksQuery = $conn->prepare($base_select_sql . "WHERE h.task_status = ? AND h.assigned_to LIKE ? ORDER BY r.room_number ASC");
    $like_search = "%$search_cleaner%";
    $tasksQuery->bind_param("ss", $status_filter, $like_search);
}

$tasksQuery->execute();
$tasks = $tasksQuery->get_result();

$managerQuery = $conn->prepare("SELECT first_name, last_name FROM staff WHERE position_name LIKE '%Manager%' OR position_name = 'Housekeeping Supervisor'");
$managerQuery->execute();
$managerResult = $managerQuery->get_result();
$managerNames = [];
while ($manager = $managerResult->fetch_assoc()) {
    $managerNames[] = $manager['first_name'] . ' ' . $manager['last_name'];
}
$managerQuery->close();

$busyStaffQuery = $conn->prepare("
    SELECT assigned_to
    FROM housekeeping_tasks
    WHERE task_status IN ('assigned', 'in progress')
");
$busyStaffQuery->execute();
$busyStaffResult = $busyStaffQuery->get_result();
$busyStaffNames = [];
while ($busyStaff = $busyStaffResult->fetch_assoc()) {
    $busyStaffNames[] = $busyStaff['assigned_to'];
}
$busyStaffQuery->close();

$allStaffQuery = $conn->prepare("
    SELECT first_name, last_name 
    FROM staff 
    WHERE position_name = 'Room Attendant' OR department_name = 'Housekeeping'
");
$allStaffQuery->execute();
$allStaffResult = $allStaffQuery->get_result();
$staffNames = [];
while ($staff = $allStaffResult->fetch_assoc()) {
    $fullName = $staff['first_name'] . ' ' . $staff['last_name'];
    if (!in_array($fullName, $busyStaffNames)) {
        $staffNames[] = $fullName;
    }
}
$allStaffQuery->close();

$roomQuery = $conn->prepare("
    SELECT r.room_number, r.status AS room_status, COALESCE(h.task_status, 'No Task') AS current_task
    FROM rooms r
    LEFT JOIN housekeeping_tasks h ON r.room_number = h.room_id AND h.task_status != 'completed'
    WHERE r.status = 'dirty' 
    ORDER BY r.room_number ASC
");
$roomQuery->execute();
$roomResult = $roomQuery->get_result();
$roomData = [];
while ($room = $roomResult->fetch_assoc()) {
    $roomData[] = $room;
}
$roomQuery->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Housekeeping Tasks | Hotel La Vista</title>
<link rel="stylesheet" href="tasks.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.countdown-timer {
    font-weight: bold;
    color: #cc0000;
}
.task-status.in-progress {
    color: #007bff;
    background-color: #e6f2ff;
}
.countdown-overdue {
    color: #990000;
    animation: blinker 1s linear infinite;
}
@keyframes blinker {
  50% { opacity: 0.5; }
}
</style>
</head>
<body>
<header>
  <div class="title-group">
    <h1><i class="fas fa-broom"></i> Room Cleaning Tasks</h1>
    <p>View and manage housekeeping tasks assigned to staff.</p>
  </div>

  <div class="header-controls">
    <a href="../housekeeping.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
    <form method="GET" class="filter-form">
      <select name="status" id="status" onchange="this.form.submit()">
        <option value="all" <?= ($status_filter=='all')?'selected':'' ?>>All</option>
        <option value="assigned" <?= ($status_filter=='assigned')?'selected':'' ?>>Assigned</option>
        <option value="in progress" <?= ($status_filter=='in progress')?'selected':'' ?>>In Progress</option>
        <option value="completed" <?= ($status_filter=='completed')?'selected':'' ?>>Completed</option>
      </select>
      <input type="text" name="cleaner" placeholder="Search Cleaner" value="<?= htmlspecialchars($search_cleaner) ?>">
      <button type="submit">Search</button>
      <button type="button" onclick="window.location.href='housekeeping_tasks.php'">Clear</button>
    </form>
    <button id="globalAssignBtn" class="assign-btn">
      <i class="fas fa-user-plus"></i> Assign Task
    </button>
  </div>
</header>
    <div class="tasks-table">
        <table>
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Assigned To</th>
                    <th>Assigned By</th>
                    <th>Status</th>
                    <th>Countdown</th> 
                    <th>Assigned At</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tasks-table-body">
                <?php while($task = $tasks->fetch_assoc()): ?>
                <tr data-task-id="<?= $task['task_id'] ?>" 
                    <?php if ($task['task_status'] === 'assigned'): ?>
                        data-start-time="<?= strtotime($task['assigned_at']) ?>" 
                        data-cutoff-seconds="<?= $cutoff_minutes_assigned * 60 ?>"
                        data-countdown-status="assigned"
                    <?php elseif ($task['task_status'] === 'in progress'): ?>
                        data-start-time="<?= strtotime($task['start_time']) ?>"
                        data-cutoff-seconds="<?= $cutoff_minutes_in_progress * 60 ?>"
                        data-countdown-status="in-progress"
                    <?php endif; ?>
                >
                    <td><?= htmlspecialchars($task['room_number'] ?? '-') ?></td>
                    <td class="assigned-to-cell"><?= htmlspecialchars($task['assigned_to'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($task['assigned_by'] ?? '-') ?></td>
                    <td class="task-status-cell">
                        <span class="task-status <?= str_replace(' ', '-', strtolower($task['task_status'])) ?>">
                            <?= ucfirst($task['task_status']) ?>
                        </span>
                    </td>
                    <td class="countdown-cell">
                        <?php if ($task['task_status'] === 'assigned' || $task['task_status'] === 'in progress'): ?>
                            <div class="countdown-timer" id="countdown-<?= $task['task_id'] ?>"></div>
                        <?php else: ?>
                            <span>-</span>
                        <?php endif; ?>
                    </td>
                    <td class="assigned-at-cell"><?= $task['assigned_at'] ?></td>
                    <td class="start-time-cell">
                        <?= $task['start_time'] ? date('M d, Y h:i A', strtotime($task['start_time'])) : '-' ?>
                    </td>
                    <td class="end-time-cell">
                        <?= $task['end_time'] ? date('M d, Y h:i A', strtotime($task['end_time'])) : '-' ?>
                    </td>
                    <td class="action-cell">
                        <?php if ($task['task_status'] === 'assigned' || $task['task_status'] === 'in progress'): ?>
                            <button class="complete-btn action-btn" data-task-id="<?= $task['task_id'] ?>">
                                <i class="fas fa-check"></i> Complete
                            </button>
                            <button class="delete-btn action-btn" data-task-id="<?= $task['task_id'] ?>">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        <?php else: ?>
                            <span>-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>
<div id="assignModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Assign Task to Staff</h2>
        <form id="assignForm">
            <input type="hidden" id="modalTaskId" name="task_id"> 
            
            <div class="form-group">
                <label for="room_number">Room Number:</label>
                <select id="room_number" name="room_number" required>
                    <option value="">-- Select Room --</option>
                    <?php foreach ($roomData as $room): 
                        $room_status = ucfirst(str_replace('_', ' ', $room['room_status']));
                        $task_status = $room['current_task'] !== 'No Task' ? ' (Task: ' . ucfirst($room['current_task']) . ')' : '';
                    ?>
                        <option value="<?= htmlspecialchars($room['room_number']) ?>">
                            <?= htmlspecialchars($room['room_number']) ?> (Status: <?= $room_status ?>) <?= $task_status ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="assignee">Select Staff (Assigned To):</label>
                <select id="assignee" name="assignee" required>
                    <option value="">-- Select Staff --</option>
                    <?php foreach ($staffNames as $name): ?>
                        <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="assigned_by">Assigned By (Manager):</label>
                <select id="assigned_by" name="assigned_by" required>
                    <option value="">-- Select Manager --</option>
                    <?php foreach ($managerNames as $name): ?>
                        <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="assign-submit-btn">Assign</button>
        </form>
    </div>
</div>
</body>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('header');
    const modal = document.getElementById('assignModal');
    const closeModal = document.querySelector('.close-modal');
    const assignForm = document.getElementById('assignForm');
    const modalTaskId = document.getElementById('modalTaskId');
    const roomNumberSelect = document.getElementById('room_number');
    const assigneeSelect = document.getElementById('assignee');
    const assignedBySelect = document.getElementById('assigned_by');
    const tasksTableBody = document.getElementById('tasks-table-body');
    const globalAssignBtn = document.getElementById('globalAssignBtn'); 
    
    const cutoffSecondsAssigned = <?= $cutoff_minutes_assigned * 60 ?>;
    const cutoffSecondsInProgress = <?= $cutoff_minutes_in_progress * 60 ?>;

    header.addEventListener('mouseenter', () => {
      header.classList.add('expanded');
    });

    header.addEventListener('mouseleave', () => {
      header.classList.remove('expanded');
    });
    
    globalAssignBtn.addEventListener('click', () => {
        modalTaskId.value = ''; 
        roomNumberSelect.value = '';
        assigneeSelect.value = '';
        assignedBySelect.value = '';
        modal.style.display = 'flex';
    });
    
    function formatMysqlDatetime(mysqlDate) {
        if (!mysqlDate) return '-';
        const dateObj = new Date(mysqlDate.replace(' ', 'T').replace(/-/g, '/'));
        if (isNaN(dateObj.getTime())) return mysqlDate;

        try {
             return new Intl.DateTimeFormat('en-US', {
                month: 'short', day: '2-digit', year: 'numeric', 
                hour: '2-digit', minute: '2-digit', hour12: true, 
                timeZone: 'Asia/Manila' 
            }).format(dateObj);
        } catch (e) {
            return mysqlDate; 
        }
    }

    function updateCountdown() {
        const rows = document.querySelectorAll('#tasks-table-body tr[data-start-time][data-cutoff-seconds]');
        const currentTime = Math.floor(Date.now() / 1000); 

        rows.forEach(row => {
            const taskId = row.dataset.taskId;
            const startTimeTimestamp = parseInt(row.dataset.startTime);
            const cutoffSeconds = parseInt(row.dataset.cutoffSeconds); 
            const countdownStatus = row.dataset.countdownStatus;
            const countdownElement = document.getElementById(`countdown-${taskId}`);
            const statusCellSpan = row.querySelector('.task-status');
            
            if (!countdownElement) return;

            const timePassed = currentTime - startTimeTimestamp;
            let timeRemaining = cutoffSeconds - timePassed;

            if (timeRemaining > 0) {
                const minutes = Math.floor(timeRemaining / 60);
                const seconds = timeRemaining % 60;
                
                countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                countdownElement.classList.remove('countdown-overdue');
                
            } else if (countdownStatus === 'assigned') {
                
                statusCellSpan.textContent = 'In Progress';
                statusCellSpan.className = 'task-status in-progress';

                const countdownCell = row.querySelector('.countdown-cell');
                if (countdownCell) countdownCell.innerHTML = '<span>-</span>';
                
                const assignedAtCell = row.querySelector('.assigned-at-cell'); 
                const startTimeCell = row.querySelector('.start-time-cell'); 

                const assignedAtText = assignedAtCell.textContent.trim();
                const formattedStartTime = formatMysqlDatetime(assignedAtText);
                
                startTimeCell.textContent = formattedStartTime;

                row.removeAttribute('data-start-time');
                row.removeAttribute('data-cutoff-seconds');
                row.removeAttribute('data-countdown-status');

            } else if (countdownStatus === 'in-progress') {
                
                let overdueTime = Math.abs(timeRemaining);
                const minutes = Math.floor(overdueTime / 60);
                const seconds = overdueTime % 60;
                
                countdownElement.textContent = `OVERDUE: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                countdownElement.classList.add('countdown-overdue');
                
            }
        });
    }

    updateCountdown();
    setInterval(updateCountdown, 1000); 

    function handleCompleteClick(button) {
        const taskId = button.dataset.taskId;
        const row = button.closest('tr');

        if (confirm('Are you sure you want to mark this task as completed?')) {
            button.disabled = true;
            button.textContent = '...';

            fetch('complete_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `task_id=${taskId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const statusCell = row.querySelector('.task-status-cell .task-status');
                    const endTimeCell = row.querySelector('.end-time-cell');
                    const actionCell = row.querySelector('.action-cell');
                    const countdownCell = row.querySelector('.countdown-cell');
                    
                    statusCell.textContent = 'Completed';
                    statusCell.className = 'task-status completed';
                    
                    endTimeCell.textContent = data.new_end_time || '-'; 
                    
                    actionCell.innerHTML = '<span>-</span>';
                    
                    countdownCell.innerHTML = '<span>-</span>';
                    row.removeAttribute('data-start-time');
                    row.removeAttribute('data-cutoff-seconds');
                    row.removeAttribute('data-countdown-status');

                    alert(`Task ${taskId} marked as completed.`);
                } else {
                    alert(`Error completing task: ${data.message}`);
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-check"></i> Complete';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-check"></i> Complete';
            });
        }
    }

    function handleDeleteClick(button) {
        const taskId = button.dataset.taskId;
        const row = button.closest('tr');

        if (confirm('Are you sure you want to DELETE this task permanently?')) {
            button.disabled = true;
            button.textContent = '...';

            fetch('delete_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `task_id=${taskId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    alert(`Task ${taskId} deleted successfully.`);
                } else {
                    alert(`Error deleting task: ${data.message}`);
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-trash"></i> Delete';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-trash"></i> Delete';
            });
        }
    }

    tasksTableBody.addEventListener('click', function(e) {
        const completeButton = e.target.closest('.complete-btn');
        const deleteButton = e.target.closest('.delete-btn');

        if (completeButton) {
            handleCompleteClick(completeButton);
        } else if (deleteButton) {
            handleDeleteClick(deleteButton);
        }
    });

    closeModal.onclick = () => {
        modal.style.display = 'none';
    }

    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    assignForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const roomNumber = roomNumberSelect.value;
        const newAssignee = assigneeSelect.value;
        const assignedBy = assignedBySelect.value;
        
        const submitBtn = assignForm.querySelector('.assign-submit-btn');
        
        if (!roomNumber) {
            alert('Please select a room number to assign a task.');
            return;
        }
        if (!assignedBy) {
            alert('Please select the manager who is assigning the task.');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Assigning...';

        fetch('assign_task.php', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `room_number=${encodeURIComponent(roomNumber)}&assignee=${encodeURIComponent(newAssignee)}&assigned_by=${encodeURIComponent(assignedBy)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Task for Room ${roomNumber} assigned to ${newAssignee}.`);
                window.location.reload(); 
            } else {
                alert(`Error assigning task: ${data.message}`);
            }
            submitBtn.disabled = false;
            submitBtn.textContent = 'Assign';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred during assignment.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Assign';
        });
    });
});
</script>


</html>
<?php $conn->close(); ?>