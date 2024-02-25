<?php

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

require './vendor/autoload.php';

############################################################################

$dispatcher = FastRoute\simpleDispatcher(
    function (FastRoute\RouteCollector $r) {
        $r->addRoute(
            'GET',
            '/',
            'App\\Controllers\\Index::action'
        );
        $r->addRoute(
            'POST',
            '/login',
            'App\\Controllers\\Login::action'
        );
    }
);

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

/** @var Response */
$response = null;
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $response = new Response(404);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response = new Response(405);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];

        $bodyRaw = file_get_contents('php://input');
        $request = new Request($httpMethod, $_SERVER['REQUEST_URI'], [], ($bodyRaw ? $bodyRaw : ''));
        list($class, $method) = explode("::", $handler, 2);

        $ctl = new $class();

        /** @var Response */
        $response = $ctl->$method($request);

        break;
}

if ($response !== null) {
    http_response_code($response->getStatusCode());
    $headers = $response->getHeaders();
    foreach ($headers as $name => $value) {
        if ($value) {
            header(sprintf('%s: %s', $name, array_shift($value)));
        }
    }
    $responseBody = $response->getBody()->getContents();
    echo $responseBody;
} else {
    http_response_code(500);
}
