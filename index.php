<?php
include './config/headers.php';
require_once './db/Conexion.php';
try {
    new Conexion();

    http_response_code(200);
    echo json_encode([
        "response" => "Ok"
    ]);
} catch (Exception $err) {
    http_response_code(500);
    echo json_encode(["response" => "error", "details" => $err->getMessage()]);
}