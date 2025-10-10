<?php
include '../../db_connect.php'; // adjust path if needed
ob_start();

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
        $stmt->bind_param("is", $task_id, $start_time);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    if (isset($_GET['check_items'])) {
        $task_id = intval($_GET['task_id']);
        $sql = "SELECT COUNT(*) as count FROM hp_tasks_items WHERE task_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode(['has_items' => $count > 0]);
        exit;
    }

    if (isset($_GET['get_room_items'])) {
        $room_id = intval($_GET['room_id']);
        // Ensure status column exists
        $alterSql = "ALTER TABLE room_items ADD COLUMN IF NOT EXISTS status ENUM('clean', 'dirty') DEFAULT 'clean'";
        $conn->query($alterSql);

        // Always show default laundry items, with quantity from room_items if exists
        $sql = "SELECT hi.item_name, COALESCE(ri.quantity, 0) as quantity, r.room_number,
                CASE
                    WHEN ri.status = 'collected' THEN 'collected'
                    WHEN r.status IN ('dirty', 'cleaning') THEN 'dirty'
                    ELSE 'clean'
                END as status
                FROM (SELECT item_id, item_name FROM hp_inventory WHERE item_name IN ('Bath Towels', 'Bed Sheets', 'Pillows', 'Blankets', 'Mats', 'Curtains')) hi
                LEFT JOIN room_items ri ON hi.item_id = ri.item_id AND ri.room_id = ?
                JOIN rooms r ON r.room_id = ?
                ORDER BY hi.item_name";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ii', $room_id, $room_id);
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
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();

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
            $itemsSql = "SELECT hi.item_name, ri.quantity FROM room_items ri JOIN hp_inventory hi ON ri.item_id = hi.item_id WHERE ri.room_id = ?";
            $itemsStmt = $conn->prepare($itemsSql);
            $itemsStmt->bind_param("i", $room_id);
            $itemsStmt->execute();
            $itemsResult = $itemsStmt->get_result();

            while ($item = $itemsResult->fetch_assoc()) {
                $item_name = $item['item_name'];
                $quantity = $item['quantity'];

                // Check if item exists in hp_inventory
                $checkSql = "SELECT item_id FROM hp_inventory WHERE item_name = ?";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->bind_param("s", $item_name);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();

                if ($checkResult->num_rows > 0) {
                    // Update existing item quantity and set status to other
                    $updateSql = "UPDATE hp_inventory SET quantity = quantity + ?, status = 'other', added_at = CURRENT_TIMESTAMP WHERE item_name = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("is", $quantity, $item_name);
                    $updateStmt->execute();
                    $updateStmt->close();
                } else {
                    // Insert new item with other status
                    $insertSql = "INSERT INTO hp_inventory (item_name, quantity, status) VALUES (?, ?, 'other')";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bind_param("si", $item_name, $quantity);
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
        }

        $_SESSION['flash_message'] = 'Task status updated successfully.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle add/collect laundry items
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['add_laundry', 'collect_laundry'])) {
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
    } elseif (!$taskStatusRow || $taskStatusRow['task_status'] !== 'in progress') {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Items can only be modified when the task status is "In Progress".']);
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
            // Check if item exists in hp_inventory
            $checkSql = "SELECT item_id FROM hp_inventory WHERE item_name = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("s", $item_name);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            $checkStmt->close();

            if ($result->num_rows > 0) {
                $item_id = $result->fetch_assoc()['item_id'];

                if ($action === 'add_laundry') {
                    // Add to inventory as clean
                    $updateSql = "UPDATE hp_inventory SET quantity = quantity + ?, status = 'clean', added_at = CURRENT_TIMESTAMP WHERE item_name = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("is", $quantity, $item_name);
                    $updateStmt->execute();
                    $updateStmt->close();
                } elseif ($action === 'collect_laundry') {
                    // Add to inventory as dirty
                    $updateSql = "UPDATE hp_inventory SET quantity = quantity + ?, status = 'dirty', added_at = CURRENT_TIMESTAMP WHERE item_name = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("is", $quantity, $item_name);
                    $updateStmt->execute();
                    $updateStmt->close();
                }

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
                $error_messages[] = "Item {$item_name} not found in inventory";
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

        // Process room items (add to hp_inventory as dirty)
        foreach ($room_valid_items as $item) {
            // Check if item exists in hp_inventory
            $checkSql = "SELECT item_id FROM hp_inventory WHERE item_name = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("s", $item['name']);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            $checkStmt->close();

            if ($result->num_rows > 0) {
                // Update existing item quantity and set status to dirty
                $updateSql = "UPDATE hp_inventory SET quantity = quantity + ?, status = 'dirty', added_at = CURRENT_TIMESTAMP WHERE item_name = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("is", $item['qty'], $item['name']);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                // Insert new dirty item
                $insertDirtySql = "INSERT INTO hp_inventory (item_name, quantity, status) VALUES (?, ?, 'dirty')";
                $insertDirtyStmt = $conn->prepare($insertDirtySql);
                $insertDirtyStmt->bind_param("si", $item['name'], $item['qty']);
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
            $updateRoomSql = "UPDATE room_items SET status = 'collected' WHERE room_id = (SELECT room_id FROM housekeeping_tasks WHERE task_id = ?) AND item_id = ?";
            $updateRoomStmt = $conn->prepare($updateRoomSql);
            $updateRoomStmt->bind_param("ii", $task_id, $item_id);
            $updateRoomStmt->execute();
            $updateRoomStmt->close();

            // Deduct from room_items
            $deductSql = "UPDATE room_items SET quantity = GREATEST(quantity - ?, 0) WHERE room_id = (SELECT room_id FROM housekeeping_tasks WHERE task_id = ?) AND item_id = ?";
            $deductStmt = $conn->prepare($deductSql);
            $deductStmt->bind_param("iii", $item['qty'], $task_id, $item_id);
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
<header style="position: relative;">
    <h1><i class="fas fa-clipboard-list"></i> Room Cleaning Task Assignment</h1>
    <p>Assign staff to clean dirty rooms.</p>
    <a href="../housekeeping.php" class="back-btn" style="position: absolute; top: 0; right: 0;"><i class="fas fa-arrow-left"></i> Back</a>
</header>


<!-- Show success message if exists -->
<?php if ($successMessage): ?>
<div id="success-message" style="display:none;"><?php echo htmlspecialchars($successMessage); ?></div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const successMsg = document.getElementById('success-message');
    if (successMsg && successMsg.textContent) {
        showAlert('✅ ' + successMsg.textContent, 'success');
    }
});
</script>
<?php endif; ?>

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
        <tr>
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

<script>
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

    if (taskStatus !== 'in progress') {
      showAlert('Items can only be added when the task status is "In Progress".', 'error');
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
            if (data.room_id) {
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
            }
        })
        .catch(error => {
            console.error('Error getting room ID:', error);
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
            const pointerEvents = item.status === 'collected' ? 'pointer-events: none; opacity: 0.5;' : '';
            html += `
                <div class="laundry-item" data-item="${item.item_name}" data-status="${item.status}" data-original-qty="${item.quantity}" style="background: rgba(255,255,255,0.1); padding: 8px; border-radius: 6px; border: 1px solid rgba(255,215,0,0.3); cursor: pointer; transition: background 0.3s; ${pointerEvents}">
                    <div>
                        <strong style="color: #FFD700;">${item.item_name}</strong><br>
                        <span class="qty-display" style="color: #ccc; font-size: 0.9rem;">Quantity in room: ${item.quantity}</span><br>
                        <span style="color: ${statusColor}; font-size: 0.8rem; font-weight: bold;">Status: ${statusText}</span>
                    </div>
                    <input type="number" name="laundry_quantity[]" min="0" max="${item.quantity}" placeholder="Qty" style="width: 100%; margin-top: 5px; padding: 4px; border-radius: 4px; border: 1px solid #555; background: #2c2c2c; color: #f1f1f1;" required>
                </div>
            `;
        });
        html += '</div>';
        html += '<div style="text-align: center; margin: 15px 0;">';
        html += '<button type="button" id="addLaundryItems" class="btn-add-small" style="background: #28a745; color: #fff; margin-right: 10px;"><i class="fas fa-plus"></i> Add Selected Items</button>';
        html += '<button type="button" id="collectLaundryItems" class="btn-add-small" style="background: #007bff; color: #fff;"><i class="fas fa-hand-paper"></i> Collect Selected Items</button>';
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
                    input.value = originalQty; // auto-fill with full quantity
                    updateQtyDisplay();
                } else {
                    input.value = '';
                    qtyDisplay.textContent = `Quantity in room: ${originalQty}`;
                }
            });

            // Real-time quantity update
            input.addEventListener('input', function() {
                updateQtyDisplay();
            });

            function updateQtyDisplay() {
                const collectQty = parseInt(input.value) || 0;
                const remaining = Math.max(0, originalQty - collectQty);
                qtyDisplay.textContent = `Quantity in room: ${remaining} (collecting ${collectQty})`;
            }
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

// Add selected laundry items to inventory as clean
function addLaundryItems() {
    const taskId = document.getElementById('item_task_id').value;
    const selectedItems = document.querySelectorAll('.laundry-item.selected');

    if (selectedItems.length === 0) {
        showAlert('Please select at least one item to add.', 'error');
        return;
    }

    // Validate quantities
    let hasValidItems = false;
    selectedItems.forEach((item) => {
        const quantity = parseInt(item.querySelector('input[name="laundry_quantity[]"]').value) || 0;
        if (quantity > 0) {
            hasValidItems = true;
        }
    });

    if (!hasValidItems) {
        showAlert('Please enter valid quantities (greater than 0) for selected items.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('action', 'add_laundry');

    selectedItems.forEach((item) => {
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
            // Update DOM directly for real-time display
            selectedItems.forEach((item) => {
                const quantityInput = item.querySelector('input[name="laundry_quantity[]"]');
                const collectedQty = parseInt(quantityInput.value) || 0;
                if (collectedQty > 0) {
                    // Update status to collected
                    item.dataset.status = 'collected';
                    item.style.pointerEvents = 'none';
                    item.style.opacity = '0.5';
                    // Update display
                    const statusSpan = item.querySelector('span[style*="font-weight: bold"]');
                    if (statusSpan) {
                        statusSpan.style.color = '#007bff';
                        statusSpan.textContent = 'Status: Collected';
                    }
                    // Update quantity display
                    const qtySpan = item.querySelector('span[style*="color: #ccc"]');
                    if (qtySpan) {
                        const currentQty = parseInt(qtySpan.textContent.match(/Quantity in room: (\d+)/)[1]);
                        const newQty = Math.max(0, currentQty - collectedQty);
                        qtySpan.textContent = `Quantity in room: ${newQty}`;
                    }
                    // Clear and disable input
                    quantityInput.value = '';
                    quantityInput.disabled = true;
                    // Remove selected class
                    item.classList.remove('selected');
                }
            });
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error adding laundry items.', 'error');
    });
}

// Collect selected laundry items (add to inventory as dirty) - only dirty items
function collectLaundryItems() {
    const taskId = document.getElementById('item_task_id').value;
    const selectedItems = Array.from(document.querySelectorAll('.laundry-item.selected')).filter(item => item.dataset.status === 'dirty' && item.dataset.status !== 'collected');

    if (selectedItems.length === 0) {
        showAlert('Please select at least one dirty item to collect.', 'error');
        return;
    }

    // Validate quantities
    let hasValidItems = false;
    selectedItems.forEach((item) => {
        const quantity = parseInt(item.querySelector('input[name="laundry_quantity[]"]').value) || 0;
        if (quantity > 0) {
            hasValidItems = true;
        }
    });

    if (!hasValidItems) {
        showAlert('Please enter valid quantities (greater than 0) for selected items.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('action', 'collect_laundry');

    selectedItems.forEach((item) => {
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
            // Reload room items
            loadRoomItems(taskId);
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
</script>

</body>
</html>