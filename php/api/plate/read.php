<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require '../../classes/Database.php';
    require '../../middlewares/Auth.php';
    
    include_once '../../classes/database.php';
    include_once '../../classes/Plate.php';
    include_once '../../functions.php';
    
    $allHeaders = getallheaders();
    $database = new Database();
    $db = $database->dbConnection();

    $auth = new Auth($db,$allHeaders);
    $items = new Plate($db);
    
    if($auth->isAuth()){
        $stmt = $items->getPlates();
        $itemCount = $stmt->rowCount();
        
        if($itemCount > 0){
        
            $plateArr = array();
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $e = array(
                    "plate_id" => $plate_id,
                    "name" => $name,
                    "charge" => $charge,
                    "date_created" => date("d.m.Y H:i", strtotime($date_created)),
                    "date_changed" => date("d.m.Y H:i", strtotime($date_changed)),
                    "user_name" => $uname,
                    "user_surname" => $usurname,
                    "user_id" => $user_id,
                    "material_name" => $mname,
                    "finish_name" => $fname,
                    "location_name" => $lname,
                    "article_name" => $aname,
                    "thickness" => $thickness,
                    "pvc" => $pvc,
                    "scotch" => $scotch,
                    "dimx" => $dimx,
                    "dimy" => $dimy,
                    "area" => polygonArea(explode(',', $dimx), explode(',', $dimy), sizeof(explode(',', $dimx)))/100,
                    "location_id" => $location_id,
                    "article_id" => $article_id,
                    "active" => $active,
                    "notes" => $notes,
                    "density" => $density
                );
                
                array_push($plateArr, $e);
            }
            echo json_encode($plateArr);
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