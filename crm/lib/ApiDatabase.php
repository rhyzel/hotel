<?php
namespace CRM\Lib;

class ApiDatabase {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct(array $config = []) {
        $this->host = $config['host'] ?? 'localhost';
        $this->db_name = $config['db_name'] ?? 'hotel';
        $this->username = $config['username'] ?? 'root';
        $this->password = $config['password'] ?? '';
    }

    public function getConnection(): \PDO {
        if ($this->conn instanceof \PDO) return $this->conn;
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
        $this->conn = new \PDO($dsn, $this->username, $this->password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
        return $this->conn;
    }
}
