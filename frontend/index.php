<?php
$pageTitle = "Inicio - Presupuestador";
$currentPage = "index";
require_once __DIR__ . '/../api/auth.php';
// Incluimos Header y Sidebar reutilizables
require_once "includes/header.php";
require_once "includes/aside.php";
?>

    <!-- Contenido principal -->
<main class="main-content">
  <section class="view-screen active">
    <header class="page-header">
      <div class="page-title">
        <h1>Inicio</h1>
        <p>Resumen de tus presupuestos recientes.</p>
      </div>
      <a href="nuevo_presupuesto.php" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i>
        <span>Nuevo presupuesto</span>
      </a>
    </header>
    <section class="card">
      <div class="section-header-flex">
        <h2>Últimos presupuestos</h2>
      </div>
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Nro</th>
              <th>Cliente</th>
              <th>Total</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody id="cuerpoTabla"></tbody>
        </table>
      </div>
    </section>
  </section>
</main>

<?php
// Incluimos Footer
require_once "includes/footer.php";
?>

<script src="js/presupuestos.js"></script>