<!-- Barra Lateral Reutilizable -->
    <aside class="barra-lateral">
      <a href="/presupuestador/index.php" class="nav-item <?php echo ($currentPage === 'inicio') ? 'active' : ''; ?>">
        <i class="fa-solid fa-house"></i>
        <span>Inicio</span>
      </a>
      <a href="/presupuestador/frontend/presupuestos.php" class="nav-item <?php echo ($currentPage === 'presupuestos') ? 'active' : ''; ?>">
        <i class="fa-regular fa-file-lines"></i>
        <span>Presupuestos</span>
      </a>
      <a href="frontend/clientes.php" class="nav-item <?php echo ($currentPage === 'clientes') ? 'active' : ''; ?>">
        <i class="fa-solid fa-user-group"></i>
        <span>Clientes</span>
      </a>
      <a href="frontend/precios.php" class="nav-item <?php echo ($currentPage === 'precios') ? 'active' : ''; ?>">
        <i class="fa-regular fa-credit-card"></i>
        <span>Mis precios</span>
      </a>
      <a href="frontend/configuracion.php" class="nav-item <?php echo ($currentPage === 'configuracion') ? 'active' : ''; ?>">
        <i class="fa-solid fa-gear"></i>
        <span>Configuración</span>
      </a>
    </aside>