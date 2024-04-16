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

            if($this->status == false){
                header('Location:/');
                exit;
            }

            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $image = $_FILES['image'];
                if(!empty($user['img'])){
                    self::deleteImage($user['img']);
                    $error = $_FILES['image']['error'];
                    if(self::checkForErrors($error)){
                        header('Location:/users');;
                    };

                    $image_name = $_FILES['image']['name'];
                    $tmp_name = $_FILES['image']['tmp_name'];
                    $filename = self::uploadImage($image_name, $tmp_name);
                    $this->qb->setImage($id, $filename);
                    
                    flash()->success('Аватар обновлен');
                }

                header('Location:/users');
            }

            $this->router->media($id, $user);
        }

        function uploadImage($image_name, $tmp_name){
            $pathinfo = pathinfo($image_name);
            $base = $pathinfo['filename'];
            $base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);
            $filename = $base . "." . $pathinfo['extension'];
            $destination = __DIR__ . '../../public/img/demo/avatars/' . $filename;
            $i = 1;
            while(file_exists($destination)){
                $filename = $base . "($i)." . $pathinfo['extension'];
                $destination = __DIR__ . '../../public/img/demo/avatars/' . $filename;
                $i++;
            }
            if(move_uploaded_file($tmp_name, $destination)){
                echo 123;
                exit;
            };
            return $filename;
        }

        function deleteImage($img){
            $destination = __DIR__ . '../../public//img/demo/avatars/' . $img;
            if(file_exists($destination)){
                unlink(__DIR__ . '../../public//img/demo/avatars/' . $img);
            }
        }

        function checkForErrors($error){
            switch($error){
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    flash()->error("Прикрепите файл");
                    break;
                case UPLOAD_ERR_INI_SIZE:
                    flash()->error("Размер изображения не должно превышать 2M");
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    flash()->error("Папка не найденa");
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    flash()->error("Изображение не переместилось");
                    break;
                default:
                    flash()->error("Возникла ошибка");
                    break;
            }
            $mime_types = ['image/jpg', 'image/jpeg', 'image/png'];
            if($_FILES['image']['tmp_name']>0){
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $_FILES['image']['tmp_name']);
                if(!in_array($mime_type, $mime_types)){
                    flash()->error("Изображение должно соответствовать форматам: jpeg/jpg/png");
                }
            }
            if(flash()->display('error')){
                return true;
            }
            return false;
        }
    }

?>