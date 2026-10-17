<?php
function obtenerUsuarioAutenticado(): ?int {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $idUsuario = filter_var(
        $_SESSION["usuario_id"] ?? null,
        FILTER_VALIDATE_INT,
        ["options" => ["min_range" => 1]]
    );

    return $idUsuario === false ? null : $idUsuario;
}

function requerirUsuarioApi(): int {
    $idUsuario = obtenerUsuarioAutenticado();

    if ($idUsuario === null) {
        header("Content-Type: application/json; charset=utf-8");
        http_response_code(401);
        echo json_encode(["error" => "Debés iniciar sesión para continuar"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    return $idUsuario;
}
