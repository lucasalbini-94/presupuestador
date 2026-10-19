const URL_API = "../api/presupuestos.php";
let solicitudActual;
const formatoMoneda = new Intl.NumberFormat("es-AR", {
    style: "currency",
    currency: "ARS",
    maximumFractionDigits: 2
});

document.addEventListener("DOMContentLoaded", () => {
    const cuerpoTabla = document.querySelector("#cuerpoTabla");
    if (!cuerpoTabla) return;

    const esInicio = document.body.dataset.page === "index";

    if (esInicio) {
        cargarPresupuestos(`${URL_API}?vista=ultimos&limite=3`, false);
    } else {
        cargarPresupuestos(`${URL_API}`, true);
        configurarFiltros();
    }
});

async function cargarPresupuestos(url, incluirAcciones = false) {
    const tbody = document.querySelector("#cuerpoTabla");
    if (!tbody) return;

    const columnas = incluirAcciones ? 5 : 4;
    solicitudActual?.abort();
    solicitudActual = new AbortController();
    mostrarMensaje(tbody, columnas, "Cargando...");

    try {
        const respuesta = await fetch(url, { signal: solicitudActual.signal });
        if (!respuesta.ok) {
            throw new Error(`Error ${respuesta.status}: No se pudo obtener la información`);
        }
        const presupuestos = await respuesta.json();
        if (!Array.isArray(presupuestos)) {
            throw new Error("La respuesta del servidor no tiene el formato esperado.");
        }
        renderizarTabla(presupuestos, incluirAcciones);
    } catch (error) {
        if (error.name === "AbortError") return;
        console.error(error);
        mostrarMensaje(tbody, columnas, "Error al cargar los datos.", "error");
    }
}

function mostrarMensaje(tbody, columnas, mensaje, tipo = "") {
    const fila = document.createElement("tr");
    const celda = document.createElement("td");
    celda.colSpan = columnas;
    celda.textContent = mensaje;
    if (tipo === "error") celda.className = "error-carga";
    fila.appendChild(celda);
    tbody.replaceChildren(fila);
}

function renderizarTabla(presupuestos, incluirAcciones) {
    const tbody = document.querySelector("#cuerpoTabla");
    const columnas = incluirAcciones ? 5 : 4;

    if (presupuestos.length === 0) {
        mostrarMensaje(tbody, columnas, "No hay presupuestos registrados.");
        return;
    }

    const filas = document.createDocumentFragment();
    presupuestos.forEach(p => {
        const tr = document.createElement("tr");

        tr.appendChild(crearCelda(p.nro));
        tr.appendChild(crearCelda(p.cliente));
        tr.appendChild(crearCelda(formatoMoneda.format(Number(p.total))));

        const celdaEstado = document.createElement("td");
        const etiquetaEstado = document.createElement("span");
        const estado = String(p.estado || "");
        const estadoNormalizado = estado.toLowerCase();
        const clasesPorEstado = {
            enviado: "badge-active estado-enviado",
            pendiente: "badge-active estado-enviado",
            aceptado: "badge-active estado-aceptado",
            aprobado: "badge-active estado-aceptado",
            rechazado: "badge-inactive estado-rechazado",
            facturado: "badge-active estado-facturado",
            borrador: "badge-inactive estado-borrador"
        };
        etiquetaEstado.className = `badge ${clasesPorEstado[estadoNormalizado] || "badge-inactive estado-borrador"}`;
        etiquetaEstado.textContent = estado;
        celdaEstado.appendChild(etiquetaEstado);
        tr.appendChild(celdaEstado);

        if (incluirAcciones) {
            const celdaAcciones = document.createElement("td");
            const acciones = document.createElement("div");
            acciones.className = "action-links";
            acciones.appendChild(crearEnlaceAccion(p.id, "Editar", "fa-solid fa-pencil"));
            celdaAcciones.appendChild(acciones);
            tr.appendChild(celdaAcciones);
        }

        filas.appendChild(tr);
    });

    tbody.replaceChildren(filas);
}

function crearCelda(valor) {
    const celda = document.createElement("td");
    celda.textContent = valor ?? "";
    return celda;
}

function crearEnlaceAccion(id, texto, claseIcono) {
    const enlace = document.createElement("a");
    enlace.href = `nuevo_presupuesto.php?id=${encodeURIComponent(id)}`;
    enlace.className = "action-link";

    const icono = document.createElement("i");
    icono.className = claseIcono;
    enlace.append(icono, document.createTextNode(` ${texto}`));
    return enlace;
}

function configurarFiltros() {
    const inputBuscar = document.querySelector("#buscar");
    const selectEstado = document.querySelector("#estado");

    function filtrar() {
        const buscar = encodeURIComponent(inputBuscar ? inputBuscar.value.trim() : "");
        const estado = selectEstado ? selectEstado.value : "todos";
        cargarPresupuestos(`${URL_API}?estado=${estado}&buscar=${buscar}`, true);
    }

    if (inputBuscar) inputBuscar.addEventListener("input", filtrar);
    if (selectEstado) selectEstado.addEventListener("change", filtrar);
}