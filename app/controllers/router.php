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
        public function users(){
            echo $this->templates->render('users', ['name' => 'Jonathan']);
        }
    }
?>