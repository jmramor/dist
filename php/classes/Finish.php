<?php

    class Finish{

        private $conn;

        private $db_table = "finish";

        public $finish_id;
        public $name;
        public $description;
        public $date_created;
        public $user_id;
        public $user_name;
        public $user_surname;

        // Database connection
        public function __construct($db){
            $this->conn = $db;
        }

        // GET
        public function getFinishes(){
            $query = "SELECT f.finish_id as finish_id, u.user_id as user_id, f.name as name, f.description as description, f.date_created as date_created, u.name as uname, u.surname as usurname FROM finish f INNER JOIN user u ON f.user_id = u.user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt;
        }

        // CREATE
        public function createFinish(){
            $query = "INSERT INTO finish SET name = :name, user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":user_id", $this->user_id);

            // execute query
            if($stmt->execute()){
                return true;
            }
            return false;
        }
    }