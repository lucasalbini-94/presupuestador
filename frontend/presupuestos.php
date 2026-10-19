<?php
$pageTitle = "Lista de Presupuestos";
$currentPage = "presupuestos";

// Incluimos Header y Aside reutilizables
require_once "includes/header.php";
require_once "includes/aside.php";
?>

    <!-- Contenido Principal -->
    <main class="main-content">
      <section class="view-screen active">
        <header class="page-header">
          <div class="page-title">
            <h1>Presupuestos</h1>
            <p>Consultá, filtrá y administrá tus presupuestos.</p>
          </div>
          <a href="nuevo_presupuesto.php" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i>
            <span>Nuevo presupuesto</span>
          </a>
        </header>

        <section class="card">
          <div class="toolbar">
            <label class="search-box">
              <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
              <input type="search" id="buscar" name="buscar" placeholder="Buscar por cliente o número" aria-label="Buscar presupuestos">
            </label>
            <label class="filters">
              <span class="sr-only">Filtrar por estado</span>
              <select id="estado" name="estado">
                <option value="todos">Todos los estados</option>
                <option value="borrador">Borrador</option>
                <option value="pendiente">Pendiente</option>
                <option value="aprobado">Aprobado</option>
                <option value="rechazado">Rechazado</option>
                <option value="facturado">Facturado</option>
              </select>
            </label>
          </div>

          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>Nro</th>
                  <th>Cliente</th>
                  <th>Total</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="cuerpoTabla"></tbody>
            </table>
          </div>
        </section>
      </section>
    </main>

<?php
require_once "includes/footer.php";
?>

<script src="js/presupuestos.js"></script>