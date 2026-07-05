<?php

declare(strict_types=1);

namespace App\Support;

use Psr\Http\Message\ResponseInterface as PsrResponse;

class Response
{
    public static function json(PsrResponse $response, mixed $data, int $status = 200): PsrResponse
    {
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public static function ok(PsrResponse $response, mixed $data, int $status = 200): PsrResponse
    {
        return self::json($response, ['data' => $data], $status);
    }

    public static function error(PsrResponse $response, string $message, int $status, array $fields = []): PsrResponse
    {
        $error = ['message' => $message];

        if ($fields !== []) {
            $error['fields'] = $fields;
        }

        return self::json($response, ['error' => $error], $status);
    }
}
