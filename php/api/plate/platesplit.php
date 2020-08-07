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
    
    $data = json_decode(file_get_contents("php://input"));
    
    $allHeaders = getallheaders();
    $database = new Database();
    $db = $database->dbConnection();
    $auth = new Auth($db,$allHeaders);

    $item = new Plate($db);

    //echo json_encode($data);

    $item->plate_id = $data->plate_id;
    $item->user_id = $data->user_id;
    $item->thickness = $data->thickness;
    $item->charge = $data->charge;
    $item->location_id = $data->location_idFirst;
    $item->density = $data->density;
    $item->article_id = $data->article_id;
    $item->thickness = $data->thickness;
    $item->name = $data->curr_name;
    $item->pvc = $data->pvc;
    $item->scotch = $data->scotch;

    $name_split = $data->name;
    $location_split = $data->location_idSecond;

    $item->dimx_prev = $data->dimx;
    $item->dimy_prev = $data->dimy;

    $dimx = $dimy = '';
    $dimx_split = $dimy_split = "";

    $dimsFirst = $data->dimsFirst;
    $dimsSecond = $data->dimsSecond;

    foreach( $dimsFirst as $el ){
            
        $dimx.=$el->x .',';
        $dimy.=$el->y .',';
    }

    foreach( $dimsSecond as $el ){
            
        $dimx_split.=$el->x .',';
        $dimy_split.=$el->y .',';
    }

    $item->dimx = substr($dimx, 0, -1);
    $item->dimy = substr($dimy, 0, -1);

    $dimx2 = substr($dimx_split, 0, -1);
    $dimy2 = substr($dimy_split, 0, -1);


    if($auth->isAuth()){
        $original_area = polygonArea(explode(',', $item->dimx_prev), explode(',', $item->dimy_prev), sizeof(explode(',', $item->dimx_prev)))/100;
        $new_area = polygonArea(explode(',', $item->dimx), explode(',', $item->dimy), sizeof(explode(',', $item->dimx)))/100;
        $current_area = polygonArea(explode(',', $dimx2), explode(',', $dimy2), sizeof(explode(',', $dimx2)))/100;
        $item->area = $original_area - $new_area;

        $volume = $item->area*(float)($item->thickness/10);

        $item->weight = ($volume*$item->density)/1000;

        if($item->plateSplit($name_split, $location_split, $dimx2, $dimy2)){
            http_response_code(200);
            $returnData = [
                "success" => 1,
                "status" => 200,
                "message" => "Plate has been successfully updated!"
            ];
        } 
        else{
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