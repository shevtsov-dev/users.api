<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\User;
use JetBrains\PhpStorm\NoReturn;
use PDO;

class UserController
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

    private function setHeaders(string $method = "GET"): void
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: $method");

        if($method != 'GET') {
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        }
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
        $this->user->readOne();

        if ($this->user->id) {
            $usersArray = $this->user;

            $this->sendResponse([
                "user" => $usersArray
            ]);
        } else $this->sendResponse("User not found.", 404);
    }

    public function deleteUser(int|string $id): void
    {
        $this->setHeaders('DELETE');

        $this->user->id = $id;
        if ($this->user->delete()) {
            $this->sendResponse("User was deleted.");
        } else $this->sendResponse("Unable to delete user.", 422);
    }

    public function createUser(): void
    {
        $this->setHeaders('POST');

        $this->inputData = json_decode(file_get_contents("php://input"));

        if (isset($this->inputData->first_name) && isset($this->inputData->last_name)) {
            $this->user->first_name = htmlspecialchars($this->inputData->first_name);
            $this->user->last_name = htmlspecialchars($this->inputData->last_name);
            $this->user->created_at = date("Y-m-d H:i:s");

            $this->user->create() ? $this->sendSuccessCreatedResponse() : $this->sendResponse("Unable to create user.", 422);
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

            $this->user->update() ? $this->sendSuccessUpdateResponse() :  $this->sendResponse("Unable to update user.", 422);
        } else $this->sendResponse("Unable to update. Data is incomplete.", 400);
    }

    private function sendResponse(array|string $message, int $statusCode = 200, array $additionalData = []): void
    {
        http_response_code($statusCode);
        $response = array_merge(["message" => $message], $additionalData);
        echo json_encode($response,JSON_UNESCAPED_UNICODE);
    }

    #[NoReturn] private function sendSuccessCreatedResponse(): void
    {
        $this->sendResponse("User was created.", 201);
        header("Location: /users/{$this->db->lastInsertId()}", true, 303);
        exit();
    }

    #[NoReturn] private function sendSuccessUpdateResponse(): void
    {
        $this->sendResponse("User was updated.", 201);
        header("Location: /users/{$this->user->id}", true, 303);
        exit();
    }
}
