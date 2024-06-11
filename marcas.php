<?php
include './config/headers.php';
include './config/variables.php';
include './config/responses.php';
require_once './db/Conexion.php';

$connect = new Conexion();

$method = $_SERVER['REQUEST_METHOD'];

if($method === "GET") {
    getAll();
}


function getAll () {
    global $connect;
    global $res_not_fount;
    global $res_ok;
    global $res_internal_err;
    global $url_assets;
    try {
        $branches = $connect->fetchAll("SELECT * FROM branches");
        
        if(!$branches) {
            $response = $res_not_fount;
        }else{
            $data = [];
            foreach ($branches as $row) {
                $url_cover = $url_assets . $row['path_image'];
                $row['url_cover'] = $url_cover;
                $data[] = $row;
            }
            $response = $res_ok;
            $response['data'] = $data;
        }

    } catch (Exception $err) {
        $response = $res_internal_err;
        $response['error'] = $err->getMessage();
    }

    http_response_code($response['code']);
    echo json_encode($response);

}