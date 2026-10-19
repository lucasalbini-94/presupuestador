<aside class="sidebar">
  <div class="brand">
    <i class="fa-solid fa-bolt"></i>
    <span>Presupuestador</span>
  </div>
  <nav aria-label="Navegación principal">
    <ul class="nav-list">
      <li class="nav-item <?php echo ($currentPage === 'index') ? 'active' : ''; ?>">
        <a href="index.php"><i class="fa-solid fa-house"></i><span>Inicio</span></a>
      </li>
      <li class="nav-item <?php echo ($currentPage === 'presupuestos') ? 'active' : ''; ?>">
        <a href="presupuestos.php"><i class="fa-regular fa-file-lines"></i><span>Presupuestos</span></a>
      </li>
      <li class="nav-item <?php echo ($currentPage === 'clientes') ? 'active' : ''; ?>">
        <a href="clientes.php"><i class="fa-solid fa-user-group"></i><span>Clientes</span></a>
      </li>
      <li class="nav-item <?php echo ($currentPage === 'precios') ? 'active' : ''; ?>">
        <a href="precios.php"><i class="fa-regular fa-credit-card"></i><span>Mis precios</span></a>
      </li>
      <li class="nav-item <?php echo ($currentPage === 'configuracion') ? 'active' : ''; ?>">
        <a href="usuarios.php"><i class="fa-solid fa-gear"></i><span>Configuración</span></a>
      </li>
    </ul>
  </nav>
</aside>