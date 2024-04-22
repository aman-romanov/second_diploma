<?php

namespace App\modules;

use DI\Container;
use DI\ContainerBuilder;
use Aura\SqlQuery\QueryFactory;
use League\Plates\Engine;
use \Tamtamchik\SimpleFlash\Flash;
use function Tamtamchik\SimpleFlash\flash;
use Delight\Auth\Auth;
use FastRoute;
use PDO;

/**
 * Класс для запуска веб-приложения.
 */

class App {
    private $container;

    /**
     * Запуск конструктора класса. Принимает контейнер для создания других классов. После на основе внесенных значении, запускается роутер, который в свое время запускает обработчик запросов.
     * 
     * @param obj $contBuilder Объект контейнера
     * @return null 
     */

    public function __construct(ContainerBuilder $contBuilder) {
        $this->container = $contBuilder;
        $this->container->addDefinitions([
            QueryFactory::class => function () {
                return new QueryFactory('mysql');
            },
        
            PDO::class => function () {
                $host = 'localhost';
                $dbname = 'second_diploma';
                $charset = 'utf8mb4';
                $username = 'tester';
                $password = 'vOJ1Cls7Q52GTIaT';
        
                return new PDO("mysql:host={$host};dbname={$dbname};charset={$charset}", $username, $password);
            },
        
            Engine::class => function () {
                return new Engine('../app/views');
            },

            Auth::class => function () {
                $cont = $this->container->build();
                return new Auth($cont->get('PDO'));
            }
            
        ]);
        self::router();
    }

    /**
     * Запуск роутера
     * 
     * @return null 
     */

    public function router(){

        $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
            $r->addRoute(['GET', 'POST'], '/', ['App\controllers\User','login']);
            $r->addRoute(['GET', 'POST'], '/users', ['App\controllers\User','users']);
            $r->addRoute('GET', '/register', ['App\controllers\User','register']);
            $r->addRoute('POST', '/register', ['App\controllers\User','register']);
            $r->addRoute('POST', '/login', ['App\controllers\User','login']);
            $r->addRoute('GET', '/logout', ['App\controllers\User','logout']);
            $r->addRoute('GET', '/profile/{id:\d+}', ['App\controllers\User','profile']);
            $r->addRoute(['GET', 'POST'], '/create', ['App\controllers\Admin','create']);
            $r->addRoute(['GET', 'POST'], '/edit/{id:\d+}', ['App\controllers\Admin','edit']);
            $r->addRoute(['GET', 'POST'], '/security/{id:\d+}', ['App\controllers\Admin','security']);
            $r->addRoute(['GET', 'POST'], '/status/{id:\d+}', ['App\controllers\Admin','status']);
            $r->addRoute(['GET', 'POST'], '/media/{id:\d+}', ['App\controllers\Admin','media']);
            $r->addRoute('GET', '/delete/{id:\d+}', ['App\controllers\Admin','delete']);
    
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
                $cont = $this->container->build();
                $cont->call($handler, $vars);
                
        }
    }
}