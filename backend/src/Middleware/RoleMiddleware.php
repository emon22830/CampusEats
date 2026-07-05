<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Support\Response;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class RoleMiddleware implements MiddlewareInterface
{
    /** @param string[] $allowedRoles */
    public function __construct(
        private readonly array $allowedRoles,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function process(Request $request, Handler $handler): PsrResponse
    {
        $role = $request->getAttribute('user_role');

        if (!in_array($role, $this->allowedRoles, true)) {
            return Response::error(
                $this->responseFactory->createResponse(),
                'You do not have permission to perform this action.',
                403
            );
        }

        return $handler->handle($request);
    }
}
