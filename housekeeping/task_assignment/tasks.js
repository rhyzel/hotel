// ====== ALERT FUNCTION ======
function showAlert(message, type='error') {
  const container = document.getElementById('alert-container');
  const alert = document.createElement('div');
  alert.className = `alert-js alert-${type}`;
  alert.innerText = message;

  // Styling
  alert.style.backgroundColor = type === 'error' ? '#dc3545' : type === 'success' ? '#28a745' : '#17a2b8';
  alert.style.color = '#fff';
  alert.style.padding = '15px 25px';
  alert.style.borderRadius = '8px';
  alert.style.marginBottom = '10px';
  alert.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
  alert.style.fontSize = '14px';
  alert.style.fontWeight = '600';
  alert.style.textAlign = 'center';
  alert.style.minWidth = '300px';
  alert.style.maxWidth = '500px';
  alert.style.opacity = '0';
  alert.style.transform = 'translateY(-20px)';
  alert.style.transition = 'all 0.4s ease';

  container.appendChild(alert);

  // Animate in
  setTimeout(() => {
    alert.style.opacity = '1';
    alert.style.transform = 'translateY(0)';
  }, 10);

  // Fade out after 5s
  setTimeout(() => {
    alert.style.opacity = '0';
    alert.style.transform = 'translateY(-20px)';
    setTimeout(() => container.contains(alert) && container.removeChild(alert), 400);
  }, 5000);
}

// ====== TIME FORMATTING ======
function formatTo12Hour(datetime) {
  if (!datetime || datetime === '0000-00-00 00:00:00' || datetime === '-') return '-';
  const date = new Date(datetime);
  return date.toLocaleString('en-US', {
    timeZone: 'Asia/Manila',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  });
}

function getCurrentPhilippineTime() {
  return new Date().toLocaleString('en-US', {
    timeZone: 'Asia/Manila',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    second: '2-digit',
    hour12: true
  });
}

// ====== UPDATE MODAL LOGIC ======
let currentStatus = '';
let currentStartTime = '';
let currentEndTime = '';

document.querySelectorAll('.btn-update').forEach(btn => {
  btn.addEventListener('click', function() {
    const taskId = this.dataset.taskId;
    const roomStatus = this.dataset.roomStatus;
    currentStatus = this.dataset.taskStatus;
    currentStartTime = this.dataset.startTime;
    currentEndTime = this.dataset.endTime;

    if (roomStatus === 'under maintenance') {
      showAlert('Cannot update task status while room is under maintenance. Please complete maintenance first.', 'error');
      return;
    }

    document.getElementById('update_task_id').value = taskId;
    document.getElementById('task_status').value = currentStatus;

    document.getElementById('updateModal').style.display = 'flex';
    updateTimeFields(currentStatus);
    loadItemsUsed(taskId, currentStartTime);
  });
});

function updateTimeFields(status) {
  const currentTime = getCurrentPhilippineTime();
  const startTimeDisplay = document.getElementById('start_time_display');
  const endTimeDisplay = document.getElementById('end_time_display');

  if (status === 'assigned') {
    startTimeDisplay.value = '-';
    endTimeDisplay.value = '-';
  } else if (status === 'in progress') {
    startTimeDisplay.value = currentStartTime && currentStartTime !== '0000-00-00 00:00:00'
      ? formatTo12Hour(currentStartTime)
      : currentTime;
    endTimeDisplay.value = '-';
  } else if (status === 'completed') {
    startTimeDisplay.value = currentStartTime && currentStartTime !== '0000-00-00 00:00:00'
      ? formatTo12Hour(currentStartTime)
      : currentTime;
    endTimeDisplay.value = currentEndTime && currentEndTime !== '0000-00-00 00:00:00'
      ? formatTo12Hour(currentEndTime)
      : currentTime;
  }
}

function checkItemsForTask(taskId) {
    return fetch(`tasks.php?ajax=1&check_items=1&task_id=${taskId}`)
        .then(response => response.json())
        .then(data => data.has_items);
}

function loadItemsUsed(taskId, startTime) {
    const itemsDiv = document.getElementById('items_used');
    itemsDiv.innerHTML = '<p style="color: #aaa; margin: 0;">Loading items...</p>';

    if (!startTime || startTime === '0000-00-00 00:00:00' || startTime === '-') {
        itemsDiv.innerHTML = '<p style="color: #aaa; margin: 0;">No start time available.</p>';
        return;
    }

    fetch(`tasks.php?ajax=1&get_items_for_task=1&task_id=${taskId}&start_time=${encodeURIComponent(startTime)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                let html = '<ul style="list-style: none; padding: 0; margin: 0;">';
                data.forEach(item => {
                    const usedAt = new Date(item.used_at).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
                    html += `<li style="padding: 5px 0; border-bottom: 1px solid #444;">${item.item_name} (Qty: ${item.quantity_needed}) - ${usedAt}</li>`;
                });
                html += '</ul>';
                itemsDiv.innerHTML = html;
            } else {
                itemsDiv.innerHTML = '<p style="color: #aaa; margin: 0;">No items used yet.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading items:', error);
            itemsDiv.innerHTML = '<p style="color: red; margin: 0;">Error loading items.</p>';
        });
}

document.getElementById('task_status').addEventListener('change', function() {
  const selected = this.value;
  const startInput = document.getElementById('start_time_display');
  const endInput = document.getElementById('end_time_display');
  const currentTime = getCurrentPhilippineTime();

  // Validation rules
  if (currentStatus === 'in progress' || currentStatus === 'completed') {
    if (selected === 'assigned') {
      showAlert('Cannot change in-progress or completed task back to Assigned.', 'error');
      this.value = currentStatus;
      return;
    }
  }

  if (currentStatus === 'assigned' && selected === 'completed') {
    showAlert('Cannot set task to Completed directly from Assigned. Start with In Progress first.', 'error');
    this.value = currentStatus;
    return;
  }

  if (currentStatus === 'completed' && (selected === 'assigned' || selected === 'in progress')) {
    showAlert('Completed task cannot be changed back.', 'error');
    this.value = currentStatus;
    return;
  }

  if (currentStatus === 'completed' && selected === 'completed') {
    showAlert('This task is already completed and cannot be updated again.', 'error');
    this.value = currentStatus;
    return;
  }


  // Update display fields
  if (selected === 'assigned') {
    startInput.value = '-';
    endInput.value = '-';
  } else if (selected === 'in progress') {
    startInput.value = currentStartTime && currentStartTime !== '0000-00-00 00:00:00'
      ? formatTo12Hour(currentStartTime)
      : currentTime;
    endInput.value = '-';
  } else if (selected === 'completed') {
    startInput.value = currentStartTime && currentStartTime !== '0000-00-00 00:00:00'
      ? formatTo12Hour(currentStartTime)
      : currentTime;
    endInput.value = currentEndTime && currentEndTime !== '0000-00-00 00:00:00'
      ? formatTo12Hour(currentEndTime)
      : currentTime;
  }
});
// ...existing code...

// ====== MAINTENANCE MODAL LOGIC ======
document.querySelectorAll('.btn-maintenance').forEach(btn => {
  btn.addEventListener('click', function() {
    // Fill modal fields with data attributes
    document.getElementById('maintenance_room_id').value = this.dataset.roomId;
    document.getElementById('requester_staff_id').value = this.dataset.staffId;
    document.getElementById('requested_by').value = this.dataset.staffName;

    // Show warning if there is a pending maintenance request
    if (this.dataset.hasPending === '1') {
      document.getElementById('maintenanceWarning').style.display = 'block';
      document.getElementById('maintenanceWarningTitle').innerHTML = `<i class="fas fa-exclamation-triangle"></i> Room Already Has ${this.dataset.maintenanceStatus} Maintenance Request`;
      document.getElementById('existingMaintenanceInfo').innerHTML =
        `<b>Description:</b> ${this.dataset.maintenanceDescription || '-'}<br>
         <b>Priority:</b> ${this.dataset.maintenancePriority || '-'}<br>
         <b>Status:</b> ${this.dataset.maintenanceStatus || '-'}<br>
         <b>Requested At:</b> ${this.dataset.maintenanceDate || '-'}`;
      // Optionally disable the form if you don't want to allow another request
      document.getElementById('maintenanceForm').style.display = 'none';
    } else {
      document.getElementById('maintenanceWarning').style.display = 'none';
      document.getElementById('existingMaintenanceInfo').innerHTML = '';
      document.getElementById('maintenanceForm').style.display = 'block';
      document.getElementById('issue_description').value = '';
      document.getElementById('priority').value = 'Medium';
    }

    document.getElementById('maintenanceModal').style.display = 'flex';
  });
});

// Close Maintenance Modal
document.getElementById('closeMaintenanceModal').addEventListener('click', function() {
  document.getElementById('maintenanceModal').style.display = 'none';
});

// Close Update Modal
document.getElementById('closeUpdateModal').addEventListener('click', function() {
  document.getElementById('updateModal').style.display = 'none';
});

// Open Task Assignment Modal
let timeUpdateInterval;
document.querySelector('.btn-assign').addEventListener('click', function() {
  document.getElementById('assigned_at_display').value = getCurrentPhilippineTime();
  document.getElementById('taskModal').style.display = 'flex';
  // Update time every second
  timeUpdateInterval = setInterval(() => {
    document.getElementById('assigned_at_display').value = getCurrentPhilippineTime();
  }, 1000);
});

// Close Task Assignment Modal
document.getElementById('closeModal').addEventListener('click', function() {
  document.getElementById('taskModal').style.display = 'none';
  clearInterval(timeUpdateInterval);
});

// ====== ADD ITEMS MODAL LOGIC ======
document.querySelectorAll('.btn-add-item').forEach(btn => {
  btn.addEventListener('click', function() {
    const taskId = this.dataset.taskId;
    const roomNumber = this.dataset.roomNumber;
    const taskStatus = this.dataset.taskStatus;

    if (taskStatus === 'completed') {
      showAlert('Items cannot be added to a completed task.', 'error');
      return;
    }

    document.getElementById('item_task_id').value = taskId;

    // Load laundry items
    loadRoomItems(taskId);

    document.getElementById('itemsModal').style.display = 'flex';
  });
});

// Close Items Modal
document.getElementById('closeItemsModal').addEventListener('click', function() {
  document.getElementById('itemsModal').style.display = 'none';
});



// Load room items for the specific room
function loadRoomItems(taskId) {
    // First get room_id from task_id
    fetch(`tasks.php?ajax=1&get_task_room=${taskId}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.room_id) {
                // Now fetch room items
                fetch(`tasks.php?ajax=1&get_room_items=1&room_id=${data.room_id}`)
                    .then(response => response.json())
                    .then(roomItems => {
                        displayRoomItems(roomItems);
                    })
                    .catch(error => {
                        console.error('Error loading room items:', error);
                        document.getElementById('roomItemsContainer').innerHTML = '<p style="color: #aaa;">Error loading room items.</p>';
                    });
            } else {
                document.getElementById('roomItemsContainer').innerHTML = '<p style="color: #aaa;">Error: Room not found for this task.</p>';
            }
        })
        .catch(error => {
            console.error('Error getting room ID:', error);
            document.getElementById('roomItemsContainer').innerHTML = '<p style="color: #aaa;">Error getting room ID.</p>';
        });
}

function displayRoomItems(items) {
    const container = document.getElementById('roomItemsContainer');
    if (items.error) {
        container.innerHTML = '<p style="color: red;">Error: ' + items.error + '</p>';
        return;
    }
    if (items.length > 0) {
        const roomNumber = items[0].room_number;
        let html = '<p style="color: #FFD700; margin-bottom: 10px; font-size: 0.9rem;">Laundry Items from Room (' + roomNumber + '):</p>';
        html += '<style>.laundry-item.selected { background: rgba(144, 238, 144, 0.4) !important; }</style>';
        html += '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">';
        items.forEach((item, index) => {
            let statusColor, statusText;
            if (item.status === 'collected') {
                statusColor = '#007bff'; // blue
                statusText = 'Collected';
            } else if (item.status === 'dirty') {
                statusColor = '#ff6b6b';
                statusText = 'Dirty';
            } else {
                statusColor = '#4ecdc4';
                statusText = 'Clean';
            }
            const statusDisplay = `<span style="color: ${statusColor}; font-size: 0.8rem; font-weight: bold;">Status: ${statusText}</span>`;
            html += `
                <div class="laundry-item" data-item="${item.item_name}" data-status="${item.status}" data-original-qty="${item.quantity}" data-current-qty="${item.quantity}" style="background: rgba(255,255,255,0.1); padding: 8px; border-radius: 6px; border: 1px solid rgba(255,215,0,0.3); cursor: pointer; transition: background 0.3s;">
                    <div>
                        <strong style="color: #FFD700;">${item.item_name}</strong><br>
                        <span class="qty-display" style="color: #ccc; font-size: 0.9rem;">Quantity in room: ${item.quantity}</span><br>
                        <span style="color: #ff6b6b; font-size: 0.8rem;">Required: ${item.required_quantity}</span><br>
                        ${statusDisplay}
                        <br><span style="color: #FFD700; font-size: 0.8rem;">Inputted quantity:</span>
                        <input type="number" name="laundry_quantity[]" min="0" max="${Math.max(0, item.required_quantity - item.quantity)}" placeholder="Qty" style="width: 100%; margin-top: 5px; padding: 4px; border-radius: 4px; border: 1px solid #555; background: #2c2c2c; color: #f1f1f1; pointer-events: auto;" required>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        html += '<div style="text-align: center; margin: 15px 0;">';
        html += '<button type="button" id="addLaundryItems" style="background: #28a745; color: #fff; margin-right: 10px; padding: 8px 16px; font-size: 12px; border: none; border-radius: 6px; cursor: pointer;"><i class="fas fa-plus"></i> Add Selected Items</button>';
        html += '<button type="button" id="collectLaundryItems" style="background: #007bff; color: #fff; padding: 8px 16px; font-size: 12px; border: none; border-radius: 6px; cursor: pointer;"><i class="fas fa-hand-paper"></i> Collect Selected Items</button>';
        html += '</div>';
        container.innerHTML = html;

        // Add click listeners for laundry items
        document.querySelectorAll('.laundry-item').forEach(item => {
            const input = item.querySelector('input[name="laundry_quantity[]"]');
            const qtyDisplay = item.querySelector('.qty-display');
            const originalQty = parseInt(item.dataset.originalQty);

            item.addEventListener('click', function() {
                if (item.dataset.status === 'collected') return; // disabled
                this.classList.toggle('selected');
                if (this.classList.contains('selected')) {
                    input.value = item.dataset.currentQty; // auto-fill with current quantity
                    updateQtyDisplay();
                } else {
                    input.value = '';
                    qtyDisplay.textContent = `Quantity in room: ${item.dataset.currentQty}`;
                }
            });

            // No real-time quantity update
        });

        // Add event listeners for the buttons
        document.getElementById('addLaundryItems').addEventListener('click', function() {
            addLaundryItems();
        });
        document.getElementById('collectLaundryItems').addEventListener('click', function() {
            collectLaundryItems();
        });
    } else {
        container.innerHTML = '<p style="color: #aaa;">No laundry items found for this room.</p>';
    }
}

// Add laundry items to inventory as clean
function addLaundryItems() {
    const taskId = document.getElementById('item_task_id').value;
    const allItems = document.querySelectorAll('.laundry-item');

    // Collect items with quantity > 0
    const itemsToProcess = [];
    allItems.forEach((item) => {
        const quantity = parseInt(item.querySelector('input[name="laundry_quantity[]"]').value) || 0;
        if (quantity > 0) {
            itemsToProcess.push(item);
        }
    });

    if (itemsToProcess.length === 0) {
        showAlert('Please enter valid quantities (greater than 0) for at least one item.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('action', 'add_laundry');

    itemsToProcess.forEach((item) => {
        const itemName = item.dataset.item;
        const quantity = item.querySelector('input[name="laundry_quantity[]"]').value;
        formData.append('laundry_item[]', itemName);
        formData.append('laundry_quantity[]', quantity);
    });

    fetch('tasks.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                showAlert(data.message, 'success');
                // Trigger immediate status update
                updateTaskStatuses();
                // Update DOM directly for real-time display
                itemsToProcess.forEach((item) => {
                    const quantityInput = item.querySelector('input[name="laundry_quantity[]"]');
                    const addedQty = parseInt(quantityInput.value) || 0;
                    if (addedQty > 0) {
                        // Update status to clean
                        item.dataset.status = 'clean';
                        // Update display
                        const statusSpan = item.querySelector('span[style*="font-weight: bold"]');
                        if (statusSpan) {
                            statusSpan.style.color = '#4ecdc4';
                            statusSpan.textContent = 'Status: Clean';
                        }
                        // Update quantity display
                        const qtySpan = item.querySelector('span[style*="color: #ccc"]');
                        if (qtySpan) {
                            const currentQty = parseInt(item.dataset.currentQty);
                            const newQty = currentQty + addedQty;
                            qtySpan.textContent = `Quantity in room: ${newQty}`;
                            // Update quantities for real-time updates
                            item.dataset.currentQty = newQty;
                            item.dataset.originalQty = newQty;
                        }
                        // Clear input but keep enabled
                        quantityInput.value = '';
                    }
                });
            } else {
                showAlert(data.message, 'error');
            }
        } catch (e) {
            showAlert('Invalid response: ' + text.substring(0, 200), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error adding laundry items: ' + error.message, 'error');
    });
}

// Collect laundry items (add to inventory with status)
function collectLaundryItems() {
    const taskId = document.getElementById('item_task_id').value;
    const allItems = document.querySelectorAll('.laundry-item');

    // Collect items with quantity > 0 and not collected
    const itemsToProcess = [];
    allItems.forEach((item) => {
        const quantity = parseInt(item.querySelector('input[name="laundry_quantity[]"]').value) || 0;
        if (quantity > 0 && item.dataset.status !== 'collected') {
            itemsToProcess.push(item);
        }
    });

    if (itemsToProcess.length === 0) {
        showAlert('Please enter valid quantities (greater than 0) for at least one item.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('action', 'collect_laundry');

    itemsToProcess.forEach((item) => {
        const itemName = item.dataset.item;
        const quantity = item.querySelector('input[name="laundry_quantity[]"]').value;
        formData.append('laundry_item[]', itemName);
        formData.append('laundry_quantity[]', quantity);
    });

    fetch('tasks.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            // Trigger immediate status update
            updateTaskStatuses();
            // Update DOM directly for real-time display
            itemsToProcess.forEach((item) => {
                const quantityInput = item.querySelector('input[name="laundry_quantity[]"]');
                const collectedQty = parseInt(quantityInput.value) || 0;
                if (collectedQty > 0) {
                    // Update status to collected
                    item.dataset.status = 'collected';
                    // Update display
                    const statusSpan = item.querySelector('span[style*="font-weight: bold"]');
                    if (statusSpan) {
                        statusSpan.style.color = '#007bff';
                        statusSpan.textContent = 'Status: Collected';
                    }
                    // Update quantity display
                    const qtySpan = item.querySelector('span[style*="color: #ccc"]');
                    if (qtySpan) {
                        const currentQty = parseInt(item.dataset.currentQty);
                        const newQty = Math.max(0, currentQty - collectedQty);
                        qtySpan.textContent = `Quantity in room: ${newQty}`;
                        // Update quantities for real-time updates
                        item.dataset.currentQty = newQty;
                        item.dataset.originalQty = newQty;
                    }
                    // Clear input
                    quantityInput.value = '';
                }
            });
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error collecting laundry items.', 'error');
    });
}


// Custom Dropdown Functionality
document.addEventListener('DOMContentLoaded', function() {
  // Initialize dropdowns
  initializeDropdowns();
});

function initializeDropdowns() {
  document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
    initializeDropdown(dropdown);
  });
}

function initializeDropdown(dropdown) {
  const header = dropdown.querySelector('.dropdown-header');
  const input = header.querySelector('input');
  const options = dropdown.querySelector('.dropdown-options');
  const hiddenInput = dropdown.parentElement.querySelector('input[type="hidden"]');
  const arrow = header.querySelector('.dropdown-arrow');

  // Toggle dropdown
  header.addEventListener('click', function(e) {
    if (e.target !== input) {
      toggleDropdown(dropdown);
    }
  });

  // Search functionality
  input.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const optionElements = options.querySelectorAll('.dropdown-option');

    optionElements.forEach(option => {
      const text = option.textContent.toLowerCase();
      if (text.includes(searchTerm)) {
        option.style.display = 'block';
      } else {
        option.style.display = 'none';
      }
    });

    if (!options.style.display || options.style.display === 'none') {
      options.style.display = 'block';
      dropdown.classList.add('open');
    }
  });

  // Select option
  options.addEventListener('click', function(e) {
    if (e.target.classList.contains('dropdown-option')) {
      const value = e.target.dataset.value;
      const text = e.target.textContent;

      input.value = text;
      hiddenInput.value = value;

      // Highlight selected
      options.querySelectorAll('.dropdown-option').forEach(opt => opt.classList.remove('selected'));
      e.target.classList.add('selected');

      toggleDropdown(dropdown);
    }
  });

  // Close on outside click
  document.addEventListener('click', function(e) {
    if (!dropdown.contains(e.target)) {
      options.style.display = 'none';
      dropdown.classList.remove('open');
    }
  });
}

function toggleDropdown(dropdown) {
  const options = dropdown.querySelector('.dropdown-options');
  const arrow = dropdown.querySelector('.dropdown-arrow');

  if (options.style.display === 'none' || !options.style.display) {
    options.style.display = 'block';
    dropdown.classList.add('open');
    dropdown.querySelector('input').focus();
  } else {
    options.style.display = 'none';
    dropdown.classList.remove('open');
  }
}

// Real-time status updates
function updateTaskStatuses() {
  fetch('tasks.php?ajax=1&get_task_statuses=1')
    .then(response => response.json())
    .then(data => {
      data.forEach(task => {
        const row = document.querySelector(`tr[data-task-id="${task.task_id}"]`);
        if (row) {
          // Update status badge
          const statusCell = row.querySelector('td:nth-child(5) .status-badge');
          if (statusCell) {
            const oldStatus = statusCell.textContent.toLowerCase().replace(' ', '-');
            const newStatus = task.task_status;
            const newStatusClass = newStatus.replace(' ', '-');

            statusCell.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            statusCell.className = `status-badge ${newStatusClass}`;

            // Update data attributes on buttons
            const updateBtn = row.querySelector('.btn-update');
            if (updateBtn) {
              updateBtn.setAttribute('data-task-status', newStatus);
              updateBtn.setAttribute('data-start-time', task.start_time);
              updateBtn.setAttribute('data-end-time', task.end_time);
              updateBtn.setAttribute('data-room-status', task.room_status);
            }

            const addItemBtn = row.querySelector('.btn-add-item');
            if (addItemBtn) {
              addItemBtn.setAttribute('data-task-status', newStatus);
            }

            const maintenanceBtn = row.querySelector('.btn-maintenance');
            if (maintenanceBtn) {
              maintenanceBtn.setAttribute('data-task-status', newStatus);
            }
          }

          // Update start time
          const startTimeCell = row.querySelector('td:nth-child(7)');
          if (startTimeCell) {
            startTimeCell.textContent = formatTo12Hour(task.start_time);
          }

          // Update end time
          const endTimeCell = row.querySelector('td:nth-child(8)');
          if (endTimeCell) {
            endTimeCell.textContent = formatTo12Hour(task.end_time);
          }
        }
      });
    })
    .catch(error => {
      console.error('Error updating task statuses:', error);
    });
}

// Start real-time updates
document.addEventListener('DOMContentLoaded', function() {
  // Initial update
  updateTaskStatuses();

  // Update every 10 seconds
  setInterval(updateTaskStatuses, 10000);
});