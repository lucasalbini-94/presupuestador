// SE CREAN VARIABLES CON LOS ELEMENTOS QUE SE VA A TRABAJAR

const agregar_precio = document.getElementById("agregar_precio");

const ventana_precio = document.getElementById("ventana_precio");

const ventana_editar_precio = document.getElementById("ventana_editar_precio");

const cancelar_editar = ventana_editar_precio.querySelector("#cancelar");

const tabla_precios = document.getElementById("tabla_precios");

const cancelar = ventana_precio.querySelector("#cancelar");

let fila_para_editar;

let boton_editar;

let boton_eliminar;

let categoria_editar;

let trabajo_editar;

let precio_editar;

// Agregar precio, si no hay sesion iniciada le da una advertencia
agregar_precio.addEventListener("click", async function(){
    // Preguntar al servidor si hay sesión
    const respuesta = await fetch("http://localhost/presupuestador/api/precios.php");

    // Si el servidor dice 401 es que no hay sesión
    if (respuesta.status === 401) {
        alert("Tenés que iniciar sesión para agregar precios.");
        return;
    }

    // Si llegó hasta acá, hay sesión abre la ventana
    ventana_precio.showModal();
});

// SE APRETA EN GUARDAR EN LA VENTANA_PRECIO Y GUARDA LOS DATOS EN LA TABLA DE PRECIOS

    ventana_precio.addEventListener("submit", async function () {
    //  Carga los valores que puso el usuario en variables
    const categoria = ventana_precio.querySelector("#categoria").value;
    const trabajo = ventana_precio.querySelector("#trabajo").value;
    const precio = ventana_precio.querySelector("#precio").value;

    // Mandar los datos al PHP
await fetch("http://localhost/presupuestador/api/precios.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
        categoria: categoria,
        trabajo: trabajo,
        precio: precio
    })
});
    
    // Se inserta una fila
    const fila = tabla_precios.insertRow();
    // Se le agregan los valores que se cargaron en las variables
    fila.dataset.id = precio.id;   // Este es el id, el usuario no lo ve porque no hace falta
    fila.insertCell().textContent = categoria;
    fila.insertCell().textContent = trabajo;
    fila.insertCell().textContent = `$ ${precio}`;
    
    // AUTOMATICAMENTE AGREGA LOS BOTONES EDITAR Y ELIMINAR
    const celdaAcciones = fila.insertCell();
  
    boton_editar = document.createElement("button");
    boton_editar.type = "button";
    boton_editar.textContent = "Editar";
    celdaAcciones.appendChild(boton_editar);

    boton_eliminar = document.createElement("button");
    boton_eliminar.type = "button";

    boton_eliminar.textContent = "Eliminar";
    celdaAcciones.appendChild(boton_eliminar);

    // SE LIMPIAN LOS VALORES PARA LA PROXIMA VEZ
    ventana_precio.querySelector("form").reset();
    });

    // SE HACE CLICK EN CANCELAR SE CIERRA SIN GUARDAR NADA
    cancelar.addEventListener("click", function () {
    ventana_precio.close();
    ventana_precio.querySelector("form").reset();
});

// CANCELAR EN LA VENTANA DE EDITAR
cancelar_editar.addEventListener("click", function () {
    ventana_editar_precio.close();
    ventana_editar_precio.querySelector("form").reset();
});

    // ----------- hasta aca todo funciona ------------// 

  // FUNCIONES PARA EDITAR Y ELIMINAR UNA FILA

  // HACER CLICK EN EDITAR

  // Reemplazá todo el bloque de boton_editar.addEventListener por esto:
  tabla_precios.addEventListener("click", async function (e) {
    // Si el click fue en un botón Editar
    if (e.target.textContent === "Editar") {
        fila_para_editar = e.target.closest("tr");

        ventana_editar_precio.showModal();

        // Se cargar los datos como placeholder en la ventana de edicion
        categoria_editar = ventana_editar_precio.querySelector("#categoria");
        categoria_editar.value = fila_para_editar.cells[0].textContent;
        trabajo_editar = ventana_editar_precio.querySelector("#trabajo");
        trabajo_editar.placeholder = fila_para_editar.cells[1].textContent;
        precio_editar = ventana_editar_precio.querySelector("#precio");
        precio_editar.placeholder = fila_para_editar.cells[2].textContent;
    }

    // Si el click fue en un botón Eliminar
    if (e.target.textContent === "Eliminar") {
        if (!confirm("¿Seguro que querés eliminar este precio?")) {
            return;
        }

        const fila = e.target.closest("tr");
        const id = fila.dataset.id;

        const respuesta = await fetch("http://localhost/presupuestador/api/precios.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                accion: "eliminar",
                id: id
            })
        });

        if (respuesta.ok) {
            fila.remove();
        } else {
            alert("No se pudo eliminar");
        }
    }
});

         // SI APRETA EN GUARDAR
        ventana_editar_precio.addEventListener("submit", async function (e) {
        e.preventDefault();

        // Se cargan los datos que hay en la ventana de edicion a variables
        const categoria = ventana_editar_precio.querySelector("#categoria").value;
        const trabajo = ventana_editar_precio.querySelector("#trabajo").value;
        const precio = ventana_editar_precio.querySelector("#precio").value;

        // Calculamos los valores finales ANTES de tocar la fila
        const trabajoFinal = trabajo !== "" ? trabajo : trabajo_editar.placeholder;
        const precioFinal  = precio  !== "" ? `$ ${precio}` : precio_editar.placeholder;

        // Precio limpio (sin "$ " ni espacios) para mandar al PHP
        const precioLimpio = precioFinal.replace("$ ", "").trim();

        const id = fila_para_editar.dataset.id;

        // Mandar al PHP
        const respuesta = await fetch("http://localhost/presupuestador/api/precios.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                accion: "editar",
                id: id,
                categoria: categoria,
                trabajo: trabajoFinal,
                precio: precioLimpio
            })
        });

        if (!respuesta.ok) {
            alert("No se pudo editar");
            return;
        }

        // Si todo salió bien, actualizamos la fila en pantalla
        fila_para_editar.cells[0].textContent = categoria;
        fila_para_editar.cells[1].textContent = trabajoFinal;
        fila_para_editar.cells[2].textContent = precioFinal;

        // Se resetean los datos para la proxima vez
        ventana_editar_precio.querySelector("form").reset();
        ventana_editar_precio.close();

        });


// ============================================
// CARGAR LOS PRECIOS DESDE EL PHP AL ABRIR
// ============================================

async function cargarPrecios() {
    // 1. Pedir los datos al PHP
    const respuesta = await fetch("http://localhost/presupuestador/api/precios.php");
    
    // 2. Convertir la respuesta a JSON (lista de objetos)
    const precios = await respuesta.json();
    
    // 3. Recorrer la lista
    for (const precio of precios) {
        // 4. Crear una fila nueva en la tabla
        const fila = tabla_precios.insertRow();
        
        // 5. Cargar los datos en las celdas
        fila.dataset.id = precio.id;
        fila.insertCell().textContent = precio.categoria;
        fila.insertCell().textContent = precio.trabajo;
        fila.insertCell().textContent = `$ ${precio.precio}`;
        
        // COMO LOS BOTONES EDITAR Y ELIMINAR SON IGUALES PARA TODOS
        // NO NECESITAN EXITIR EN LA BASE DATOS, SE AGREGAN SIEMPRE
        // QUE APAREZCA UN PRECIO
        const celdaAcciones = fila.insertCell();
  
        boton_editar = document.createElement("button");
        boton_editar.type = "button";
        boton_editar.textContent = "Editar";
        celdaAcciones.appendChild(boton_editar);

        boton_eliminar = document.createElement("button");
        boton_eliminar.type = "button";

        boton_eliminar.textContent = "Eliminar";
        celdaAcciones.appendChild(boton_eliminar);
        }
}

// 6. Ejecutar la función cuando la página termina de cargar
cargarPrecios();