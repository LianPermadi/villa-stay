<?php

header('Content-Type: application/json');

echo json_encode([
    'php' => PHP_VERSION,
    'app_key_set' => ! empty($_ENV['APP_KEY'] ?? getenv('APP_KEY')),
    'app_env' => $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: null,
    'app_debug' => $_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?: null,
    'app_url' => $_ENV['APP_URL'] ?? getenv('APP_URL') ?: null,
    'db_connection' => $_ENV['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?: null,
    'db_host_set' => ! empty($_ENV['DB_HOST'] ?? getenv('DB_HOST')),
], JSON_PRETTY_PRINT);
