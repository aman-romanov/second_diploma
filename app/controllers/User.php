<?php
    namespace App\controllers;

    use App\controllers\UserInfo;
    use function Tamtamchik\SimpleFlash\flash;
    use App\modules\Users;

    /**
     * Класс с функционалом обычного пользователя
     */

    class User extends UserInfo{

        /**
         * Регистрация пользователя через почту и пароль. Метод сначала проверяет тип запроса на Get и Post. При Get запросе проверяется авторизован ли пользователь. При положительном ответе идет переадресация на главную страницу, или же выводится верстка страницы регистрации. 
         * 
         * При Post запросе, сначала полученные проходят валидацию на заполненность, после идет регистрация пользователя посредством компонента и переадресация на главную страницу.
         * 
         * @param array $data Данные пользователя
         * @return null 
         */

        public function register($data = null){
            $data = $_POST;
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                if(empty($data)){
                    flash()->error('Заполните поля');
                    header('Location:/register');
                }
                $user = $this->user->createUser($data['email'], $data['password']);
                if($user){
                    flash()->success('Войдите в ваш аккаунт');
                    header('Location:/');
                    exit;
                }
                $this->router->register();
                exit;
            }
            if($this->status == true){
                header('Location:/users');
            }
            $this->router->register();
        }

        /**
         * Верстка главной страницы со списком пользователей. Сначала проверяется авторизация пользователя. После через методы компонента Auth извлекаются данные авторизованного пользователя и данные всех пользователей в бд. Параметры передаются в верстку страницы в качестве аргументов и после выводится страница.
         * 
         * @param null 
         * @return null 
         */

        public function users(){
            if($this->status == false){
                header('Location:/');
            }

            $auth_user = $this->getUserData();
            $users = $this->getAllUsers();
            $this->router->users($auth_user, $users);
        }

        /**
         * Автризация пользователя через почту и пароль. Сначала идет проверка метода запроса на GET. При положительном ответе, проверется статус пользователя на авторизацию и после верстка страницы логина. 
         * 
         * При POST запросе, сначала идет валидация полученных данных и после авторизация через метод компонента. При удачном авторизации, в свойства $status и $role передаются данные пользователя. В конце идет переадресация на главную страницу. Если возникнет ошибка, то текст записывается в метод flash() и переадресация обратно на страницу логина.
         * 
         * @param array $data Данные пользователя
         * @return null 
         */

        public function login($data = null){
            $data = $_POST;
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                
                if($this->status == true){
                    header('Location:/users');
                }
                $this->router->login();
                exit;
            }

            
            
            if(empty($data['email'])){
                flash()->error('Заполните поля');
                header('Location:/');
                exit;
            }

            try {
                $this->auth->login($data['email'], $data['password']);
                if($this->auth->isLoggedIn()){
                    $this->status = true;
                }
                if($this->auth->hasRole(\Delight\Auth\Role::ADMIN)){
                    $this->role = 1;
                }
                header('Location:/users');

            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                flash()->error('Введите корректный почтовый адрес!');
                header('Location:/');
            }
            catch (\Delight\Auth\InvalidPasswordException $e) {
                flash()->error('Почта или пароль не соответствуют');
                header('Location:/');
            }
            catch (\Delight\Auth\EmailNotVerifiedException $e) {
                flash()->error('Почта не подтверждена');
                header('Location:/');
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                flash()->error('Слишком частые попытки авторизации');
                header('Location:/');
            }
            
            // $auth_user = $this->getUserData();
            // $users = $this->getAllUsers();
            // var_dump($auth_user);
            // var_dump($this->status);
            // var_dump($users);
            // $this->router->users($auth_user, $users);
        }

        /**
         * Выход пользователя с аккаунта и переадресация на страницу логина.
         * 
         * @param null 
         * @return null 
         */
        public function logout(){
            $this->auth->logOut();
            header('Location:/');
        }

        /**
         * Getter данных авторизованного пользователя из свойств класса.
         * 
         * @param null 
         * @return null 
         */
        
        public function getUserData(){
            $data = [
                'id' => $this->auth->getUserId(),
                'role' => $this->role,
                'is_logged_in' => $this->state
            ];
            return $data;
            
        }

        /**
         * Getter всех пользователей из бд.
         * 
         * @param null 
         * @return array Данные всех пользователей из бд. 
         */

        public function getAllUsers(){
            $users = $this->qb->selectAll("users");
            return $users = $this->user->getStatus($users);
        }

        /**
         * Обработчик профиля пользователя. На основе id пользователя идет запрос в бд. После валидация пользователя на авторизацию и при положительном ответе - верстка профиля пользователя. В аргкменты методы класса Router передаются данные пользователя и id.
         * 
         * @param int id пользователя
         * @return null 
         */

        public function profile($id){
            $user = $this->qb->getUserByID($id);

            if($this->status == false){
                header('Location:/');
            }
            $this->router->profile($id, $user);
            exit;
        }
    }
