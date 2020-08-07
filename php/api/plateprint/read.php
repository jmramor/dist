<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require '../../classes/Database.php';
    require '../../middlewares/Auth.php';
    
    include_once '../../classes/database.php';
    include_once '../../classes/Plateprint.php';
    include_once '../../functions.php';
    
    $data = json_decode(file_get_contents("php://input"));
    
    $allHeaders = getallheaders();
    $database = new Database();
    $db = $database->dbConnection();
    $auth = new Auth($db,$allHeaders);

    $items = new Plateprint($db);

    $items->plate_id = isset($_GET['id']) ? $_GET['id'] : die();

    if($auth->isAuth()){
        $stmt = $items->getPlateprints();
        $itemCount = $stmt->rowCount();
        
        if($itemCount > 0){
        
            $platePrintArr = array();
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $e = array(
                    "plateprint_id" => $plateprint_id,
                    "plate_id" => $plate_id,
                    "date_created" => date("d.m.Y H:i", strtotime($date_created)),
                    "user_name" => $user_name,
                    "user_surname" => $user_surname,
                    "user_id" => $user_id,
                    "weight" => $weight
                );
                
                array_push($platePrintArr, $e);
            }
            echo json_encode($platePrintArr);
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