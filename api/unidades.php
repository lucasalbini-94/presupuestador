<?php
require_once __DIR__ . "/auth.php";
requerirUsuarioApi();
require_once __DIR__ . "/conexion.php";

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    header("Allow: GET");
    echo json_encode(["error" => "Método no permitido"], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $conexion->query("SELECT id, descripcion FROM unidad ORDER BY descripcion");
echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
