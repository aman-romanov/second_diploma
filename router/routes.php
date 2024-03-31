<?php
    Use App\controllers\newUser;

    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/marlin/second_diploma/', ['App\controllers\router','login']);
        $r->addRoute('GET', '/marlin/second_diploma/register', ['App\controllers\Router','register']);
        $r->addRoute('POST', '/marlin/second_diploma/users', ['App\controllers\Router','users']);
        $r->addRoute('POST', '/marlin/second_diploma/newuser', ['App\controllers\User','register']);
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
            if(is_array($routeInfo[1])){
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                if(!empty($_POST)){
                    $vars = $_POST;
                }
                $controller = new $handler[0];
                call_user_func([$controller, $handler[1]], $vars);
            } else{
                $handler = $routeInfo[1];
                $vars = $_POST;
                call_user_func($handler, $vars);
            }
            
    }
?>