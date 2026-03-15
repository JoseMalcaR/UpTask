<?php

function conectarDB(): mysqli {

$env = parse_ini_file(__DIR__ . '/../.env');

if ($env === false) {
    die('Error: No se pudo cargar el archivo .env');
}

$db = new mysqli(
    $env['DB_HOST'],
    $env['DB_USER'],
    $env['DB_PASS'],
    $env['DB_NAME']
);

    if(!$db) {
        die('Error de conexión');
    }

    return $db;
}
