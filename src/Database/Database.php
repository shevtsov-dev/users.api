<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

class Database
{
    private string $host = "MySQL-8.2";
    private string $user = "root";
    private string $pass = "";
    private string $dbname = "users_api";
    public ?PDO $conn = null;
    public JsonDatabase $jsonDatabase;

    public function __construct()
    {
        $this->jsonDatabase = new JsonDatabase();
    }

    public function getConnection() : PDO|JsonDatabase|null
    {
        if ($_ENV["DB_SOURCE"] === "mysql") {
            if ($this->conn === null) {
                $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->pass);
            }
            return $this->conn;
        } else {
            return $this->jsonDatabase;
        }
    }
}
