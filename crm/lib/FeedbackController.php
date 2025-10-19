<?php
namespace CRM\Lib;

class FeedbackController {
    private FeedbackRepository $repo;

    public function __construct(FeedbackRepository $repo) {
        $this->repo = $repo;
    }

    public function getTableColumns(string $table): array {
        return $this->repo->getTableColumns($table);
    }

    public function list(array $query = []): array {
        $querySql = "SELECT * FROM feedback WHERE 1=1";
        $params = [];

        if (isset($query['type']) && $query['type'] !== 'all') {
            $querySql .= " AND type = :type";
            $params[':type'] = $query['type'];
        }

        if (isset($query['status']) && $query['status'] !== 'all') {
            $querySql .= " AND status = :status";
            $params[':status'] = $query['status'];
        }

        $querySql .= " ORDER BY created_at DESC";

        $items = $this->repo->select($querySql, $params);

        foreach ($items as &$item) {
            // Always set comment = message for compatibility
            $item['comment'] = $item['message'];
            $item['type'] = $item['type'] ?? 'review';
            $item['status'] = $item['status'] ?? 'pending';
            // Add guest_profile_url if guest_id is present
            if (!empty($item['guest_id'])) {
                $item['guest_profile_url'] = 'guests.php?guest_id=' . urlencode($item['guest_id']);
            }
        }
        unset($item);

        return $items;
    }

    public function create(array $input, array $feedbackColumns): array {
        $type = $input['type'] ?? 'review';
        $validTypes = ['review', 'suggestion', 'compliment', 'service_feedback'];
        if (!in_array($type, $validTypes)) $type = 'review';

        $rating = isset($input['rating']) ? intval($input['rating']) : null;
        if ($rating !== null && ($rating < 1 || $rating > 5)) $rating = null;

        $fields = ['guest_name', 'type', 'status', 'created_at'];
        $values = [':guest_name', ':type', ':status', 'NOW()'];
        $params = [
            ':guest_name' => trim($input['guest_name']),
            ':type' => $type,
            ':status' => $input['status'] ?? 'pending'
        ];

        if (in_array('guest_id', $feedbackColumns) && !empty($input['guest_id'])) {
            // validate guest existence
            $stmt = $this->repo->execute("SELECT guest_id FROM guests WHERE guest_id = :guest_id", [':guest_id' => $input['guest_id']]);
            if ($stmt->fetch()) {
                $fields[] = 'guest_id'; $values[] = ':guest_id'; $params[':guest_id'] = $input['guest_id'];
            }
        }

        // Always use 'message' for insert
        $fields[] = 'message'; $values[] = ':message'; $params[':message'] = trim($input['comment']);

        if ($rating !== null && in_array('rating', $feedbackColumns)) {
            $fields[] = 'rating'; $values[] = ':rating'; $params[':rating'] = $rating;
        }

        $sql = "INSERT INTO feedback (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->repo->execute($sql, $params);

        $id = $this->repo->lastInsertId();
        $new = $this->repo->selectOne("SELECT * FROM feedback WHERE id = :id", [':id' => $id]);

        if ($new) {
            $new['comment'] = $new['message'];
            if (!empty($new['guest_id'])) {
                $new['guest_profile_url'] = 'guests.php?guest_id=' . urlencode($new['guest_id']);
            }
        }

        return $new ?? [];
    }

    public function update(array $input, array $feedbackColumns): array {
        if (empty($input['id'])) return ['success' => false, 'error' => 'Missing feedback ID'];

        $updateFields = [];
        $params = [':id' => $input['id']];

        // Always use 'message' for update
        if (isset($input['guest_name'])) {
            $updateFields[] = "guest_name = :guest_name";
            $params[':guest_name'] = trim($input['guest_name']);
        }
        if (isset($input['comment'])) {
            $updateFields[] = "message = :message";
            $params[':message'] = trim($input['comment']);
        }
        if (isset($input['rating'])) {
            $updateFields[] = "rating = :rating";
            $params[':rating'] = intval($input['rating']);
        }
        if (isset($input['status'])) {
            $updateFields[] = "status = :status";
            $params[':status'] = $input['status'];
        }
        if (isset($input['reply'])) {
            $updateFields[] = "reply = :reply";
            $params[':reply'] = trim($input['reply']);
            if (!isset($input['status'])) {
                $updateFields[] = "status = 'approved'";
            }
        }
        if (isset($input['type'])) {
            $updateFields[] = "type = :type";
            $params[':type'] = $input['type'];
        }

        if (empty($updateFields)) return ['success' => false, 'error' => 'No fields to update'];

        $sql = "UPDATE feedback SET " . implode(', ', $updateFields) . " WHERE id = :id";
        $this->repo->execute($sql, $params);

        return ['success' => true, 'message' => 'Feedback updated successfully'];
    }

    public function delete(array $input): array {
        if (empty($input['id'])) return ['success' => false, 'error' => 'Missing feedback ID'];

        $stmt = $this->repo->execute("DELETE FROM feedback WHERE id = :id", [':id' => $input['id']]);
        if ($stmt->rowCount() > 0) return ['success' => true, 'message' => 'Feedback deleted successfully'];
        return ['success' => false, 'error' => 'Feedback not found'];
    }
}
