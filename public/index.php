<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Autoload & Bootstrap placeholder for dev container
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
}

echo json_encode([
    'service' => 'Simple Stock Flow API',
    'status' => 'online',
    'architecture' => 'Onion 4-Layers',
    'version' => '1.0.0',
    'timestamp' => date('c'),
]);
