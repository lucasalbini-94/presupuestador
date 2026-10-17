<?php
require_once __DIR__ . "/auth.php";
obtenerUsuarioAutenticado();

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Método no válido"]);
    exit;
}

$datos = json_decode(file_get_contents("php://input"), true);

if (!is_array($datos)) {
    http_response_code(400);
    echo json_encode(["error" => "El formato de las credenciales no es válido"], JSON_UNESCAPED_UNICODE);
    exit;
}

$email = trim((string)($datos["email_usuario"] ?? ""));
$password = (string)($datos["password_usuario"] ?? "");

if ($email === "" || $password === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Ingresá un email y una contraseña válidos"], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . "/conexion.php";

$stmt = $conexion->prepare("SELECT id, nombre, apellido, email, telefono, pass FROM usuario WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if (!$usuario) {
    http_response_code(401);
    echo json_encode(["error" => "Credenciales inválidas"]);
    exit;
}

if (password_verify($password, $usuario["pass"])) {
    session_regenerate_id(true);
    $_SESSION["usuario_id"] = (int)$usuario["id"];
    http_response_code(200);
    echo json_encode([
        "id" => (int)$usuario["id"],
        "nombre" => $usuario["nombre"],
        "apellido" => $usuario["apellido"],
        "email" => $usuario["email"],
        "telefono" => $usuario["telefono"]
    ], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Credenciales inválidas"]);
}
