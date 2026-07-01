<?php

declare(strict_types=1);

use App\Database\Connection;
use App\Middleware\CorsMiddleware;
use App\Middleware\JsonErrorMiddleware;
use App\Middleware\JwtAuthMiddleware;
use App\Support\JwtService;
use DI\Container;
use Dotenv\Dotenv;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;

require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    Dotenv::createImmutable(__DIR__ . '/..')->load();
}

$settings = require __DIR__ . '/../src/Config/settings.php';

$container = new Container();

$container->set('settings', $settings);

$container->set(PDO::class, fn () => Connection::get($settings));

$container->set(ResponseFactoryInterface::class, fn () => new ResponseFactory());

$container->set(JwtService::class, fn () => new JwtService($settings['jwt']['secret'], $settings['jwt']['ttl']));

$container->set(JwtAuthMiddleware::class, fn ($c) => new JwtAuthMiddleware(
    $c->get(JwtService::class),
    $c->get(ResponseFactoryInterface::class)
));

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$isDebug = $settings['app_env'] !== 'production';

$errorMiddleware = $app->addErrorMiddleware($isDebug, true, true);
$errorMiddleware->setDefaultErrorHandler(new JsonErrorMiddleware(
    $container->get(ResponseFactoryInterface::class),
    $isDebug
));

$app->add(new CorsMiddleware($settings['cors_origin'], $container->get(ResponseFactoryInterface::class)));

(require __DIR__ . '/../src/Routes/api.php')($app);

$app->run();
