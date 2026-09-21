<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

if (!defined('APP_NAME')) {
    define('APP_NAME', env('APP_NAME', 'Bimbel Alahaido'));
}

if (!function_exists('getDBConnection')) {
    function getDBConnection() {
        try {
            return \Illuminate\Support\Facades\DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $db   = env('DB_DATABASE', 'bimbel_portal');
            $user = env('DB_USERNAME', 'root');
            $pass = env('DB_PASSWORD', '');
            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SyncSessionUser::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/*',
            'login',
            'register',
            'logout',
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'active' => \App\Http\Middleware\ActiveStatusMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
