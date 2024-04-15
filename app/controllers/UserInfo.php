<?php
namespace App\controllers;
use App\modules\Users;
use App\modules\QueryBuilder;
use \Tamtamchik\SimpleFlash\Flash;
use App\controllers\Router;
use function Tamtamchik\SimpleFlash\flash;



class UserInfo {

    protected $user;
    protected $auth;
    protected $router;
    protected $qb;

    public function __construct(){
        $this->user = new Users();
        $this->router = new Router();
        $this->qb = new QueryBuilder();
        $this->auth = $this->user->getAuth();
    }
}
?>