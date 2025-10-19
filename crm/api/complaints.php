<?php
// complaints.php - REST API for managing complaints

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$method = $_SERVER['REQUEST_METHOD'] ?? null;
$overrideMethod = $_REQUEST['_method'] ?? null;
if ($overrideMethod) {
    $method = strtoupper($overrideMethod);
}

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/database.php';

use CRM\Lib\ComplaintController;

$db = new Database();
$conn = $db->getConnection();
$controller = new ComplaintController($conn);

// Helper for uniform responses
function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit();
}

// Default to GET if executed directly
if ($method === null) {
    $method = 'GET';
}

try {
    switch ($method) {
        // ------------------------------------------------
        // GET: List all complaints (no type restriction)
        // ------------------------------------------------
        case 'GET':
            $query = "SELECT * FROM complaints";
            $params = [];

            if (isset($_GET['status']) && $_GET['status'] !== 'all') {
                // ✅ FIX: Case-insensitive status filter
                $query .= " WHERE LOWER(status) = LOWER(:status)";
                $params[':status'] = $_GET['status'];
            }

            if (isset($_GET['search'])) {
                $searchTerm = '%' . $_GET['search'] . '%';
                if (empty($params)) {
                    $query .= " WHERE (guest_name LIKE :search OR comment LIKE :search)";
                } else {
                    $query .= " AND (guest_name LIKE :search OR comment LIKE :search)";
                }
                $params[':search'] = $searchTerm;
            }

            $query .= " ORDER BY created_at DESC";
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ✅ FIX: Case-insensitive active count
            $countQuery = "SELECT COUNT(*) AS active_count FROM complaints 
                          WHERE LOWER(status) IN ('pending','in-progress')";
            $countStmt = $conn->query($countQuery);
            $activeCount = $countStmt->fetch(PDO::FETCH_ASSOC)['active_count'] ?? 0;

            respond(['success' => true, 'data' => [
                'items' => $items,
                'active_count' => (int)$activeCount
            ]]);
            break;

        // ------------------------------------------------
        // POST: Create a new complaint
        // ------------------------------------------------
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true) ?? [];

            if (empty($input['guest_name']) || empty($input['comment'])) {
                respond(['success' => false, 'error' => 'Missing required fields: guest_name and comment'], 400);
            }

            // ✅ FIX: Force type to 'complaint' if not provided or empty
            $type = !empty($input['type']) ? strtolower(trim($input['type'])) : 'complaint';
            
            // ✅ FIX: Force status to lowercase
            $status = !empty($input['status']) ? strtolower(trim($input['status'])) : 'pending';

            // ✅ DEBUG: Log what we're receiving
            error_log("Complaint POST - Received type: " . ($input['type'] ?? 'NULL') . ", Using: " . $type);

            $params = [
                ':guest_id' => $input['guest_id'] ?? null,
                ':guest_name' => trim($input['guest_name']),
                ':comment' => trim($input['comment']),
                ':type' => $type,
                ':status' => $status,
                ':reply' => !empty($input['reply']) ? trim($input['reply']) : null
            ];

            // ✅ FIX: Explicit SQL with proper column order
            $sql = "INSERT INTO complaints (guest_id, guest_name, comment, type, status, reply, created_at) 
                    VALUES (:guest_id, :guest_name, :comment, :type, :status, :reply, NOW())";
            
            $stmt = $conn->prepare($sql);
            $success = $stmt->execute($params);

            if (!$success) {
                error_log("Complaint INSERT failed: " . json_encode($stmt->errorInfo()));
                respond(['success' => false, 'error' => 'Failed to insert complaint', 'debug' => $stmt->errorInfo()], 500);
            }

            $id = $conn->lastInsertId();
            $stmt = $conn->prepare("SELECT * FROM complaints WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $new = $stmt->fetch(PDO::FETCH_ASSOC);

            error_log("Complaint created: " . json_encode($new));

            respond(['success' => true, 'data' => $new], 201);
            break;

        // ------------------------------------------------
        // PUT: Update complaint by ID
        // ------------------------------------------------
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            if (empty($input['id'])) {
                respond(['success' => false, 'error' => 'Missing complaint ID'], 400);
            }

            $updateFields = [];
            $params = [':id' => $input['id']];

            if (isset($input['guest_name'])) {
                $updateFields[] = "guest_name = :guest_name";
                $params[':guest_name'] = $input['guest_name'];
            }
            if (isset($input['comment'])) {
                $updateFields[] = "comment = :comment";
                $params[':comment'] = $input['comment'];
            }
            if (isset($input['status'])) {
                $updateFields[] = "status = :status";
                // ✅ FIX: Force lowercase status
                $params[':status'] = strtolower($input['status']);
            }
            if (isset($input['type'])) {
                $updateFields[] = "type = :type";
                // ✅ FIX: Force lowercase type
                $params[':type'] = strtolower($input['type']);
            }
            if (isset($input['reply'])) {
                $updateFields[] = "reply = :reply";
                $params[':reply'] = $input['reply'];
            }

            if (empty($updateFields)) {
                respond(['success' => false, 'error' => 'No fields to update'], 400);
            }

            $sql = "UPDATE complaints SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            respond(['success' => true, 'message' => 'Complaint updated successfully']);
            break;

        // ------------------------------------------------
        // DELETE: Delete complaint by ID
        // ------------------------------------------------
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $id = $input['id'] ?? $_GET['id'] ?? null;
            if (empty($id)) {
                respond(['success' => false, 'error' => 'Missing complaint ID'], 400);
            }

            $stmt = $conn->prepare("DELETE FROM complaints WHERE id = :id");
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() > 0) {
                respond(['success' => true, 'message' => 'Complaint deleted successfully']);
            } else {
                respond(['success' => false, 'error' => 'Complaint not found'], 404);
            }
            break;

        default:
            respond(['success' => false, 'error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    respond(['success' => false, 'error' => 'Internal server error: ' . $e->getMessage()], 500);
}
?>