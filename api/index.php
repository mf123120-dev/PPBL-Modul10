<?php 
 
use Illuminate\Http\Request; 
 
define('LARAVEL_START', microtime(true));

// Defaults for Vercel; any Environment Variable set in the Vercel dashboard overrides these.
// DB_PASSWORD is secret and must be set in Vercel (the repo is public).
$defaults = [
    'LOG_CHANNEL' => 'stderr', // filesystem is read-only except /tmp
    'APP_ENV' => 'production',
    'DB_CONNECTION' => 'pgsql',
    'DB_HOST' => 'aws-1-ap-northeast-1.pooler.supabase.com',
    'DB_PORT' => '5432',
    'DB_DATABASE' => 'postgres',
    'DB_USERNAME' => 'postgres.xhnpnoywrfrioymzapsh',
    'DB_SSLMODE' => 'require',
    'SESSION_DRIVER' => 'array',
    'CACHE_STORE' => 'array',
];
foreach ($defaults as $key => $value) {
    $current = getenv($key);
    if ($current === false || trim($current) === '') {
        putenv("$key=$value");
        $_ENV[$key] = $_SERVER[$key] = $value;
    } elseif ($current !== trim($current)) {
        putenv("$key=".trim($current));
        $_ENV[$key] = $_SERVER[$key] = trim($current);
    }
}

if (getenv('DB_PASSWORD') === false || getenv('DB_PASSWORD') === '') {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'DB_PASSWORD belum di-set di Vercel Environment Variables.']);
    exit;
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