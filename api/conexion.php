<?php
require_once __DIR__ . "/config.php";

$origen = $_SERVER["HTTP_ORIGIN"] ?? "";
$origenesPermitidos = [
    "http://127.0.0.1:5000",
    "http://localhost:5000",
    "http://localhost"
];
if (in_array($origen, $origenesPermitidos, true)) {
    header("Acces-Control-Allow-Origin: $origen");
    header("Acces-Control-Allow-Credentials: true");
    header("Vary: Origin");
}

try {
    $conexion = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $ex) {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode(["error" => "Error de conexión con la base de datos"]);
    exit();
}