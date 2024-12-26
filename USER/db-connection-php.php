<?php
class Database {
    private $host = 'localhost';
    private $username = 'your_username';
    private $password = 'your_password';
    private $database = 'travelease';
    public $conn;

    public function __construct() {
        // Create connection
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function __destruct() {
        // Close connection
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
