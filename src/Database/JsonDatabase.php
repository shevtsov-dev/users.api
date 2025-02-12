<?php

declare(strict_types=1);

namespace App\Database;

use http\Exception\RuntimeException;

class JsonDatabase
{
    public string $jsonDatabase = __DIR__ . '\..\..\storage\data.json';


    public function getJsonDatabase(): array
    {
        if (!file_exists($this->jsonDatabase)) {
            $errorMessage = "[" . date("Y-m-d H:i:s") . "] Error: file $this->jsonDatabase not founded." . PHP_EOL;
            error_log($errorMessage, 3, __DIR__ . '\..\..\logs\errors.log');
            throw new RuntimeException($errorMessage);
        }

        $jsonData = file_get_contents($this->jsonDatabase);
        return json_decode($jsonData, true) ?? [];
    }

    public function setJsonDatabase(array $jsonDatabase): bool
    {
        return file_put_contents($this->jsonDatabase, json_encode($jsonDatabase, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) !== false;
    }
}