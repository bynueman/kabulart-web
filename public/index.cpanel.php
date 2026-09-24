<?php

/**
 * Kabul Art Gallery — cPanel Production Front Controller
 *
 * Target Location: $HOME/public_html/index.php
 * Laravel App:    $HOME/kabulgallery
 */

error_reporting(E_ALL & ~E_DEPRECATED);

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Application Path Resolution
|--------------------------------------------------------------------------
| References the Laravel application installed at ~/kabulgallery relative
| to this file in ~/public_html/index.php.
*/
$appPath = dirname(__DIR__) . '/kabulgallery';

if (!file_exists($appPath . '/bootstrap/app.php')) {
    // Fallback in case repository directory differs
    $appPath = dirname(__DIR__);
}

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
*/
if (file_exists($maintenance = $appPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/
require $appPath . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/
$app = require_once $appPath . '/bootstrap/app.php';

// Set public path to this directory (~/public_html)
$app->usePublicPath(__DIR__);

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
