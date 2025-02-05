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
    public ?PDO $conn = null;
    public JsonDatabase $jsonDatabase;

    public function __construct()
    {
        $this->jsonDatabase = new JsonDatabase();
    }

    public function getConnection() : PDO|JsonDatabase|null
    {
        try {
            if ($_ENV["DB_SOURCE"] === "mysql") {
                if ($this->conn === null) {
                    $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->pass);
                }
                return $this->conn;
            } else {
                return $this->jsonDatabase;
            }
        } catch (PDOException $e) {
            error_log("[" . date("Y-m-d H:i:s") . "] Connect to DataBase Error: " . mb_convert_encoding($e->getMessage(), 'utf8', 'Windows-1251') . PHP_EOL, 3, __DIR__ . '\..\..\logs\errors.log');
            return null;
        }
    }
}
