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

    $item->name = $data->name;
    $item->user_id = $data->user_id;
    $item->location_id = $data->location_id;
    $item->article_id = $data->article_id;
    $item->charge = $data->charge;
    $item->thickness = $data->thickness;
    $item->pvc = $data->pvc;
    $item->scotch = $data->scotch;
    /*
    $item->dimx = $data->dimx;
    $item->dimy = $data->dimy;
    */
    $dimx = $dimy = "0";

    $dims = $data->dims;

    foreach( $dims as $el ){
        
        $dimx.=','.$el->x;
        $dimy.=','.$el->y;
    }

    $dimx.=',0';
    $dimy.=',0';

    $item->dimx = $dimx;
    $item->dimy = $dimy;

    if($auth->isAuth()){
        if($item->createPlate()){
            $returnData = [
                "success" => 0,
                "status" => 200,
                "message" => "Article has been successfully created!"
            ];
        } else{
            $returnData = [
                "success" => 0,
                "status" => 500,
                "message" => "Error!"
            ];
        }
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