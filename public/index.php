<?php

use Laminas\Diactoros\ServerRequestFactory;
use App\ViewController\Login;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/login', Login::class);
});

$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        http_response_code(404);
        echo '404 Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        http_response_code(405);
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // ... call $handler with $vars
        $vc = new $handler();
        $response = $vc($request);
        break;
}

if ($response) {
    http_response_code($response->getStatusCode());
    foreach ($response->getHeaders() as $header => $values) {
        header(sprintf("%s: %s\n", $header, implode(', ', $values)));
    }
    echo $response->getBody();
}
