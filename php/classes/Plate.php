<?php
    include_once '../../functions.php';
    class Plate{

        private $conn;

        private $db_table = "plate";

        public $plate_id;
        public $name;
        public $charge;
        public $date_created;
        public $user_name;
        public $user_surname;
        public $user_id;
        public $material_name;
        public $finish_name;
        public $thickness;
        public $pvc;
        public $scotch;
        public $date_changed;
        public $location_name;
        public $area;
        public $dimx;
        public $dimy;
        public $article_name;
        public $article_id;
        public $location_id;
        public $active;
        public $density;
        public $weight;
        public $dimx_prev;
        public $dimy_prev;

        // Database connection
        public function __construct($db){
            $this->conn = $db;
        }

        // GET
        public function getPlates(){
            $query = "SELECT p.article_id as article_id, p.notes as notes, p.location_id as location_id, p.plate_id as plate_id, p.name as name, p.charge as charge, p.date_created as date_created, p.date_changed as date_changed, u.name as uname, u.surname as usurname, u.user_id as user_id, m.name as mname, f.name as fname, p.dimx as dimx, p.dimy as dimy, l.name as lname, p.pvc as pvc, p.scotch as scotch, a.name as aname, p.thickness as thickness, p.active as active, m.density as density FROM plate p, user u, article a, material m, finish f, location l WHERE p.article_id = a.article_id and a.material_id = m.material_id and a.finish_id = f.finish_id and u.user_id = p.user_id and l.location_id = p.location_id";
            
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

        // Create
        public function createPlate(){

            $query = "INSERT INTO plate SET name = :name, article_id = :article_id, location_id = :location_id, charge = :charge, thickness = :thickness, pvc = :pvc, scotch = :scotch, user_id = :user_id, dimx = :dimx, dimy = :dimy";

            $stmt = $this->conn->prepare($query);
            
            $this->name=strip_tags($this->name);

            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":location_id", $this->location_id);
            $stmt->bindParam(":article_id", $this->article_id);
            $stmt->bindParam(":charge", $this->charge);
            $stmt->bindParam(":thickness", $this->thickness);
            $stmt->bindParam(":pvc", $this->pvc);
            $stmt->bindParam(":scotch", $this->scotch);
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":dimx", $this->dimx);
            $stmt->bindParam(":dimy", $this->dimy);

            // execute query
            if($stmt->execute()){
                return true;
            }
  
            return false;
        }

        // Update
        public function updatePlate($archive_update){
            $query = "UPDATE plate SET location_id = :location_id, dimx = :dimx, dimy = :dimy, active = :active, date_changed = CURRENT_TIMESTAMP(), charge = :charge WHERE plate_id = :plate_id";
            
            $stmt = $this->conn->prepare($query);
            
            if($archive_update == 1){
                $archive_query = "INSERT INTO plate_archive SET `plate_id` = :plate_id, `dimx` = :dimx, `dimy` = :dimy, `user_id`= :user_id";
                
                $archive_stmt = $this->conn->prepare($archive_query);

                $archive_stmt->bindParam(":dimx", $this->dimx_prev);
                $archive_stmt->bindParam(":dimy", $this->dimy_prev);
                $archive_stmt->bindParam(":user_id", $this->user_id);
                $archive_stmt->bindParam(":plate_id", $this->plate_id);
    
                $archive_stmt->execute();

                $print_query = "INSERT INTO plate_print SET `plate_id` = :plate_id, `user_id`= :user_id, `weight` = :weight";

                $print_stmt = $this->conn->prepare($print_query);

                $print_stmt->bindParam(":user_id", $this->user_id);
                $print_stmt->bindParam(":plate_id", $this->plate_id);

                $print_stmt->bindParam(":weight", $this->weight);

                $print_stmt->execute();
            }

            $stmt->bindParam(":dimx", $this->dimx);
            $stmt->bindParam(":dimy", $this->dimy);
            $stmt->bindParam(":location_id", $this->location_id);
            $stmt->bindParam(":plate_id", $this->plate_id);
            $stmt->bindParam(":active", $this->active );
            $stmt->bindParam(":charge", $this->charge );

            // execute query
            if($stmt->execute()){
                return true;
            }
  
            return false;
        }

        public function plateSplit( $name2, $loc2, $dims2x, $dims2y){
            $active = 1;


            $query_update = "UPDATE plate SET location_id = :location_id, dimx = :dimx, dimy = :dimy, active = :active, date_changed = CURRENT_TIMESTAMP(), charge = :charge WHERE plate_id = :plate_id";
            $query_print = "INSERT INTO plate_print SET `plate_id` = :plate_id, `user_id`= :user_id, `weight` = :weight";
            $archive_query = "INSERT INTO plate_archive SET `plate_id` = :plate_id, `dimx` = :dimx, `dimy` = :dimy, `user_id`= :user_id";
            $query_new = "INSERT INTO plate SET name = :name, article_id = :article_id, location_id = :location_id, charge = :charge, thickness = :thickness, pvc = :pvc, scotch = :scotch, user_id = :user_id, dimx = :dimx, dimy = :dimy";

            $stmts_true = true;
            
            $stmt = $this->conn->prepare($query_update);
            
            $stmt->bindParam(":dimx", $this->dimx);
            $stmt->bindParam(":dimy", $this->dimy);
            $stmt->bindParam(":location_id", $this->location_id);
            $stmt->bindParam(":plate_id", $this->plate_id);
            $stmt->bindParam(":active", $active );
            $stmt->bindParam(":charge", $this->charge );

            //archieve
            $archive_stmt = $this->conn->prepare($archive_query);

            $archive_stmt->bindParam(":dimx", $this->dimx_prev);
            $archive_stmt->bindParam(":dimy", $this->dimy_prev);
            $archive_stmt->bindParam(":user_id", $this->user_id);
            $archive_stmt->bindParam(":plate_id", $this->plate_id);
            
            //create
            $create_stmt = $this->conn->prepare($query_new);

            $create_stmt->bindParam(":name", $name2);
            $create_stmt->bindParam(":location_id", $loc2);
            $create_stmt->bindParam(":article_id", $this->article_id);
            $create_stmt->bindParam(":charge", $this->charge);
            $create_stmt->bindParam(":thickness", $this->thickness);
            $create_stmt->bindParam(":pvc", $this->pvc);
            $create_stmt->bindParam(":scotch", $this->scotch);
            $create_stmt->bindParam(":user_id", $this->user_id);
            $create_stmt->bindParam(":dimx", $dims2x);
            $create_stmt->bindParam(":dimy", $dims2y);

            //print
            $print_stmt = $this->conn->prepare($query_print);

            $print_stmt->bindParam(":user_id", $this->user_id);
            $print_stmt->bindParam(":plate_id", $this->plate_id);
            $print_stmt->bindParam(":weight", $this->weight);

            if(!$stmt->execute()){
                $stmts_true = false;
            }
            if(!$create_stmt->execute()){
                $stmts_true = false;
            }
            if(!$print_stmt->execute()){
                $stmts_true = false;
            }
            if(!$archive_stmt->execute()){
                $stmts_true = false;
            }

            if($stmts_true == true ){
                return true;
            }

            return false;
        }
    }
