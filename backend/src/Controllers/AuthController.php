<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Support\JwtService;
use App\Support\Response;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class AuthController
{
    private const SELF_REGISTERABLE_ROLES = ['customer', 'vendor'];

    public function __construct(
        private readonly UserRepository $users,
        private readonly JwtService $jwt
    ) {
    }

    public function register(Request $request, PsrResponse $response): PsrResponse
    {
        $body = (array) $request->getParsedBody();

        $rules = v::key('name', v::stringType()->length(2, 120))
            ->key('email', v::email())
            ->key('password', v::stringType()->length(8, 72))
            ->key('role', v::in(self::SELF_REGISTERABLE_ROLES));

        try {
            $rules->assert($body);
        } catch (NestedValidationException $e) {
            return Response::error($response, 'Validation failed.', 422, $e->getMessages());
        }

        if ($this->users->findByEmail($body['email']) !== null) {
            return Response::error($response, 'An account with this email already exists.', 409);
        }

        $hash = password_hash($body['password'], PASSWORD_ARGON2ID);
        $userId = $this->users->create($body['name'], $body['email'], $hash, $body['role']);

        $token = $this->jwt->issue($userId, $body['email'], $body['role']);

        return Response::ok($response, [
            'token' => $token,
            'user' => [
                'id' => $userId,
                'name' => $body['name'],
                'email' => $body['email'],
                'role' => $body['role'],
            ],
        ], 201);
    }

    public function login(Request $request, PsrResponse $response): PsrResponse
    {
        $body = (array) $request->getParsedBody();

        $rules = v::key('email', v::email())
            ->key('password', v::stringType()->notEmpty());

        try {
            $rules->assert($body);
        } catch (NestedValidationException $e) {
            return Response::error($response, 'Validation failed.', 422, $e->getMessages());
        }

        $user = $this->users->findByEmail($body['email']);

        if ($user === null || !password_verify($body['password'], $user['password_hash'])) {
            return Response::error($response, 'Invalid email or password.', 401);
        }

        $token = $this->jwt->issue((int) $user['id'], $user['email'], $user['role']);

        return Response::ok($response, [
            'token' => $token,
            'user' => [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ]);
    }

    public function me(Request $request, PsrResponse $response): PsrResponse
    {
        $user = $this->users->findById((int) $request->getAttribute('user_id'));

        if ($user === null) {
            return Response::error($response, 'User not found.', 404);
        }

        return Response::ok($response, [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);
    }
}
