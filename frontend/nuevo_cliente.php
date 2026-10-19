<?php
require_once __DIR__ . '/../api/auth.php';
$id_usuario_activo = obtenerUsuarioAutenticado();
if ($id_usuario_activo === null) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../api/conexion.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $partes_nombre = explode(' ', $nombre, 2);
    $nombre_cliente = $partes_nombre[0];
    $apellido_cliente = $partes_nombre[1] ?? 'Sin apellido';

    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $localidad = trim($_POST['localidad'] ?? '');
    $direccion_completa = $direccion . ($localidad ? ', ' . $localidad : '');

    if (!empty($nombre)) {
        try {
            $stmt = $conexion->prepare("INSERT INTO cliente (nombre, apellido, direccion, telefono, email, id_usuario) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre_cliente, $apellido_cliente, $direccion_completa, $telefono, $email, $id_usuario_activo]);
            header('Location: clientes.php?exito=1');
            exit;
        } catch (PDOException $e) {
            error_log('Error al guardar cliente: ' . $e->getMessage());
            $error = 'No se pudo guardar el cliente. Revisá los datos e intentá nuevamente.';
        }
    } else {
        $error = 'El campo Nombre y Apellido es obligatorio.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nuevo cliente - Presupuestador</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

  <div class="app-container">
    <aside class="sidebar">
      <div class="brand">
        <i class="fa-regular fa-lightbulb"></i>
        <span>Presupuestador</span>
      </div>
      <nav>
        <ul class="nav-list">
          <li class="nav-item"><a href="index.php"><i class="fa-solid fa-house"></i> Inicio</a></li>
          <li class="nav-item"><a href="nuevo_presupuesto.php"><i class="fa-solid fa-file-lines"></i> Presupuestos</a></li>
          <li class="nav-item active"><a href="clientes.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
          <li class="nav-item"><a href="precios.php"><i class="fa-solid fa-tags"></i> Mis precios</a></li>
          <li class="nav-item"><a href="usuarios.php"><i class="fa-solid fa-gear"></i> Configuración</a></li>
        </ul>
      </nav>
    </aside>

    <main class="main-content">
      <section class="view-screen active">
        
        <header class="page-header">
          <div class="page-title">
            <h1>Nuevo cliente</h1>
          </div>
        </header>

        <div class="back-btn-wrapper">
          <a href="clientes.php" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver a Clientes
          </a>
        </div>

        <?php if (!empty($error)): ?>
          <div class="card" style="background-color: #f8d7da; color: #842029; margin-bottom: 1rem; padding: 1rem;">
            <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>

        <!-- Formulario apuntando a sí mismo -->
        <form action="nuevo_cliente.php" method="POST" class="card form-section">
          <h3>Datos Personales y de Contacto</h3>
          
          <div class="form-grid-3">
            <div class="form-group">
              <label for="nombre">Nombre y Apellido *</label>
              <input type="text" id="nombre" name="nombre" placeholder="Nombre completo" required>
            </div>

            <div class="form-group">
              <label for="telefono">Teléfono</label>
              <input type="tel" id="telefono" name="telefono" placeholder="Ej: +54 11 ...">
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" placeholder="correo@ejemplo.com">
            </div>

            <div class="form-group">
              <label for="direccion">Dirección</label>
              <input type="text" id="direccion" name="direccion" placeholder="Calle y número">
            </div>

            <div class="form-group">
              <label for="localidad">Localidad</label>
              <input type="text" id="localidad" name="localidad" placeholder="Localidad / Ciudad">
            </div>
          </div>

          <div class="section-header-flex" style="margin-top: 2rem; justify-content: flex-end; gap: 1rem;">
            <a href="clientes.php" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cliente</button>
          </div>
        </form>

      </section>
    </main>
  </div>
</body>
</html>