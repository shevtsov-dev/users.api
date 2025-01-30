<?php

declare(strict_types=1);

use App\Controllers\UserController;

require_once '../vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

$request_uri = trim($request_uri, '/');
$user_controller = new UserController();

switch ($request_method) {
    case 'GET':
        if ($request_uri == 'users') {
            $user_controller->readAllUsers();
        } elseif (preg_match('/^users\/(\d+)$/', $request_uri, $matches)) {

            $user_controller->readOneUser($matches[1]);
        }
        break;
    case 'POST':
        if ($request_uri == 'users') {
            $user_controller->createUser();
        }
        break;
    case 'PUT':
        if (preg_match('/^users\/(\d+)$/', $request_uri, $matches)) {
            $user_controller->updateUser($matches[1]);

        }
        break;
    case 'DELETE':
        if (preg_match('/^users\/(\d+)$/', $request_uri, $matches)) {
            $user_controller->deleteUser($matches[1]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
        break;
}
