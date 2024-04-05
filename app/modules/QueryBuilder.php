<?php
    namespace App\modules;

    use Aura\SqlQuery\QueryFactory;
    use PDO;

    class Users {
        private $db;
        private $queryFactory;
        private $select;

        public function __construct(){
            $this->db = new PDO("mysql:host=localhost;dbname=second_diploma;charset=utf8", "tester", "vOJ1Cls7Q52GTIaT");
            $this->queryFactory = new QueryFactory('sqlite');
            $this->select = $queryFactory->newSelect();
        }

        public function selectAll($table){
            $select
                ->cols('*')
                ->from($table);
            
            $sth = $this->pdo->prepare($select->getStatement());
            $sth->execute($select->getBindValues());
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getUserByEmail($email){
            
        }
    }