<?php
require_once __DIR__ . "/../../api/auth.php";
if (obtenerUsuarioAutenticado() === null) {
  header("Location: login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? $pageTitle . " - Presupuestador" : "Presupuestador"; ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Estilos Globales -->
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="body" data-page="<?php echo htmlspecialchars($currentPage ?? '', ENT_QUOTES, 'UTF-8'); ?>">

  <!-- Barra Superior -->
  <header class="barra-superior">
    <div class="brand">
      <i class="fa-solid fa-bolt"></i>
      <span>Presupuestador</span>
    </div>
    <div class="usuario">
      <div class="usuario-perfil">
        <div class="avatar">
          <i class="fa-solid fa-user"></i>
        </div>
      </div>
      <button type="button" class="cerrar-sesion" onclick="cerrarSesion(this)">Cerrar Sesión</button>
    </div>
  </header>

  <div class="app-container">