document.querySelector("#formLogin").addEventListener("submit", async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const error = document.querySelector("#errorLogin");
    const button = form.querySelector("button");
    const emailInput = form.querySelector('[name="email_usuario"]');
    const passwordInput = form.querySelector('[name="password_usuario"]');

    if (!emailInput || !passwordInput) {
        error.textContent = "No se encontraron los campos del formulario. Recargá la página e intentá nuevamente.";
        error.hidden = false;
        return;
    }

    error.hidden = true;
    button.disabled = true;

    try {
        const respuesta = await fetch(form.action, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                email_usuario: emailInput.value,
                password_usuario: passwordInput.value
            })
        });
        const resultado = await respuesta.json();

        if (!respuesta.ok) {
            throw new Error(resultado.error || "No se pudo iniciar sesión.");
        }

        window.location.assign("../index.php");
    } catch (err) {
        error.textContent = err.message || "No se pudo conectar con el servidor.";
        error.hidden = false;
        button.disabled = false;
    }
});