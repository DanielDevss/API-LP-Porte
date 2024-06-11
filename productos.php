<?php
include './config/headers.php';
include './config/variables.php';
include './config/responses.php';
require_once './db/Conexion.php';

$connect = new Conexion();

$method = $_SERVER['REQUEST_METHOD'];

if($method === "GET") {
    if(isset($_GET['categorias'])) {
        getCategories();
    }
    else{
        getAll();
    }
}

function getCategories () {
    global $connect;
    global $res_ok;
    global $res_not_fount;
    global $res_internal_err;

    try{
        $categorias = $connect->fetchAll("SELECT * FROM categorias");
        if($categorias) {
            $response = $res_ok;
            $response['data'] = $categorias;
        }else{
            $response = $res_not_fount;
        }
        
    }catch(Exception $err) {
        $response = $res_internal_err;
        $response['error'] = $err->getMessage();
    }

    http_response_code($response['code']);
    echo json_encode($response, JSON_NUMERIC_CHECK);
}

function getAll () {
    global $connect;
    global $url_assets;
    global $res_ok;
    global $res_not_fount;
    global $res_internal_err;
    try {
        
        $products = $connect->fetchAll("SELECT b.name branch, c.name category ,prd.* FROM products prd LEFT JOIN branches b ON b.id = prd.id_branch LEFT JOIN categorias c ON c.id = prd.id_category WHERE prd.status = 'activo'");
        $data = [];

        if(!$products) {
            $response = $res_not_fount;
        }else{
            foreach($products as $row) {
                $id = $row['id'];
                $prices = $connect->fetchAll("SELECT id, plan, amount, stripe_price_id FROM prices WHERE id_product = ?", [$id]);
                $row['url'] = $url_assets;
                $row['url_cover'] = $url_assets . $row['cover'];
                $row['prices'] = $prices;
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
    echo json_encode($response, JSON_NUMERIC_CHECK);
}