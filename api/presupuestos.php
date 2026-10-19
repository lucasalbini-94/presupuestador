<?php
require_once __DIR__ . "/auth.php";
$id_usuario = requerirUsuarioApi();

require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/models/Presupuesto.php";

header("Content-Type: application/json; charset=utf-8");

$metodo = $_SERVER['REQUEST_METHOD'];
$modelo = new Presupuesto($conexion);

if ($metodo === 'GET') {
    if (isset($_GET['id'])) {
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            http_response_code(400);
            echo json_encode(["error" => "El identificador del presupuesto no es válido"], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $presupuesto = $modelo->obtenerPorId($id_usuario, $id);
        if ($presupuesto === null) {
            http_response_code(404);
            echo json_encode(["error" => "No se encontró el presupuesto"], JSON_UNESCAPED_UNICODE);
            exit();
        }

        echo json_encode($presupuesto, JSON_UNESCAPED_UNICODE);
        exit();
    }

    if (isset($_GET['vista']) && $_GET['vista'] === 'ultimos') {
        $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 3;
        echo json_encode($modelo->obtenerUltimos($id_usuario, $limite), JSON_UNESCAPED_UNICODE);
        exit();
    }

    $estado = $_GET['estado'] ?? null;
    $buscar = trim((string)($_GET['buscar'] ?? ''));
    $idCliente = null;
    if (isset($_GET['id_cliente'])) {
        $idCliente = filter_var($_GET['id_cliente'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($idCliente === false) {
            http_response_code(400);
            echo json_encode(["error" => "El identificador del cliente no es válido"], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    echo json_encode(
        $modelo->obtenerTodos($id_usuario, $estado, $buscar, $idCliente),
        JSON_UNESCAPED_UNICODE
    );
    exit();
}

if ($metodo === 'POST' || $metodo === 'PUT') {
    $datos = json_decode(file_get_contents('php://input'), true);
    if (!is_array($datos)) {
        http_response_code(400);
        echo json_encode(["error" => "El cuerpo de la solicitud no es válido"], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if (isset($datos['id_estado'])) {
        if ($metodo !== 'PUT') {
            http_response_code(405);
            header("Allow: GET, PUT");
            echo json_encode(["error" => "Usá PUT para cambiar el estado"], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $idPresupuesto = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $idEstado = filter_var($datos['id_estado'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($idPresupuesto === false || $idEstado === false) {
            http_response_code(400);
            echo json_encode(["error" => "El presupuesto o el estado no son válidos"], JSON_UNESCAPED_UNICODE);
            exit();
        }

        try {
            $modelo->cambiar_estado($id_usuario, $idPresupuesto, $idEstado);
            echo json_encode(["success" => true, "message" => "Estado actualizado correctamente"], JSON_UNESCAPED_UNICODE);
        } catch (InvalidArgumentException $error) {
            http_response_code(400);
            echo json_encode(["error" => $error->getMessage()], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $error) {
            error_log("Error al cambiar estado del presupuesto: " . $error->getMessage());
            http_response_code(500);
            echo json_encode(["error" => "No se pudo cambiar el estado del presupuesto"], JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

    $idCliente = filter_var($datos['id_cliente'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $itemsEntrada = $datos['items'] ?? null;
    if ($idCliente === false || !is_array($itemsEntrada) || count($itemsEntrada) === 0) {
        http_response_code(400);
        echo json_encode(["error" => "Seleccioná un cliente y agregá al menos un trabajo"], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $items = [];
    foreach ($itemsEntrada as $item) {
        if (!is_array($item)) {
            http_response_code(400);
            echo json_encode(["error" => "Hay un trabajo con datos inválidos"], JSON_UNESCAPED_UNICODE);
            exit();
        }
        $idTrabajo = filter_var($item['id_trabajo'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $cantidad = filter_var($item['cantidad'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($idTrabajo === false || $cantidad === false) {
            http_response_code(400);
            echo json_encode(["error" => "Revisá los trabajos y sus cantidades"], JSON_UNESCAPED_UNICODE);
            exit();
        }
        $items[] = ['id_trabajo' => $idTrabajo, 'cantidad' => $cantidad];
    }

    $idPresupuesto = null;
    if ($metodo === 'PUT') {
        $idPresupuesto = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($idPresupuesto === false) {
            http_response_code(400);
            echo json_encode(["error" => "El identificador del presupuesto no es válido"], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    try {
        $idGuardado = $modelo->guardar($id_usuario, $idCliente, $items, $idPresupuesto);
        http_response_code($metodo === 'POST' ? 201 : 200);
        echo json_encode(["id" => $idGuardado], JSON_UNESCAPED_UNICODE);
    } catch (InvalidArgumentException $error) {
        http_response_code(400);
        echo json_encode(["error" => $error->getMessage()], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $error) {
        error_log("Error al guardar presupuesto: " . $error->getMessage());
        http_response_code(500);
        echo json_encode(["error" => "No se pudo guardar el presupuesto"], JSON_UNESCAPED_UNICODE);
    }
    exit();
}

http_response_code(405);
header("Allow: GET, POST, PUT");
echo json_encode(["error" => "Método no permitido"], JSON_UNESCAPED_UNICODE);
