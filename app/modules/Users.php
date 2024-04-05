<?php
    namespace App\modules;
    use \Tamtamchik\SimpleFlash\Flash;
    use function Tamtamchik\SimpleFlash\flash;
    use PDO;

    class Users {

        private $db;
        private $auth;

        public function __construct(){
            $this->db = new PDO("mysql:host=localhost;dbname=second_diploma;charset=utf8", "tester", "vOJ1Cls7Q52GTIaT");
            $this->auth = new \Delight\Auth\Auth($this->db);
        }

        public function register($email, $password, $username=null){
            try {
                $userId = $this->auth->register($email, $password, $username, function ($selector, $token) {
                });
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

        public function login($data){
            if(!empty($data)){
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
        }

        public function logout(){
            $this->auth->logOut();
            $this->auth->destroySession();
        }
    }

?>