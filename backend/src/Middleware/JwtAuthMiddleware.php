<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Support\JwtService;
use App\Support\Response;
use Firebase\JWT\ExpiredException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use UnexpectedValueException;

class JwtAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly JwtService $jwt,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function process(Request $request, Handler $handler): PsrResponse
    {
        $header = $request->getHeaderLine('Authorization');

        if (!str_starts_with($header, 'Bearer ')) {
            return $this->unauthorized('Missing bearer token.');
        }

        $token = substr($header, 7);

        try {
            $claims = $this->jwt->verify($token);
        } catch (ExpiredException) {
            return $this->unauthorized('Token has expired.');
        } catch (UnexpectedValueException) {
            return $this->unauthorized('Invalid token.');
        }

        $request = $request
            ->withAttribute('user_id', (int) $claims['sub'])
            ->withAttribute('user_email', $claims['email'])
            ->withAttribute('user_role', $claims['role']);

        return $handler->handle($request);
    }

    private function unauthorized(string $message): PsrResponse
    {
        return Response::error($this->responseFactory->createResponse(), $message, 401);
    }
}
