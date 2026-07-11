<?php

session_start();

// Load autoloaded classes
require __DIR__ . '/vendor/autoload.php';

use Boy\S3Materi\Router;

// Initialize custom routing engine
$router = new Router();

// Load routes definitions
require __DIR__ . '/routes/web.php';

// Dispatch incoming request
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($uri, $method);
