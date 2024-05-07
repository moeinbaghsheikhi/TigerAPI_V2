<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/App/Routers/routes.php';

// Include DatabaseConnection class
require_once __DIR__ . '/App/Database/Connection.php';

// Create a new instance of DatabaseConnection
$database = new \App\Database\Connection();
$pdo = $database->getPdo();

// Resolve request
$requestMethod = $_SERVER["REQUEST_METHOD"];

$version = getApiVersion();

$path = getPath(false);

$router->resolve($version, $requestMethod, $path);