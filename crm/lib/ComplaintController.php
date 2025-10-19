<?php
namespace CRM\Lib;

class ComplaintController {
    private $conn;

    public function __construct(\PDO $conn) {
        $this->conn = $conn;
    }

    public function stats() {
        $stmt = $this->conn->prepare("SELECT 
                      COUNT(*) AS total_complaints,
                      SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) AS resolved_complaints,
                      SUM(CASE WHEN status IN ('pending','in-progress') THEN 1 ELSE 0 END) AS active_complaints,
                      SUM(CASE WHEN type = 'suggestion' THEN 1 ELSE 0 END) AS total_suggestions,
                      SUM(CASE WHEN type = 'compliment' THEN 1 ELSE 0 END) AS total_compliments,
                      ROUND(
                        CASE 
                          WHEN COUNT(*) > 0 THEN (SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) / COUNT(*)) * 100
                          ELSE 0 
                        END,
                        1
                      ) AS resolution_rate
                    FROM complaints");
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function list(array $filters, array $availableColumns) {
        $query = "SELECT * FROM complaints WHERE 1=1";
        $params = [];
        $messageField = in_array('comment', $availableColumns) ? 'comment' : 'message';

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query .= " AND type = :type";
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['search'])) {
            $query .= " AND (guest_name LIKE :search OR $messageField LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $query .= " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Normalize
        foreach ($items as &$item) {
            if (in_array('comment', $availableColumns) && !isset($item['message'])) {
                $item['message'] = $item['comment'];
            } elseif (in_array('message', $availableColumns) && !isset($item['comment'])) {
                $item['comment'] = $item['message'];
            }
            $item['type'] = $item['type'] ?? 'complaint';
            $item['status'] = $item['status'] ?? 'pending';
        }

        return $items;
    }

    public function create(array $input, array $complaintsColumns, string $guestNameQuery, string $guestPrimaryKey) {
        // resolve guest name
        $guestName = $input['guest_name'] ?? '';
        $guest_id = null;
        if (!empty($input['guest_id'])) {
            $stmt = $this->conn->prepare("SELECT $guestNameQuery as name FROM guests WHERE $guestPrimaryKey = :guest_id");
            $stmt->execute([':guest_id' => $input['guest_id']]);
            $guest = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($guest) {
                $guestName = $guest['name'];
                $guest_id = $input['guest_id'];
            }
        }

    $type = $input['type'] ?? 'complaint'; // Accept any type string (service, facility, staff, other, etc.)

    // rating excluded for complaints

        $fields = ['guest_name', 'status', 'type', 'created_at'];
        $values = [':guest_name', ':status', ':type', 'NOW()'];
        $params = [':guest_name' => $guestName, ':status' => $input['status'] ?? 'pending', ':type' => $type];

        if (in_array('guest_id', $complaintsColumns) && $guest_id) {
            $fields[] = 'guest_id'; $values[] = ':guest_id'; $params[':guest_id'] = $guest_id;
        }

        $messageField = in_array('comment', $complaintsColumns) ? 'comment' : 'message';
        $fields[] = $messageField; $values[] = ':comment'; $params[':comment'] = trim($input['comment']);

    // rating excluded for complaints
        if (!empty($input['reply']) && in_array('reply', $complaintsColumns)) { $fields[] = 'reply'; $values[] = ':reply'; $params[':reply'] = $input['reply']; }

        $sql = "INSERT INTO complaints (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        $id = $this->conn->lastInsertId();
        $stmt = $this->conn->prepare("SELECT * FROM complaints WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $new = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (in_array('comment', $complaintsColumns) && !isset($new['message'])) {
            $new['message'] = $new['comment'];
        }

        return $new;
    }
}