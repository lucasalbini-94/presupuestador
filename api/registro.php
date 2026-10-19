<?php
require_once __DIR__ . "/auth.php";
obtenerUsuarioAutenticado();

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Allow: POST");
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"], JSON_UNESCAPED_UNICODE);
    exit;
}

$datos = json_decode(file_get_contents("php://input"), true);
if (!is_array($datos)) {
    http_response_code(400);
    echo json_encode(["error" => "El formato de los datos no es válido"], JSON_UNESCAPED_UNICODE);
    exit;
}

$nombre = trim((string)($datos["nombre"] ?? ""));
$apellido = trim((string)($datos["apellido"] ?? ""));
$telefono = trim((string)($datos["telefono"] ?? ""));
$email = trim((string)($datos["email"] ?? ""));
$password = (string)($datos["password"] ?? "");
$tieneMatricula = $datos["matricula"] ?? false;
$numeroMatricula = $datos["nro_matricula"] ?? null;

if ($nombre === "" || mb_strlen($nombre) > 100 ||
    $apellido === "" || mb_strlen($apellido) > 100) {
    http_response_code(400);
    echo json_encode(["error" => "Ingresá un nombre y apellido válidos"], JSON_UNESCAPED_UNICODE);
    exit;
}

if (mb_strlen($telefono) > 20) {
    http_response_code(400);
    echo json_encode(["error" => "El teléfono no puede superar los 20 caracteres"], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($email === "" || mb_strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Ingresá un email válido de hasta 100 caracteres"], JSON_UNESCAPED_UNICODE);
    exit;
}

if (strlen($password) < 8 || strlen($password) > 72) {
    http_response_code(400);
    echo json_encode(["error" => "La contraseña debe tener entre 8 y 72 caracteres"], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!is_bool($tieneMatricula)) {
    http_response_code(400);
    echo json_encode(["error" => "El dato de matrícula no es válido"], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($tieneMatricula) {
    $numeroValido = filter_var(
        $numeroMatricula,
        FILTER_VALIDATE_INT,
        ["options" => ["min_range" => 1, "max_range" => 2147483647]]
    );
    if ($numeroValido === false) {
        http_response_code(400);
        echo json_encode(["error" => "Ingresá un número de matrícula válido"], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $numeroMatricula = $numeroValido;
} else {
    $numeroMatricula = null;
}

require_once __DIR__ . "/conexion.php";

try {
    $conexion->beginTransaction();

    $stmt = $conexion->prepare(
        "INSERT INTO usuario (nombre, apellido, telefono, email, pass)
         VALUES (:nombre, :apellido, :telefono, :email, :pass)"
    );
    $stmt->execute([
        ":nombre" => $nombre,
        ":apellido" => $apellido,
        ":telefono" => $telefono === "" ? null : $telefono,
        ":email" => $email,
        ":pass" => password_hash($password, PASSWORD_DEFAULT)
    ]);
    $idUsuario = (int)$conexion->lastInsertId();

    $stmt = $conexion->prepare(
        "INSERT INTO configuracion (id_usuario, matricula, nro_matricula)
         VALUES (:id_usuario, :matricula, :nro_matricula)"
    );
    $stmt->bindValue(":id_usuario", $idUsuario, PDO::PARAM_INT);
    $stmt->bindValue(":matricula", $tieneMatricula ? 1 : 0, PDO::PARAM_INT);
    if ($numeroMatricula === null) {
        $stmt->bindValue(":nro_matricula", null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(":nro_matricula", $numeroMatricula, PDO::PARAM_INT);
    }
    $stmt->execute();

    $conexion->commit();

    session_regenerate_id(true);
    $_SESSION["usuario_id"] = $idUsuario;

    http_response_code(201);
    echo json_encode(["mensaje" => "La cuenta se creó correctamente"], JSON_UNESCAPED_UNICODE);
} catch (PDOException $error) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    if ($error->getCode() === "23000" && (int)($error->errorInfo[1] ?? 0) === 1062) {
        http_response_code(409);
        echo json_encode(["error" => "Ya existe una cuenta registrada con ese email"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    error_log("Error al registrar usuario: " . $error->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "No se pudo crear la cuenta. Intentá nuevamente más tarde."], JSON_UNESCAPED_UNICODE);
}
