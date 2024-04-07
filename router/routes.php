<?php
    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/marlin/second_diploma/', ['App\controllers\Router','login']);
        $r->addRoute('GET', '/marlin/second_diploma/register', ['App\controllers\Router','register']);
        $r->addRoute('POST', '/marlin/second_diploma/register', ['App\controllers\User','register']);
        $r->addRoute(['GET', 'POST'], '/marlin/second_diploma/users', ['App\controllers\User','login']);
        $r->addRoute('POST', '/marlin/second_diploma/login', ['App\controllers\User','login']);
        $r->addRoute('GET', '/marlin/second_diploma/logout', ['App\controllers\User','logout']);
        $r->addRoute('GET', '/marlin/second_diploma/create', ['App\controllers\Router','create']);
        $r->addRoute('GET', '/marlin/second_diploma/edit/{id:\d+}', ['App\controllers\Router','edit']);
        $r->addRoute('GET', '/marlin/second_diploma/security/{id:\d+}', ['App\controllers\Router','security']);
        $r->addRoute('GET', '/marlin/second_diploma/status/{id:\d+}', ['App\controllers\Router','status']);
        $r->addRoute('GET', '/marlin/second_diploma/media/{id:\d+}', ['App\controllers\Router','media']);
        $r->addRoute('GET', '/marlin/second_diploma/delete/{id:\d+}', ['App\controllers\Router','delete']);

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
            break;
        case FastRoute\Dispatcher::FOUND:
            
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];
            if(empty($routeInfo[2])){
                $vars = $_POST;
            }
            $controller = new $handler[0];
            call_user_func([$controller, $handler[1]], $vars);
            
    }
?>