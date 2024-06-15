<?php
include './config/headers.php';
include './config/variables.php';
include './config/responses.php';
include './utils/utils.php';
require_once './db/Conexion.php';
require "./vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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

/* ----------------------------- OBTENER POR ID ----------------------------- */
// LINK OBTENER POR ID
function getById($id) {
    global $connect;
    global $res_not_fount;
    global $res_ok;
    global $res_internal_err;
    global $url_assets;

    try {
        $accountData = $connect->fetch("SELECT * FROM clients WHERE id = ?", [$id]);
        if($accountData) {
            $response = $res_ok;
            $response['data'] = $accountData;
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

/* ------------------------------ OBTENER TODOS ----------------------------- */
// LINK OBTENER TODOS
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

/* -------------------------------- REGISTRAR ------------------------------- */
// LINK REGISTRAR
function register($data) {
    global $connect;
    global $res_not_fount;
    global $res_ok;
    global $res_internal_err;
    global $url_assets;

    $firstname = isset($data['first_name']) ? trim($data['first_name']) : null;
    $lastname = isset($data['last_name']) ? trim($data['last_name']) : null;
    $phone = isset($data['phone']) ? trim($data['phone']) : null;
    $email = isset($data['email']) ? trim($data['email']) : null;
    $rfc = isset($data['rfc']) ? trim($data['rfc']) : null;
    $password = isset($data['password']) ? trim($data['password']) : null;
    $password_repeat = isset($data['password_repeat']) ? trim($data['password_repeat']) : null;
    $terms = isset($data['terms']) ? $data['terms'] : null;
    $policy = isset($data['policy']) ? $data['policy'] : null;

    
    
    $response = $res_not_fount;
    if(!$terms) {
        $response['message'] = "Acepta los terminos y condiciones.";
        http_response_code(404);
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
    }
    if(!$policy) {
        $response['message'] = "Acepta las politicas de privacidad.";
        http_response_code(404);
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
    }

    if (empty($firstname)) {
        $response['message'] = "No has ingresado el campo nombre";
        http_response_code(404);
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
    }
    
    if (empty($lastname)) {
        $response['message'] = "No has ingresado el campo apellido";
        http_response_code(404);
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
    }
    
    if (empty($phone)) {
        $response['message'] = "No has ingresado el campo número de teléfono";
        http_response_code(404);
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
    }
    
    if (empty($email)) {
        $response['message'] = "No has ingresado el campo correo electrónico";
        http_response_code(404);
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
        }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $response['message'] = "El correo ingresado es inválido";
        http_response_code(404);
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
    }

    if (empty($password)) {
        $response['message'] = "No has ingresado el campo contraseña";
        http_response_code(404);
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
    }
    
    if (empty($password_repeat)) {
        $response['message'] = "No has ingresado el campo repetir contraseña";
        http_response_code(404);
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
    }
    
    if ($password !== $password_repeat) {
        $response['message'] = "Las contraseñas no coinciden";
        http_response_code(404);
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
    }
    
    if(empty($rfc)) {
        $response['message'] = "No haz ingresado el campo RFC";
        http_response_code(404);
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
    }

    // if(!validarRFC($rfc)) {
    //     $response['message'] = "El RFC ingresado no es valido, verificalo e intentalo nuevamente.";
    //     http_response_code(404);
    //     echo json_encode($response, JSON_NUMERIC_CHECK);
    //     exit;
    // }
    

    try {
        $searchMail = $connect->fetch("SELECT * FROM clients WHERE email = ?", [$email]);

        if($searchMail) {
            $response = $res_not_fount;
            $response['message'] = "El correo $email se encuentra registrado en la plataforma";
            http_response_code(404);
            echo json_encode($response, JSON_NUMERIC_CHECK);
            exit;
        }
        
        $uuid = guidv4();
        $password = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO clients SET id = ?, firstname=?, lastname=?, phone=?,email=?, rfc=?, hashPassword=?, create_at=CURRENT_TIMESTAMP";
        $params = [$uuid, $firstname, $lastname, $phone, $email, $rfc, $password];
        
        $connect->query($query, $params);
        $response = $res_ok;
        $response['message'] = "Muchas por ser parte de la comunidad LP Porte, ya puedes iniciar sesión";


    } catch (Exception $err) {
        $response = $res_internal_err;
        $response['error'] = $err->getMessage();
    }

    http_response_code($response['code']);
    echo json_encode($response, JSON_NUMERIC_CHECK);
}

/* ----------------------------- INICIAR SESION ----------------------------- */
// LINK INICIAR SESION
function auth($data) {
    global $connect;
    global $res_not_fount;
    global $res_ok;
    global $res_internal_err;
    global $res_unauthorized;
    global $url_assets;


    $email = isset($data['email']) ? trim($data['email']) : null;
    $password = isset($data['password']) ? trim($data['password']) : null;

    if(empty($email) || !$email) {
        $response = $res_not_fount;
        $response['message'] = "No haz ingresado un correo electrónico.";
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
    }

    if(empty($password) || !$password) {
        $response = $res_not_fount;
        $response['message'] = "No haz ingresado un correo electrónico.";
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
    }

    try {
        $searchUser = $connect->fetch("SELECT * FROM clients WHERE email = ?", [$data['email']]);
        if($searchUser) {
            $hash = $searchUser['hashPassword'];
            $isCorrectPass = password_verify($password, $hash);
            if($isCorrectPass) {
                $response = $res_ok;
                $iduser = $searchUser['id'];
                $secretKey = "lpporte_##2024>Danidevs|www.danidevs.com|www.lpporte.com";
                $payload = [
                    "id_user" => $iduser,
                    "email" => $email,
                    "auth" => true
                ];
                $token = JWT::encode($payload, $secretKey, 'HS256');
                $response['token'] = $token;
            }else{
                $response = $res_unauthorized;
                $response = "Contraseña incorrecta";
            }
        }else{
            $response = $res_not_fount;
            $response['message'] = "No se encontro un correo electrónico";
        }
    } catch (Exception $err) {
        $response = $res_internal_err;
        $response['error'] = $err->getMessage();
    }
     
    echo json_encode($response, JSON_NUMERIC_CHECK);
    exit;
}