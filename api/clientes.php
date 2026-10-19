<?php
require_once __DIR__ . "/auth.php";
$idUsuario = requerirUsuarioApi();

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"], JSON_UNESCAPED_UNICODE);
    exit;
}

$busqueda = trim((string)($_GET["search"] ?? $_GET["q"] ?? ""));

require_once __DIR__ . "/conexion.php";

$sql = "SELECT id, nombre, apellido, telefono, email, direccion
        FROM cliente
        WHERE id_usuario = :id_usuario";
$parametros = [":id_usuario" => $idUsuario];

if ($busqueda !== "") {
    $sql .= " AND (
                nombre LIKE :busqueda_nombre
                OR apellido LIKE :busqueda_apellido
                OR CONCAT(nombre, ' ', apellido) LIKE :busqueda_nombre_completo
                OR email LIKE :busqueda_email
                OR telefono LIKE :busqueda_telefono
            )";
    $termino = "%" . $busqueda . "%";
    $parametros[":busqueda_nombre"] = $termino;
    $parametros[":busqueda_apellido"] = $termino;
    $parametros[":busqueda_nombre_completo"] = $termino;
    $parametros[":busqueda_email"] = $termino;
    $parametros[":busqueda_telefono"] = $termino;
}

$sql .= " ORDER BY apellido, nombre";
$stmt = $conexion->prepare($sql);
$stmt->execute($parametros);

echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
