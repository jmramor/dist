<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require '../../classes/Database.php';
    require '../../middlewares/Auth.php';
    
    include_once '../../classes/Database.php';
    include_once '../../classes/Location.php';

    $allHeaders = getallheaders();
    $database = new Database();
    $db = $database->dbConnection();

    $auth = new Auth($db,$allHeaders);
    $items = new Location($db);
    
    if($auth->isAuth()){
        $stmt = $items->getLocations();
        $itemCount = $stmt->rowCount();
        
        if($itemCount > 0){
        
            $locationArr = array();
            $locationArr["body"] = array();
            $locationArr["itemCount"] = $itemCount;
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $e = array(
                    "location_id" => $location_id,
                    "name" => $name,
                    "date_created" => $date_created,
                    "user_name" => $uname,
                    "user_surname" => $usurname,
                    "user_id" => $user_id
                );
    
                array_push($locationArr["body"], $e);
            }
            echo json_encode($locationArr);
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