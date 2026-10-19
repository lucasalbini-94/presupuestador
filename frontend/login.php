<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Presupuestador</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Estilos Globales -->
     <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="auth-page">
    <main class="auth-card">
        <h1 class="auth-title">Iniciar Sesión</h1>
        <form id="formLogin" class="auth-form" action="../api/login.php" method="post">
            <div class="form-group">
                <label for="email_usuario">Email</label>
                <input type="email" id="email_usuario" name="email_usuario" autocomplete="username" required>
            </div>
            <div class="form-group">
                <label for="password_usuario">Contraseña</label>
                <input type="password" id="password_usuario" name="password_usuario" autocomplete="current-password" required>
            </div>
            <a href="registro.php" class="auth-link">¿No tienes cuenta? Regístrate</a>
            <p id="errorLogin" class="auth-error" role="alert" hidden></p>

            <button type="submit" class="btn btn-primary">Ingresar</button>
        </form>
    </main>
    <script src="js/login.js"></script>
</body>
</html>