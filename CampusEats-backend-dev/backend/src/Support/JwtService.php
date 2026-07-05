<?php

declare(strict_types=1);

namespace App\Support;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    public function __construct(
        private readonly string $secret,
        private readonly int $ttlSeconds
    ) {
    }

    public function issue(int $userId, string $email, string $role): string
    {
        $now = time();

        $payload = [
            'sub' => $userId,
            'email' => $email,
            'role' => $role,
            'iat' => $now,
            'exp' => $now + $this->ttlSeconds,
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * @return array{sub: int, email: string, role: string, iat: int, exp: int}
     */
    public function verify(string $token): array
    {
        $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));

        return (array) $decoded;
    }
}
