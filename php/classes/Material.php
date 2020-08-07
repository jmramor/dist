<?php

    class Material{

        private $conn;

        private $db_table = "material";

        public $material_id;
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
        public function getMaterials(){
            $query = "SELECT m.material_id as material_id, u.user_id as user_id, m.name as name, m.description as description, m.date_created as date_created, u.name as uname, u.surname as usurname, m.density as density FROM material m INNER JOIN user u ON m.user_id = u.user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt;
        }

        // CREATE
        public function createMaterial(){
            $query = "INSERT INTO material SET name = :name, density = :density, user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":density", $this->density);
            $stmt->bindParam(":user_id", $this->user_id);

            // execute query
            if($stmt->execute()){
                return true;
            }
            return false;
        }


    }