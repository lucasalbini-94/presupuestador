<?php
require_once __DIR__ . "/auth.php";
$id_usuario = requerirUsuarioApi();

require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/models/Presupuesto.php";

header("Content-Type: application/json; charset=utf-8");

$metodo = $_SERVER['REQUEST_METHOD'];
$modelo = new Presupuesto($conexion);

if ($metodo === 'GET') {
    // Si se piden los ultimos para el index
    if (isset($_GET['vista']) && $_GET['vista'] === 'ultimos') {
        $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 3;
        $datos = $modelo->obtenerUltimos($id_usuario, $limite);
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit();
    }

    // Listado general con filtros
    $estado = $_GET['estado'] ?? null;
    $buscar = $_GET['buscar'] ?? null;
    $datos = $modelo->obtenerTodos($id_usuario, $estado, $buscar);

    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit();
}

// Si llega algún método no contemplado aún
http_response_code(405);
echo json_encode(["error" => "Método no permitido"]);