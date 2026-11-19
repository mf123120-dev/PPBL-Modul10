<?php 
 
use Illuminate\Http\Request; 
 
define('LARAVEL_START', microtime(true));

// Vercel's filesystem is read-only except /tmp: send logs to stderr so they show up in Vercel Logs.
if (getenv('LOG_CHANNEL') === false) {
    putenv('LOG_CHANNEL=stderr');
    $_ENV['LOG_CHANNEL'] = $_SERVER['LOG_CHANNEL'] = 'stderr';
}
 
require __DIR__.'/../vendor/autoload.php'; 
 
$app = require __DIR__.'/../bootstrap/app.php'; 
 
$storage = sys_get_temp_dir().'/inventory-laravel'; 
foreach (['framework/cache/data', 'framework/sessions', 'framework/views'] as $dir) { 
    $path = $storage.'/'.$dir; 
    if (! is_dir($path)) { 
        mkdir($path, 0775, true); 
    } 
} 
 
$app->useStoragePath($storage); 
$app->handleRequest(Request::capture()); 