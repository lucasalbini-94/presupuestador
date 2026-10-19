<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/conexion.php";

header("Content-Type: application/json; charset=utf-8");

if($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $conexion->prepare("SELECT id, descripcion FROM categoria");
$stmt->execute();

echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);