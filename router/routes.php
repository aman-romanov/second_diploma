<?php
    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addRoute(['GET', 'POST'], '/', ['App\controllers\User','login']);
        $r->addRoute(['GET', 'POST'], '/users', ['App\controllers\User','users']);
        $r->addRoute('GET', '/register', ['App\controllers\User','register']);
        $r->addRoute('POST', '/register', ['App\controllers\User','register']);
        $r->addRoute('POST', '/login', ['App\controllers\User','login']);
        $r->addRoute('GET', '/logout', ['App\controllers\User','logout']);
        $r->addRoute('GET', '/create', ['App\controllers\User','create']);
        $r->addRoute(['GET', 'POST'], '/edit/{id:\d+}', ['App\controllers\Admin','edit']);
        $r->addRoute(['GET', 'POST'], '/security/{id:\d+}', ['App\controllers\Admin','security']);
        $r->addRoute(['GET', 'POST'], '/status/{id:\d+}', ['App\controllers\Admin','status']);
        $r->addRoute(['GET', 'POST'], '/media/{id:\d+}', ['App\controllers\Admin','media']);
        $r->addRoute('GET', '/delete/{id:\d+}', ['App\controllers\Router','delete']);

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