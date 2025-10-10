<?php
include 'tasks_main.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Task Assignment | Housekeeping</title>
<link rel="stylesheet" href="tasks.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
<div class="container">
<header>
    <h1><i class="fas fa-clipboard-list"></i> Room Cleaning Task Assignment</h1>
    <p>Assign staff to clean dirty rooms.</p>
</header>



<!-- Show error message if exists -->
<?php if ($showErrorMsg): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    showAlert('<?php echo addslashes(htmlspecialchars($showErrorMsg)); ?>', 'error');
});
</script>
<?php endif; ?>

<!-- Assign Task Button -->
<button type="button" class="btn-assign toggle-form">
    <i class="fas fa-plus"></i> Assign Room Cleaning Task
</button>
<br><br>

<!-- Assign Task Modal -->
<div class="modal-overlay" id="taskModal">
    <div class="modal">
        <button type="button" class="close-btn" id="closeModal"><i class="fas fa-times"></i></button>
        <h2>Room Cleaning</h2>
        <form method="POST" class="assignment-form">
            <div>
                <label for="room_id">Select Dirty Room:</label>
                <select name="room_id" id="room_id" required>
                    <option value="">-- Choose Room --</option>
                    <?php if ($rooms_result && $rooms_result->num_rows > 0): ?>
                        <?php while ($room = $rooms_result->fetch_assoc()): ?>
                            <option value="<?php echo (int)$room['room_id']; ?>"
                                    data-assigned="<?php echo $room['is_assigned']; ?>">
                                Room <?php echo htmlspecialchars($room['room_number']); ?> (<?php echo htmlspecialchars($room['room_type']); ?>)
                                <?php if ($room['is_assigned']): ?> - ALREADY ASSIGNED<?php endif; ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="">No dirty rooms available</option>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label for="staff_id">Assign to Staff:</label>
                <select name="staff" id="staff" required>
                    <option value="">-- Choose Staff --</option>
                    <?php if ($staff_result && $staff_result->num_rows > 0): ?>
                        <?php while ($staff = $staff_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($staff['staff_id']); ?>">
                                <?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name'] . ' (' . $staff['position_name'] . ')'); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="">No housekeeping staff found</option>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label for="assigned_by">Assigned by:</label>
                <select name="assigned_by" id="assigned_by" required style="background-color: #444; color: #fff; border: 1px solid #555;">
                    <option value="">-- Choose Assigner --</option>
                    <?php if ($assigner_result && $assigner_result->num_rows > 0): ?>
                        <?php while ($assigner = $assigner_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($assigner['staff_id']); ?>" style="background-color: #444; color: #fff;">
                                <?php echo htmlspecialchars($assigner['first_name'] . ' ' . $assigner['last_name'] . ' (' . $assigner['position_name'] . ')'); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="">No Executive Housekeepers found</option>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label for="assigned_at_display">Assigned At:</label>
                <input type="text" id="assigned_at_display" disabled readonly style="background-color: #444; color: #fff; border: 1px solid #555; cursor: not-allowed;" />
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-check"></i> Submit Assignment</button>
        </form>
    </div>
</div>

<!-- Maintenance Request Modal -->
<div class="modal-overlay" id="maintenanceModal">
  <div class="modal">
    <button type="button" class="close-btn" id="closeMaintenanceModal"><i class="fas fa-times"></i></button>
    <h2>Request Maintenance</h2>

    <!-- Warning container for existing maintenance -->
    <div id="maintenanceWarning" style="display: none; background: rgba(255, 193, 7, 0.2); border: 1px solid #ffc107; border-radius: 6px; padding: 15px; margin-bottom: 15px;">
      <div id="maintenanceWarningTitle" style="color: #ffc107; font-weight: 600; margin-bottom: 10px;">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <div id="existingMaintenanceInfo" style="color: #f1f1f1; font-size: 0.9rem;"></div>
    </div>

    <form method="POST" id="maintenanceForm">
      <input type="hidden" name="maintenance_room_id" id="maintenance_room_id">
      <input type="hidden" name="assigned_staff_id" id="assigned_staff_id">
      <input type="hidden" name="requester_staff_id" id="requester_staff_id">
      <div>
        <label for="issue_description">Issue Description:</label>
        <textarea name="issue_description" id="issue_description" rows="4" required placeholder="Describe the maintenance issue..."></textarea>
      </div>
      <div>
        <label for="priority">Priority Level:</label>
        <select name="priority" id="priority" required>
          <option value="Low">Low</option>
          <option value="Medium" selected>Medium</option>
          <option value="High">High</option>
          <option value="Critical">Critical</option>
        </select>
      </div>
      <div>
        <label for="requested_by">Requested By:</label>
        <input type="text" name="requested_by" id="requested_by" required placeholder="Your name" readonly style="background-color: #f8f9fa; cursor: not-allowed;" />
      </div>
      <button type="submit" class="btn-submit"><i class="fas fa-tools"></i> Submit Request</button>
    </form>
  </div>
</div>

<!-- Update Task Modal -->
<div class="modal-overlay" id="updateModal">
  <div class="modal">
    <button type="button" class="close-btn" id="closeUpdateModal"><i class="fas fa-times"></i></button>
    <h2>Update Task Status</h2>
    <form method="POST" id="updateForm">
      <input type="hidden" name="update_task_id" id="update_task_id">
      <div>
        <label for="task_status">Select Task Status:</label>
        <select name="task_status" id="task_status" required>
          <option value="assigned">Assigned</option>
          <option value="in progress">In Progress</option>
          <option value="completed">Completed</option>
        </select>
      </div>
      <div>
        <label>Start Time:</label>
        <input type="text" id="start_time_display" disabled placeholder="-" />
      </div>
      <div>
        <label>End Time:</label>
        <input type="text" id="end_time_display" disabled placeholder="-" />
      </div>
      <button type="submit" class="btn-submit"><i class="fas fa-check"></i> Update Status</button>
    </form>
  </div>
</div>

<!-- Add Items Modal -->
<div class="modal-overlay" id="itemsModal">
  <div class="modal" style="width: 800px; max-width: 95%; max-height: 80vh; overflow-y: auto;">
    <button type="button" class="close-btn" id="closeItemsModal"><i class="fas fa-times"></i></button>
    <h2>Manage Laundry Items</h2>
    <form method="POST" id="itemsForm">
      <input type="hidden" name="task_id" id="item_task_id">

      <!-- Laundry Items Section -->
      <div>
        <div id="roomItemsContainer" style="margin-bottom: 20px; padding: 10px; background: rgba(255,215,0,0.1); border-radius: 8px;">
          <!-- Laundry items will be loaded here -->
        </div>
      </div>

    </form>
  </div>
</div>

<!-- Task List -->
<h2>Assigned Tasks</h2>
<table class="tasks-table">
    <thead>
        <tr>
            <th>Task ID</th>
            <th>Room Number</th>
            <th>Assigned To</th>
            <th>Assigned By</th>
            <th>Task Status</th>
            <th>Assigned At</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($tasks_result && $tasks_result->num_rows > 0): ?>
        <?php while ($task = $tasks_result->fetch_assoc()): ?>
        <?php
            // Create safe CSS class
            $status_class = strtolower(str_replace(' ', '-', $task['task_status']));
        ?>
        <tr data-task-id="<?php echo $task['task_id']; ?>">
            <td><?php echo (int)$task['task_id']; ?></td>
            <td><?php echo htmlspecialchars($task['room_number']); ?></td>
            <td><?php echo htmlspecialchars($task['staff'] . ' - ' . $task['first_name'] . ' ' . $task['last_name']); ?></td>
            <td><?php echo htmlspecialchars($task['assigned_by'] . ' - ' . $task['assigner_first'] . ' ' . $task['assigner_last']); ?></td>
            <td><span class="status-badge <?php echo $status_class; ?>"><?php echo ucfirst(htmlspecialchars($task['task_status'])); ?></span></td>
            <td><?php echo formatTo12Hour($task['assigned_at']); ?></td>
            <td><?php echo formatTo12Hour($task['start_time']); ?></td>
            <td><?php echo formatTo12Hour($task['end_time']); ?></td>
            <td>
                <div style="display: flex; flex-direction: column; gap: 10px; align-items: center;">
                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="btn-update"
                                data-task-id="<?php echo $task['task_id']; ?>"
                                data-task-status="<?php echo $task['task_status']; ?>"
                                data-start-time="<?php echo $task['start_time']; ?>"
                                data-end-time="<?php echo $task['end_time']; ?>"
                                data-room-status="<?php echo $task['room_status']; ?>">
                            <i class="fas fa-edit"></i> Update
                        </button>
                        <button type="button" class="btn-add-item"
                                data-task-id="<?php echo $task['task_id']; ?>"
                                data-room-number="<?php echo $task['room_number']; ?>"
                                data-task-status="<?php echo $task['task_status']; ?>">
                            <i class="fas fa-plus"></i> Add Items
                        </button>
                    </div>
                    <button type="button" class="btn-maintenance"
                            data-room-id="<?php echo $task['room_id']; ?>"
                            data-room-number="<?php echo $task['room_number']; ?>"
                            data-room-type="<?php echo $task['room_type']; ?>"
                            data-staff-id="<?php echo $task['staff']; ?>"
                            data-staff-name="<?php echo htmlspecialchars($task['first_name'] . ' ' . $task['last_name']); ?>"
                            data-task-status="<?php echo $task['task_status']; ?>"
                            data-has-pending="<?php echo $task['request_id'] ? '1' : '0'; ?>"
                            data-maintenance-description="<?php echo $task['request_id'] ? htmlspecialchars($task['issue_description']) : ''; ?>"
                            data-maintenance-priority="<?php echo $task['request_id'] ? $task['priority'] : ''; ?>"
                            data-maintenance-status="<?php echo $task['request_id'] ? $task['maintenance_status'] : ''; ?>"
                            data-maintenance-date="<?php echo $task['request_id'] ? formatTo12Hour($task['maintenance_requested_at']) : ''; ?>">
                        <i class="fas fa-tools"></i> Request Maintenance
                    </button>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php else: ?>
        <tr>
            <td colspan="9" style="text-align: center; padding: 20px; color: #aaa;">No tasks have been assigned yet.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>



<a href="../housekeeping.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Housekeeping</a>
</div>
</div>
<!-- Alert container -->
<div id="alert-container" style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;min-width:300px;"></div>

<script src="tasks.js"></script>

</body>
</html>