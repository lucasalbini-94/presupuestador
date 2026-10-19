const API_URL = '../api/clientes.php';
let clientes = [];
let busquedaActual = '';

document.addEventListener('DOMContentLoaded', () => {
    cargarClientes();

    const inputBuscar = document.getElementById('search-input');
    if (inputBuscar) {
        inputBuscar.addEventListener('input', (e) => {
            busquedaActual = e.target.value.trim().toLocaleLowerCase();
            renderizarClientes();
        });
    }

    const tbody = document.getElementById('tabla-clientes');
    if (tbody) {
        tbody.addEventListener('click', async (e) => {
            const botonFicha = e.target.closest('.btn-ver-ficha');
            if (botonFicha) {
                seleccionarCliente(botonFicha.dataset.id);
                return;
            }

            const botonEliminar = e.target.closest('.btn-eliminar-cliente');
            if (botonEliminar && confirm('¿Estás seguro de que deseas eliminar este cliente y todos sus presupuestos asociados?')) {
                try {
                    const res = await fetch('api.php?action=eliminar_cliente', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id_cliente: botonEliminar.dataset.id })
                    });
                    const data = await res.json();
                    if (!res.ok || !data.success) {
                        throw new Error(data.error || 'No se pudo eliminar el cliente.');
                    }

                    clientes = clientes.filter(cliente => String(cliente.id) !== botonEliminar.dataset.id);
                    renderizarClientes();
                    const nombreElem = document.getElementById('detalle-cliente-nombre');
                    if (nombreElem && String(window.idClienteActual) === botonEliminar.dataset.id) {
                        nombreElem.textContent = 'Seleccione un cliente';
                        document.getElementById('detalle-cliente-info')?.replaceChildren();
                        document.getElementById('detalle-cliente-obs').textContent = '-';
                        const contenedorPresupuestos = document.getElementById('detalle-presupuestos');
                        if (contenedorPresupuestos) {
                            const titulo = document.createElement('h3');
                            titulo.textContent = 'Presupuestos anteriores';
                            contenedorPresupuestos.replaceChildren(titulo);
                        }
                        window.idClienteActual = null;
                    }
                } catch (error) {
                    console.error('Error al eliminar cliente:', error);
                    alert(error.message || 'No se pudo eliminar el cliente.');
                }
            }
        });
    }
});

// 1. Cargar los clientes una vez y mantenerlos en memoria para la tabla y la ficha.
function cargarClientes() {
    fetch(API_URL)
        .then(res => res.json())
        .then(response => {
            if (!Array.isArray(response)) {
                throw new Error(response.error || 'La respuesta de clientes no es válida.');
            }
            clientes = response;
            renderizarClientes();
        })
        .catch(err => {
            console.error('Error al cargar clientes:', err);
            const tbody = document.getElementById('tabla-clientes');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="4">No se pudieron cargar los clientes.</td></tr>';
            }
        });
}

function renderizarClientes() {
    const tbody = document.getElementById('tabla-clientes');
    if (!tbody) return;

    const clientesFiltrados = clientes.filter(cliente => {
        const textoCliente = [
            cliente.nombre,
            cliente.apellido,
            cliente.telefono,
            cliente.email,
            cliente.direccion
        ].filter(Boolean).join(' ').toLocaleLowerCase();
        return textoCliente.includes(busquedaActual);
    });

    tbody.replaceChildren();
    if (clientesFiltrados.length === 0) {
        const fila = document.createElement('tr');
        const celda = document.createElement('td');
        celda.colSpan = 4;
        celda.textContent = 'No se encontraron clientes.';
        fila.appendChild(celda);
        tbody.appendChild(fila);
        return;
    }

    clientesFiltrados.forEach(cliente => {
        const fila = document.createElement('tr');
        const nombre = document.createElement('td');
        const nombreNegrita = document.createElement('strong');
        nombreNegrita.textContent = `${cliente.nombre || ''} ${cliente.apellido || ''}`.trim();
        nombre.appendChild(nombreNegrita);

        const telefono = document.createElement('td');
        telefono.textContent = cliente.telefono || '-';
        const direccion = document.createElement('td');
        direccion.textContent = cliente.direccion || '-';

        const acciones = document.createElement('td');
        acciones.className = 'actions-cell';
        const contenedorAcciones = document.createElement('div');
        contenedorAcciones.className = 'row-actions';
        const botonFicha = document.createElement('button');
        botonFicha.type = 'button';
        botonFicha.className = 'action-btn btn-ver-ficha';
        botonFicha.dataset.id = cliente.id;
        botonFicha.title = 'Ver ficha';
        botonFicha.setAttribute('aria-label', 'Ver ficha');
        const iconoFicha = document.createElement('i');
        iconoFicha.className = 'fa-solid fa-eye';
        botonFicha.appendChild(iconoFicha);

        const botonEliminar = document.createElement('button');
        botonEliminar.type = 'button';
        botonEliminar.className = 'action-btn text-danger btn-eliminar-cliente';
        botonEliminar.dataset.id = cliente.id;
        botonEliminar.title = 'Eliminar cliente';
        botonEliminar.setAttribute('aria-label', 'Eliminar cliente');
        const iconoEliminar = document.createElement('i');
        iconoEliminar.className = 'fa-regular fa-trash-can';
        botonEliminar.appendChild(iconoEliminar);

        contenedorAcciones.append(botonFicha, botonEliminar);
        acciones.appendChild(contenedorAcciones);
        fila.append(nombre, telefono, direccion, acciones);
        tbody.appendChild(fila);
    });
}

// 2. Mostrar en la ficha los datos del cliente ya cargados en memoria.
function seleccionarCliente(id) {
    const cliente = clientes.find(item => String(item.id) === String(id));
    if (!cliente) return;

    window.idClienteActual = cliente.id;

    const nombreCompleto = `${cliente.nombre || ''} ${cliente.apellido || ''}`.trim();
    const nombreElem = document.getElementById('detalle-cliente-nombre');
    if (nombreElem) nombreElem.textContent = nombreCompleto || 'Cliente';

    const info = document.getElementById('detalle-cliente-info');
    if (info) {
        info.replaceChildren();
        [
            ['Teléfono', cliente.telefono],
            ['Email', cliente.email],
            ['Dirección', cliente.direccion]
        ].forEach(([etiqueta, valor]) => {
            const grupo = document.createElement('div');
            grupo.className = 'form-group';
            const label = document.createElement('label');
            label.textContent = etiqueta;
            const dato = document.createElement('p');
            dato.className = 'text-bold';
            dato.textContent = valor || '-';
            grupo.append(label, dato);
            info.appendChild(grupo);
        });
    }

    const obsElem = document.getElementById('detalle-cliente-obs');
    if (obsElem) obsElem.textContent = cliente.observaciones || '-';

    const btnNuevoPresupuesto = document.querySelector('.section-header-flex a.btn-outline');
    if (btnNuevoPresupuesto) {
        btnNuevoPresupuesto.href = `nuevo_presupuesto.php?id_cliente=${encodeURIComponent(cliente.id)}`;
    }

    const contenedorPresupuestos = document.getElementById('detalle-presupuestos');
    if (contenedorPresupuestos) {
        const titulo = document.createElement('h3');
        titulo.textContent = 'Presupuestos anteriores';
        const mensaje = document.createElement('p');
        mensaje.textContent = 'Cargando presupuestos...';
        contenedorPresupuestos.replaceChildren(titulo, mensaje);
        cargarPresupuestosCliente(cliente.id, contenedorPresupuestos);
    }
}

async function cargarPresupuestosCliente(idCliente, contenedor) {
    try {
        const respuesta = await fetch(`../api/presupuestos.php?id_cliente=${encodeURIComponent(idCliente)}`);
        const presupuestos = await respuesta.json();
        if (!respuesta.ok || !Array.isArray(presupuestos)) {
            throw new Error(presupuestos.error || 'No se pudieron cargar los presupuestos.');
        }

        if (String(window.idClienteActual) !== String(idCliente)) return;

        const titulo = document.createElement('h3');
        titulo.textContent = 'Presupuestos anteriores';
        contenedor.replaceChildren(titulo);

        if (presupuestos.length === 0) {
            const mensaje = document.createElement('p');
            mensaje.textContent = 'Este cliente no registra presupuestos anteriores.';
            contenedor.appendChild(mensaje);
            return;
        }

        presupuestos.forEach(presupuesto => {
            const fila = document.createElement('div');
            fila.style.cssText = 'padding: 0.8rem 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;';

            const enlace = document.createElement('a');
            enlace.href = `nuevo_presupuesto.php?id=${encodeURIComponent(presupuesto.id)}`;
            enlace.style.cssText = 'flex-grow: 1; color: inherit; text-decoration: none;';

            const numero = document.createElement('strong');
            numero.textContent = presupuesto.nro || `N° ${String(presupuesto.id).padStart(4, '0')}`;
            const detalle = document.createTextNode(` - ${formatearMonto(presupuesto.total)} `);
            const estado = document.createElement('span');
            estado.className = 'badge';
            estado.textContent = presupuesto.estado || '';

            enlace.append(numero, detalle, estado);
            fila.appendChild(enlace);
            contenedor.appendChild(fila);
        });
    } catch (error) {
        console.error('Error al cargar presupuestos del cliente:', error);
        if (String(window.idClienteActual) !== String(idCliente)) return;
        const titulo = document.createElement('h3');
        titulo.textContent = 'Presupuestos anteriores';
        const mensaje = document.createElement('p');
        mensaje.textContent = 'No se pudieron cargar los presupuestos.';
        contenedor.replaceChildren(titulo, mensaje);
    }
}

function formatearMonto(monto) {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS',
        maximumFractionDigits: 2
    }).format(Number(monto) || 0);
}
