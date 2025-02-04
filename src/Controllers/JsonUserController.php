<?php

namespace App\Controllers;

use App\Database\JsonDatabase;
use JetBrains\PhpStorm\NoReturn;

class JsonUserController
{
    public JsonDatabase $db;
    public array $user;

    public function __construct()
    {
        $this->db = new JsonDatabase();
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
        $this->setHeaders('GET');
        $users = $this->db->getJsonDatabase();
        $this->sendResponse(['users' => $users], 200);
    }

    public function readOneUser(int|string $id): void
    {
        $this->setHeaders('GET');
        $users = $this->db->getJsonDatabase();
        foreach ($users as $user) {
            if ($id == $user['id']) {
                $this->sendResponse(['user' => $user]);
                break;
            }
        }
    }

    public function deleteUser(int|string $id): void
    {
        $this->setHeaders('DELETE');

        $users = $this->db->getJsonDatabase();

        foreach ($users as $key => $user) {
            if ($id == $user['id']) {
                unset($users[$key]);
                $users = array_values($users);
                $this->db->setJsonDatabase($users);
                $this->sendResponse('User deleted', 204);
                break;
            }
        }
    }

    public function createUser(): void
    {
        $this->setHeaders('POST');

        $users = $this->db->getJsonDatabase();
        $maxId = 0;

        foreach ($users as $user) {
            if($user['id'] > $maxId) $maxId = $user['id'];
        }

        $maxId += 1;

        $inputData = json_decode(file_get_contents('php://input'), true);

        $users[] = [
            'id' => (string) $maxId,
            'first_name' => $inputData['first_name'] ?? '',
            'last_name' => $inputData['last_name'] ?? '',
            'created_at' => $inputData['created_at'] ?? date('Y-m-d H:i:s'),
            'updated_at' => null
        ];


        $this->db->setJsonDatabase($users);
        $this->sendSuccessCreatedResponse($maxId);
    }

    public function updateUser(int|string $id): void
    {
        $this->setHeaders('PUT');

        $users = $this->db->getJsonDatabase();

        $inputData = json_decode(file_get_contents('php://input'), true);

        $userIndex = null;
        foreach ($users as $index => $user) {
            if ($id == $user['id']) {
                $userIndex = $index;
                break;
            }
        }

        if ($userIndex) {
            $users[$userIndex]['first_name'] = $inputData['first_name'] ?? $users[$userIndex]['first_name'];
            $users[$userIndex]['last_name'] = $inputData['last_name'] ?? $users[$userIndex]['last_name'];
            $users[$userIndex]['updated_at'] = $inputData['updated_at'] ?? date('Y-m-d H:i:s');
        }

        $this->db->setJsonDatabase($users);
        $this->sendSuccessUpdateResponse($id);

    }

    private function sendResponse(array|string $message, int $statusCode = 200, array $additionalData = []): void
    {
        http_response_code($statusCode);
        $response = array_merge(["message" => $message], $additionalData);
        echo json_encode($response,JSON_UNESCAPED_UNICODE);
    }

    #[NoReturn] private function sendSuccessCreatedResponse(int $userId): void
    {
        $this->sendResponse("User was created.", 201);
        header("Location: /users/$userId", true, 303);
        exit();
    }

    #[NoReturn] private function sendSuccessUpdateResponse(int $userIndex): void
    {
        $this->sendResponse("User was updated.", 201);
        header("Location: /users/$userIndex", true, 303);
        exit();
    }
}