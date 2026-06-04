<?php

if (($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG')) === 'true') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

$autoload = __DIR__.'/../vendor/autoload.php';

if (! file_exists($autoload)) {
    http_response_code(500);
    header('Content-Type: text/plain');
    echo 'Laravel vendor/autoload.php not found. Composer dependencies were not installed during deployment.';
    return;
}

require __DIR__.'/../public/index.php';
