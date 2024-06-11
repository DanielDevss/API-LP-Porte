<?php

$res_ok = [
    "code" => 200,
    'response' => "ok",
    'message' => 'Operación completa'
];

$res_internal_err = [
    "code" => 500,
    "response" => "internal error",
    "message" => "Ocurrion un error interno",
    "error" => null
];

$res_not_fount = [
    'code' => 404,
    "response" => "not fount",
    "message" => "Recursos solcitados no encontrados"
];

$res_unauthorized = [
    'code' => 401,
    'response' => 'unauthorized',
    'message' => 'No tienes acceso'
];