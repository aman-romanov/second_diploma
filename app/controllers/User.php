<?php
    namespace App\controllers;
    use App\modules\Users;


    class User {

        public function register($data){
            $user = new Users;
            $user->register($data['email'], $data['password']);
            die('success');
        }

    }
