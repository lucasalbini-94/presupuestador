<?php
$pageTitle = "Clientes";
$currentPage = "clientes";

// Incluimos Header y Aside reutilizables
require_once "includes/header.php";
require_once "includes/aside.php";
?>

<!-- Contenido Principal -->
    <main class="main-content">
      <section class="view-screen active">
        
        <!-- Encabezado Superior con Búsqueda -->
        <header class="page-header">
          <div class="page-title">
            <h1>Clientes</h1>
            <p>Consultá la información y el historial de tus clientes.</p>
          </div>
          <div class="toolbar" style="margin-bottom: 0;">
            <div class="search-box">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="search-input" placeholder="Buscar cliente...">
            </div>
          </div>
          <div class="header-actions">
            <a href="nuevo_cliente.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo cliente</a>
          </div>
        </header>

        <!-- Lista de Clientes -->
        <div class="card form-section">
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Teléfono</th>
                  <th>Localidad</th>
                  <th class="actions-cell"></th>
                </tr>
              </thead>
              <tbody id="tabla-clientes">
                <!-- Se llena dinámicamente mediante JS -->
              </tbody>
            </table>
          </div>
        </div>

        <!-- Tarjeta de Detalle del Cliente Seleccionado -->
        <div class="card form-section">
          <div class="section-header-flex">
            <span class="page-title">
              <h1 id="detalle-cliente-nombre">Seleccione un cliente</h1>
            </span>
            <a href="nuevo_presupuesto.php" class="btn btn-outline">+ Nuevo presupuesto</a>
          </div>

          <div class="quote-summary-container" style="border-top: none; padding-top: 0; margin-top: 0;">
            <!-- Información del Cliente -->
            <div class="notes-block">
              <div class="form-grid-3" id="detalle-cliente-info">
                <!-- Se llena dinámicamente mediante JS -->
              </div>

              <div class="form-group" style="margin-top: 1rem;">
                <label>Observaciones</label>
                <p class="text-bold" id="detalle-cliente-obs">-</p>
              </div>
            </div>

            <!-- Presupuestos Anteriores del Cliente -->
            <div class="totals-block card" id="detalle-presupuestos" style="box-shadow: none;">
              <h3>Presupuestos anteriores</h3>
              <!-- Se llena dinámicamente mediante JS -->
            </div>
          </div>
        </div>
      <?php include "includes/contacto.php"; ?>
      </section>
    </main>

<?php require_once "includes/footer.php"; ?>
<script src="js/nuevo_cliente.js"></script>