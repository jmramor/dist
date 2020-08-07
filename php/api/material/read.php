<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require '../../classes/Database.php';
    require '../../middlewares/Auth.php';
    
    include_once '../../classes/database.php';
    include_once '../../classes/Material.php';

    $allHeaders = getallheaders();
    $database = new Database();
    $db = $database->dbConnection();

    $auth = new Auth($db,$allHeaders);
    $items = new Material($db);
    
    if($auth->isAuth()){
        $stmt = $items->getMaterials();
        $itemCount = $stmt->rowCount();
        
        if($itemCount > 0){
        
            $materialArr = array();
            $materialArr["body"] = array();
            $materialArr["itemCount"] = $itemCount;
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $e = array(
                    "material_id" => $material_id,
                    "name" => $name,
                    "description" => $description,
                    "density" => $density,
                    "date_created" => $date_created,
                    "user_name" => $uname,
                    "user_surname" => $usurname,
                    "user_id" => $user_id
                );
    
                array_push($materialArr["body"], $e);
            }
            echo json_encode($materialArr);
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