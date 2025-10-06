<?php
namespace CRM\Lib;

class FeedbackRepository {
    private \PDO $conn;

    public function __construct(\PDO $conn) {
        $this->conn = $conn;
    }

    public function getTableColumns(string $table): array {
        try {
            $stmt = $this->conn->query("DESCRIBE $table");
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function select(string $sql, array $params = []): array {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function selectOne(string $sql, array $params = []): ?array {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    public function execute(string $sql, array $params = []): \PDOStatement {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function lastInsertId(): string {
        return $this->conn->lastInsertId();
    }
}
