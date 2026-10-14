<?php
$pageTitle = "Inicio - Presupuestador";
$currentPage = "index";

// Incluimos Header y Sidebar reutilizables
require_once "includes/header.php";
require_once "includes/aside.php";
?>

    <!-- Contenido principal -->
     <main class="contenedor-principal">
        <h1 class="titulo-pagina">Inicio</h1>
        <button class="btn-primario">
            <i class="fa-solid fa-plus"></i>
            <span>Nuevo presupuesto</span>
        </button>
        <section class="tarjeta">
            <div class="tarjeta-header">
                <h2>Ultimos presupuestos</h2>
            </div>
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTabla">
                        <!-- Datos obternidos de la DB -->
                    </tbody>
                </table>
            </div>
        </section>
     </main>

<?php
// Incluimos Footer
require_once "includes/footer.php";
?>

<script src="js/presupuestos.js"></script>