<?php
namespace CRM\Lib;

class ComplaintRepository {
    private \PDO $db;

    public function __construct(\PDO $db) {
        $this->db = $db;
    }

    public function getAll(array $filters = []): array {
        $sql = "SELECT * FROM complaints WHERE 1=1";
        $params = [];
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['type'])) {
            $sql .= " AND type = :type";
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (guest_name LIKE :search OR comment LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($row) => new Complaint($row), $rows);
    }

    public function getById(int $id): ?Complaint {
        $stmt = $this->db->prepare("SELECT * FROM complaints WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? new Complaint($row) : null;
    }

    public function create(array $data): Complaint {
        $fields = ['guest_id', 'guest_name', 'comment', 'type', 'rating', 'status', 'reply', 'created_at'];
        $placeholders = [':guest_id', ':guest_name', ':comment', ':type', ':rating', ':status', ':reply', 'NOW()'];
        $params = [
            ':guest_id' => $data['guest_id'] ?? null,
            ':guest_name' => $data['guest_name'] ?? '',
            ':comment' => $data['comment'] ?? '',
            ':type' => $data['type'] ?? 'complaint',
            ':rating' => $data['rating'] ?? null,
            ':status' => $data['status'] ?? 'pending',
            ':reply' => $data['reply'] ?? null
        ];
        $sql = "INSERT INTO complaints (guest_id, guest_name, comment, type, rating, status, reply, created_at)
                VALUES (:guest_id, :guest_name, :comment, :type, :rating, :status, :reply, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $id = $this->db->lastInsertId();
        return $this->getById($id);
    }

    public function update(array $data): bool {
        if (empty($data['id'])) return false;
        $fields = [];
        $params = [':id' => $data['id']];
        foreach (['guest_id','guest_name','comment','type','rating','status','reply'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        if (!$fields) return false;
        $sql = "UPDATE complaints SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM complaints WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
