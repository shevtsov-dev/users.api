<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\AbstractUserController;
use App\Database\Database;
use App\Interfaces\UsersInterfaces;
use App\Models\User;
use PDO;

class UserController extends AbstractUserController implements UsersInterfaces
{
    private PDO $db;
    private User $user;

    private object|array|null $inputData;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function readAllUsers(): void
    {
        $this->setHeaders();

        $stmt = $this->user->read();
        $numberRows = $stmt->rowCount();

        if ($numberRows) {
            $usersArray = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $usersArray[] = $row;
            }
            $this->sendResponse([
                "users" => $usersArray
            ]);
        } else $this->sendResponse("User not found.", 404);
    }

    public function readOneUser(int|string $id): void
    {
        $this->setHeaders();

        $this->user->id = (int) $id;
        if($this->user->readOne()) {
            $usersArray = $this->user;

            $this->sendResponse([
                "user" => $usersArray
            ]);
        } else $this->sendNotFoundResponse("User $id:");
    }

    public function createUser(): void
    {
        $this->setHeaders('POST');

        $this->inputData = json_decode(file_get_contents("php://input"));

        if (isset($this->inputData->first_name) && isset($this->inputData->last_name)) {
            $this->user->first_name = htmlspecialchars($this->inputData->first_name);
            $this->user->last_name = htmlspecialchars($this->inputData->last_name);
            $this->user->created_at = date("Y-m-d H:i:s");

            $this->user->create() ? $this->sendSuccessCreatedResponse($this->db->lastInsertId()) : $this->sendResponse("Unable to create user.", 422);
        } else $this->sendResponse('Unable to create. Data is incomplete.', 400);
    }

    public function updateUser(int|string $id): void
    {
        $this->setHeaders('PUT');

        $this->inputData = json_decode(file_get_contents("php://input"));

        if (isset($this->inputData->first_name) && isset($this->inputData->last_name)) {
            $this->user->id = $id;
            $this->user->first_name = htmlspecialchars($this->inputData->first_name);
            $this->user->last_name = htmlspecialchars($this->inputData->last_name);
            $this->user->updated_at = date("Y-m-d H:i:s");

            $this->user->update() ? $this->sendSuccessUpdateResponse($this->user->id) :  $this->sendResponse("Unable to update user.", 422);
        } else $this->sendResponse("Unable to update. Data is incomplete.", 400);
    }

    public function deleteUser(int|string $id): void
    {
        $this->setHeaders('DELETE');

        $this->user->id = $id;
        if ($this->user->delete()) {
            $this->sendSuccessDeletedResponse();
        } else $this->sendResponse("Unable to delete user.", 422);
    }
}
