const formularioRegistro = document.querySelector("#formRegistro");

formularioRegistro.addEventListener("submit", async event => {
    event.preventDefault();

    const error = document.querySelector("#errorRegistro");
    const boton = formularioRegistro.querySelector('button[type="submit"]');
    const matriculaActiva = document.querySelector("#matricula").checked;
    const numeroMatricula = document.querySelector("#tbxMatricula").value.trim();

    if (matriculaActiva && (!numeroMatricula || !Number.isInteger(Number(numeroMatricula)) ||
        Number(numeroMatricula) < 1 || Number(numeroMatricula) > 2147483647)) {
        error.textContent = "Ingresá un número de matrícula válido.";
        error.hidden = false;
        document.querySelector("#tbxMatricula").focus();
        return;
    }

    error.hidden = true;
    boton.disabled = true;

    try {
        const respuesta = await fetch(formularioRegistro.action, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                nombre: document.querySelector("#tbxNombre").value.trim(),
                apellido: document.querySelector("#tbxApellido").value.trim(),
                telefono: document.querySelector("#tbxTelefono").value.trim(),
                email: document.querySelector("#tbxEmail").value.trim(),
                password: document.querySelector("#tbxPassword").value,
                matricula: matriculaActiva,
                nro_matricula: matriculaActiva ? Number(numeroMatricula) : null
            })
        });
        const resultado = await respuesta.json();

        if (!respuesta.ok) {
            throw new Error(resultado.error || "No se pudo crear la cuenta.");
        }

        window.location.assign("../index.php");
    } catch (err) {
        error.textContent = err.message || "No se pudo conectar con el servidor.";
        error.hidden = false;
        boton.disabled = false;
    }
});

document.querySelector("#matricula").addEventListener("change", event => {
    const campoMatricula = document.querySelector("#tbxMatricula");
    campoMatricula.disabled = !event.currentTarget.checked;
    campoMatricula.required = event.currentTarget.checked;
    if (!event.currentTarget.checked) {
        campoMatricula.value = "";
    } else {
        campoMatricula.focus();
    }
});
