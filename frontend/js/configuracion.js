const select = document.getElementById("validez");

const matricula = document.getElementById("matricula");

const num_matricula = document.getElementById("num_matricula");

const datos_personales = document.getElementById("datos_personales");

const iniciar_sesion = document.getElementById("iniciar_sesion");

const cerrar_sesion = document.getElementById("cerrar_sesion");

// Se crean los dias en la validez
for (let i = 1; i <= 30; i++) {
  const opcion = document.createElement("option");
  opcion.value = i;
  if (i > 1)
  {dia = " días"}
  else
  {dia = " día"}
  opcion.textContent = i.toString() + dia;
  select.appendChild(opcion);
}
/*
// Se cambia si la matricula puede o no ponerse dependiendo si esta elegido con o sin matricula
matricula.addEventListener("change", function () {
  if (matricula.checked) {
    num_matricula.disabled = false;
  } else {
    num_matricula.disabled = true;
  }
});
*/
iniciar_sesion.addEventListener("submit", async function (e) {
  e.preventDefault();
  const email_usuario = iniciar_sesion.elements.email_usuario.value;
  const password_usuario = iniciar_sesion.elements.password_usuario.value;

  try {
    const respuesta = await fetch("http://localhost/presupuestador/api/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            email_usuario: email_usuario,
            password_usuario: password_usuario
        })
    });

    const resultado = await respuesta.json();

    if (!respuesta.ok) {
        console.error("Error del servidor:", resultado.error);
        alert("Email o contraseña incorrectos");
        return;
    }

    console.log("Respuesta:", resultado);
    alert("Sesion iniciada");
    
} catch (error) {
    console.error("Error en la petición:", error);
    alert("Email o contraseña incorrectos");
}

});

cerrar_sesion.addEventListener("click", async function () {

    try {
        const respuesta = await fetch("http://localhost/presupuestador/api/logout.php", {
            method: "POST"
        });

        const resultado = await respuesta.json();

        if (!respuesta.ok) {
            console.error("Error del servidor:", resultado.error);
            alert("No se pudo cerrar la sesión");
            return;
        }

        console.log("Respuesta:", resultado);
        alert("Sesión cerrada");

    } catch (error) {
        console.error("Error en la petición:", error);
        alert("No se pudo cerrar la sesión");
    }

});


datos_personales.addEventListener("submit", async function (e) {
  e.preventDefault();

  // Aca ya tengo todo lo escrito en el formulario
  const nombre = datos_personales.elements.nombre.value;
  const apellido = datos_personales.elements.apellido.value;
  const telefono = datos_personales.elements.telefono.value;
  const email = datos_personales.elements.email.value;
  const password = datos_personales.elements.password.value;
/*   const calle = datos_personales.elements.calle.value;
  const altura = datos_personales.elements.altura.value;
  const ciudad = datos_personales.elements.ciudad.value;
  const provincia = datos_personales.elements.provincia.value;
  const validez = select.value;
  let tiene_matricula;
  let matricula_numero;
  if (matricula.checked) {
    tiene_matricula = "Si";
    matricula_numero = num_matricula.value;
  } else {
    tiene_matricula = "No";
  } */

// Mandar los datos al PHP
try {
    const respuesta = await fetch("http://localhost/presupuestador/api/usuarios.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            nombre: nombre,
            apellido: apellido,
            telefono: telefono,
            email: email,
            password: password
      //      calle: calle,
      //      altura: altura,
      //      ciudad: ciudad,
      //      provincia: provincia,
      //      validez: validez
        })
    });

    const resultado = await respuesta.json();

    if (!respuesta.ok) {
        console.error("Error del servidor:", resultado.error);
        alert("No se pudo crear el usuario. El mail no puede repetirse");
        return;
    }

    console.log("Respuesta:", resultado);
    alert("Usuario creado correctamente");
    datos_personales.reset();
    
} catch (error) {
    console.error("Error en la petición:", error);
    alert("No se pudo crear el usuario");
}

});
