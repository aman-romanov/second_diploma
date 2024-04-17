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

            if($this->status == false){
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

            if($this->status == false){
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

        public function status($id){
            $id = $id['id'];
            $user = $this->qb->getUserByID($id);

            if($this->status == false){
                header('Location:/');
                exit;
            }
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $status = $this->user->convertStatus($_POST['status']);
                $result = $this->qb->setStatus($id, $status);
                if($result){
                    flash()->success('Данные обновлены');
                }
                header('Location:/users');
            }
            $this->router->status($id, $user);
        }

        public function media($id){
            $id = $id['id'];
            $user = $this->qb->getUserByID($id);

            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $image = $_FILES['image'];
                if(!empty($user['img'])){
                    $this->img->deleteImage($user['img']);
                    $error = $_FILES['image']['error'];
                    if($this->img->checkForErrors($error)){
                        header('Location:/users');;
                    };

                    $image_name = $_FILES['image']['name'];
                    $tmp_name = $_FILES['image']['tmp_name'];
                    $filename = $this->img->uploadImage($image_name, $tmp_name);
                    $this->qb->setImage($id, $filename);
                    
                    flash()->success('Аватар обновлен');
                }

                header('Location:/users');
            }

            $this->router->media($id, $user);
        }

        public function delete($id){
            $id = $id['id'];
            $user = $this->qb->getUserByID($id);
            
            if($this->status == false){
                header('Location:/');
                exit;
            }

            $this->qb->deleteUser($id);
            header('Location:/users');
        }

        public function create($data){
            if($this->status == false){
                header('Location:/');
                exit;
            }

            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $image = $_FILES['image'];
                $error = $_FILES['image']['error'];
                $image_name = $_FILES['image']['name'];
                $tmp_name = $_FILES['image']['tmp_name'];
                if($this->user->createUser($data['email'], $data['password'], $data['username'])){
                    $user = $this->qb->checkEmail($data['email']);
                    $this->qb->updateData($user['id'], $data);
                    $this->qb->updateSocials($user['id'], $data);

                    $status = $this->user->convertStatus($data['status']);
                    $this->qb->setStatus($user['id'], $status);

                    $this->img->checkForErrors($error);
                    $filename = $this->img->uploadImage($image_name, $tmp_name);
                    $this->qb->setImage($user['id'], $filename);

                    flash()->success('Пользователь добавлен');
                }
                header('Location:/users');
            }

            $this->router->create();
        }
    }

?>