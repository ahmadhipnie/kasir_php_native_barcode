<?php

/**
 * Router for PHP built-in server.
 * Usage: php -S localhost:8000 -t public public/router.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve existing static files directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Route everything else to index.php
require_once __DIR__ . '/index.php';
