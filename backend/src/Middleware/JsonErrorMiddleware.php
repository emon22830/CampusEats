<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Support\Response;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpException;
use Throwable;

class JsonErrorMiddleware
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly bool $debug
    ) {
    }

    public function __invoke(
        Request $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ) {
        $status = $exception instanceof HttpException ? $exception->getCode() : 500;
        $status = $status >= 400 && $status < 600 ? $status : 500;

        $message = $status === 500 && !$this->debug
            ? 'Something went wrong. Please try again later.'
            : $exception->getMessage();

        return Response::error($this->responseFactory->createResponse(), $message, $status);
    }
}
