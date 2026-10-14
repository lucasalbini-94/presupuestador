<?php
$pageTitle = "Lista de Presupuestos";
$currentPage = "presupuestos";

// Incluimos Header y Sidebar reutilizables
require_once "includes/header.php";
require_once "includes/aside.php";
?>

    <!-- Contenido Principal -->
    <main class="contenedor-principal">
      <h1 class="titulo-pagina">Lista de presupuestos</h1>
      <a href="nuevo_presupuesto.php" class="btn-primario">
        <i class="fa-solid fa-plus"></i>
        <span>Nuevo presupuesto</span>
      </a>

      <section class="tarjeta">
        <!-- Barra de Búsqueda y Filtros -->
        <div class="filtros-barra">
          <div class="filtros-grupo">
            <label for="buscar">Buscar:</label>
            <div class="buscador">
              <input type="text" id="buscar" name="buscar" placeholder="">
              <i class="fa-solid fa-magnifying-glass"></i>
            </div>
          </div>

          <div class="filtros-grupo">
            <label for="estado">Estado:</label>
            <select id="estado" name="estado" class="seleccion-estado">
              <option value="todos">Todos</option>
              <option value="enviado">Enviado</option>
              <option value="aceptado">Aceptado</option>
              <option value="borrador">Borrador</option>
            </select>
          </div>
        </div>

        <!-- Tabla de Presupuestos -->
        <div class="tabla-contenedor">
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
            <!-- Datos obtenidos de la DB -->
            <tbody id="cuerpoTabla"></tbody>
          </table>
        </div>
      </section>
    </main>

<?php
require_once "includes/footer.php";
?>

<script src="js/presupuestos.js"></script>