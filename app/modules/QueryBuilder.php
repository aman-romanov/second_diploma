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
    }