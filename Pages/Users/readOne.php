<?php

use App\Database\Database;
use App\Models\User;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");

$database = new Database();
$db = $database->getConnection();

$users = new User($db);

$users->id = USER_ID;

$users->readOne();

if ($users->first_name != null) {
    $usersArray = array(
        "id" => $users->id,
        "first_name" => $users->first_name,
        "last_name" => $users->last_name,
        "createdAt" => $users->createdAt,
        "updatedAt" => $users->updatedAt,
    );

    http_response_code(200);
    echo json_encode($usersArray);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "User not found."), JSON_UNESCAPED_UNICODE);
}




