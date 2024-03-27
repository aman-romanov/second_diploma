<?php
    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/marlin/second_diploma/', ['App\controllers\router','index']);
        $r->addRoute('GET', '/marlin/second_diploma/register', ['App\controllers\router','register']);
        $r->addRoute('POST', '/marlin/second_diploma/users', ['App\controllers\router','users']);
        $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
    });
    
    // Fetch method and URI from somewhere
    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    
    // Strip query string (?foo=bar) and decode URI
    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }
    $uri = rawurldecode($uri);
    
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            echo '404 Not Found';
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            echo '500 Error';
            // ... 405 Method Not Allowed
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];
            $controller = new $handler[0];
            call_user_func([$controller, $handler[1]], $vars);
            // ... call $handler with $vars
            break;
    }
?>