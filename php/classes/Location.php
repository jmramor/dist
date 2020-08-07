<?php

    class Location{

        private $conn;

        private $db_table = "location";

        public $location_id;
        public $name;
        public $date_created;
        public $user_id;
        public $user_name;
        public $user_surname;

        // Database connection
        public function __construct($db){
            $this->conn = $db;
        }

        // GET
        public function getLocations(){
            $query = "SELECT l.location_id as location_id, u.user_id as user_id, l.name as name, l.date_created as date_created, u.name as uname, u.surname as usurname FROM location l INNER JOIN user u ON l.user_id = u.user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt;
        }

        // CREATE
        public function createLocation (){
            $query = "INSERT INTO location SET name = :name, user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            
            $this->name=strip_tags($this->name);

            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":user_id", $this->user_id);

            // execute query
            if($stmt->execute()){
                return true;
            }
  
            return false;
        }


    }