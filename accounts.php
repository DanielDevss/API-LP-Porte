<?php
include './config/headers.php';
include './config/variables.php';
include './config/responses.php';
require_once './db/Conexion.php';

$connect = new Conexion();

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? trim($_GET['id']) : null;
$action = isset($_GET['action']) ? $_GET['action'] : null;

if($method === 'GET') {
    if($id) {
        getById($id);
    }else{
        getAll();
    }
}
if($method === "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    if($action === "auth") {
        auth($data);
    }
    if($action === "register") {
        register($data);
    }
}

function getById($id) {
    global $connect;
    global $res_not_fount;
    global $res_ok;
    global $res_internal_err;
    global $url_assets;
}

function getAll() {
    global $connect;
    global $res_not_fount;
    global $res_ok;
    global $res_internal_err;
    global $url_assets;
    
    try {
        $response = $res_not_fount;
    } catch (Exception $err) {
        $response = $res_internal_err;
        $response['error'] = $err->getMessage();
    }

    http_response_code($response['code']);
    echo json_encode($response, JSON_NUMERIC_CHECK);
}

function register($data) {
    global $connect;
    global $res_not_fount;
    global $res_ok;
    global $res_internal_err;
    global $url_assets;
}

function auth($data) {
    global $connect;
    global $res_not_fount;
    global $res_ok;
    global $res_internal_err;
    global $url_assets;
}