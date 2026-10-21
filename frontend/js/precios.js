const API_PRECIOS = "../api/precios.php";
const API_CATEGORIAS = "../api/categorias.php";
const API_UNIDADES = "../api/unidades.php";

const agregarPrecio = document.getElementById("agregar_precio");
const ventanaPrecio = document.getElementById("ventana_precio");
const ventanaEditarPrecio = document.getElementById("ventana_editar_precio");
const tablaPrecios = document.getElementById("tabla_precios");
const formularioNuevo = ventanaPrecio.querySelector("form");
const formularioEditar = ventanaEditarPrecio.querySelector("form");
const selectorCategoriaNuevo = formularioNuevo.querySelector('[name="categoria"]');
const selectorUnidadNueva = formularioNuevo.querySelector('[name="unidad"]');
const selectorCategoriaEditar = formularioEditar.querySelector('[name="categoria"]');
const selectorUnidadEditar = formularioEditar.querySelector('[name="unidad"]');
let filaParaEditar = null;

async function leerRespuesta(respuesta) {
    const datos = await respuesta.json();
    if (!respuesta.ok) {
        throw new Error(datos.error || "No se pudo completar la solicitud.");
    }
    return datos;
}

async function cargarCatalogo(url, selectores) {
    const respuesta = await fetch(url);
    const opciones = await leerRespuesta(respuesta);

    for (const selector of selectores) {
        selector.replaceChildren(new Option("Seleccioná una opción", ""));
        for (const opcion of opciones) {
            selector.add(new Option(opcion.descripcion, opcion.id));
        }
    }
}

function agregarBotones(fila) {
    const celdaAcciones = fila.insertCell();
    const editar = document.createElement("button");
    editar.type = "button";
    editar.textContent = "Editar";
    celdaAcciones.appendChild(editar);

    const eliminar = document.createElement("button");
    eliminar.type = "button";
    eliminar.textContent = "Eliminar";
    celdaAcciones.appendChild(eliminar);
}

function crearFila(precio) {
    const fila = tablaPrecios.tBodies[0].insertRow();
    fila.dataset.id = precio.id_trabajo;
    fila.dataset.idCategoria = precio.id_categoria;
    fila.dataset.idUnidad = precio.id_unidad;
    fila.insertCell().textContent = precio.categoria;
    fila.insertCell().textContent = precio.trabajo;
    fila.insertCell().textContent = precio.unidad;
    fila.insertCell().textContent = `$ ${precio.precio}`;
    agregarBotones(fila);
    return fila;
}

async function cargarPrecios() {
    const respuesta = await fetch(API_PRECIOS);
    const precios = await leerRespuesta(respuesta);
    tablaPrecios.tBodies[0].replaceChildren();
    for (const precio of precios) {
        crearFila(precio);
    }
}

agregarPrecio.addEventListener("click", () => ventanaPrecio.showModal());

for (const dialogo of [ventanaPrecio, ventanaEditarPrecio]) {
    dialogo.querySelector('[type="button"]').addEventListener("click", () => {
        dialogo.close();
        dialogo.querySelector("form").reset();
    });
}

formularioNuevo.addEventListener("submit", async (evento) => {
    evento.preventDefault();
    const formData = new FormData(formularioNuevo);

    try {
        const respuesta = await fetch(API_PRECIOS, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                id_categoria: formData.get("categoria"),
                id_unidad: formData.get("unidad"),
                trabajo: formData.get("trabajo"),
                precio: formData.get("precio")
            })
        });
        await leerRespuesta(respuesta);
        await cargarPrecios();
        formularioNuevo.reset();
        ventanaPrecio.close();
    } catch (error) {
        alert(error.message);
    }
});

tablaPrecios.addEventListener("click", async (evento) => {
    const boton = evento.target.closest("button");
    if (!boton) {
        return;
    }

    const fila = boton.closest("tr");
    if (boton.textContent === "Editar") {
        filaParaEditar = fila;
        selectorCategoriaEditar.value = fila.dataset.idCategoria;
        selectorUnidadEditar.value = fila.dataset.idUnidad;
        formularioEditar.elements.trabajo.value = fila.cells[1].textContent;
        formularioEditar.elements.precio.value = fila.cells[3].textContent.replace(/^\$\s*/, "");
        ventanaEditarPrecio.showModal();
        return;
    }

    if (boton.textContent === "Eliminar" && confirm("¿Seguro que querés eliminar este precio?")) {
        try {
            const respuesta = await fetch(API_PRECIOS, {
                method: "DELETE",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ id_trabajo: fila.dataset.id })
            });
            await leerRespuesta(respuesta);
            fila.remove();
        } catch (error) {
            alert(error.message);
        }
    }
});

formularioEditar.addEventListener("submit", async (evento) => {
    evento.preventDefault();
    const formData = new FormData(formularioEditar);

    try {
        const respuesta = await fetch(API_PRECIOS, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                id_trabajo: filaParaEditar.dataset.id,
                id_categoria: formData.get("categoria"),
                id_unidad: formData.get("unidad"),
                trabajo: formData.get("trabajo"),
                precio: formData.get("precio")
            })
        });
        await leerRespuesta(respuesta);
        await cargarPrecios();
        formularioEditar.reset();
        ventanaEditarPrecio.close();
        filaParaEditar = null;
    } catch (error) {
        alert(error.message);
    }
});

Promise.all([
    cargarCatalogo(API_CATEGORIAS, [selectorCategoriaNuevo, selectorCategoriaEditar]),
    cargarCatalogo(API_UNIDADES, [selectorUnidadNueva, selectorUnidadEditar]),
    cargarPrecios()
]).catch((error) => {
    console.error("No se pudieron cargar los datos de precios:", error);
    alert(error.message);
});
