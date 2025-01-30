<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\User;
use JetBrains\PhpStorm\NoReturn;
use PDO;

class UserController
{
    private ?PDO $db;
    private ?User $user;

    private object|array|null $data;

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
    }

    public function readAllUsers(): void
    {
        $this->setHeaders();
        header("Access-Control-Max-Age: 3600");

        $stmt = $this->user->read();
        $number_rows = $stmt->rowCount();

        if ($number_rows) {
            $users_arr = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $users_arr[] = $row;
            }
            $this->sendResponse([
                "users" => $users_arr
            ]);
        } else $this->sendResponse("User not found.", 404);
    }

    public function readOneUser(int|string $id): void
    {
        $this->setHeaders();
        header("Access-Control-Allow-Headers: access");
        header("Access-Control-Allow-Credentials: true");

        $this->user->id = (int) $id;
        $this->user->readOne();

        if ($this->user->id) {
            $users_arr = $this->user;

            $this->sendResponse([
                "user" => $users_arr
            ]);
        } else $this->sendResponse("User not found.", 404);
    }

    public function deleteUser(int|string $id): void
    {
        $this->setHeaders('DELETE');
        header("Access-Control-Allow-Headers: access");
        header("Access-Control-Allow-Credentials: true");

        $this->user->id = $id;
        if ($this->user->delete()) {
            $this->sendResponse("User was deleted.");
        } else $this->sendResponse("Unable to delete user.", 422);
    }

    public function createUser(): void
    {
        $this->setHeaders('POST');
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        header("Access-Control-Max-Age: 3600");

        $this->data = json_decode(file_get_contents("php://input"));

        if (isset($this->data->first_name) && isset($this->data->last_name)) {
            $this->user->first_name = htmlspecialchars($this->data->first_name);
            $this->user->last_name = htmlspecialchars($this->data->last_name);
            $this->user->created_at = date("Y-m-d H:i:s");

            $this->user->create() ? $this->sendSuccessCreatedResponse() : $this->sendResponse("Unable to create user.", 422);
        } else $this->sendResponse('Unable to create. Data is incomplete.', 400);
    }

    public function updateUser(int|string $id): void
    {
        $this->setHeaders('PUT');
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        $this->data = json_decode(file_get_contents("php://input"));

        if (isset($this->data->first_name) && isset($this->data->last_name)) {
            $this->user->id = $id;
            $this->user->first_name = htmlspecialchars($this->data->first_name);
            $this->user->last_name = htmlspecialchars($this->data->last_name);
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
