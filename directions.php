<?php

use function PHPSTORM_META\type;

include './utils/utils.php';
include './config/headers.php';
include './config/variables.php';
include './config/responses.php';
require_once './db/Conexion.php';

$connect = new Conexion();

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : "add";

$id = isset($_GET['id']) ? trim($_GET['id']) : null;
$id_user = isset($_GET['id_user']) ? trim($_GET['id_user']) : null;
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : null;
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
$state = isset($_POST['state']) ? trim($_POST['state']) : null;
$type = isset($_POST['type']) ? trim($_POST['type']) : null;
$street = isset($_POST['street']) ? trim($_POST['street']) : null;
$ext = isset($_POST['ext']) ? trim($_POST['ext']) : null;
$int = isset($_POST['int']) ? trim($_POST['int']) : null;
$city = isset($_POST['city']) ? trim($_POST['city']) : null;
$location = isset($_POST['location']) ? trim($_POST['location']) : null;
$zip = isset($_POST['zip']) ? trim($_POST['zip']) : null;
$references = isset($_POST['references']) ? trim($_POST['references']) : null;



if ($method === "POST") {
    if($action == "add") {
        createDirection($id_user, $fullname, $phone, $state, $type, $street, $ext, $int, $city, $location, $zip, $references);
        exit;
    }
}
if ($method === "GET") {
    getAll($id_user);
}

// LINK OBTENER DIRECCIONES
function getAll ($id_user) {
    global $connect;
    global $res_internal_err;
    global $res_not_fount;
    global $res_ok;
    
    try{
        $directions = $connect->fetchAll("SELECT * FROM clients_dir WHERE id_client = ?", [$id_user]);
        if($directions) {
            $response = $res_ok;
            $response['data'] = $directions;
        }else{
            $response = $res_not_fount;
        }

    }
    catch(Exception $err) {
        $response = $res_internal_err;
        $response['error'] = $err->getMessage();
    }

    http_response_code($response['code']);
    echo json_encode($response, JSON_NUMERIC_CHECK);
}

// LINK CREAR DIRECCION
function createDirection($id_user, $fullname, $phone, $state, $type, $street, $ext, $int, $city, $location, $zip, $references){
    global $connect;
    global $res_internal_err;
    global $res_not_fount;
    global $res_ok;

    // Creamos el alias
    $alias = "$type #$zip - $fullname";
    if ($alias == 'otro') {
        $alias = "#$zip - $fullname";
    }
    // Verificamos los parametros
    if(empty($fullname) || !$fullname) {
        echo json_encode(["response" => "empty value", "message" => "El campo de nombre completo se encuentra vacio."]);
        exit;
    } 
    if(empty($phone) || !$phone) {
        echo json_encode(["response" => "empty value", "message" => "El campo número de teléfono se encuentra vacio."]);
        exit;
    } 
    if(empty($state) || !$state) {
        echo json_encode(["response" => "empty value", "message" => "No haz seleccionado un estado."]);
        exit;
    } 
    if(empty($street) || !$street) {
        echo json_encode(["response" => "empty value", "message" => "El campo cale se encuentra vacio."]);
        exit;
    } 
    if(empty($ext) || !$ext) {
        echo json_encode(["response" => "empty value", "message" => "El campo número exterior se encuentra vacío."]);
        exit;
    } 
    if(empty($city) || !$city) {
        echo json_encode(["response" => "empty value", "message" => "El campo ciudad se encuentra vacío."]);
        exit;
    } 
    if(empty($location) || !$location) {
        echo json_encode(["response" => "empty value", "message" => "El campo colonía se encuentra vacío."]);
        exit;
    } 
    if(empty($zip) || !$zip) {
        echo json_encode(["response" => "empty value", "message" => "El campo código postal se encuentra vacío."]);
        exit;
    }

    try {

        $uuid = guidv4();

        $query = "INSERT INTO clients_dir SET id=?, id_client=?, cp=?, alias=?, state=?, city=?, location=?, street=?, no_ext=?, no_int=?, phone=?, fullname=?, type=?, create_at=CURRENT_TIMESTAMP";
        $params = [$uuid, $id_user, $zip, $alias, $state, $city, $location, $street, $ext, $int, $phone, $fullname, $type];
        $connect->query($query, $params);

        $response = $res_ok;
        $response['message'] = "Se ha agregado una nueva dirección a la lista.";
    } catch (Exception $err) {
        $response = $res_internal_err;
        $response['error'] = $err->getMessage();
    }
    
    http_response_code($response['code']);
    echo json_encode($response, JSON_NUMERIC_CHECK);
}
