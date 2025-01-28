<?php

namespace App\Controllers;

use App\Database\Database;
use App\Models\User;
use PDO;

class UserController
{
    private $db;
    private $user;

    private $data;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    private function setHeaders($method = "GET")
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: $method");
    }

    public function readAllUsers()
    {
        $this->setHeaders();
        header("Access-Control-Max-Age: 3600");

        $stmt = $this->user->read();
        $num = $stmt->rowCount();

        if ($num) {
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

    public function readOneUser($id)
    {
        $this->setHeaders();
        header("Access-Control-Allow-Headers: access");
        header("Access-Control-Allow-Credentials: true");

        $this->user->id = $id;
        $this->user->readOne();

        if ($this->user->id) {
            $users_arr = $this->user;

            $this->sendResponse([
                "user" => $users_arr
            ]);
        } else $this->sendResponse("User not found.", 404);
    }

    public function deleteUser($id)
    {
        $this->setHeaders('DELETE');
        header("Access-Control-Allow-Headers: access");
        header("Access-Control-Allow-Credentials: true");

        $this->user->id = $id;
        if ($this->user->delete()) {
            $this->sendResponse("User was deleted.");
        } else $this->sendResponse("Unable to delete user.", 422);
    }

    public function createUser()
    {
        $this->setHeaders('POST');
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        header("Access-Control-Max-Age: 3600");

        $this->data = json_decode(file_get_contents("php://input"));

        if (isset($this->data->first_name) && isset($this->data->last_name)) {
            $this->user->first_name = htmlspecialchars($this->data->first_name);
            $this->user->last_name = htmlspecialchars($this->data->last_name);
            $this->user->createdAt = date("Y-m-d H:i:s");

            $this->user->create() ? $this->sendSussessCreatedResponse() : $this->sendResponse("Unable to create user.", 422);
        } else $this->sendResponse('Unable to create. Data is incomplete.', 400);
    }

    public function updateUser($id)
    {
        $this->setHeaders('PUT');
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        $this->data = json_decode(file_get_contents("php://input"));

        if (isset($this->data->first_name) && isset($this->data->last_name)) {
            $this->user->id = $id;
            $this->user->first_name = htmlspecialchars($this->data->first_name);
            $this->user->last_name = htmlspecialchars($this->data->last_name);
            $this->user->updatedAt = date("Y-m-d H:i:s");

            $this->user->update() ? $this->sendSuccessUpdateResponse() :  $this->sendResponse("Unable to update user.", 422);
        } else $this->sendResponse("Unable to update. Data is incomplete.", 400);
    }

    private function sendResponse($message, $statusCode = 200, $additionalData = [])
    {
        http_response_code($statusCode);
        $response = array_merge(["message" => $message], $additionalData);
        echo json_encode($response,JSON_UNESCAPED_UNICODE);
    }

    private function sendSussessCreatedResponse()
    {
        $this->sendResponse("User was created.", 201);
        header("Location: /users/{$this->db->lastInsertId()}", true, 303);
        exit();
    }

    private function sendSuccessUpdateResponse()
    {
        $this->sendResponse("User was updated.", 201);
        header("Location: /users/{$this->user->id}", true, 303);
        exit();
    }

}
