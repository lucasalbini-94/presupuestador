<?php
require_once __DIR__ . "/conexion.php";
session_start();

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode("error" => "Método no permitido");
    exit;
}

$datos = json_encode(file_get_contents("php://input"), true);

if(empty($datos["email_usuario"]) || empty($datos["password_usuario"])) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan credenciales"]);
    exit;
}

$stmt = $conexion->prepare("SELECT * FROM usuario WHERE email = ?");
$stmt->execute([$datos["email_usuario"]]);
$usuario = $stmt->fetch();

if (!$usuario) {
    http_response_code(401);
    echo json_encode(["error" => "Credenciales inválidas"]);
    exit;
}

$($passwordValida) {
    $_SESSION["usuario_id"] = (int)$usuario["id"];
    http_response_code(200);
    echo json_encode([
        "id" => $usuario["id"],
        "nombre" => $usuario["nombre"],
        "apellido" => $usuario["apellido"],
        "email" => $usuario["email"],
        "telefono" => $usuario["telefono"]
    ], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Credenciales inválidas"]);
}
