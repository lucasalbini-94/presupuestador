const cerrar_sesion = document.getElementById("cerrar_sesion");
const eliminar_cuenta = document.getElementById("eliminar_cuenta");

// CERRAR SESIÓN
if (cerrar_sesion) {
    cerrar_sesion.addEventListener("click", async function (e) {
        e.preventDefault();
        console.log("Listener de cerrar sesión ejecutado");
        try {
            const respuesta = await fetch("http://localhost/presupuestador/api/logout.php", {
                method: "POST",
                credentials: "include"
            });

            const resultado = await respuesta.json();

            if (!respuesta.ok) {
                console.error("Error del servidor:", resultado.error);
                alert("No se pudo cerrar la sesión");
                return;
            }

            console.log("Respuesta:", resultado);
            alert("Sesión cerrada");
            location.reload();

        } catch (error) {
            console.error("Error en la petición:", error);
            alert("No se pudo cerrar la sesión");
        }
    });
}

// ELIMINAR CUENTA
if (eliminar_cuenta) {
    eliminar_cuenta.addEventListener("click", async function (e) {
        e.preventDefault();

        // SE PIDE LA CONTRASEÑA PARA ELIMINAR LA CUENTA
        const password = prompt("Para eliminar tu cuenta, escribí tu contraseña:");
        if (password === null) {
            return; // el usuario canceló
        }

        if (password.trim() === "") {
            alert("Tenés que escribir tu contraseña.");
            return;
        }

        if (!confirm("¿Seguro que querés eliminar tu cuenta? Se borrarán también todos tus precios.")) {
            return;
        }

        try {
            const respuesta = await fetch("http://localhost/presupuestador/api/usuarios.php", {
                method: "POST",
                credentials: "include",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    accion: "eliminar_cuenta",
                    password: password
                })
            });

            const resultado = await respuesta.json();

            if (!respuesta.ok) {
                console.error("Error del servidor:", resultado.error);
                alert(resultado.error || "No se pudo eliminar la cuenta");
                return;
            }

            console.log("Respuesta:", resultado);
            alert("Cuenta eliminada");
            window.location.href = "login.php";

        } catch (error) {
            console.error("Error en la petición:", error);
            alert("No se pudo eliminar la cuenta");
        }
    });
}