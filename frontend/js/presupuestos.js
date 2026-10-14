const URL_API = "../api/presupuestos.php";

document.addEventListener("DOMContentLoaded", () => {
    const cuerpoTabla = document.querySelector("#cuerpoTabla");
    if (!cuerpoTabla) return;

    // Detectamos si es index o la lista completa
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
    const columnas = incluirAcciones ? 5 : 4;
    tbody.innerHTML = `<tr><td colspan="${columnas}">Cargando...</td></tr>`;

    try {
        const respuesta = await fetch(url);
        if (!respuesta.ok) {
            throw new Error(`Error ${respuesta.status}: No se pudo obtener la información`);
        }
        const presupuestos = await respuesta.json();
        renderizarTabla(presupuestos, incluirAcciones);
    } catch (error) {
        console.error(error);
        tbody.innerHTML = `<tr><td colspan="${columnas}" style="color: red;">Error al cargar los datos.</td></tr>`;
    }
}

function renderizarTabla(presupuestos, incluirAcciones) {
    const tbody = document.querySelector("#cuerpoTabla");
    const columnas = incluirAcciones ? 5 : 4;
    tbody.innerHTML = "";

    if (presupuestos.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${columnas}">No hay presupuestos registrados.</td></tr>`;
        return;
    }

    presupuestos.forEach(p => {
        const tr = document.createElement("tr");

        let claseEstado = "estado-borrador";
        const estado = p.estado ? p.estado.toLowerCase() : "";
        if (estado === "enviado") claseEstado = "estado-enviado";
        if (estado === "aceptado") claseEstado = "estado-aceptado";

        const columnasBase = `
            <td>${p.nro}</td>
            <td>${p.cliente}</td>
            <td>$${Number(p.total).toLocaleString("es-AR")}</td>
            <td><span class="estado ${claseEstado}">${p.estado}</span></td>
        `;

        const columnaAcciones = incluirAcciones ? `
            <td>
                <div class="action-links">
                    <a href="ver_presupuesto.php?id=${p.id}" class="action-link"><i class="fa-regular fa-eye"></i> Ver</a>
                    <a href="editar_presupuesto.php?id=${p.id}" class="action-link"><i class="fa-solid fa-pencil"></i> Editar</a>
                    <a href="pdf_presupuesto.php?id=${p.id}" class="action-link"><i class="fa-regular fa-file-pdf"></i></a>
                </div>
            </td>
        ` : "";

        tr.innerHTML = columnasBase + columnaAcciones;
        tbody.appendChild(tr);
    });
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