<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
 * This project was imported with a response-injection infection that appended
 * hidden gambling links after otherwise valid HTML and CSV responses. Keep one
 * outer buffer at the front controller so a previously cached/injected tail
 * cannot corrupt a response while the affected local environment is cleaned.
 */
ob_start(static function (string $output): string {
    if (!preg_match('/RATU311|KUPU178|THOR311|CERI188/i', $output)) {
        return $output;
    }

    return preg_replace(
        '#\s*<div\s+style=["\']display\s*:\s*none["\']>.*?</div>\s*#is',
        '',
        $output
    ) ?? $output;
});

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);

if (ob_get_level() > 0) {
    ob_end_flush();
}
