<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    private $host = "MySQL-8.2";
    private $user = "root";
    private $pass = "";
    private $dbname = "users_api";
    public $conn;

    public function getConnection()
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
