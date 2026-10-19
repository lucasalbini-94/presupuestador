<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Presupuestador</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Estilos Globales -->
     <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="auth-page">
    <main class="auth-card">
        <h1 class="auth-title">Registrar Usuario</h1>
        <form id="formRegistro" class="auth-form" action="../api/registro.php" method="post">
            <div class="form-group">
                <label for="tbxNombre">Nombre</label>
                <input type="text" id="tbxNombre" name="tbxNombre" autocomplete="given-name" maxlength="100" required>
            </div>
            <div class="form-group">
                <label for="tbxApellido">Apellido</label>
                <input type="text" id="tbxApellido" name="tbxApellido" autocomplete="family-name" maxlength="100" required>
            </div>
            <div class="form-group">
                <label for="tbxTelefono">Teléfono</label>
                <input type="tel" id="tbxTelefono" name="tbxTelefono" autocomplete="tel" maxlength="20">
            </div>
            <div class="form-group">
                <label for="tbxEmail">Email</label>
                <input type="email" id="tbxEmail" name="tbxEmail" autocomplete="email" maxlength="100" required>
            </div>
            <div class="form-group">
                <label for="tbxPassword">Contraseña</label>
                <input type="password" id="tbxPassword" name="tbxPassword" autocomplete="new-password" minlength="8" maxlength="72" required>
            </div>
            <div class="form-group matricula-group">
                <label for="matricula">¿Tiene matrícula?</label>
                <input type="checkbox" id="matricula" name="matricula">
                <input type="number" id="tbxMatricula" name="tbxMatricula" placeholder="Ingrese su matrícula" autocomplete="off" min="1" max="2147483647" step="1" disabled>
            </div>
            <a href="login.php" class="auth-link">¿Ya tienes cuenta? Inicia sesión</a>
            <p id="errorRegistro" class="auth-error" role="alert" hidden></p>

            <button type="submit" class="btn btn-primary">Registrar</button>
        </form>
    </main>
    <script src="js/registro.js"></script>
</body>
</html>