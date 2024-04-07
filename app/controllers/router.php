<?php
    namespace App\controllers;
    use League\Plates\Engine;

    Class Router {

        private $templates;


        function __construct(){
            $this->templates = new Engine('../app/views');
        }
        public function login(){
            echo $this->templates->render('page_login', ['name' => 'Jonathan']);
        }
        public function register(){
            echo $this->templates->render('page_register', ['name' => 'Jonathan']);
        }
        public function users($auth_user, $users){
            echo $this->templates->render('users', ['auth' => $auth_user, 'users' => $users]);
        }
        public function create(){
            echo $this->templates->render('create_user', ['name' => 'Jonathan']);
        }
        public function edit($id){
            echo $this->templates->render('edit', ['id' => $id]);
        }
        public function media($id){
            echo $this->templates->render('media', ['id' => $id]);
        }
        public function security($id){
            echo $this->templates->render('security', ['id' => $id]);
        }
        public function status($id){
            echo $this->templates->render('status', ['id' => $id]);
        }
    }
?>