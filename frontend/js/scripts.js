async function cerrarSesion(button) {
    button.disabled = true;

    try {
        const respuesta = await fetch('../api/logout.php', {
            method: 'POST'
        });

        if (!respuesta.ok) {
            throw new Error(`No se pudo cerrar sesión (HTTP ${respuesta.status}).`);
        }

        window.location.assign('login.html');
    } catch (error) {
        console.error('Error al cerrar sesión:', error);
        alert(error.message || 'No se pudo cerrar sesión. Intentá nuevamente.');
        button.disabled = false;
    }
}