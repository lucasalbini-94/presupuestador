<?php
require_once __DIR__ . "/auth.php";
$idUsuario = requerirUsuarioApi();

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"], JSON_UNESCAPED_UNICODE);
    exit;
}

$busqueda = trim((string)($_GET["q"] ?? ""));
if (mb_strlen($busqueda) < 2) {
    echo json_encode([], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . "/conexion.php";

$stmt = $conexion->prepare(
    "SELECT id, nombre, apellido, telefono, email, direccion
     FROM cliente
     WHERE id_usuario = :id_usuario
       AND (
           nombre LIKE :busqueda
           OR apellido LIKE :busqueda_apellido
           OR CONCAT(nombre, ' ', apellido) LIKE :busqueda_nombre_completo
           OR email LIKE :busqueda_email
           OR telefono LIKE :busqueda_telefono
       )
     ORDER BY apellido, nombre
     LIMIT 20"
);
$stmt->execute([
    ":id_usuario" => $idUsuario,
    ":busqueda" => "%" . $busqueda . "%",
    ":busqueda_apellido" => "%" . $busqueda . "%",
    ":busqueda_nombre_completo" => "%" . $busqueda . "%",
    ":busqueda_email" => "%" . $busqueda . "%",
    ":busqueda_telefono" => "%" . $busqueda . "%"
]);

echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
