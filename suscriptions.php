<?php
include './config/headers.php';
include './config/responses.php';
require_once './db/Conexion.php';
require_once './utils/utils.php';

$connect = new Conexion();

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : null;
$id = isset($_GET['id']) ? trim($_GET['id']) : null;
$id_user = isset($_GET['id_user']) ? trim($_GET['id_user']) : null;
$id_suscription = isset($_GET['id_suscription']) ? trim($_GET['id_suscription']) : null;

if($method === "GET") {
    if($action == 'byUser') {
        getByUser($id_user);
        exit;
    }
    else{
        getAll();
        exit;
    }
}

if($method === "POST") {
    if($action == "changePlan") {
        changePlan($id_user, $id_suscription);
        exit;
    }
}

// LINK CAMBIAR DE PLAN
function changePlan ($id_user, $id_suscription) {
    global $connect;
    global $res_ok;
    global $res_internal_err;
    try {
        if(empty($id_suscription)) {
            $connect->query("DELETE FROM clients_suscriptions WHERE id_client = ?", [$id_user]);
            $response = $res_ok;
            $response['message'] = "Se ha removido el plan de suscripción";
        }else{
            $suscriptionActive = $connect->fetch("SELECT * FROM clients_suscriptions WHERE id_client = ?", [$id_user]);
            if($suscriptionActive) {
                $connect->query("DELETE FROM clients_suscriptions WHERE id_client = ?", [$id_user]);
            }
            $uuid = guidv4();
            $connect->query("INSERT INTO clients_suscriptions SET id_client=?, id_suscription=?, create_at=CURRENT_TIMESTAMP, id=?", [$id_user, $id_suscription, $uuid]);
            $response = $res_ok;
            $response['message'] = "Se actualizo tu plan de suscripción";
        }
    } catch (Exception $err) {
        $response = $res_internal_err;
        $response['error'] = $err->getMessage();
    }
    http_response_code($response['code']);
    echo json_encode($response, JSON_NUMERIC_CHECK);
}

// LINK OBTENER INFORMACION

function getAll() {
    global $connect;
    global $res_internal_err;
    global $res_ok;
    global $res_not_fount;
    try {
        $suscriptions = $connect->fetchAll("SELECT * FROM suscriptions;");
        if($suscriptions) {
            $response = $res_ok;
            $response['data'] = $suscriptions;
        }else{
            $response = $res_not_fount;
        }
    } catch (Exception $err) {
        $response = $res_internal_err;
        $response['error'] = $err->getMessage();
    }

    echo json_encode($response, JSON_NUMERIC_CHECK);
}


function getByUser ($id_user) {
    global $connect;
    global $res_ok;
    global $res_internal_err;
    global $res_not_fount;
    global $res_unauthorized;

    if(empty($id_user) || $id_user == null) {
        $response = $res_unauthorized;
        $response['id_user'] = $id_user;
        http_response_code($response['code']);
        echo json_encode($response);
        exit;
    }

    try {
        $user = $connect->fetch("SELECT c.*, s.name FROM clients_suscriptions c LEFT JOIN suscriptions s ON s.id = c.id_suscription WHERE id_client = ?", [$id_user]);

        if($user) {
            $response = $res_ok;
            $response['data'] = $user;
        }else{
            $response = $res_not_fount;
        }

    } catch (Exception $err) {
        $response = $res_internal_err;
        $response['error'] = $err->getMessage();
    }

    http_response_code($response['code']);
    echo json_encode($response, JSON_NUMERIC_CHECK);
}