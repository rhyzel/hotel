<?php
namespace CRM\Lib;

class CampaignRepository {
    private \PDO $db;

    public function __construct(\PDO $db) {
        $this->db = $db;
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM campaigns ORDER BY created_at DESC");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($row) => new Campaign($row), $rows);
    }

    public function getById(int $id): ?Campaign {
        $stmt = $this->db->prepare("SELECT * FROM campaigns WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? new Campaign($row) : null;
    }

    public function create(array $data): Campaign {
        $sql = "INSERT INTO campaigns 
            (name, description, type, target_audience, message, status, schedule, sent_count, open_rate, click_rate, created_by_user, created_at)
            VALUES (:name, :description, :type, :target_audience, :message, :status, :schedule, :sent_count, :open_rate, :click_rate, :created_by_user, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':type' => $data['type'] ?? 'email',
            ':target_audience' => $data['target_audience'],
            ':message' => $data['message'],
            ':status' => $data['status'] ?? 'draft',
            ':schedule' => $data['schedule'] ?? null,
            ':sent_count' => isset($data['sent_count']) ? intval($data['sent_count']) : 0,
            ':open_rate' => isset($data['open_rate']) ? floatval($data['open_rate']) : 0.0,
            ':click_rate' => isset($data['click_rate']) ? floatval($data['click_rate']) : 0.0,
            ':created_by_user' => $data['created_by_user'] ?? null
        ]);
        $id = $this->db->lastInsertId();
        return $this->getById($id);
    }

    public function update(array $data): bool {
        if (empty($data['id'])) return false;
        $fields = [];
        $params = [':id' => $data['id']];
        foreach (['name','description','type','target_audience','message','status','schedule','sent_count','open_rate','click_rate','created_by_user'] as $field) {
            if (isset($data[$field])) {
                // Cast numeric fields
                if (in_array($field, ['sent_count'])) {
                    $params[":$field"] = intval($data[$field]);
                } elseif (in_array($field, ['open_rate', 'click_rate'])) {
                    $params[":$field"] = floatval($data[$field]);
                } else {
                    $params[":$field"] = $data[$field];
                }
                $fields[] = "$field = :$field";
            }
        }
        if (!$fields) return false;
        $sql = "UPDATE campaigns SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM campaigns WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
