<?php
    namespace App\modules;
    use \Tamtamchik\SimpleFlash\Flash;
    use function Tamtamchik\SimpleFlash\flash;
    use PDO;

    class Users {

        private $db;
        private $auth;
        private $status = false;
        private $role = 0;

        public function __construct(){
            $this->db = new PDO ("mysql:host=localhost;dbname=second_diploma;charset=utf8mb4", 'tester', 'vOJ1Cls7Q52GTIaT');
            $this->auth = new \Delight\Auth\Auth($this->db);
        }

        public function createUser($email, $password, $username=null){
            try {
                $userId = $this->auth->register($email, $password, $username, function ($selector, $token) {
                    $this->auth->confirmEmail($selector, $token);});
                
                return true;
            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                flash()->error('Введите корректный почтовый адрес!');
            }
            catch (\Delight\Auth\InvalidPasswordException $e) {
                flash()->error('Пароль не корректен!');
            }
            catch (\Delight\Auth\UserAlreadyExistsException $e) {
                flash()->error('Электронный адрес уже занят!');
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                flash()->error('Частые попытки регистрации. Попробуйте позднее.');
            }
        }

        public function authenticate($data){
            try {
                $this->auth->login($data['email'], $data['password']);
            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                flash()->error('Введите корректный почтовый адрес!');
            }
            catch (\Delight\Auth\InvalidPasswordException $e) {
                flash()->error('Почта или пароль не соответствуют');
            }
            catch (\Delight\Auth\EmailNotVerifiedException $e) {
                flash()->error('Почта или пароль не соответствуют');
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                flash()->error('Слишком частые попытки авторизации');
            }
        }

        public function getAuth(){
            return $this->auth;
        }

        public function getStatus($users){
            $i=0;
            while (isset($users[$i])) {
                switch($users[$i]['status']){
                    case 0:
                        $users[$i]['status'] = 'success';
                    break;
                    case 1:
                        $users[$i]['status'] = 'warning';
                    break;
                    case 2:
                        $users[$i]['status'] = 'danger';
                    break;
                }
                $i++;
            }

            return $users;
        }

        public function convertStatus($status){
            
            switch($status){
                case 'Онлайн':
                    $status = 0;
                break;
                case 'Отошел':
                    $status = 1;
                break;
                case 'Не беспокоить':
                    $status = 2;
                break;
            }
                

            return $status;
        }
    }

?>