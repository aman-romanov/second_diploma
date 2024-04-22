<?php

    namespace App\controllers;

    use App\controllers\UserInfo;
    use App\controllers\User;
    use function Tamtamchik\SimpleFlash\flash;
    use App\modules\Users;

    /**
     * Класс с функционалом администратора.
     */

    class Admin extends User {

        /**
         * Редактирование личных данных пользователя. На основе полученного id пользователя идет запрос в бд. При валидации пользователя на авторизацию, последует проверка метода запроса. Post запрос вносит полученные данные в бд через метод класса QueryBuilder и передаресует на главную страницу. Get запрос выводит просто страницу с формой.
         * 
         * @param int $id id пользователя
         * @return null 
         */

        public function edit($id){
            
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

        /**
         * Редактирование почты и пароля пользователя. Сначала метод на оснвое id получает данные пользователя из бд. При валидации авторизованности пользователя идет обработка полученных данных или верстка страницы с формой.
         * 
         * При Post запросе идет проверка почты на наличие в бд. Если почта уже занята и она не соответствует с почтовым адресом самого пользователя, то выводится ошибка и переадресация на главную страницу. Если почта не занята, то методу класса QueryBuilder передается Id и почта из формы. Позже, пароли проверяются на совпадение и при положительном ответе, также методом класса QueryBuilder вносятся изменения.
         * 
         * @param int $id id пользователя
         * @return null 
         */

        public function security($id){
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

        /**
         * Изменение статуса пользователя. На основе Id идет запрос в бд, после валидация авторизации пользователя. Если метод запроса Post, то полученный статус сначала конвертуется в число и после заносится в бд.
         * 
         * @param int $id id пользователя
         * @return null 
         */

        public function status($id){
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

        /**
         * Изменение фотографии пользователя по id. Сначала идет запрос в бд, после проверка метода запроса. При Get запросе просто выводится экран верстки. В Post запросе, проверяется наличие аватарки у пользователя и удаляется. Позже идет валидация файла и при наличии, идет переадресация на главную страничку с флэш сообщением. Если все ок, то картинка перемещается в папку avatars и название вносится в бд.
         * 
         * @param int $id id пользователя
         * @return null 
         */

        public function media($id){
            $user = $this->qb->getUserByID($id);

            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $image = $_FILES['image'];
                if(!empty($user['img'])){
                    $this->img->deleteImage($user['img']);
                }

                $error = $_FILES['image']['error'];
                if($this->img->checkForErrors($error)){
                    header('Location:/users');;
                };

                $image_name = $_FILES['image']['name'];
                $tmp_name = $_FILES['image']['tmp_name'];
                $filename = $this->img->uploadImage($image_name, $tmp_name);
                $this->qb->setImage($id, $filename);
                
                flash()->success('Аватар обновлен');

                header('Location:/users');
            }

            $this->router->media($id, $user);
        }

        /**
         * Удаление пользователя из бд. Сначала по id провертся наличие пользоватетя и после удаление записи из бд. 
         * 
         * @param int $id id пользователя
         * @return null 
         */

        public function delete($id){
            $user = $this->qb->getUserByID($id);
            
            if($this->status == false){
                header('Location:/');
                exit;
            }

            $this->qb->deleteUser($id);
            header('Location:/users');
        }

         /**
         * Создание нового пользователя. Процесс состоит из нескольких этапов. Сначала создается запись на основе почты, пароля и имени пользователя. После запис дополняется личными данными, социальными сетями, статусом и изображением. Принимаемый аргумент изначально в статусе null, так как при Get запросе просто выводится верстка страницы.
         * 
         * @param array $data Данные пользователя с формы
         * @return null 
         */

        public function create($data = null){
            $data = $_POST;
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