<?php

    class Plateprint{

        private $conn;

        private $db_table = "plate_print";

        public $plate_print_id;
        public $plate_id;
        public $date_created;
        public $user_name;
        public $user_surname;
        public $user_id;
        public $weight;

        // Database connection
        public function __construct($db){
            $this->conn = $db;
        }

        // GET
        public function getPlateprints(){
            $query = "SELECT pp.plateprint_id as plateprint_id, pp.plate_id as plate_id, pp.weight as weight, pp.date_created as date_created, u.name as user_name, u.surname as user_surname, u.user_id as user_id FROM plate_print pp, plate p, user u WHERE pp.plate_id = p.plate_id and u.user_id = pp.user_id and p.plate_id = " . $this->plate_id . " ORDER BY pp.date_created DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt;
        }

        // GET ONE
        public function getPlateprint(){
            $query = "SELECT pp.plateprint_id as plateprint_id, pp.plate_id as plate_id, pp.weight as weight, pp.date_created as date_created, u.name as user_name, u.surname as user_surname, u.user_id as user_id FROM plate_print pp, plate p, user u WHERE pp.plate_id = p.plate_id and u.user_id = pp.user_id and pp.plateprint_id = " . $this->plateprint_id;
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt;
        }

        
    }
