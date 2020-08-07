<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require '../../middlewares/Auth.php';
    
    include_once '../../classes/Database.php';
    include_once '../../classes/Plateprint.php';
    include_once '../../functions.php';
    
    $allHeaders = getallheaders();
    $database = new Database();
    $db = $database->dbConnection();
    $auth = new Auth($db,$allHeaders);

    $item = new Plateprint($db);

    $data = json_decode(file_get_contents("php://input"));
    
    $item->plateprint_id = isset($_GET['id']) ? $_GET['id'] : die();

    if($auth->isAuth()){
        $stmt = $item->getPlateprint();
        $itemCount = $stmt->rowCount();
        
        if($itemCount = 1){
        
            $plateArr = array();
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $plateArr = array(
                    "plateprint_id" => $plateprint_id,
                    "plate_id" => $plate_id,
                    "date_created" => date("d.m.Y H:i", strtotime($date_created)),
                    "user_name" => $user_name,
                    "user_surname" => $user_surname,
                    "user_id" => $user_id,
                    "weight" => $weight
                );
            }
            echo json_encode($plateArr);
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
    