<?php
session_start();
$pageTitle = "Mis precios";
$currentPage = "mis_precios";

// Incluimos Header y Sidebar reutilizables
require_once "includes/header.php";
require_once "includes/aside.php";
?>

<!-- Contenido principal -->

<section class="iniciar_sesion">
  <h2>CERRAR SESION O ELIMINAR CUENTA</h2>

  <?php if (isset($_SESSION["usuario_id"])): ?>
    <p>Ya tenés una sesión iniciada.</p>
    <button type="button" id="cerrar_sesion">Cerrar sesión</button>
    <button type="button" id="eliminar_cuenta">Eliminar mi cuenta</button>
  <?php else: ?>
    <p>No tenés una sesión iniciada. <a href="login.php">Iniciar sesión</a></p>
  <?php endif; ?>

</section>

<?php
// Incluimos Footer
require_once "includes/footer.php";
?>

<script src="js/usuarios.js"></script>