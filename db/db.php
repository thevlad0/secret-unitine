<?php

    class DB {
        private $connection;

        public function __construct() {
            $this->connection = new PDO("mysql:host=localhost:3306;dbname=secretunitine (1)", 'root', '');       
        }

        public function getConnection() {
            return $this->connection;
        }
    }

?>