<?php
require_once "../api/auth.php";
$pageTitle = "Mis precios";
$currentPage = "precios";

// Incluimos Header y Sidebar reutilizables
require_once "includes/header.php";
require_once "includes/aside.php";
?>

<main class="main-content">
  <section class="view-screen active">
    <header class="page-header">
      <div class="page-title">
        <h1>Mis precios</h1>
        <p>Administrá los precios que usás para preparar presupuestos.</p>
      </div>
    </header>
    <section class="menu_dos">
      <h2>Acciones</h2>
      <button type="button" id="nuevo_trabajo" class="btn btn-outline">Nuevo trabajo</button>
      <button type="button" id="importar" class="btn btn-outline">Importar base</button>
      <button type="button" id="agregar_precio" class="btn btn-primary">Agregar precio</button>
    </section>

<!-- VENTANA QUE APARECE PARA AGREGAR UN NUEVO PRECIO -->
<dialog id="ventana_precio">
  <h2>Agregar precio</h2>

  <form method="dialog" class="dialog-form">

        <label for="categoria">Categoría</label>
        <select id="categoria" name="categoria">
            <option value="Cableado">Cableado</option>
            <option value="Canalización">Canalización</option>
            <option value="Tablero">Tablero</option>
        </select> 

        <label for="trabajo">Trabajo</label>
        <input type="text" id="trabajo" name="trabajo" required/>

        <label for="precio">Precio</label>
        <input type="number" id="precio" name="precio" required/>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" id="cancelar" class="btn btn-outline">Cancelar</button>

  </form>
</dialog>

<!--   -->

<!-- VENTANA QUE APARECE CUANDO EDITO UN PRECIO YA EXISTENTE -->
<dialog id="ventana_editar_precio">
  <h2>Agregar precio</h2>

  <form method="dialog" class="dialog-form">

        <label for="categoria">Categoría</label>
        <select id="categoria" name="categoria">
            <option value="Cableado">Cableado</option>
            <option value="Canalización">Canalización</option>
            <option value="Tablero">Tablero</option>
        </select> 

        <label for="trabajo">Trabajo</label>
        <input type="text" id="trabajo" name="trabajo" />

        <label for="precio">Precio</label>
        <input type="number" id="precio" name="precio" />

        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" id="cancelar" class="btn btn-outline">Cancelar</button>

  </form>
</dialog>

<!--   -->

<section class="menu_tres">

    <h2>Precios por trabajo</h2>

    <div class="table-container">
    <table id="tabla_precios">

        <thead>
            <tr>
                <th>Categoría</th>
                <th>Trabajo</th>
                <th>Mi precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
            <tbody>
            <!-- Acá el JavaScript va a insertar las filas -->
            </tbody>

    </table>
    </div>

</section>
  </section>
</main>
    
<?php
// Incluimos Footer
require_once "includes/footer.php";
?>

<script src="js/precios.js"></script>