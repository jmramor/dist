<?php

    class Article{

        private $conn;

        private $db_table = "article";

        public $article_id;
        public $name;
        public $description;
        public $date_created;
        public $user_id;
        public $user_name;
        public $user_surname;
        public $material_name;
        public $finish_name;
        public $material_id;
        public $finish_id;

        // Database connection
        public function __construct($db){
            $this->conn = $db;
        }

        // GET
        public function getArticles(){
            $query = "SELECT a.article_id as article_id, a.name as name, a.description as description, a.date_created as date_created, u.user_id as user_id, u.name as uname, u.surname as usurname, f.name as fname, m.name as mname FROM article a, user u, material m, finish f WHERE a.material_id = m.material_id and a.finish_id = f.finish_id and a.user_id = u.user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt;
        }

        // CREATE
        public function createArticle(){
            $query = "INSERT INTO article SET name = :name, description = :description, material_id = :material_id, finish_id = :finish_id, user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            
            $this->name=strip_tags($this->name);

            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":material_id", $this->material_id);
            $stmt->bindParam(":finish_id", $this->finish_id);
            $stmt->bindParam(":user_id", $this->user_id);

            // execute query
            if($stmt->execute()){
                return true;
            }
  
            return false;
        }


    }