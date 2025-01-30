<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    private string $host = "MySQL-8.2";
    private string $user = "root";
    private string $pass = "";
    private string $dbname = "users_api";
    public ?PDO $conn;

    public function getConnection() : PDO|null
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->pass);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        return $this->conn;
    }
}
