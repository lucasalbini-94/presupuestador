<?php
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

require "conexion.php";
session_start();

// ─── CORS ───
$origen = $_SERVER["HTTP_ORIGIN"] ?? "";
$origenesPermitidos = [
    "http://127.0.0.1:5500",
    "http://localhost:5500",
    "http://localhost"
];
if (in_array($origen, $origenesPermitidos, true)) {
    header("Access-Control-Allow-Origin: $origen");
    header("Vary: Origin");
    header("Access-Control-Allow-Credentials: true");
}

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$metodo = $_SERVER["REQUEST_METHOD"];

if ($metodo === "OPTIONS") {
    http_response_code(204);
    exit;
}

/* ==========================
   GET - Listar todos
   ========================== */
if ($metodo === "GET") {
    $consulta = $conexion->query("SELECT * FROM usuario");
    $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($usuarios, JSON_UNESCAPED_UNICODE);
}

/* ==========================
   POST - Acciones / Guardar uno nuevo
   ========================== */
elseif ($metodo === "POST") {
    $cuerpo = file_get_contents("php://input");
    $datos = json_decode($cuerpo, true);

    // ─────────────────────────────────────────
    // ¿Vino una acción? (eliminar_cuenta, etc.)
    // ─────────────────────────────────────────
    if (isset($datos["accion"])) {

        // ───── ELIMINAR CUENTA ─────
        if ($datos["accion"] === "eliminar_cuenta") {

            // VE SI HAY UNA SESION INICIADA
            if (!isset($_SESSION["usuario_id"])) {
                http_response_code(401);
                echo json_encode(["error" => "No hay sesión iniciada"]);
                exit;
            }
            // GUARDA EL ID DEL USUARIO QUE TIENE LA SESION INICIADA
            $usuarioId = $_SESSION["usuario_id"];

            // Verificar que venga la contraseña
            if (!isset($datos["password"])) {
                http_response_code(400);
                echo json_encode(["error" => "Falta la contraseña"]);
                exit;
            }

            // SE BUSCA LA CONTRASEÑA EN LA BASE DE DATOS (columna: pass)
            $sqlPass = "SELECT pass FROM usuario WHERE id = ?";
            $stmtPass = $conexion->prepare($sqlPass);
            $stmtPass->execute([$usuarioId]);
            $usuarioPass = $stmtPass->fetch(PDO::FETCH_ASSOC);

            // SI LA INGRESADA ES DISTINTA A LA VERDADERA NO SE PUEDE BORRAR EL USUARIO
            if (!$usuarioPass || !password_verify($datos["password"], $usuarioPass["pass"])) {
                http_response_code(401);
                echo json_encode(["error" => "Contraseña incorrecta"]);
                exit;
            }

            // SI LAS CONTRASEÑAS COINCIDEN SE BORRA EL USUARIO Y SE CIERRA LA SESION
            try {
                $conexion->beginTransaction();

                // 1. Borrar presupuesto_trabajo de los presupuestos del usuario
                $sqlPT = "DELETE pt FROM presupuesto_trabajo pt
                          INNER JOIN presupuesto p ON pt.id_presupuesto = p.id
                          INNER JOIN cliente c ON p.id_cliente = c.id
                          WHERE c.id_usuario = ?";
                $stmtPT = $conexion->prepare($sqlPT);
                $stmtPT->execute([$usuarioId]);

                // 2. Borrar presupuestos de los clientes del usuario
                $sqlP = "DELETE p FROM presupuesto p
                         INNER JOIN cliente c ON p.id_cliente = c.id
                         WHERE c.id_usuario = ?";
                $stmtP = $conexion->prepare($sqlP);
                $stmtP->execute([$usuarioId]);

                // 3. Borrar clientes del usuario
                $sqlC = "DELETE FROM cliente WHERE id_usuario = ?";
                $stmtC = $conexion->prepare($sqlC);
                $stmtC->execute([$usuarioId]);

                // 4. Borrar precios modificados del usuario (usuario_trabajo)
                $sqlUT = "DELETE FROM usuario_trabajo WHERE id_usuario = ?";
                $stmtUT = $conexion->prepare($sqlUT);
                $stmtUT->execute([$usuarioId]);

                // 5. Borrar configuración del usuario
                $sqlConf = "DELETE FROM configuracion WHERE id_usuario = ?";
                $stmtConf = $conexion->prepare($sqlConf);
                $stmtConf->execute([$usuarioId]);

                // 6. Finalmente, borrar el usuario
                $sqlUsuario = "DELETE FROM usuario WHERE id = ?";
                $stmtUsuario = $conexion->prepare($sqlUsuario);
                $stmtUsuario->execute([$usuarioId]);

                $conexion->commit();

                // 7. Cerrar sesión
                session_destroy();

                echo json_encode(["success" => true, "mensaje" => "Cuenta eliminada"]);

            } catch (PDOException $e) {
                if ($conexion->inTransaction()) {
                    $conexion->rollBack();
                }
                http_response_code(500);
                echo json_encode(["error" => "No se pudo eliminar la cuenta: " . $e->getMessage()]);
            }
        }

        // ───── OTRA ACCIÓN ─────
        else {
            http_response_code(400);
            echo json_encode(["error" => "Acción desconocida"]);
        }

        exit;
    }

    // ─────────────────────────────────────────
    // Si NO vino acción → INSERTAR NUEVO USUARIO
    // ─────────────────────────────────────────
    if (!isset($datos["nombre"]) || !isset($datos["apellido"]) || !isset($datos["telefono"]) || !isset($datos["email"]) || !isset($datos["password"])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos"]);
        exit;
    }

    try {
        $sql = "INSERT INTO usuario (nombre, apellido, telefono, email, pass)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $datos["nombre"],
            $datos["apellido"],
            $datos["telefono"],
            $datos["email"],
            $datos["password"]
        ]);

        $nuevoUsuarioId = $conexion->lastInsertId();

        // Crear configuración base para el usuario nuevo
        $sqlConf = "INSERT INTO configuracion (id_usuario, validez_presupuesto, matricula, nro_matricula, porcentaje_matricula)
                    VALUES (?, 15, 0, NULL, NULL)";
        $stmtConf = $conexion->prepare($sqlConf);
        $stmtConf->execute([$nuevoUsuarioId]);

        echo json_encode(["success" => true, "mensaje" => "Datos guardados correctamente"]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al insertar: " . $e->getMessage()]);
    }
}
/* ==========================
   Otro método - No permitido
   ========================== */
else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}
?>