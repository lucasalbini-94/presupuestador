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

// HACER CLICK EN AGREGAR_PRECIO

agregar_precio.addEventListener("click", function(){
    // SE ABRE LA VENTANA_PRECIO PARA INGRESAR LOS DATOS
    ventana_precio.showModal();
});

// SE APRETA EN GUARDAR EN LA VENTANA_PRECIO Y GUARDA LOS DATOS EN LA TABLA DE PRECIOS

    ventana_precio.addEventListener("submit", function () {
    //  Carga los valores que puso el usuario en variables
    const categoria = ventana_precio.querySelector("#categoria").value;
    const trabajo = ventana_precio.querySelector("#trabajo").value;
    const precio = ventana_precio.querySelector("#precio").value;
    
    // Se inserta una fila
    const fila = tabla_precios.insertRow();
    // Se le agregan los valores que se cargaron en las variables
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

    // ----------- hasta aca todo funciona ------------// 

  // FUNCIONES PARA EDITAR Y ELIMINAR UNA FILA

  // HACER CLICK EN EDITAR

  // Reemplazá todo el bloque de boton_editar.addEventListener por esto:
  tabla_precios.addEventListener("click", function (e) {
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
        if (confirm("¿Seguro que querés eliminar este precio?")) {
        e.target.closest("tr").remove(); }
    }
});

        // SI APRETA EN GUARDAR
        ventana_editar_precio.addEventListener("submit", function () {
        // Se cargan los datos que hay en la ventana de edicion a variables
        const categoria = ventana_editar_precio.querySelector("#categoria").value;
        const trabajo = ventana_editar_precio.querySelector("#trabajo").value;
        const precio = ventana_editar_precio.querySelector("#precio").value;
        
        // Se guardan los nuevos datos
        fila_para_editar.cells[0].textContent = categoria;
        // Si escribio algo cambio por lo que escribio
        if (trabajo != ""){
        fila_para_editar.cells[1].textContent = trabajo;}
        // Si no escribio nada se usa el placeholder que tiene el dato anterior
        // sirve tanto si lo borro o no, si esta vacio tambien usa esto
        else{
        fila_para_editar.cells[1].textContent = trabajo_editar.placeholder;
        }
        // Repito la misma logica con el precio
        if (precio != ""){
        fila_para_editar.cells[2].textContent = `$ ${precio}`;}
        else{
        fila_para_editar.cells[2].textContent = precio_editar.placeholder;
        }

        // Se resetean los datos para la proxima vez
        ventana_editar_precio.querySelector("form").reset();
        });