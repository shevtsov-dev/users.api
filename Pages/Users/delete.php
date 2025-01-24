<?php

use App\Database\Database;
use App\Models\User;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: DELETE");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");

$database = new Database();
$db = $database->getConnection();

$users = new User($db);

$users->id = USER_ID;

if ($users->delete()) {

    http_response_code(200);

    echo json_encode(array("message" => "User was deleted."), JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(503);

    echo json_encode(array("message" => "Unable to delete user."), JSON_UNESCAPED_UNICODE);
}
