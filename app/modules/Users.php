<?php
    namespace App\modules;
    
    use Delight\Auth\Auth;
    use Tamtamchik\SimpleFlash\Flash;
    use function Tamtamchik\SimpleFlash\flash;
    use Aura\SqlQuery\QueryFactory;
    use PDO;

    /**
     * Класс взаимодействия пользователя с бд.
     */

    class Users {

        private $db;
        private $auth;
        private $status = false;
        private $role = 0;

        public function __construct(PDO $pdo, Auth $auth){
            $this->db = $pdo;
            $this->auth = $auth;
        }

        /**
         * Создание нового пользователя на основе почты, пароля и имении. Изначально имя в значении null, так как во время регисрации нет поля для имени.
         * 
         * @param string $email Почта пользователя
         * @param string $passsword Пароль пользователя
         * @param string $username Имя пользователя
         * @return bool true при создании записи
         */

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

        /**
         * Авторизация пользователя с помощью почты и пароля
         * 
         * @param string $email Почта пользователя
         * @param string $passsword Пароль пользователя
         * @return null 
         */

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

        /**
         * Getter для компонента Auth
         * 
         * @return obj 
         */

        public function getAuth(){
            return $this->auth;
        }

        /**
         * Конвертация статусов всех пользователй в текстовый формат. 
         * 
         * @param array $users Данные всех пользователей
         * @return array $result Ассоциативный массив с данными пользователя 
         */

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

        /**
         * Конвертация статуса пользователя в числовое значение. Делается потому, что столбец может принимать лишь числовые значения. Так было указано при создании таблицы с помощь компонента Auth.
         * 
         * @param string $status Статус пользователя
         * @return int $status Конвертированный статус
         */

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