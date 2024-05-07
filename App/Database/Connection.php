<?php

namespace App\Database;

use Dotenv\Dotenv;
use PDO;
use PDOException;

class Connection {
    protected $pdo;

    public function __construct() {
        // Load environment variables
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();

        // Read database connection parameters from environment variables
        $type = $_ENV['DB_TYPE'];
        $database = $_ENV['DB_NAME'];
        $host = $_ENV['DB_HOST'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $options = [];

        try {
            $this->pdo = new PDO($type.':host='.$host.';dbname='.$database, $username, $password, $options);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getPdo() {
        return $this->pdo;
    }
}
