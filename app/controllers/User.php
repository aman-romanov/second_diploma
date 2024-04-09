<?php
    namespace App\controllers;

    use App\controllers\UserInfo;
    use function Tamtamchik\SimpleFlash\flash;



    class User extends UserInfo{

        public function register($data){
            if($this->state == true){
                $auth_user = $this->getUserData();
                $users = $this->getAllUsers();
                header('Location:/');
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

        public function users(){
            if($_SESSION['is_logged_in'] == true){
                header('Location:/');
            }

            $auth_user = $this->getUserData();
            $users = $this->getAllUsers();;
            $this->router->users($auth_user, $users);
        }

        public function login($data){
            if($this->status == false && $_SESSION['is_logged_in'] !== true){
                if(empty($data['email'])){
                    flash()->error('Заполните поля');
                    $this->router->login();
                    exit;
                }
                try {
                    $this->auth->login($data['email'], $data['password']);
                    $_SESSION['is_logged_in'] = true;
                    header('Location:/users');

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
    }
