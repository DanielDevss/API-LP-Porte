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

function getAll() {
    global $connect;
    global $res_not_fount;
    global $res_ok;
    global $res_internal_err;
    global $url_assets;

    try {
        $dealers = $connect->fetchAll("SELECT * FROM dealers ");
        if($dealers) {
            $data = [];
            foreach($dealers as $row) {
                $url_photo = $url_assets . $row['photo'];
                $row['url_photo'] = $url_photo;
                $data[] = $row;
            }
            $response = $res_ok;
            $response['data'] = $data;
        }else{
            $response = $res_not_fount;
        }

    } catch (Exception $err) {
        $response = $res_internal_err;
        $response['error'] = $err->getMessage();
    }

    echo json_encode($response, JSON_NUMERIC_CHECK);
}