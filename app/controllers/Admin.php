<?php

    namespace App\controllers;

    use App\controllers\UserInfo;
    use App\controllers\User;
    use function Tamtamchik\SimpleFlash\flash;
    use App\modules\Users;

    class Admin extends User {

        public function edit($id){
            $id = $id['id'];
            $user = $this->qb->getUserByID($id);

            if(!$this->auth->isLoggedIn()){
                header('Location:/');
                exit;
            }
            

            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                if($this->qb->updateData($id, $_POST)){
                    flash()->success('Данные обновлены');
                    header('Location:/users');
                    exit;
                }
            }

            $this->router->edit($id, $user);
        }

        public function security($id){
            $id = $id['id'];
            $user = $this->qb->getUserByID($id);

            if(!$this->auth->isLoggedIn()){
                header('Location:/');
                exit;
            }

            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                if($this->qb->checkEmail($_POST['email'])){
                    if($_POST['email'] !== $user['email']){
                        flash()->error('Почтовый адрес уже занят');
                        header('Location:/users');
                        exit;
                    }
                }
                if(isset($_POST['email'])){
                    if($this->qb->changeEmail($id, $_POST['email'])){
                        flash()->success('Данные обновлены');
                    }
                }
                if(isset($_POST['password'])){
                    if($_POST['password'] !== $_POST['passwordVerify']){
                        flash()->error('Пароли не совпадают');
                        header('Location:/users');
                        exit;
                    }
                    if($this->qb->changePassword($id, $_POST['password'])){
                        flash()->success('Данные обновлены');
                    }
                }

                header('Location:/users');
            }
            $this->router->security($id, $user);
        }
    }

?>