<?php

declare(strict_types=1);

use App\Controllers\JsonUserController;
use App\Controllers\UserController;
use Dotenv\Dotenv;

require_once '../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

$requestUri = trim($requestUri, '/');
$_ENV['DB_SOURCE'] === 'mysql' ? $userController = new UserController() : $userController = new JsonUserController();

switch ($requestMethod) {
    case 'GET':
        if ($requestUri == 'users') {
            $userController->readAllUsers();
        } elseif (preg_match('/^users\/(\d+)$/', $requestUri, $matches)) {

            $userController->readOneUser($matches[1]);
        }
        break;
    case 'POST':
        if ($requestUri == 'users') {
            $userController->createUser();
        }
        break;
    case 'PUT':
        if (preg_match('/^users\/(\d+)$/', $requestUri, $matches)) {
            $userController->updateUser($matches[1]);

        }
        break;
    case 'DELETE':
        if (preg_match('/^users\/(\d+)$/', $requestUri, $matches)) {
            $userController->deleteUser($matches[1]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
        break;
}
