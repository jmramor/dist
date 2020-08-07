<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require '../../middlewares/Auth.php';
    
    include_once '../../classes/Database.php';
    include_once '../../classes/Plate.php';
    include_once '../../functions.php';
    
    $allHeaders = getallheaders();
    $database = new Database();
    $db = $database->dbConnection();
    $auth = new Auth($db,$allHeaders);

    $item = new Plate($db);

    $data = json_decode(file_get_contents("php://input"));
    
    $item->plate_id = isset($_GET['id']) ? $_GET['id'] : die();

    if($auth->isAuth()){
        $stmt = $item->getPlate();
        $itemCount = $stmt->rowCount();
        
        if($itemCount = 1){
        
            $plateArr = array();
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $plateArr = array(
                    "plate_id" => (int)$plate_id,
                    "name" => $name,
                    "charge" => $charge,
                    "date_created" => date("d.m.Y H:i", strtotime($date_created)),
                    "date_changed" => date("d.m.Y H:i", strtotime($date_changed)),
                    "user_name" => $uname,
                    "user_surname" => $usurname,
                    "user_id" => (int)$user_id,
                    "material_name" => $mname,
                    "finish_name" => $fname,
                    "location_name" => $lname,
                    "article_name" => $aname,
                    "thickness" => (int)$thickness,
                    "pvc" => (int)$pvc,
                    "scotch" => (int)$scotch,
                    "dimx" => $dimx,
                    "dimy" => $dimy,
                    "area" => (int)polygonArea(explode(',', $dimx), explode(',', $dimy), sizeof(explode(',', $dimx)))/100,
                    "location_id" => (int)$location_id,
                    "article_id" => (int)$article_id,
                    "active" => (int)$active,
                    "notes" => $notes,
                    "density" => $density
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
    