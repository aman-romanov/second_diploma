<?php
namespace App\controllers;
use App\modules\Users;
use App\modules\QueryBuilder;
use App\modules\Image;
use \Tamtamchik\SimpleFlash\Flash;
use App\controllers\Router;
use function Tamtamchik\SimpleFlash\flash;

/**
 * Базовый класс пользователя для храннеия всех свойств и нужных компонентов.
 */

class UserInfo {

    protected $user;
    protected $auth;
    protected $router;
    protected $qb;
    protected $img;
    protected $status = false;
    protected $role = 0;

    public function __construct(Users $user, Router $router, QueryBuilder $qb, Image $img){
        $this->user = $user;
        $this->router = $router;
        $this->qb = $qb;
        $this->img = $img;
        $this->auth = $this->user->getAuth();
        if($this->auth->isLoggedIn()){
            $this->status = true;
        }
        if($this->auth->hasRole(\Delight\Auth\Role::ADMIN)){
            $this->role = 1;
        }
    }
}
?>