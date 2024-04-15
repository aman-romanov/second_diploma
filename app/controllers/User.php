<?php
    namespace App\controllers;

    use App\controllers\UserInfo;
    use function Tamtamchik\SimpleFlash\flash;
    use App\modules\Users;



    class User extends UserInfo{

        public function register($data = null){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                if(empty($data)){
                    flash()->error('Заполните поля');
                    header('Location:/register');
                }
                $user = $this->user->createUser($data['email'], $data['password']);
                if($user){
                    flash()->success('Войдите в ваш аккаунт');
                    header('Location:/');
                    exit;
                }
                $this->router->register();
                exit;
            }
            if($this->auth->isLoggedIn()){
                header('Location:/users');
            }
            $this->router->register();
        }

        public function users(){
            if(!$this->auth->isLoggedIn()){
                header('Location:/');
            }

            $auth_user = $this->getUserData();
            $users = $this->getAllUsers();
            $this->router->users($auth_user, $users);
        }

        public function login($data = null){
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                
                if($this->user->getUserStatus()){
                    header('Location:/users');
                }
                $this->router->login();
                exit;
            }
            
            if(empty($data['email'])){
                flash()->error('Заполните поля');
                header('Location:/');
                exit;
            }

            try {
                $this->auth->login($data['email'], $data['password']);
                header('Location:/users');

            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                flash()->error('Введите корректный почтовый адрес!');
                header('Location:/');
            }
            catch (\Delight\Auth\InvalidPasswordException $e) {
                flash()->error('Почта или пароль не соответствуют');
                header('Location:/');
            }
            catch (\Delight\Auth\EmailNotVerifiedException $e) {
                flash()->error('Почта не подтверждена');
                header('Location:/');
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                flash()->error('Слишком частые попытки авторизации');
                header('Location:/');
            }
            
            // $auth_user = $this->getUserData();
            // $users = $this->getAllUsers();
            // var_dump($auth_user);
            // var_dump($this->status);
            // var_dump($users);
            // $this->router->users($auth_user, $users);
        }


        public function logout(){
            unset($_SESSION['is_logged_in']);
            $this->auth->logOut();
            $this->auth->destroySession();
            header('Location:/');
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

        public function create($data = null){
            if(!$this->auth->isLoggedIn()){
                header('Location:/login');
            }
            $this->router->create();
        }
    }
