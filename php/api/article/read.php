<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require '../../classes/Database.php';
    require '../../middlewares/Auth.php';
    
    include_once '../../classes/Database.php';
    include_once '../../classes/Article.php';

    $allHeaders = getallheaders();
    $database = new Database();
    $db = $database->dbConnection();

    $auth = new Auth($db,$allHeaders);
    $items = new Article($db);
    
    if($auth->isAuth()){
        $stmt = $items->getArticles();
        $itemCount = $stmt->rowCount();
        
        if($itemCount > 0){
        
            $articleArr = array();
            $articleArr["body"] = array();
            $articleArr["itemCount"] = $itemCount;
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $e = array(
                    "article_id" => $article_id,
                    "name" => $name,
                    "description" => $description,
                    "date_created" => $date_created,
                    "uname" => $uname,
                    "usurname" => $usurname,
                    "user_id" => $user_id,
                    "mname" => $mname,
                    "fname" => $fname,
                );
    
                array_push($articleArr["body"], $e);
            }
            echo json_encode($articleArr);
        }
    
        else{
            http_response_code(404);
            echo json_encode(
                array("message" => "No record found.")
            );
        }   
    }
    else{
        $returnData = [
            "success" => 0,
            "status" => 401,
            "message" => "Unauthorized"
        ];
        echo json_encode($returnData);
    }
    
?>