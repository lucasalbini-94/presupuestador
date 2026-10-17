<?php
require_once __DIR__ . "/../../api/auth.php";
if (obtenerUsuarioAutenticado() === null) {
  header("Location: login.html");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? $pageTitle . " - Presupuestador" : "Presupuestador"; ?></title>
  
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Estilos Globales -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <!-- Barra Superior -->
  <header class="barra-superior">
    <div class="marca">
      <i class="fa-solid fa-bolt"></i>
      <span>Presupuestador</span>
    </div>
    <div class="usuario">
      <div class="usuario-perfil">
        <div class="avatar">
          <i class="fa-solid fa-user"></i>
        </div>
      </div>
    </div>
  </header>

  <div class="contenedor">