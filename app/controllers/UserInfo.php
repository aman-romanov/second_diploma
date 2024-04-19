<?php
namespace App\controllers;
use App\modules\Users;
use App\modules\QueryBuilder;
use App\modules\Image;
use \Tamtamchik\SimpleFlash\Flash;
use App\controllers\Router;
use function Tamtamchik\SimpleFlash\flash;



class UserInfo {

    protected $user;
    protected $auth;
    protected $router;
    protected $qb;
    protected $img;
    protected $status = false;
    protected $role = 0;

    public function __construct(){
        $this->user = new Users();
        $this->router = new Router();
        $this->qb = new QueryBuilder();
        $this->img = new Image();
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