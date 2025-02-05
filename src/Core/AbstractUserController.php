<?php

declare(strict_types=1);

namespace App\Core;

use App\Database\JsonDatabase;
use JetBrains\PhpStorm\NoReturn;

abstract class AbstractUserController
{
    protected function setHeaders(string $method = "GET"): void
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: $method");

        if($method != 'GET') {
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        }
    }

    protected function sendResponse(array|string $message, int $statusCode = 200, array $additionalData = []): void
    {
        http_response_code($statusCode);
        $response = array_merge(["message" => $message], $additionalData);
        echo json_encode($response,JSON_UNESCAPED_UNICODE);
    }

    #[NoReturn] protected function sendSuccessCreatedResponse(int|string $userId): void
    {
        $this->sendResponse("User was created.", 201);
        header("Location: /users/$userId", true, 303);
        exit();
    }

    #[NoReturn] protected function sendSuccessUpdateResponse(int|string $userId): void
    {
        $this->sendResponse("User was updated.", 201);
        header("Location: /users/$userId", true, 303);
        exit();
    }

    protected function sendSuccessDeletedResponse(): void
    {
        http_response_code( 204);
    }

    protected function sendNotFoundResponse(string $resource): void
    {
        $this->sendResponse("$resource not found.", 404);
    }
}
