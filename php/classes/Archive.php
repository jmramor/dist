<?php

    class Archive{

        private $conn;

        private $db_table = "plate_archive";

        public $plate_archive_id;
        public $plate_id;
        public $date_created;
        public $user_name;
        public $user_surname;
        public $user_id;
        public $thickness;
        public $area;
        public $dimx;
        public $dimy;
        public $density;
        public $weight;

        // Database connection
        public function __construct($db){
            $this->conn = $db;
        }

        // GET
        public function getArchive(){
            $query = "SELECT pa.platearchive_id as platearchive_id, pa.plate_id as plate_id, pa.dimx as dimx, pa.dimy as dimy, pa.user_id as user_id, u.name as u_name, u.surname as u_surname, pa.date_created as date_created, p.thickness as thickness, m.density as density FROM plate_archive pa, plate p, article a, material m, user u WHERE pa.plate_id = p.plate_id and p.article_id = a.article_id and a.material_id = m.material_id and pa.user_id = u.user_id and p.plate_id = " . $this->plate_id;
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt;
        }

        // GET ONE
        public function getPlate(){
            $query = "SELECT p.article_id as article_id, p.notes as notes, p.location_id as location_id, p.plate_id as plate_id, p.name as name, p.charge as charge, p.date_created as date_created, p.date_changed as date_changed, u.name as uname, u.surname as usurname, u.user_id as user_id, m.name as mname, f.name as fname, p.dimx as dimx, p.dimy as dimy, l.name as lname, p.pvc as pvc, p.scotch as scotch, a.name as aname, p.thickness as thickness, p.active as active, m.density as density FROM plate p, user u, article a, material m, finish f, location l WHERE p.article_id = a.article_id and a.material_id = m.material_id and a.finish_id = f.finish_id and u.user_id = p.user_id and l.location_id = p.location_id and p.plate_id = " . $this->plate_id;
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt;
        }

        
    }
