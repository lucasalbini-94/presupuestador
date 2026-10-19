<?php
require_once __DIR__ . '/../api/auth.php';
$id_usuario_activo = obtenerUsuarioAutenticado();
if ($id_usuario_activo === null) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../api/conexion.php';
require_once __DIR__ . '/../api/models/Presupuesto.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCliente = filter_var($_POST['id_cliente'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $idsTrabajo = $_POST['trabajo_id'] ?? [];
    $cantidades = $_POST['cantidad'] ?? [];
    $items = [];

    if ($idCliente !== false && is_array($idsTrabajo) && is_array($cantidades)) {
        foreach ($idsTrabajo as $indice => $idTrabajo) {
            $idTrabajo = filter_var($idTrabajo, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $cantidad = filter_var($cantidades[$indice] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($idTrabajo !== false && $cantidad !== false) {
                $items[] = ['id_trabajo' => $idTrabajo, 'cantidad' => $cantidad];
            }
        }
    }

    try {
        if ($idCliente === false || !$items) {
            throw new InvalidArgumentException('Seleccioná un cliente y al menos un trabajo válido.');
        }
        (new Presupuesto($conexion))->guardar($id_usuario_activo, $idCliente, $items);
        header('Location: presupuestos.php?creado=1');
        exit;
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    } catch (Throwable $exception) {
        error_log('Error al crear presupuesto: ' . $exception->getMessage());
        $error = 'No se pudo guardar el presupuesto. Intentá nuevamente.';
    }
}

$stmt_clientes = $conexion->prepare(
    'SELECT id, nombre, apellido FROM cliente WHERE id_usuario = ? ORDER BY apellido, nombre'
);
$stmt_clientes->execute([$id_usuario_activo]);
$clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);

$stmt_trabajos = $conexion->prepare(
    'SELECT t.id, t.nombre, t.id_categoria, t.precio
     FROM trabajo t
     INNER JOIN categoria c ON t.id_categoria = c.id
     WHERE t.id_usuario = ?
     ORDER BY c.descripcion, t.nombre'
);
$stmt_trabajos->execute([$id_usuario_activo]);
$trabajos_catalogo = $stmt_trabajos->fetchAll(PDO::FETCH_ASSOC);

$stmt_categorias = $conexion->prepare(
    'SELECT id, descripcion FROM categoria ORDER BY descripcion'
);
$stmt_categorias->execute();
$categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nuevo presupuesto - Presupuestador</title>
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
          <li class="nav-item active"><a href="nuevo_presupuesto.php"><i class="fa-solid fa-file-lines"></i> Presupuestos</a></li>
          <li class="nav-item"><a href="clientes.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
          <li class="nav-item"><a href="precios.php"><i class="fa-solid fa-tags"></i> Mis precios</a></li>
          <li class="nav-item"><a href="usuarios.php"><i class="fa-solid fa-gear"></i> Configuración</a></li>
        </ul>
      </nav>
    </aside>

    <main class="main-content">
      <section class="view-screen active">
        
        <header class="page-header">
          <div class="page-title">
            <h1>Nuevo presupuesto</h1>
            <p>Elegí un cliente y agregá los trabajos para calcular el presupuesto.</p>
          </div>
        </header>

        <?php if ($error !== ''): ?>
          <p class="auth-error" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form action="nuevo_presupuesto.php" method="POST">
          <!-- Bloque 1: Seleccionar Cliente de la Base de Datos -->
          <div class="card form-section">
            <div class="section-header-flex">
              <h3>Datos del Cliente</h3>
              <a href="nuevo_cliente.php" class="btn btn-outline">+ Crear nuevo cliente</a>
            </div>

            <div class="form-grid-3">
              <div class="form-group" style="grid-column: span 3;">
                <label for="id_cliente">Seleccionar Cliente Existente *</label>
                <select name="id_cliente" id="id_cliente" class="input-table" required>
                  <option value="" disabled selected>Seleccione un cliente...</option>
                  <?php foreach ($clientes as $c): ?>
                    <option value="<?php echo $c['id']; ?>">
                      <?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellido']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <!-- Bloque 2: Trabajos a Realizar -->
          <div class="card form-section">
            <div class="section-header-flex">
              <h3>Trabajos a realizar</h3>
              <button type="button" id="btn-add-trabajo" class="btn btn-primary">+ Añadir trabajo</button>
            </div>

            <div class="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Categoria</th>
                    <th>Trabajo del Catálogo</th>
                    <th>Precio Unit.</th>
                    <th>Cant.</th>
                    <th>Subtotal</th>
                    <th class="actions-cell"></th>
                  </tr>
                </thead>
                <tbody id="tabla-trabajos">
                  <tr>
                    <td>
                      <select name="categoria[]" class="input-table select-categoria" required>
                        <option value="" disabled selected>Seleccionar categoría...</option>
                        <?php foreach ($categorias as $categoria): ?>
                          <option value="<?php echo (int)$categoria['id']; ?>"><?php echo htmlspecialchars($categoria['descripcion'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                    <td>
                      <select name="trabajo_id[]" class="input-table select-trabajo" required disabled>
                        <option value="" disabled selected>Seleccionar trabajo...</option>
                        <?php foreach ($trabajos_catalogo as $trabajo): ?>
                          <option value="<?php echo (int)$trabajo['id']; ?>"
                                  data-categoria="<?php echo (int)$trabajo['id_categoria']; ?>"
                                  data-precio="<?php echo htmlspecialchars((string)$trabajo['precio'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($trabajo['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                    <td><input type="number" step="0.01" name="precio_unitario[]" class="input-table input-precio" placeholder="$ 0,00" readonly></td>
                    <td><input type="number" name="cantidad[]" class="input-table input-cantidad" value="1" min="1" placeholder="0" required></td>
                    <td class="text-bold celda-subtotal">$0.00</td>
                    <td class="actions-cell">
                      <button type="button" class="action-btn text-danger btn-eliminar-fila"><i class="fa-regular fa-trash-can"></i></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Bloque de Guardado -->
          <div class="card form-section">
            <div class="quote-summary-container">
              <div class="notes-block">
                <h2 style="font-size: 1.25rem;" id="total-presupuesto">Total $0,00</h2>
                <input type="hidden" name="monto_total_oculto" id="monto_total_oculto" value="0">
              </div>
              <div class="header-actions">
                <button type="submit" class="btn btn-primary" id="btn-finalizar-presupuesto">Guardar Presupuesto</button>
              </div>
            </div>
          </div>

          <!-- Bloque 5: Envios por mail y Whatsapp -->
        <div class="card form-section">
          <h3>Envios por mail y Whatsapp</h3>
          <div class="quote-summary-container">
            <div class="notes-block">
            </div>
            <div class="header-actions">
              <button type="button" class="btn btn-primary" id="btn-enviar-email">Enviar por Email</button>
              <button type="button" class="btn btn-primary" id="btn-enviar-whatsapp">Enviar por WhatsApp</button>
            </div>
          </div>
        </div>
        </form>

      </section>
    </main>
  </div>

  <!-- Puedes conservar tu lógica de JS para calcular subtotales dinámicamente -->
  <script src="js/nuevo_presupuesto.js"></script>
</body>
</html>