<?php

require_once '../vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

switch ($requestMethod) {
    case 'GET':
        if ($requestUri == '/users') {
            require_once '../Pages/Users/read.php';
        } elseif (preg_match('/^\/users\/(\d+)$/', $requestUri, $matches)) {
            define('USER_ID', $matches[1]);
            require '../Pages/Users/readOne.php';
        }
        break;
    case 'POST':
        if ($requestUri == '/users') {
            require '../Pages/Users/create.php';
        }
        break;
    case 'PUT':
        if (preg_match('/^\/users\/(\d+)$/', $requestUri, $matches)) {
            define('USER_ID', $matches[1]);
            require '../Pages/Users/update.php';
        }
        break;
    case 'DELETE':
        if (preg_match('/^\/users\/(\d+)$/', $requestUri, $matches)) {
            define('USER_ID', $matches[1]);
            require '../Pages/Users/delete.php';
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
        break;
}
