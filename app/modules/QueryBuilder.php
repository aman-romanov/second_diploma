<?php
    namespace App\modules;

    use Aura\SqlQuery\QueryFactory;
    use PDO;

    /**
     * Класс запросов в бд.
     */

    class QueryBuilder {
        private $db;
        private $queryFactory;

        public function __construct(PDO $pdo, QueryFactory $qf){
            $this->db = $pdo;
            $this->queryFactory = $qf;
        }

        /**
         * Запрос данных всех пользователей в таблице.
         * 
         * @param string $table Название таблицы
         * @return array $result Ассоциативный массив с данными пользователей 
         */

        public function selectAll($table){
            $select = $this->queryFactory->newSelect();
            $select->cols(['*'])
                ->from($table);

            $sth = $this->db->prepare($select->getStatement());
            $sth->execute($select->getBindValues());
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }

        /**
         * Запрос данных одного пользователя по Id.
         * 
         * @param int $id Id пользователя
         * @return array $result Ассоциативный массив с данными пользователя 
         */

        public function getUserByID($id){
            $select = $this->queryFactory->newSelect();
            $select->cols(['*'])
                ->from('users')
                ->where('id = :id', ['id' => $id]);
            $sth = $this->db->prepare($select->getStatement());
            $sth->execute($select->getBindValues());
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            return $result;
        }
        /**
         * Обновление личных данных пользователя
         * 
         * @param int $id Id пользователя
         * @param array $data Данные пользователя с формы
         * @return boolean $result true если данные обновлены
         */

        public function updateData($id, $data=[]){
            $update = $this->queryFactory->newUpdate();
            $update
                ->table('users')
                ->cols([
                    'username' => ':username',
                    'job_title' => ':job_title',
                    'phone' => ':phone',
                    'address' => ':address'
                ])
                ->where('id = :id')
                ->bindValues([
                    ':id' => $id,
                    ':username' => $data['username'],
                    ':job_title' => $data['job_title'],
                    ':phone' => $data['phone'],
                    ':address' => $data['address']
                ]);
            $sth = $this->db->prepare($update->getStatement());
            $sth->execute($update->getBindValues());
            return true;
        }

        /**
         * Обновление данных соц сетей пользователя
         * 
         * @param int $id Id пользователя
         * @param array $data Данные пользователя с формы
         * @return boolean $result true если данные обновлены
         */

        public function updateSocials($id, $data=[]){
            $update = $this->queryFactory->newUpdate();
            $update
                ->table('users')
                ->cols([
                    'instagram' => ':instagram',
                    'telegram' => ':telegram',
                    'vk' => ':vk'
                ])
                ->where('id = :id')
                ->bindValues([
                    ':id' => $id,
                    ':instagram' => $data['instagram'],
                    ':telegram' => $data['telegram'],
                    ':vk' => $data['vk']
                ]);
            $sth = $this->db->prepare($update->getStatement());
            $sth->execute($update->getBindValues());
            return true;
        }

        /**
         * Проверка наличие пользователя по почте
         * 
         * @param string $email Почта пользователя
         * @return array $result Ассоциативный массив с данными пользователя 
         */

        public function checkEmail($email){
            $select = $this->queryFactory->newSelect();
            $select->cols(['*'])
                ->from('users')
                ->where('email = :email', ['email' => $email]);
            $sth = $this->db->prepare($select->getStatement());
            $sth->execute($select->getBindValues());
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            return $result;
        }
         /**
         * Смена почты пользователя
         * 
         * @param int $id Id пользователя
         * @param string $email Почта пользователя
         * @return boolean $result true если данные обновлены
         */

        public function changeEmail($id, $email){
            $update = $this->queryFactory->newUpdate();
            $update
                ->table('users')
                ->cols([
                    'email' => ':email'
                ])
                ->where('id = :id')
                ->bindValues([
                    ':id' => $id,
                    ':email' => $email
                ]);
            $sth = $this->db->prepare($update->getStatement());
            $sth->execute($update->getBindValues());
            return true;
        }

        /**
         * Смена пароля пользователя
         * 
         * @param int $id Id пользователя
         * @param string $password Пароль пользователя
         * @return boolean $result true если данные обновлены
         */

        public function changePassword($id, $password){
            $password = password_hash($password, PASSWORD_DEFAULT);
            $update = $this->queryFactory->newUpdate();
            $update
                ->table('users')
                ->cols([
                    'password' => ':password'
                ])
                ->where('id = :id')
                ->bindValues([
                    ':id' => $id,
                    ':password' => $password
                ]);
            $sth = $this->db->prepare($update->getStatement());
            $sth->execute($update->getBindValues());
            return true;
        }

        /**
         * Обноваление статуса пользователя
         * 
         * @param int $id Id пользователя
         * @param int $status Стаус пользователя конвертированное в число
         * @return boolean $result true если данные обновлены
         */

        public function setStatus($id, $status){
            $update = $this->queryFactory->newUpdate();
            $update
                ->table('users')
                ->cols([
                    'status' => ':status'
                ])
                ->where('id = :id')
                ->bindValues([
                    ':id' => $id,
                    ':status' => $status
                ]);
            $sth = $this->db->prepare($update->getStatement());
            $sth->execute($update->getBindValues());
            return true;
        }

        /**
         * Обноваление аватарки пользователя
         * 
         * @param int $id Id пользователя
         * @param string $string Наименование файла
         * @return boolean $result true если данные обновлены
         */

        public function setImage($id, $filename){
            $update = $this->queryFactory->newUpdate();
            $update
                ->table('users')
                ->cols([
                    'img' => ':img'
                ])
                ->where('id = :id')
                ->bindValues([
                    ':id' => $id,
                    ':img' => $filename
                ]);
            $sth = $this->db->prepare($update->getStatement());
            $sth->execute($update->getBindValues());
            return true;
        }

        /**
         * Удаление записи с пользователем
         * 
         * @param int $id Id пользователя
         * @return boolean $result true если запись удалена
         */

        public function deleteUser($id){
            $delete = $this->queryFactory->newDelete();
            $delete
                ->from('users')
                ->where('id = :id')
                ->bindValue(':id', $id);
            $sth = $this->db->prepare($delete->getStatement());
            $sth->execute($delete->getBindValues());
            return true;
        }

    }