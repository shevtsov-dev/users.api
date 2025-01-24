<?php

use App\Database\Database;
use App\Models\User;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");

$database = new Database();
$db = $database->getConnection();

$users = new User($db);

$stmt = $users->read();
$num = $stmt->rowCount();

if ($num) {
    $users_arr = array();
    $users_arr["Users"] = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $users_item = array(
            "id" => $id,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "createdAt" => $createdAt,
            "updatedAt" => $updatedAt,
        );
        array_push($users_arr["Users"], $users_item);
    }

    http_response_code(200);

    echo json_encode($users_arr);
} else {
    http_response_code(404);

    echo json_encode(array("message" => "No User found."), JSON_UNESCAPED_UNICODE);
}