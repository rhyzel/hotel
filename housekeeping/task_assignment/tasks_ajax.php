<?php
// AJAX handlers for tasks

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
                CASE
                    WHEN ri.item_name = 'Bath Towels' THEN
                        CASE r.room_type
                            WHEN 'Single Room' THEN 2
                            WHEN 'Double Room' THEN 3
                            WHEN 'Twin Room' THEN 4
                            WHEN 'Deluxe Room' THEN 5
                            WHEN 'Suite' THEN 7
                            WHEN 'Family Room' THEN 7
                            WHEN 'VIP Room' THEN 9
                            ELSE 4
                        END
                    WHEN ri.item_name = 'Bed Sheets' THEN
                        CASE r.room_type
                            WHEN 'Single Room' THEN 1
                            WHEN 'Double Room' THEN 2
                            WHEN 'Twin Room' THEN 2
                            WHEN 'Deluxe Room' THEN 4
                            WHEN 'Suite' THEN 5
                            WHEN 'Family Room' THEN 6
                            WHEN 'VIP Room' THEN 5
                            ELSE 2
                        END
                    WHEN ri.item_name = 'Pillows' THEN
                        CASE r.room_type
                            WHEN 'Single Room' THEN 2
                            WHEN 'Double Room' THEN 4
                            WHEN 'Twin Room' THEN 4
                            WHEN 'Deluxe Room' THEN 5
                            WHEN 'Suite' THEN 7
                            WHEN 'Family Room' THEN 7
                            WHEN 'VIP Room' THEN 9
                            ELSE 2
                        END
                    WHEN ri.item_name = 'Blankets' THEN
                        CASE r.room_type
                            WHEN 'Single Room' THEN 1
                            WHEN 'Double Room' THEN 2
                            WHEN 'Twin Room' THEN 2
                            WHEN 'Deluxe Room' THEN 3
                            WHEN 'Suite' THEN 4
                            WHEN 'Family Room' THEN 4
                            WHEN 'VIP Room' THEN 5
                            ELSE 1
                        END
                    WHEN ri.item_name = 'Mats' THEN
                        CASE r.room_type
                            WHEN 'Single Room' THEN 2
                            WHEN 'Double Room' THEN 3
                            WHEN 'Twin Room' THEN 3
                            WHEN 'Deluxe Room' THEN 4
                            WHEN 'Suite' THEN 5
                            WHEN 'Family Room' THEN 5
                            WHEN 'VIP Room' THEN 5
                            ELSE 1
                        END
                    WHEN ri.item_name = 'Curtains' THEN
                        CASE r.room_type
                            WHEN 'Single Room' THEN 1
                            WHEN 'Double Room' THEN 2
                            WHEN 'Twin Room' THEN 3
                            WHEN 'Deluxe Room' THEN 4
                            WHEN 'Suite' THEN 5
                            WHEN 'Family Room' THEN 5
                            WHEN 'VIP Room' THEN 6
                            ELSE 1
                        END
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

    if (isset($_GET['get_task_statuses'])) {
        $sql = "
            SELECT t.task_id, t.task_status, t.start_time, t.end_time, r.status as room_status
            FROM housekeeping_tasks t
            JOIN rooms r ON t.room_id = r.room_id
            ORDER BY t.task_id
        ";
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
}
?>