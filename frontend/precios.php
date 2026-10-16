<?php
$pageTitle = "Mis precios";
$currentPage = "precios";

// Incluimos Header y Sidebar reutilizables
require_once "includes/header.php";
require_once "includes/aside.php";
?>

    <!-- Contenido principal -->
<section class="menu_dos">
    <h2>Mis precios</h2>

    <button type="button" id="nuevo_trabajo">Nuevo trabajo</button>

    <button type="button" id="importar">Importar base</button>

    <button type="button" id="agregar_precio">Agregar precio</button>
</section>

<!-- VENTANA QUE APARECE PARA AGREGAR UN NUEVO PRECIO -->
<dialog id="ventana_precio">
  <h2>Agregar precio</h2>

  <form method="dialog">

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

        <button type="submit">Guardar</button>
        <button type="button" id="cancelar">Cancelar</button>

  </form>
</dialog>

<!--   -->

<!-- VENTANA QUE APARECE CUANDO EDITO UN PRECIO YA EXISTENTE -->
<dialog id="ventana_editar_precio">
  <h2>Agregar precio</h2>

  <form method="dialog">

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

        <button type="submit">Guardar</button>
        <button type="button" id="cancelar">Cancelar</button>

  </form>
</dialog>

<!--   -->

<section class="menu_tres">

    <h2>Mis precios</h2>

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

</section>

<?php
// Incluimos Footer
require_once "includes/footer.php";
?>

<script src="js/precios.js"></script>