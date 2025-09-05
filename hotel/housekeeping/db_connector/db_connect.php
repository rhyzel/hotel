<?php
/**
 * Database connection wrapper
 * Usage:
 *   $db = new Database();
 *   $conn = $db->getConnection();
 */
class Database {
    private string $host;
    private string $user;
    private string $pass;
    private string $dbName;
    public ?mysqli $conn = null;

    public function __construct(array $config = []) {
        // defaults for local XAMPP
        $this->host = $config['host'] ?? '127.0.0.1';
        $this->user = $config['user'] ?? 'root';
        $this->pass = $config['pass'] ?? '';
        $this->dbName = $config['db'] ?? 'housekeepingdb';

        // Make mysqli throw exceptions on errors
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbName);
            // set charset
            $this->conn->set_charset('utf8mb4');
            // Maintain public property for backward compatibility
            // (some files still reference $db->conn)
            // $this->conn is already public now
        } catch (Throwable $e) {
            // rethrow to allow caller to handle the error
            throw new RuntimeException('Database connection failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /** Return the mysqli connection */
    public function getConnection(): mysqli {
        if ($this->conn === null) throw new RuntimeException('No database connection');
        return $this->conn;
    }

    /** Return the configured database name */
    public function getDbName(): string {
        return $this->dbName;
    }

    /** Perform a lightweight ping/health check */
    public function ping(): bool {
        try {
            return $this->getConnection()->ping();
        } catch (Throwable $e) {
            return false;
        }
    }

    /** Close the connection */
    public function close(): void {
        if ($this->conn) {
            $this->conn->close();
            $this->conn = null;
        }
    }
}

?>