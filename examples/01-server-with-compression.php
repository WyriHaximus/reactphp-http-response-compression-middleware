<?php

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Http\Server;

require __DIR__ . '/../vendor/autoload.php';

$loop = Factory::create();

$server = new Server(new \React\Http\MiddlewareRunner([
    new \Sikei\React\Http\Middleware\ResponseCompressionMiddleware([
        new \Sikei\React\Http\Middleware\CompressionGzipHandler(),
        new \Sikei\React\Http\Middleware\CompressionDeflateHandler(),
    ]),
    function (ServerRequestInterface $request, callable $next) {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'some' => 'nice',
            'json' => 'values',
        ]));
    },
]));

$socket = new \React\Socket\Server(isset($argv[1]) ? $argv[1] : '0.0.0.0:0', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();