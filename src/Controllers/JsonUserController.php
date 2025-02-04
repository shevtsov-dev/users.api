<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\AbstractUserController;
use App\Database\JsonDatabase;
use App\Interfaces\UsersInterfaces;

class JsonUserController extends AbstractUserController implements UsersInterfaces
{
    public JsonDatabase $db;
    public array $user;

    public function __construct()
    {
        $this->db = new JsonDatabase();
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

    public function deleteUser(int|string $id): void
    {
        $this->setHeaders('DELETE');

        $users = $this->db->getJsonDatabase();

        foreach ($users as $key => $user) {
            if ($id == $user['id']) {
                unset($users[$key]);
                $users = array_values($users);
                $this->db->setJsonDatabase($users);
                $this->sendResponse("User was deleted.");
                break;
            }
        }
    }
}