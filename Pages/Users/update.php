<?php

use App\Database\Database;
use App\Models\User;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"));

if (isset($data->first_name) && isset($data->last_name)) {

    $database = new Database();
    $db = $database->getConnection();
    $users = new User($db);

    $users->id = USER_ID;
    $users->first_name = $data->first_name;
    $users->last_name = $data->last_name;
    $users->updatedAt = date("Y-m-d H:i:s");

    if ($users->update()) {
        http_response_code(201);
        echo json_encode(array("message" => "User was updated."), JSON_UNESCAPED_UNICODE);
        header("Location: /users/{$users->id}", true, 303);
        exit();
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to update user."), JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to update user. Data is incomplete."), JSON_UNESCAPED_UNICODE);
}