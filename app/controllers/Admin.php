<?php

    namespace App\controllers;

    use App\controllers\User;
    use function Tamtamchik\SimpleFlash\flash;

    class Admin extends User {
        public function edit($id){
            $id = $id['id'];
            if($_SESSION['is_logged_in'] = false){
                header('Location:/');
            }
            

            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                if($this->qb->updateData($id, $_POST)){
                    flash()->success('Данные обновлены');
                    header('Location:/users');
                    exit;
                }
            }

            $user = $this->qb->getUserByID($id);
            $this->router->edit($id, $user);
        }
    }

?>