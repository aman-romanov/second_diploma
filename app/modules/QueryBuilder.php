<?php
    namespace App\modules;

    use Aura\SqlQuery\QueryFactory;
    use PDO;

    class QueryBuilder {
        private $db;
        private $queryFactory;

        public function __construct(){
            $this->db = new PDO("mysql:host=localhost;dbname=second_diploma;charset=utf8mb4", 'tester', 'vOJ1Cls7Q52GTIaT');
            $this->queryFactory = new QueryFactory('mysql');
        }

        public function selectAll($table){
            $select = $this->queryFactory->newSelect();
            $select->cols(['*'])
                ->from($table);

            $sth = $this->db->prepare($select->getStatement());
            $sth->execute($select->getBindValues());
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }

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