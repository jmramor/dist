<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require '../../classes/Database.php';
    require '../../middlewares/Auth.php';
    
    include_once '../../classes/database.php';
    include_once '../../classes/Finish.php';

    $allHeaders = getallheaders();
    $database = new Database();
    $db = $database->dbConnection();

    $auth = new Auth($db,$allHeaders);
    $items = new Finish($db);
    
    if($auth->isAuth()){
        $stmt = $items->getFinishes();
        $itemCount = $stmt->rowCount();
        
        if($itemCount > 0){
        
            $finishArr = array();
            $finishArr["body"] = array();
            $finishArr["itemCount"] = $itemCount;
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $e = array(
                    "finish_id" => $finish_id,
                    "name" => $name,
                    "description" => $description,
                    "date_created" => $date_created,
                    "user_name" => $uname,
                    "user_surname" => $usurname,
                    "user_id" => $user_id
                );
    
                array_push($finishArr["body"], $e);
            }
            echo json_encode($finishArr);
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