<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require '../../classes/Database.php';
    require '../../middlewares/Auth.php';
    
    include_once '../../classes/database.php';
    include_once '../../classes/Archive.php';
    include_once '../../functions.php';
    
    $data = json_decode(file_get_contents("php://input"));
    
    $allHeaders = getallheaders();
    $database = new Database();
    $db = $database->dbConnection();
    $auth = new Auth($db,$allHeaders);

    $items = new Archive($db);

    $items->plate_id = isset($_GET['id']) ? $_GET['id'] : die();

    if($auth->isAuth()){
        $stmt = $items->getArchive();
        $itemCount = $stmt->rowCount();
        
        if($itemCount > 0){
        
            $plateArchiveArr = array();
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $e = array(
                    "platearchive_id" => $platearchive_id,
                    "plate_id" => $plate_id,
                    "date_created" => date("d.m.Y H:i", strtotime($date_created)),
                    "user_name" => $u_name,
                    "user_surname" => $u_surname,
                    "user_id" => $user_id,
                    "thickness" => $thickness,
                    "dimx" => $dimx,
                    "dimy" => $dimy,
                    "area" => polygonArea(explode(',', $dimx), explode(',', $dimy), sizeof(explode(',', $dimx)))/100,
                    "density" => $density,
                    "weight" => (((polygonArea(explode(',', $dimx), explode(',', $dimy), sizeof(explode(',', $dimx)))/100)*($thickness/10))*$density)/1000
                );
                
                array_push($plateArchiveArr, $e);
            }
            echo json_encode($plateArchiveArr);
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