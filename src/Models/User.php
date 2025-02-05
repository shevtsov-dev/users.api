<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOStatement;

class User
{
    private ?PDO $conn;
    private string $table_name = 'users';

    public int|string $id;
    public string $first_name;
    public string $last_name;
    public string $created_at;
    public ?string $updated_at;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    function read(): PDOStatement
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        return $stmt;
    }

    function readOne(): bool
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            foreach ($row as $key => $value) {
                $this->$key = $value;
            }
            return true;
        }
        return false;
    }

    function create(): bool
    {
        $query = "INSERT INTO " . $this->table_name . " SET first_name=:first_name, last_name=:last_name, created_at=:created_at";

        $stmt = $this->conn->prepare($query);

        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));

        $stmt->bindParam(":first_name", $this->first_name, PDO::PARAM_STR);
        $stmt->bindParam(":last_name", $this->last_name, PDO::PARAM_STR);
        $stmt->bindParam(":created_at", $this->created_at, PDO::PARAM_STR);

        return $stmt->execute();
    }

    function update(): bool
    {
        $query = "UPDATE " . $this->table_name . " SET first_name=:first_name, last_name=:last_name, updated_at=:updated_at WHERE id=:id LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->updated_at = htmlspecialchars(strip_tags($this->updated_at));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":first_name", $this->first_name, PDO::PARAM_STR);
        $stmt->bindParam(":last_name", $this->last_name, PDO::PARAM_STR);
        $stmt->bindParam(":updated_at", $this->updated_at, PDO::PARAM_STR);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    function delete(): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
