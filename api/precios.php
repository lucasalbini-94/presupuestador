<?php
// api/precios.php
require_once __DIR__ . "/auth.php";

header("Content-Type: application/json; charset=utf-8");
$id_usuario = requerirUsuarioApi();

require_once __DIR__ . "/conexion.php";

$metodo = $_SERVER["REQUEST_METHOD"];


// GET: Listar trabajos con precio base o personalizado
if ($metodo === "GET") {
    $sql = "SELECT 
                t.id AS id_trabajo,
                c.descripcion AS categoria,
                t.nombre AS trabajo,
                u.descripcion AS unidad,
                t.precio_base,
                ut.precio_modificado,
                COALESCE(ut.precio_modificado, t.precio_base) AS precio
            FROM trabajo t
            JOIN categoria c ON t.id_categoria = c.id
            JOIN unidad u ON t.id_unidad = u.id
            LEFT JOIN usuario_trabajo ut 
                   ON t.id = ut.id_trabajo AND ut.id_usuario = :id_usuario
            ORDER BY c.id, t.nombre";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([":id_usuario" => $id_usuario]);
    $precios = $stmt->fetchAll();

    echo json_encode($precios, JSON_UNESCAPED_UNICODE);
    exit;
}


// POST: Modificar o fijar precio personalizado
if ($metodo === "POST") {
    $datos = json_decode(file_get_contents("php://input"), true);

    if (!isset($datos["id_trabajo"]) || !isset($datos["precio"])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan campos requeridos (id_trabajo, precio)"]);
        exit;
    }

    try {
        // Inserta o actualiza el precio modificado en usuario_trabajo
        $sql = "INSERT INTO usuario_trabajo (id_usuario, id_trabajo, precio_modificado)
                VALUES (:id_usuario, :id_trabajo, :precio)
                ON DUPLICATE KEY UPDATE precio_modificado = :precio_update";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ":id_usuario"     => $id_usuario,
            ":id_trabajo"     => (int)$datos["id_trabajo"],
            ":precio"         => (float)$datos["precio"],
            ":precio_update"  => (float)$datos["precio"]
        ]);

        http_response_code(200);
        echo json_encode(["mensaje" => "Precio actualizado con éxito"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(["error" => "Método no permitido"]);