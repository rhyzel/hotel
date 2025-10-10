<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();
include_once dirname(__DIR__, 2) . '/db_connect.php';
if (!isset($conn) || !$conn) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit;
}

// Handle AJAX requests for items
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    if (isset($_GET['get_all_items'])) {
        $status = $_GET['status'] ?? 'all';
        $where = "";
        if ($status !== 'all') {
            $where = "WHERE status = '$status'";
        }
        $sql = "SELECT item_name FROM hp_inventory $where ORDER BY item_name";
        $result = $conn->query($sql);

        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    if (isset($_GET['get_items_for_task'])) {
        $task_id = intval($_GET['task_id']);
        $start_time = $_GET['start_time'];

        $sql = "SELECT hti.quantity_needed, hi.item_name, hti.used_at
                FROM hp_tasks_items hti
                JOIN hp_inventory hi ON hti.item_id = hi.item_id
                WHERE hti.task_id = ? AND hti.used_at >= ?
                ORDER BY hti.used_at DESC";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("is", $task_id, $start_time);
            $stmt->execute();
            $result = $stmt->get_result();

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->close();
        } else {
            $data = ['error' => 'Prepare failed: ' . $conn->error];
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    if (isset($_GET['check_items'])) {
        $task_id = intval($_GET['task_id']);
        $sql = "SELECT COUNT(*) as count FROM hp_tasks_items WHERE task_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $task_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            $stmt->close();
        } else {
            $count = 0;
        }

        header('Content-Type: application/json');
        echo json_encode(['has_items' => $count > 0]);
        exit;
    }

    if (isset($_GET['get_room_items'])) {
        $room_id = intval($_GET['room_id']);
        // Ensure status column exists
        $alterSql = "ALTER TABLE room_items ADD COLUMN IF NOT EXISTS status ENUM('clean', 'dirty', 'collected') DEFAULT 'clean'";
        $conn->query($alterSql);

        // Show all items from the room
        $sql = "SELECT ri.item_name, ri.quantity, r.room_number, ri.status,
                CASE ri.item_name
                    WHEN 'Bath Towels' THEN 4
                    WHEN 'Bed Sheets' THEN 2
                    WHEN 'Pillows' THEN 2
                    WHEN 'Blankets' THEN 1
                    WHEN 'Mats' THEN 1
                    WHEN 'Curtains' THEN 1
                    ELSE 0
                END as required_quantity
                FROM room_items ri
                JOIN rooms r ON r.room_id = ri.room_id
                WHERE ri.room_id = ?
                ORDER BY ri.item_name";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $room_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->close();
        } else {
            $data = ['error' => 'Prepare failed: ' . $conn->error];
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    if (isset($_GET['get_task_room'])) {
        $task_id = intval($_GET['get_task_room']);
        $sql = "SELECT room_id FROM housekeeping_tasks WHERE task_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $task_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
        } else {
            $data = ['error' => 'Prepare failed: ' . $conn->error];
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

session_start();

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

$showSuccess = false;
$showMaintenanceSuccess = false;
$showErrorMsg = null;

// Get and clear flash messages
if (isset($_SESSION['flash_message'])) {
    $successMessage = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
} else {
    $successMessage = '';
}

if (isset($_SESSION['error_message'])) {
    $showErrorMsg = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Handle maintenance request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maintenance_room_id'], $_POST['issue_description'])) {
    $room_id = intval($_POST['maintenance_room_id']);
    $issue_description = trim($_POST['issue_description']);
    $priority = $_POST['priority'];
    $requested_by = trim($_POST['requested_by']);
    $requester_staff_id = trim($_POST['requester_staff_id']);
    $assigned_staff_id = null;

    // Check if task is in progress - prevent maintenance request
    $checkInProgressSql = "SELECT COUNT(*) as count FROM housekeeping_tasks WHERE room_id = ? AND task_status = 'in progress'";
    $checkStmt = $conn->prepare($checkInProgressSql);
    $checkStmt->bind_param("i", $room_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $row = $result->fetch_assoc();
    $checkStmt->close();

    if ($row['count'] > 0) {
        $_SESSION['error_message'] = 'Cannot request maintenance while cleaning is in progress. Please complete the cleaning task first.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        // Create maintenance_requests table if it doesn't exist
        $createTableSql = "CREATE TABLE IF NOT EXISTS maintenance_requests (
            request_id INT AUTO_INCREMENT PRIMARY KEY,
            room_id INT NOT NULL,
            issue_description TEXT NOT NULL,
            priority VARCHAR(20) DEFAULT 'Medium',
            status VARCHAR(20) DEFAULT 'Pending',
            requested_by VARCHAR(100) NOT NULL,
            assigned_staff_id VARCHAR(20) NULL,
            requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            assigned_to VARCHAR(100) NULL,
            completed_at TIMESTAMP NULL,
            notes TEXT NULL,
            FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
        )";
        $conn->query($createTableSql);

        // Insert maintenance request with explicit 'Pending' status
        $insertMaintenanceSql = "INSERT INTO maintenance_requests (room_id, issue_description, priority, requested_by, requester_staff_id, assigned_staff_id, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($insertMaintenanceSql);
        if ($stmt) {
            $stmt->bind_param("isssss", $room_id, $issue_description, $priority, $requested_by, $requester_staff_id, $assigned_staff_id);
            $stmt->execute();
            $stmt->close();

            // Update room status to 'under maintenance'
            $updateRoomSql = "UPDATE rooms SET status = 'under maintenance' WHERE room_id = ?";
            $roomStmt = $conn->prepare($updateRoomSql);
            $roomStmt->bind_param("i", $room_id);
            $roomStmt->execute();
            $roomStmt->close();

            $_SESSION['flash_message'] = 'Maintenance request submitted successfully.';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION['error_message'] = 'Maintenance request failed: ' . $conn->error;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}

// Handle task status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task_id'], $_POST['task_status'])) {
    $task_id = intval($_POST['update_task_id']);
    $task_status = $_POST['task_status'];

    // Check if room is under maintenance
    $checkMaintenanceSql = "SELECT r.status FROM rooms r
                           JOIN housekeeping_tasks t ON r.room_id = t.room_id
                           WHERE t.task_id = ?";
    $checkStmt = $conn->prepare($checkMaintenanceSql);
    $checkStmt->bind_param("i", $task_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $roomData = $result->fetch_assoc();
    $checkStmt->close();

    if ($roomData['status'] === 'under maintenance') {
        $_SESSION['error_message'] = 'Cannot update task status while room is under maintenance. Please complete maintenance first.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        // Check if trying to complete without items
        if ($task_status === 'completed') {
            $itemsCheckSql = "SELECT COUNT(*) as count FROM hp_tasks_items WHERE task_id = ?";
            $itemsCheckStmt = $conn->prepare($itemsCheckSql);
            $itemsCheckStmt->bind_param("i", $task_id);
            $itemsCheckStmt->execute();
            $itemsResult = $itemsCheckStmt->get_result();
            $itemsCount = $itemsResult->fetch_assoc()['count'];
            $itemsCheckStmt->close();

            if ($itemsCount == 0) {
                $_SESSION['error_message'] = 'Cannot complete task without adding required items.';
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        }

        // Check if trying to complete without items
        if ($task_status === 'completed') {
            $itemsCheckSql = "SELECT COUNT(*) as count FROM hp_tasks_items WHERE task_id = ?";
            $itemsCheckStmt = $conn->prepare($itemsCheckSql);
            $itemsCheckStmt->bind_param("i", $task_id);
            $itemsCheckStmt->execute();
            $itemsResult = $itemsCheckStmt->get_result();
            $itemsCount = $itemsResult->fetch_assoc()['count'];
            $itemsCheckStmt->close();

            if ($itemsCount == 0) {
                $_SESSION['error_message'] = 'Cannot complete the task without adding required items.';
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        }

        // Check if status is the same
        $checkCompletedSql = "SELECT task_status FROM housekeeping_tasks WHERE task_id = ?";
        $checkStmt = $conn->prepare($checkCompletedSql);
        $checkStmt->bind_param("i", $task_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $currentTask = $result->fetch_assoc();
        $checkStmt->close();


        if ($currentTask['task_status'] === 'completed' && $task_status === 'completed') {
            $_SESSION['error_message'] = 'This task is already completed and cannot be updated to completed again.';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }

        // Fetch existing start_time for the task
        $existing_start = null;
        $result = $conn->prepare("SELECT start_time FROM housekeeping_tasks WHERE task_id = ?");
        $result->bind_param("i", $task_id);
        $result->execute();
        $result->bind_result($existing_start);
        $result->fetch();
        $result->close();

        // Default null values
        $start_time = $existing_start;
        $end_time = null;
        $now = date('Y-m-d H:i:s');

        // ✅ Prevent overwriting start_time if it already exists
        if ($task_status === 'in progress') {
            if (!$start_time || $start_time === '0000-00-00 00:00:00') {
                $start_time = $now;
            }
            // else → keep old start_time, don't overwrite
        } elseif ($task_status === 'completed') {
            if (!$start_time || $start_time === '0000-00-00 00:00:00') {
                $start_time = $now; // allow only if start_time was empty
            }
            $end_time = $now; // end_time can be updated
        }

        // ✅ Update query that never touches start_time if it already exists
        if ($existing_start && $existing_start !== '0000-00-00 00:00:00') {
            // If start_time already set, only update status + end_time
            $stmt = $conn->prepare("UPDATE housekeeping_tasks
                                    SET task_status = ?,
                                        end_time = ?
                                    WHERE task_id = ?");
            $stmt->bind_param("ssi", $task_status, $end_time, $task_id);
        } else {
            // If no start_time yet, allow inserting it
            $stmt = $conn->prepare("UPDATE housekeeping_tasks
                                    SET task_status = ?,
                                        start_time = ?,
                                        end_time = ?
                                    WHERE task_id = ?");
            $stmt->bind_param("sssi", $task_status, $start_time, $end_time, $task_id);
        }
        $stmt->execute();
        $stmt->close();

        // Update room status
        if ($task_status === 'in progress') {
            $updateRoomSql = "UPDATE rooms r
                              JOIN housekeeping_tasks t ON r.room_id = t.room_id
                              SET r.status = 'cleaning'
                              WHERE t.task_id = ?";
            $stmt = $conn->prepare($updateRoomSql);
            $stmt->bind_param("i", $task_id);
            $stmt->execute();
            $stmt->close();
        } elseif ($task_status === 'completed') {
            $updateRoomSql = "UPDATE rooms r
                              JOIN housekeeping_tasks t ON r.room_id = t.room_id
                              SET r.status = 'available'
                              WHERE t.task_id = ?";
            $stmt = $conn->prepare($updateRoomSql);
            $stmt->bind_param("i", $task_id);
            $stmt->execute();
            $stmt->close();

            // Automatically collect all laundry items from the room as dirty
            $roomIdSql = "SELECT room_id FROM housekeeping_tasks WHERE task_id = ?";
            $roomStmt = $conn->prepare($roomIdSql);
            $roomStmt->bind_param("i", $task_id);
            $roomStmt->execute();
            $roomResult = $roomStmt->get_result();
            $roomData = $roomResult->fetch_assoc();
            $room_id = $roomData['room_id'];
            $roomStmt->close();

            // Get all items for this room
            $itemsSql = "SELECT hi.item_name, ri.quantity, ri.status FROM room_items ri JOIN hp_inventory hi ON ri.item_name = hi.item_name WHERE ri.room_id = ?";
            $itemsStmt = $conn->prepare($itemsSql);
            $itemsStmt->bind_param("i", $room_id);
            $itemsStmt->execute();
            $itemsResult = $itemsStmt->get_result();

            while ($item = $itemsResult->fetch_assoc()) {
                $item_name = $item['item_name'];
                $quantity = $item['quantity'];
                $status = $item['status'];

                // Check if item exists in hp_inventory
                $checkSql = "SELECT item_id FROM hp_inventory WHERE item_name = ?";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->bind_param("s", $item_name);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();

                if ($checkResult->num_rows > 0) {
                    // Update existing item quantity and set status to the room_items status
                    $updateSql = "UPDATE hp_inventory SET quantity = quantity + ?, status = ?, added_at = CURRENT_TIMESTAMP WHERE item_name = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("iss", $quantity, $status, $item_name);
                    $updateStmt->execute();
                    $updateStmt->close();
                } else {
                    // Insert new item with the room_items status
                    $insertSql = "INSERT INTO hp_inventory (item_name, quantity, status) VALUES (?, ?, ?)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bind_param("sis", $item_name, $quantity, $status);
                    $insertStmt->execute();
                    $insertStmt->close();
                }

                // Get item_id for hp_tasks_items
                $getIdSql = "SELECT item_id FROM hp_inventory WHERE item_name = ?";
                $getIdStmt = $conn->prepare($getIdSql);
                $getIdStmt->bind_param("s", $item_name);
                $getIdStmt->execute();
                $idResult = $getIdStmt->get_result();
                $itemData = $idResult->fetch_assoc();
                $item_id = $itemData['item_id'];
                $getIdStmt->close();

                // Insert into hp_tasks_items (record that these items were collected)
                $insertTaskSql = "INSERT INTO hp_tasks_items (task_id, item_id, quantity_needed) VALUES (?, ?, ?)";
                $taskStmt = $conn->prepare($insertTaskSql);
                $taskStmt->bind_param("iii", $task_id, $item_id, $quantity);
                $taskStmt->execute();
                $taskStmt->close();
            }
            $itemsStmt->close();

            // Set default clean laundry quantities for the room
            $defaultQuantities = [
                'Bath Towels' => 4,
                'Bed Sheets' => 2,
                'Pillows' => 2,
                'Blankets' => 1,
                'Mats' => 1,
                'Curtains' => 1
            ];

            foreach ($defaultQuantities as $item_name => $default_qty) {
                // Check if item exists in room_items
                $checkRoomSql = "SELECT id FROM room_items WHERE room_id = ? AND item_name = ?";
                $checkRoomStmt = $conn->prepare($checkRoomSql);
                $checkRoomStmt->bind_param("is", $room_id, $item_name);
                $checkRoomStmt->execute();
                $roomCheckResult = $checkRoomStmt->get_result();

                if ($roomCheckResult->num_rows > 0) {
                    // Update existing room item
                    $updateRoomSql = "UPDATE room_items SET quantity = ?, status = 'clean' WHERE room_id = ? AND item_name = ?";
                    $updateRoomStmt = $conn->prepare($updateRoomSql);
                    $updateRoomStmt->bind_param("iis", $default_qty, $room_id, $item_name);
                    $updateRoomStmt->execute();
                    $updateRoomStmt->close();
                } else {
                    // Insert new room item
                    $insertRoomSql = "INSERT INTO room_items (room_id, item_name, quantity, status) VALUES (?, ?, ?, 'clean')";
                    $insertRoomStmt = $conn->prepare($insertRoomSql);
                    $insertRoomStmt->bind_param("isis", $room_id, $item_name, $default_qty);
                    $insertRoomStmt->execute();
                    $insertRoomStmt->close();
                }
                $checkRoomStmt->close();
            }
        }

        $_SESSION['flash_message'] = 'Task status updated successfully.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle add/collect laundry items
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['add_laundry', 'collect_laundry'])) {
    // Ensure status column exists and has correct enum
    $alterSql = "ALTER TABLE room_items MODIFY COLUMN status ENUM('clean', 'dirty', 'collected') DEFAULT 'clean'";
    $conn->query($alterSql);

    $task_id = intval($_POST['task_id']);
    $action = $_POST['action'];

    // Check if task is in progress
    $taskStatusSql = "SELECT task_status FROM housekeeping_tasks WHERE task_id = ?";
    $taskStatusStmt = $conn->prepare($taskStatusSql);
    $taskStatusStmt->bind_param("i", $task_id);
    $taskStatusStmt->execute();
    $taskStatusResult = $taskStatusStmt->get_result();
    $taskStatusRow = $taskStatusResult->fetch_assoc();
    $taskStatusStmt->close();

    if ($taskStatusRow['task_status'] === 'completed') {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Cannot modify items for a completed task.']);
        exit;
    }

    $laundry_item_names = $_POST['laundry_item'] ?? [];
    $laundry_quantities = $_POST['laundry_quantity'] ?? [];

    $success_count = 0;
    $error_messages = [];

    for ($i = 0; $i < count($laundry_item_names); $i++) {
        $item_name = trim($laundry_item_names[$i]);
        $quantity = intval($laundry_quantities[$i]);

        if (!empty($item_name) && $quantity > 0) {
            if ($action === 'add_laundry') {
                // First check if room_id exists
                $roomSql = "SELECT room_id FROM housekeeping_tasks WHERE task_id = ?";
                $roomStmt = $conn->prepare($roomSql);
                $roomStmt->bind_param("i", $task_id);
                $roomStmt->execute();
                $roomResult = $roomStmt->get_result();
                $roomRow = $roomResult->fetch_assoc();
                $roomStmt->close();
                if (!$roomRow || !$roomRow['room_id']) {
                    $error_messages[] = "Task not found or has no room assigned.";
                    continue;
                }
                $room_id = $roomRow['room_id'];

                // Check if item exists in room_items
                $checkRoomSql = "SELECT id FROM room_items WHERE room_id = ? AND item_name = ?";
                $checkRoomStmt = $conn->prepare($checkRoomSql);
                $checkRoomStmt->bind_param("is", $room_id, $item_name);
                $checkRoomStmt->execute();
                $checkRoomResult = $checkRoomStmt->get_result();
                $checkRoomStmt->close();

                // Check if there is enough clean quantity in hp_inventory
                $cleanQtySql = "SELECT quantity FROM hp_inventory WHERE item_name = ? AND status = 'clean'";
                $cleanQtyStmt = $conn->prepare($cleanQtySql);
                $cleanQtyStmt->bind_param("s", $item_name);
                $cleanQtyStmt->execute();
                $cleanQtyResult = $cleanQtyStmt->get_result();
                $cleanQtyRow = $cleanQtyResult->fetch_assoc();
                $cleanQtyStmt->close();
                $available_clean = $cleanQtyRow ? $cleanQtyRow['quantity'] : 0;

                if ($available_clean < $quantity) {
                    $error_messages[] = "Not enough clean {$item_name} in inventory. Requested: {$quantity}, Available: {$available_clean}.";
                    continue;
                }

                if ($checkRoomResult->num_rows > 0) {
                    // Update existing
                    $updateRoomSql = "UPDATE room_items SET quantity = quantity + ?, status = 'clean' WHERE room_id = ? AND item_name = ?";
                    $updateRoomStmt = $conn->prepare($updateRoomSql);
                    if (!$updateRoomStmt) {
                        $error_messages[] = "Failed to prepare room_items update: " . $conn->error;
                        continue;
                    }
                    $updateRoomStmt->bind_param("iis", $quantity, $room_id, $item_name);
                } else {
                    // Insert new
                    $updateRoomSql = "INSERT INTO room_items (room_id, item_name, quantity, status) VALUES (?, ?, ?, 'clean')";
                    $updateRoomStmt = $conn->prepare($updateRoomSql);
                    if (!$updateRoomStmt) {
                        $error_messages[] = "Failed to prepare room_items insert: " . $conn->error;
                        continue;
                    }
                    $updateRoomStmt->bind_param("isi", $room_id, $item_name, $quantity);
                }

                if (!$updateRoomStmt->execute()) {
                    $error_messages[] = "Failed to add to room_items: " . $updateRoomStmt->error;
                    $updateRoomStmt->close();
                    continue;
                }
                $updateRoomStmt->close();

                // Deduct from hp_inventory clean quantity
                $deductInvSql = "UPDATE hp_inventory SET quantity = quantity - ? WHERE item_name = ? AND status = 'clean'";
                $deductInvStmt = $conn->prepare($deductInvSql);
                if (!$deductInvStmt) {
                    $error_messages[] = "Failed to prepare deduct inventory query: " . $conn->error;
                    continue;
                }
                $deductInvStmt->bind_param("is", $quantity, $item_name);
                $deductInvStmt->execute();
                $deductInvStmt->close();

                // Check if item exists in hp_inventory for hp_tasks_items
                $checkSql = "SELECT item_id FROM hp_inventory WHERE item_name = ?";
                $checkStmt = $conn->prepare($checkSql);
                if (!$checkStmt) {
                    $error_messages[] = "Failed to prepare check inventory query: " . $conn->error;
                    continue;
                }
                $checkStmt->bind_param("s", $item_name);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                $checkStmt->close();

                if ($result->num_rows > 0) {
                    $item_id = $result->fetch_assoc()['item_id'];
                    // Insert into hp_tasks_items
                    $insertTaskSql = "INSERT INTO hp_tasks_items (task_id, item_id, quantity_needed) VALUES (?, ?, ?)";
                    $taskStmt = $conn->prepare($insertTaskSql);
                    if ($taskStmt) {
                        $taskStmt->bind_param("iii", $task_id, $item_id, $quantity);
                        if ($taskStmt->execute()) {
                            $success_count++;
                        } else {
                            $error_messages[] = "Failed to record {$item_name}: " . $taskStmt->error;
                        }
                        $taskStmt->close();
                    }
                } else {
                    $success_count++;
                }
            } elseif ($action === 'collect_laundry') {
                // Check current quantity in room_items
                $roomQtySql = "SELECT quantity FROM room_items WHERE room_id = (SELECT room_id FROM housekeeping_tasks WHERE task_id = ?) AND item_name = ?";
                $roomQtyStmt = $conn->prepare($roomQtySql);
                if (!$roomQtyStmt) {
                    $error_messages[] = "Failed to prepare room quantity query: " . $conn->error;
                    continue;
                }
                $roomQtyStmt->bind_param("is", $task_id, $item_name);
                $roomQtyStmt->execute();
                $roomQtyResult = $roomQtyStmt->get_result();
                $roomQtyRow = $roomQtyResult->fetch_assoc();
                $current_quantity = $roomQtyRow ? $roomQtyRow['quantity'] : 0;
                $roomQtyStmt->close();

                if ($quantity > $current_quantity) {
                    $error_messages[] = "Cannot collect {$quantity} of {$item_name}. Only {$current_quantity} available in room.";
                    continue;
                }

                // Check if item exists in hp_inventory
                $checkSql = "SELECT item_id FROM hp_inventory WHERE item_name = ?";
                $checkStmt = $conn->prepare($checkSql);
                if (!$checkStmt) {
                    $error_messages[] = "Failed to prepare check inventory query: " . $conn->error;
                    continue;
                }
                $checkStmt->bind_param("s", $item_name);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                $checkStmt->close();

                if ($result->num_rows > 0) {
                    $item_id = $result->fetch_assoc()['item_id'];

                    // Get the current status from room_items
                    $statusSql = "SELECT status FROM room_items WHERE room_id = (SELECT room_id FROM housekeeping_tasks WHERE task_id = ?) AND item_name = ?";
                    $statusStmt = $conn->prepare($statusSql);
                    if (!$statusStmt) {
                        $error_messages[] = "Failed to prepare status query: " . $conn->error;
                        continue;
                    }
                    $statusStmt->bind_param("is", $task_id, $item_name);
                    $statusStmt->execute();
                    $statusResult = $statusStmt->get_result();
                    $statusRow = $statusResult->fetch_assoc();
                    $item_status = $statusRow ? $statusRow['status'] : 'dirty'; // default to dirty if not found
                    $statusStmt->close();

                    // Add to inventory with the item's status
                    $updateSql = "UPDATE hp_inventory SET quantity = quantity + ?, status = ?, added_at = CURRENT_TIMESTAMP WHERE item_name = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    if (!$updateStmt) {
                        $error_messages[] = "Failed to prepare update inventory query: " . $conn->error;
                        continue;
                    }
                    $updateStmt->bind_param("iss", $quantity, $item_status, $item_name);
                    $updateStmt->execute();
                    $updateStmt->close();
                } else {
                    // Item not found in inventory, insert new item
                    // Get the current status from room_items
                    $statusSql = "SELECT status FROM room_items WHERE room_id = (SELECT room_id FROM housekeeping_tasks WHERE task_id = ?) AND item_name = ?";
                    $statusStmt = $conn->prepare($statusSql);
                    if (!$statusStmt) {
                        $error_messages[] = "Failed to prepare status query: " . $conn->error;
                        continue;
                    }
                    $statusStmt->bind_param("is", $task_id, $item_name);
                    $statusStmt->execute();
                    $statusResult = $statusStmt->get_result();
                    $statusRow = $statusResult->fetch_assoc();
                    $item_status = $statusRow ? $statusRow['status'] : 'dirty'; // default to dirty if not found
                    $statusStmt->close();

                    $insertSql = "INSERT INTO hp_inventory (item_name, quantity, status) VALUES (?, ?, ?)";
                    $insertStmt = $conn->prepare($insertSql);
                    if (!$insertStmt) {
                        $error_messages[] = "Failed to prepare insert inventory query: " . $conn->error;
                        continue;
                    }
                    $insertStmt->bind_param("sis", $item_name, $quantity, $item_status);
                    $insertStmt->execute();
                    $insertStmt->close();

                    // Get the new item_id
                    $getIdSql = "SELECT item_id FROM hp_inventory WHERE item_name = ?";
                    $getIdStmt = $conn->prepare($getIdSql);
                    if (!$getIdStmt) {
                        $error_messages[] = "Failed to prepare get id query: " . $conn->error;
                        continue;
                    }
                    $getIdStmt->bind_param("s", $item_name);
                    $getIdStmt->execute();
                    $idResult = $getIdStmt->get_result();
                    $item_id = $idResult->fetch_assoc()['item_id'];
                    $getIdStmt->close();
                }

                // Deduct from room_items
                $deductRoomSql = "UPDATE room_items SET quantity = GREATEST(quantity - ?, 0) WHERE room_id = (SELECT room_id FROM housekeeping_tasks WHERE task_id = ?) AND item_name = ?";
                $deductRoomStmt = $conn->prepare($deductRoomSql);
                if (!$deductRoomStmt) {
                    $error_messages[] = "Failed to prepare deduct room query: " . $conn->error;
                    continue;
                }
                $deductRoomStmt->bind_param("iis", $quantity, $task_id, $item_name);
                $deductRoomStmt->execute();
                $deductRoomStmt->close();

                // Update room_items status to 'collected'
                $updateRoomStatusSql = "UPDATE room_items SET status = 'collected' WHERE room_id = (SELECT room_id FROM housekeeping_tasks WHERE task_id = ?) AND item_name = ?";
                $updateRoomStatusStmt = $conn->prepare($updateRoomStatusSql);
                if (!$updateRoomStatusStmt) {
                    $error_messages[] = "Failed to prepare update room status query: " . $conn->error;
                    continue;
                }
                $updateRoomStatusStmt->bind_param("is", $task_id, $item_name);
                $updateRoomStatusStmt->execute();
                $updateRoomStatusStmt->close();

                // Insert into hp_tasks_items
                $insertTaskSql = "INSERT INTO hp_tasks_items (task_id, item_id, quantity_needed) VALUES (?, ?, ?)";
                $taskStmt = $conn->prepare($insertTaskSql);
                if ($taskStmt) {
                    $taskStmt->bind_param("iii", $task_id, $item_id, $quantity);
                    if ($taskStmt->execute()) {
                        $success_count++;
                    } else {
                        $error_messages[] = "Failed to record {$item_name}: " . $taskStmt->error;
                    }
                    $taskStmt->close();
                }
            }
        }
    }

    ob_clean();
    header('Content-Type: application/json');
    if ($success_count > 0) {
        $action_text = $action === 'add_laundry' ? 'added' : 'collected';
        $message = "{$success_count} laundry item(s) {$action_text} successfully.";
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        $message = !empty($error_messages) ? implode('; ', $error_messages) : 'No items were processed.';
        echo json_encode(['success' => false, 'message' => $message]);
    }
    exit;
}

// Handle adding items to task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id']) && isset($_POST['item_name'])) {
    $task_id = intval($_POST['task_id']);

    // Check if task is in progress
    $taskStatusSql = "SELECT task_status FROM housekeeping_tasks WHERE task_id = ?";
    $taskStatusStmt = $conn->prepare($taskStatusSql);
    $taskStatusStmt->bind_param("i", $task_id);
    $taskStatusStmt->execute();
    $taskStatusResult = $taskStatusStmt->get_result();
    $taskStatusRow = $taskStatusResult->fetch_assoc();
    $taskStatusStmt->close();

    if ($taskStatusRow['task_status'] === 'completed') {
        $_SESSION['error_message'] = 'Cannot add items to a completed task.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } elseif (!$taskStatusRow || $taskStatusRow['task_status'] !== 'in progress') {
        $_SESSION['error_message'] = 'Items can only be added when the task status is "In Progress".';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    $item_names = $_POST['item_name'] ?? [];
    $quantities = $_POST['quantity_needed'] ?? [];
    $notes_array = $_POST['notes'] ?? [];

    // Handle room items (collected from dirty room)
    $room_item_names = $_POST['room_item'] ?? [];
    $room_quantities = $_POST['room_quantity'] ?? [];

    // Create hp_tasks_items table if it doesn't exist
    $createTableSql = "CREATE TABLE IF NOT EXISTS hp_tasks_items (
        hp_items_id INT AUTO_INCREMENT PRIMARY KEY,
        task_id INT NOT NULL,
        item_id INT NOT NULL,
        quantity_needed INT NOT NULL DEFAULT 0,
        used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (task_id) REFERENCES housekeeping_tasks(task_id) ON DELETE CASCADE,
        FOREIGN KEY (item_id) REFERENCES hp_inventory(item_id) ON DELETE CASCADE
    )";
    $conn->query($createTableSql);

    // Add quantity_needed column if it doesn't exist
    $alterSql = "ALTER TABLE hp_tasks_items ADD COLUMN IF NOT EXISTS quantity_needed INT NOT NULL DEFAULT 0";
    $conn->query($alterSql);

    $success_count = 0;
    $error_messages = [];

    // First, validate all quantities
    $valid_items = [];
    for ($i = 0; $i < count($item_names); $i++) {
        $item_name = trim($item_names[$i]);
        $quantity_needed = intval($quantities[$i]);
        $notes = trim($notes_array[$i] ?? '');

        if (!empty($item_name) && $quantity_needed > 0) {
            // Check current quantity in hp_inventory
            $checkSql = "SELECT item_id, quantity FROM hp_inventory WHERE item_name = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("s", $item_name);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            $row = $result->fetch_assoc();
            $checkStmt->close();

            if ($row) {
                $item_id = intval($row['item_id']);
                $current_quantity = intval($row['quantity']);
                if ($quantity_needed > $current_quantity) {
                    $error_messages[] = "Insufficient quantity for {$item_name}. Requested: {$quantity_needed}, Available: {$current_quantity}";
                } else {
                    $valid_items[] = ['item_id' => $item_id, 'name' => $item_name, 'qty' => $quantity_needed];
                }
            }
        }
    }

    // Handle room items (collected from dirty room) - add to hp_inventory as dirty
    $room_valid_items = [];
    for ($i = 0; $i < count($room_item_names); $i++) {
        $item_name = trim($room_item_names[$i]);
        $quantity_collected = intval($room_quantities[$i]);

        if (!empty($item_name) && $quantity_collected > 0) {
            $room_valid_items[] = ['name' => $item_name, 'qty' => $quantity_collected];
        }
    }

    // If no errors, process both regular items and room items
    if (empty($error_messages)) {
        // Process regular items (deduct from hp_inventory)
        foreach ($valid_items as $item) {
            // Deduct quantity
            $deductSql = "UPDATE hp_inventory SET quantity = quantity - ? WHERE item_name = ?";
            $deductStmt = $conn->prepare($deductSql);
            $deductStmt->bind_param("is", $item['qty'], $item['name']);
            $deductStmt->execute();
            $deductStmt->close();

            // Insert into hp_tasks_items
            $insertSql = "INSERT INTO hp_tasks_items (task_id, item_id, quantity_needed) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertSql);
            if ($stmt) {
                $stmt->bind_param("iii", $task_id, $item['item_id'], $item['qty']);
                if ($stmt->execute()) {
                    $success_count++;
                } else {
                    $error_messages[] = "Failed to add {$item['name']}: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_messages[] = "Failed to prepare statement for {$item['name']}";
            }
        }

        // Process room items (add to hp_inventory with their status)
        foreach ($room_valid_items as $item) {
            // Get the current status from room_items
            $statusSql = "SELECT status FROM room_items WHERE room_id = (SELECT room_id FROM housekeeping_tasks WHERE task_id = ?) AND item_name = ?";
            $statusStmt = $conn->prepare($statusSql);
            $statusStmt->bind_param("is", $task_id, $item['name']);
            $statusStmt->execute();
            $statusResult = $statusStmt->get_result();
            $statusRow = $statusResult->fetch_assoc();
            $item_status = $statusRow ? $statusRow['status'] : 'dirty'; // default to dirty if not found
            $statusStmt->close();

            // Check if item exists in hp_inventory
            $checkSql = "SELECT item_id FROM hp_inventory WHERE item_name = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("s", $item['name']);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            $checkStmt->close();

            if ($result->num_rows > 0) {
                // Update existing item quantity and set status to the item's status
                $updateSql = "UPDATE hp_inventory SET quantity = quantity + ?, status = ?, added_at = CURRENT_TIMESTAMP WHERE item_name = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("iss", $item['qty'], $item_status, $item['name']);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                // Insert new item with the item's status
                $insertDirtySql = "INSERT INTO hp_inventory (item_name, quantity, status) VALUES (?, ?, ?)";
                $insertDirtyStmt = $conn->prepare($insertDirtySql);
                $insertDirtyStmt->bind_param("sis", $item['name'], $item['qty'], $item_status);
                $insertDirtyStmt->execute();
                $insertDirtyStmt->close();
            }

            // Get item_id for hp_tasks_items
            $getIdSql = "SELECT item_id FROM hp_inventory WHERE item_name = ?";
            $getIdStmt = $conn->prepare($getIdSql);
            $getIdStmt->bind_param("s", $item['name']);
            $getIdStmt->execute();
            $idResult = $getIdStmt->get_result();
            $item_id = $idResult->fetch_assoc()['item_id'];
            $getIdStmt->close();

            // Update room_items status to 'collected'
            $updateRoomSql = "UPDATE room_items SET status = 'collected' WHERE room_id = (SELECT room_id FROM housekeeping_tasks WHERE task_id = ?) AND item_name = ?";
            $updateRoomStmt = $conn->prepare($updateRoomSql);
            $updateRoomStmt->bind_param("is", $task_id, $item_name);
            $updateRoomStmt->execute();
            $updateRoomStmt->close();

            // Deduct from room_items
            $deductSql = "UPDATE room_items SET quantity = GREATEST(quantity - ?, 0) WHERE room_id = (SELECT room_id FROM housekeeping_tasks WHERE task_id = ?) AND item_name = ?";
            $deductStmt = $conn->prepare($deductSql);
            $deductStmt->bind_param("iis", $item['qty'], $task_id, $item_name);
            $deductStmt->execute();
            $deductStmt->close();

            // Insert into hp_tasks_items
            $insertTaskSql = "INSERT INTO hp_tasks_items (task_id, item_id, quantity_needed) VALUES (?, ?, ?)";
            $taskStmt = $conn->prepare($insertTaskSql);
            if ($taskStmt) {
                $taskStmt->bind_param("iii", $task_id, $item_id, $item['qty']);
                if ($taskStmt->execute()) {
                    $success_count++;
                } else {
                    $error_messages[] = "Failed to add collected {$item['name']}: " . $taskStmt->error;
                }
                $taskStmt->close();
            }
        }
    }

    ob_clean();
    header('Content-Type: application/json');
    if ($success_count > 0) {
        $message = "{$success_count} item(s) collected successfully.";
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        $message = !empty($error_messages) ? implode('; ', $error_messages) : 'No items were collected.';
        echo json_encode(['success' => false, 'message' => $message]);
    }
    exit;
}

// Handle new task assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_id'], $_POST['staff'], $_POST['assigned_by']) && !isset($_POST['update_task_id']) && !isset($_POST['maintenance_room_id'])) {
    $room_id = intval($_POST['room_id']);
    $staff = trim($_POST['staff']);
    $assigned_by = trim($_POST['assigned_by']);
    $task_status = 'assigned';

    // Check if room is already assigned (only active tasks)
    $checkAssignedSql = "SELECT COUNT(*) as count FROM housekeeping_tasks
                         WHERE room_id = ?
                         AND task_status IN ('assigned', 'in progress')
                         AND (end_time IS NULL OR end_time = '0000-00-00 00:00:00')";
    $checkStmt = $conn->prepare($checkAssignedSql);
    $checkStmt->bind_param("i", $room_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $row = $result->fetch_assoc();
    $checkStmt->close();

    if ($row['count'] > 0) {
        $_SESSION['error_message'] = 'This room is already assigned to another staff member and cannot be assigned again until the current task is completed.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $insertSql = "INSERT INTO housekeeping_tasks (room_id, assigned_to, assigned_by, task_status, assigned_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insertSql);
        if ($stmt) {
            $stmt->bind_param("isss", $room_id, $staff, $assigned_by, $task_status);
            $stmt->execute();
            $stmt->close();

            $_SESSION['flash_message'] = 'Task successfully assigned.';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION['error_message'] = 'Prepare failed: ' . $conn->error;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}


// Fetch dirty rooms and their assignment status (excluding completed tasks)
$rooms_sql = "SELECT r.room_id, r.room_number, r.room_type,
                     CASE WHEN ht.room_id IS NOT NULL THEN 1 ELSE 0 END as is_assigned
              FROM rooms r
              LEFT JOIN housekeeping_tasks ht ON r.room_id = ht.room_id 
                   AND ht.task_status IN ('assigned', 'in progress')
                   AND (ht.end_time IS NULL OR ht.end_time = '0000-00-00 00:00:00')
              WHERE r.status = 'dirty' 
              ORDER BY r.room_number ASC";
$rooms_result = $conn->query($rooms_sql);

// Fetch housekeeping staff
$housekeeping_positions = [
    'Linen Room Attendant',
    'Laundry Supervisor',
    'Public Area Attendant',
    'Assistant Housekeeper',
    'Room Attendant'
];
$placeholders = "'" . implode("','", $housekeeping_positions) . "'";
$staff_sql = "SELECT staff_id, first_name, last_name, position_name FROM staff WHERE position_name IN ($placeholders) ORDER BY first_name, last_name";
$staff_result = $conn->query($staff_sql);

// Fetch executive housekeepers for assignment
$assigner_sql = "SELECT staff_id, first_name, last_name, position_name FROM staff WHERE position_name = 'Executive Housekeeper' ORDER BY first_name, last_name";
$assigner_result = $conn->query($assigner_sql);

// Fetch assigned tasks with maintenance request info and room status
$tasks_sql = "
    SELECT t.task_id, t.room_id, t.assigned_to as staff, t.assigned_by, t.task_status, t.assigned_at, t.start_time, t.end_time,
           r.room_number, r.room_type, r.status as room_status,
           s.first_name, s.last_name, s.position_name,
           asgn.first_name as assigner_first, asgn.last_name as assigner_last, asgn.position_name as assigner_position,
           mr.request_id, mr.issue_description, mr.priority, mr.status as maintenance_status,
           mr.requested_at as maintenance_requested_at
    FROM housekeeping_tasks t
    JOIN rooms r ON t.room_id = r.room_id
    JOIN staff s ON t.assigned_to = s.staff_id
    LEFT JOIN staff asgn ON t.assigned_by = asgn.staff_id
    LEFT JOIN maintenance_requests mr ON t.room_id = mr.room_id
                                      AND mr.status IN ('Pending', 'In Progress')
                                      AND mr.request_id = (
                                          SELECT MAX(request_id)
                                          FROM maintenance_requests mr2
                                          WHERE mr2.room_id = mr.room_id
                                            AND mr2.status IN ('Pending', 'In Progress')
                                      )
    ORDER BY t.assigned_at DESC
";
$tasks_result = $conn->query($tasks_sql);

// Fetch all hp_inventory items for the dropdown
$hp_inventory_sql = "SELECT item_name FROM hp_inventory ORDER BY item_name";
$hp_inventory_result = $conn->query($hp_inventory_sql);

// Get all items
$all_items = [];
if ($hp_inventory_result && $hp_inventory_result->num_rows > 0) {
    while ($item = $hp_inventory_result->fetch_assoc()) {
        $all_items[] = $item;
    }
}

// Helper function to format datetime to 12-hour format
function formatTo12Hour($datetime) {
    if (!$datetime || $datetime === '0000-00-00 00:00:00') {
        return '-';
    }
    return date('M j, Y g:i A', strtotime($datetime));
}

?>