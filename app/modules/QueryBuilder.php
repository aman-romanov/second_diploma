<?php
    namespace App\modules;

    use Aura\SqlQuery\QueryFactory;
    use PDO;

    class QueryBuilder {
        private $db;
        private $queryFactory;

        public function __construct(){
            $this->db = new PDO("mysql:host=localhost;dbname=second_diploma;charset=utf8mb4", "tester", "vOJ1Cls7Q52GTIaT");
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
            $sth = $this->pdo->prepare($update->getStatement());
            if($sth->execute($update->getBindValues())){
                return true;
            }
        }
    }