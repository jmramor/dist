<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require '../../middlewares/Auth.php';
    
    include_once '../../classes/Database.php';
    include_once '../../classes/Location.php';
    include_once '../../functions.php';
    
    $allHeaders = getallheaders();
    $database = new Database();
    $db = $database->dbConnection();
    $auth = new Auth($db,$allHeaders);

    $item = new Location($db);

    $data = json_decode(file_get_contents("php://input"));

    $item->name = $data->name;
    $item->user_id = $data->user_id;
    
    if($auth->isAuth()){
        if($item->createLocation()){
            $returnData = [
                "success" => 0,
                "status" => 200,
                "message" => "Location has been successfully created!"
            ];
        } else{
            $returnData = [
                "success" => 0,
                "status" => 500,
                "message" => "Error!"
            ];
        }/*
        $returnData = [
            "success" => 0,
            "status" => 500,
            "message" => "Error!",
            "name" => $item->name,
            "user_id" => $item->user_id,
            "data" => $data
        ];*/
        echo json_encode($returnData);
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