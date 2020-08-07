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

    $item->plate_id = $data->plate_id;
    $item->user_id = $data->user_id;
    $item->density = $data->density;
    $item->thickness = $data->thickness;

    if($data->location_id != "" ){
        $item->location_id = $data->location_id;
    }
    else{
        $item->location_id = $data->loc;
    }
    if($data->active != "" ){
        $item->active = $data->active;
    }
    else{
        $item->active = $data->act;
    }
    if($data->charge != "" ){
        $item->charge = $data->charge;
    }
    else{
        $item->charge = $data->chrg;
    }

    $dimx = $dimy = '';

    $dims = $data->dims;

    foreach( $dims as $el ){
            
        $dimx.=$el->x .',';
        $dimy.=$el->y .',';
    }

    $item->dimx = substr($dimx, 0, -1);
    $item->dimy = substr($dimy, 0, -1);

    if($item->dimx == '0,,0' || $item->dimx == '0,0,,0,0' || $item->dimx == '0,0,,,0,0'){
        $item->dimx = $data->dimx;
        $item->dimy = $data->dimy;
    }
    if(preg_match('/(0,0)(,){2,}(0,0)/', $item->dimx) == 1){
        
        $item->dimx = $data->dimx;
        $item->dimy = $data->dimy;
        
    }
    $item->dimx_prev = $data->dimx;
    $item->dimy_prev = $data->dimy;

    if($auth->isAuth()){
        if($item->dimx != $data->dimx || $item->dimy != $data->dimy ){
            $new_area = polygonArea(explode(',', $item->dimx), explode(',', $item->dimy), sizeof(explode(',', $item->dimx)))/100;
            $current_area = polygonArea(explode(',', $data->dimx), explode(',', $data->dimy), sizeof(explode(',', $data->dimx)))/100;
            $item->area = $current_area - $new_area;

            $volume = $item->area*(float)($item->thickness/10);

            $item->weight = ($volume*$item->density)/1000;

            if($item->updatePlate(1)){
                http_response_code(200);
                $returnData = [
                    "success" => 0,
                    "status" => 200,
                    "message" => "Plate has been successfully updated!",
                    "item_data" => [$item->plate_id, $item->dimx, $item->dimy, $item->active, $item->location_id, 1, $item->thickness, $item->area]
                ];
            } else{
                $returnData = [
                    "success" => 0,
                    "status" => 500,
                    "message" => "Error!"
                ];
            }
        }
        else{
            if($item->updatePlate(0)){
                http_response_code(200);
                $returnData = [
                    "success" => 0,
                    "status" => 200,
                    "message" => "Plate has been successfully updated!",
                    "item_data" => [$item->plate_id, $item->dimx, $item->dimy, $item->active, $item->location_id, 0, $item->thickness, $item->area]
                ];
            } else{
                $returnData = [
                    "success" => 0,
                    "status" => 500,
                    "message" => "Error!"
                ];
            }
        }
        
        echo json_encode($returnData);
    }
    else{
        $returnData = [
            "success" => 0,
            "status" => 401,
            "message" => "Unauthorized",
            "dims" => $data->dims
        ];
        echo json_encode($returnData);
    }
?>