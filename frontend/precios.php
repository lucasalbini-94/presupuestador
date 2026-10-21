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
      <button type="button" id="agregar_precio" class="btn btn-primary">Agregar precio</button>
    </section>

<!-- VENTANA QUE APARECE PARA AGREGAR UN NUEVO PRECIO -->
<dialog id="ventana_precio">
  <h2>Agregar precio</h2>

  <form class="dialog-form">

        <label for="categoria_nuevo">Categoría</label>
        <select id="categoria_nuevo" name="categoria" required>
            <option value="">Cargando categorías...</option>
        </select>

        <label for="unidad_nueva">Unidad</label>
        <select id="unidad_nueva" name="unidad" required>
            <option value="">Cargando unidades...</option>
        </select>

        <label for="trabajo_nuevo">Trabajo</label>
        <input type="text" id="trabajo_nuevo" name="trabajo" required/>

        <label for="precio_nuevo">Precio</label>
        <input type="number" id="precio_nuevo" name="precio" min="0" step="0.01" required/>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-outline">Cancelar</button>

  </form>
</dialog>

<!--   -->

<!-- VENTANA QUE APARECE CUANDO EDITO UN PRECIO YA EXISTENTE -->
<dialog id="ventana_editar_precio">
  <h2>Editar precio</h2>

  <form class="dialog-form">

        <label for="categoria_editar">Categoría</label>
        <select id="categoria_editar" name="categoria" required>
            <option value="">Cargando categorías...</option>
        </select>

        <label for="unidad_editar">Unidad</label>
        <select id="unidad_editar" name="unidad" required>
            <option value="">Cargando unidades...</option>
        </select>

        <label for="trabajo_editar">Trabajo</label>
        <input type="text" id="trabajo_editar" name="trabajo" required/>

        <label for="precio_editar">Precio</label>
        <input type="number" id="precio_editar" name="precio" min="0" step="0.01" required/>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-outline">Cancelar</button>

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
                <th>Unidad</th>
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