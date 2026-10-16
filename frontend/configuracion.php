<?php
$pageTitle = "Configuracion";
$currentPage = "configuracion";

// Incluimos Header y Sidebar reutilizables
require_once "includes/header.php";
require_once "includes/aside.php";
?>

 <section class="iniciar_sesion">
      <h2>INICIAR SESION</h2>
      
      <form id="iniciar_sesion" >
        <label for="email_usuario">Email</label>
        <input type="email" id="email_usuario" name="email_usuario" required/>

        <label for="password_usuario">Constraseña -5 a 10 caracteres-</label>
        <input type="password" id="password_usuario" name="password_usuario" required minlength="5" maxlength="10" />

        <button type="submit">Iniciar sesión</button>
        </form>
        <button type="button" id="cerrar_sesion">Cerrar sesión</button>

    </section>

    <section class="datos_personales">
      <h2>CREAR NUEVO USUARIO</h2>

      <form id="datos_personales" >
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" required/>

        <label for="apellido">Apellido</label>
        <input type="text" id="apellido" name="apellido" required/>

        <label for="telefono">Teléfono</label>
        <input type="tel" id="telefono" name="telefono" required/>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required/>

        <label for="password">Contraseña -5 a 10 caracteres-</label>
        <input type="password" id="password" name="password" required minlength="5" maxlength="10" />

        <button type="submit">Guardar</button>
        </form>

      </section>

<!--        <h3>Dirección</h3>

        <label for="calle">Calle</label>
        <input type="text" id="calle" name="calle" required/>

        <label for="altura">Altura</label>
        <input type="number" id="altura" name="altura" required/>

        <label for="ciudad">Ciudad</label>
        <input type="text" id="ciudad" name="ciudad" required/>

        <label for="provincia">Provincia</label>
        <input type="text" id="provincia" name="provincia" required/>

        <button type="submit">Guardar</button>
      </form>
    </section>
-->
    <section>
    <h2>Periodo de validez del presupuesto</h2>
      <select id="validez" name="pais" required>
      </select>
    </section>
<!-- 
    <section>
    <h2>Perfil profesional</h2>
    <input type="checkbox" id="matricula" name="matricula">
    <label for="matricula">¿Tiene matricula?</label>
      <input type="number" id="num_matricula" name="num_matricula" placeholder="N° de matricula" disabled>

    </section> -->

  <?php
// Incluimos Footer
require_once "includes/footer.php";
?>

<script src="js/configuracion.js"></script>
