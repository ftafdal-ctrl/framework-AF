<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Core/App.php';

use App\Core\App;

// Framework-AF Entry Point
header('Content-Type: text/html; charset=UTF-8');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; frame-ancestors 'none'; base-uri 'self'; form-action 'self'");
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');

echo '<!DOCTYPE html><html lang="id"><head><title>Framework-AF</title></head><body><h1>Framework-AF v' . App::getVersion() . ' Siap Digunakan</h1></body></html>';
