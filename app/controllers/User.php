<?php
    namespace App\controllers;
    use App\modules\Users;
    use App\modules\QueryBuilder;
    use \Tamtamchik\SimpleFlash\Flash;
    use App\controllers\Router;
    use function Tamtamchik\SimpleFlash\flash;



    class User {

        private $user;
        private $auth;
        private $router;
        private $qb;

        public function __construct(){
            $this->user = new Users;
            $this->auth = $this->user->getAuth();
            $this->router = new Router;
            $this->qb = new QueryBuilder;
        }

        public function register($data){
            if($this->auth->isLoggedIn()){
                $auth_user = $this->getUserData();
                $users = $this->getAllUsers();
                $this->router->users($auth_user, $users);
            }
            if(empty($data['email'])){
                flash()->error('Заполните поля');
               $this->router->register();
            }
            $user = $this->user->createUser($data['email'], $data['password']);
            if($user){
                flash()->success('Войдите в ваш аккаунт');
                $this->router->login();
                exit;
            }
            $this->router->register();
            
            
        }

        public function login($data){
            if(!$this->auth->isLoggedIn()){
                if(empty($data['email'])){
                    flash()->error('Заполните поля');
                    $this->router->login();
                    exit;
                }
                try {
                    $this->auth->login($data['email'], $data['password']);
                }
                catch (\Delight\Auth\InvalidEmailException $e) {
                    flash()->error('Введите корректный почтовый адрес!');
                    $this->router->login();
                }
                catch (\Delight\Auth\InvalidPasswordException $e) {
                    flash()->error('Почта или пароль не соответствуют');
                    $this->router->login();
                }
                catch (\Delight\Auth\EmailNotVerifiedException $e) {
                    flash()->error('Почта не подтверждена');
                    $this->router->login();
                }
                catch (\Delight\Auth\TooManyRequestsException $e) {
                    flash()->error('Слишком частые попытки авторизации');
                    $this->router->login();
                }
            }

            $auth_user = $this->getUserData();
            $users = $this->getAllUsers();
            $this->router->users($auth_user, $users);
            exit;
        }

        public function logout(){
            $this->auth->logOut();
            $this->auth->destroySession();
            $this->router->login();
        }
        
        public function getUserData(){
            $all_roles = $this->auth->getRoles();
            $role = 0;
            foreach($all_roles as $role){
                switch($role){
                    case "ADMIN":
                        $role = 1;
                    break;
                }
            }
            $data = [
                'id' => $this->auth->getUserId(),
                'role' => $role,
                'is_logged_in' => true
            ];
            return $data;
            
        }

        public function getAllUsers(){
            $users = $this->qb->selectAll("users");
            return $users = $this->user->setStatus($users);
        }
    }
