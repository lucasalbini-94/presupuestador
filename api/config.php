<?php
function entorno($nombre, $porDefecto = "") {
    $valor = getenv($nombre);
    return $valor !== false ? $valor : $porDefecto;
}

define("DB_HOST", entorno("DB_HOST", "gondola.proxy.rlwy.net"));
define("DB_PORT", entorno("DB_PORT", "22256"));
define("DB_NAME", entorno("DB_NAME", "presupuestador"));
define("DB_USER", entorno("DB_USER", "root"));
define("DB_PASS", entorno("DB_PASS", "aUYkjFreYoAtLHzplBtfbBbevAzjIYDa"));
