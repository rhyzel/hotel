<?php
class Database {
    public $conn;

    public function __construct() {
        $servername = "localhost";
        $username = "root";
        $password = ""; // Empty password for default XAMPP MySQL setup
        $dbname = "housekeepingdb";

        // Create connection
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
}
?>