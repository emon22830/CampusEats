<?php

declare(strict_types=1);

$env = static function (string $key, ?string $default = null): ?string {
    $value = $_ENV[$key] ?? getenv($key);

    return $value === false || $value === null ? $default : $value;
};

return [
    'app_env' => $env('APP_ENV', 'local'),
    'db' => [
        'host' => $env('DB_HOST', '127.0.0.1'),
        'port' => $env('DB_PORT', '3306'),
        'name' => $env('DB_NAME', 'campuseats'),
        'user' => $env('DB_USER', 'campuseats'),
        'pass' => $env('DB_PASS', 'campuseats'),
    ],
    'jwt' => [
        'secret' => $env('JWT_SECRET', ''),
        'ttl' => (int) $env('JWT_TTL', '86400'),
    ],
    'cors_origin' => $env('CORS_ORIGIN', '*'),
];
