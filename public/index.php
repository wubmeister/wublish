<?php

use Laminas\Diactoros\ServerRequestFactory;
use App\ViewController\Login;
use App\ViewController\Logout;
use App\ViewController\Pages;
use App\ViewController\Frontend;
use Laminas\Diactoros\Response\HtmlResponse;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute([ 'GET', 'POST' ], '/login', Login::class);
    $r->addRoute([ 'GET', 'POST' ], '/logout', Logout::class);
    $r->addRoute([ 'GET', 'POST' ], '/admin/pages', Pages::class);
    $r->addRoute([ 'GET', 'POST' ], '/', Frontend::class);
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
        $response = new HtmlResponse('<h1>404 Not Found</h1>', 404);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        $response = new HtmlResponse('<h1>405 Method Not Allowed</h1>', 405);
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
