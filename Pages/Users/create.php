<?php

use App\Database\Database;
use App\Models\User;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"));

if (isset($data->first_name) && isset($data->last_name)) {

    $database = new Database();
    $db = $database->getConnection();
    $users = new User($db);

    $users->first_name = $data->first_name;
    $users->last_name = $data->last_name;
    $users->createdAt = date("Y-m-d H:i:s");

    if ($users->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "User was created."), JSON_UNESCAPED_UNICODE);
        header("Location: /users/{$db->lastInsertId()}", true, 303);
        exit();
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create user."), JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create user. Data is incomplete."), JSON_UNESCAPED_UNICODE);
}
