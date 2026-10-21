<?php
require_once __DIR__ . "/auth.php";
$id_usuario = requerirUsuarioApi();
require_once __DIR__ . "/conexion.php";

header("Content-Type: application/json; charset=utf-8");

$metodo = $_SERVER["REQUEST_METHOD"];

if ($metodo === "GET") {
    $sql = "SELECT
                t.id AS id_trabajo,
                c.id AS id_categoria,
                c.descripcion AS categoria,
                u.id AS id_unidad,
                u.descripcion AS unidad,
                t.nombre AS trabajo,
                t.precio
            FROM trabajo t
            JOIN categoria c ON t.id_categoria = c.id
            JOIN unidad u ON t.id_unidad = u.id
            WHERE t.id_usuario = :id_usuario
            ORDER BY c.descripcion, t.nombre";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([":id_usuario" => $id_usuario]);
    echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
    exit;
}

if ($metodo === "POST" || $metodo === "PUT") {
    $datos = json_decode(file_get_contents("php://input"), true);
    if (!is_array($datos)) {
        http_response_code(400);
        echo json_encode(["error" => "El cuerpo de la solicitud no es válido"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $idCategoria = filter_var($datos["id_categoria"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    $idUnidad = filter_var($datos["id_unidad"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    $trabajo = trim((string)($datos["trabajo"] ?? ""));
    $precioEntrada = $datos["precio"] ?? null;

    if ($idCategoria === false || $idUnidad === false || $trabajo === "" ||
        !is_numeric($precioEntrada) || (float)$precioEntrada < 0) {
        http_response_code(400);
        echo json_encode(["error" => "Completá una categoría, unidad, trabajo y precio válidos"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $precio = (float)$precioEntrada;

    try {
        if ($metodo === "POST") {
            $sql = "INSERT INTO trabajo (nombre, precio, id_categoria, id_unidad, id_usuario)
                    VALUES (:nombre, :precio, :id_categoria, :id_unidad, :id_usuario)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ":nombre" => $trabajo,
                ":precio" => $precio,
                ":id_categoria" => $idCategoria,
                ":id_unidad" => $idUnidad,
                ":id_usuario" => $id_usuario
            ]);

            http_response_code(201);
            echo json_encode(["id_trabajo" => (int)$conexion->lastInsertId()], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $idTrabajo = filter_var($datos["id_trabajo"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        if ($idTrabajo === false) {
            http_response_code(400);
            echo json_encode(["error" => "El identificador del trabajo no es válido"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $stmt = $conexion->prepare(
            "UPDATE trabajo
             SET nombre = :nombre, precio = :precio, id_categoria = :id_categoria, id_unidad = :id_unidad
             WHERE id = :id_trabajo AND id_usuario = :id_usuario"
        );
        $stmt->execute([
            ":nombre" => $trabajo,
            ":precio" => $precio,
            ":id_categoria" => $idCategoria,
            ":id_unidad" => $idUnidad,
            ":id_trabajo" => $idTrabajo,
            ":id_usuario" => $id_usuario
        ]);

        if ($stmt->rowCount() === 0) {
            $verificar = $conexion->prepare("SELECT id FROM trabajo WHERE id = :id AND id_usuario = :id_usuario");
            $verificar->execute([":id" => $idTrabajo, ":id_usuario" => $id_usuario]);
            if (!$verificar->fetch()) {
                http_response_code(404);
                echo json_encode(["error" => "No se encontró el trabajo"], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        echo json_encode(["success" => true], JSON_UNESCAPED_UNICODE);
        exit;
    } catch (PDOException $error) {
        error_log("Error al guardar el precio: " . $error->getMessage());
        http_response_code(500);
        echo json_encode(["error" => "No se pudo guardar el precio"], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if ($metodo === "DELETE") {
    parse_str(file_get_contents("php://input"), $datos);
    $idTrabajo = filter_var($datos["id_trabajo"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    if ($idTrabajo === false) {
        http_response_code(400);
        echo json_encode(["error" => "El identificador del trabajo no es válido"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        $stmt = $conexion->prepare("DELETE FROM trabajo WHERE id = :id AND id_usuario = :id_usuario");
        $stmt->execute([":id" => $idTrabajo, ":id_usuario" => $id_usuario]);
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(["error" => "No se encontró el trabajo"], JSON_UNESCAPED_UNICODE);
            exit;
        }
    } catch (PDOException $error) {
        error_log("Error al eliminar el precio: " . $error->getMessage());
        http_response_code(409);
        echo json_encode(["error" => "No se puede eliminar un trabajo que ya está incluido en un presupuesto"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(["success" => true], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(405);
header("Allow: GET, POST, PUT, DELETE");
echo json_encode(["error" => "Método no permitido"], JSON_UNESCAPED_UNICODE);
