<?php
    namespace App\controllers;
    use App\modules\Users;
    use \Tamtamchik\SimpleFlash\Flash;
    use function Tamtamchik\SimpleFlash\flash;



    class User {

        public function register($data){
            $user = new Users;
            if(!empty($data)){
                if($user->register($data['email'], $data['password'])){
                    $flash->success('Войдите в ваш аккаунт');
                    header('Location:/marlin/second_diploma/');
                }
            }
        }

        public function login($data){
            if(!empty($data)){
                $user = new Users;
                if($user->login($data)){
                    header('Location:/marlin/second_diploma/users');
                }
                header('Location:/marlin/second_diploma/');
            }
        }

        public function logout(){
            $user = new Users;
            $user->logout();
            header('Location:/marlin/second_diploma/');
        }

    }
