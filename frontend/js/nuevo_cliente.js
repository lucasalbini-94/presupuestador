document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM cargado: Iniciando listado de clientes...");
    cargarClientes();

    const inputBuscar = document.getElementById('search-input');
    if (inputBuscar) {
        inputBuscar.addEventListener('input', (e) => {
            cargarClientes(e.target.value);
        });
    }
});

// 1. Cargar y listar los clientes en la tabla
function cargarClientes(busqueda = '') {
    let url = 'api.php?action=get_clientes';
    if (busqueda) {
        url += `&search=${encodeURIComponent(busqueda)}`;
    }

    fetch(url)
        .then(res => res.json())
        .then(response => {
            console.log("Datos de clientes recibidos:", response);
            const clientes = Array.isArray(response) ? response : (response.data || []);
            const tbody = document.getElementById('tabla-clientes');
            
            if (!tbody) {
                console.error("No se encontró el elemento #tabla-clientes en el HTML");
                return;
            }

            tbody.innerHTML = '';

            if (clientes.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; padding: 2rem; color: #666;">No se encontraron clientes.</td></tr>`;
                return;
            }

            clientes.forEach(c => {
                const tr = document.createElement('tr');
                const nombreCompleto = c.nombre || `${c.nombre || ''} ${c.apellido || ''}`.trim();
                
                tr.innerHTML = `
                    <td><strong>${nombreCompleto}</strong></td>
                    <td>${c.telefono || '-'}</td>
                    <td>${c.localidad || c.direccion || '-'}</td>
                    <td class="actions-cell">
                      <div class="row-actions">
                        <button type="button" class="action-btn btn-ver-ficha" data-id="${c.id}" title="Ver ficha" aria-label="Ver ficha">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <button type="button" class="action-btn text-danger btn-eliminar-cliente" data-id="${c.id}" title="Eliminar cliente" aria-label="Eliminar cliente">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                      </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            // Evento para eliminar cliente
            document.querySelectorAll('.btn-eliminar-cliente').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const idCliente = btn.getAttribute('data-id');
                    
                    if (confirm('¿Estás seguro de que deseas eliminar este cliente y todos sus presupuestos asociados?')) {
                        try {
                            const res = await fetch('api.php?action=eliminar_cliente', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ id_cliente: idCliente })
                            });
                            const data = await res.json();
                            
                            if (data.success) {
                                alert('Cliente eliminado con éxito.');
                                cargarClientes(); // Recarga la tabla automáticamente
                                
                                // Limpiar la tarjeta de detalle si justo era el cliente seleccionado
                                const nombreElem = document.getElementById('detalle-cliente-nombre');
                                if (nombreElem) nombreElem.innerText = 'Seleccione un cliente';
                                const contenedorPresupuestos = document.getElementById('detalle-presupuestos');
                                if (contenedorPresupuestos) contenedorPresupuestos.innerHTML = '<h3>Presupuestos anteriores</h3>';
                            } else {
                                alert('Error al eliminar: ' + (data.error || 'No se pudo procesar'));
                            }
                        } catch (err) {
                            console.error('Error de conexión:', err);
                        }
                    }
                });
            });

            // Agregar el evento de clic directamente a cada botón del ojo generado
            const botonesOjo = document.querySelectorAll('.btn-ver-ficha');
            console.log(`Se encontraron ${botonesOjo.length} botones de ojo para asociar.`);
            
            botonesOjo.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const idCliente = btn.getAttribute('data-id');
                    console.log("¡Hiciste clic en el cliente con ID:", idCliente);
                    seleccionarCliente(idCliente);
                });
            });
        })
        .catch(err => console.error('Error al cargar clientes:', err));
}

// 2. Función para obtener la ficha y actualizar la vista
function seleccionarCliente(id) {
    console.log("Consultando detalle del cliente ID:", id);
    fetch(`api.php?action=get_cliente&id=${id}`)
        .then(res => res.json())
        .then(cliente => {
            if (!cliente || cliente.error) {
                alert('No se pudo cargar la información del cliente.');
                return;
            }

            window.idClienteActual = cliente.id;

            // Nombre en la ficha
            const nombreElem = document.getElementById('detalle-cliente-nombre');
            if (nombreElem) {
                const nombreCompleto = `${cliente.nombre || ''} ${cliente.apellido || ''}`.trim();
                nombreElem.innerText = nombreCompleto;
            }

            // Observaciones
            const obsElem = document.getElementById('detalle-cliente-obs');
            if (obsElem) {
                obsElem.innerText = cliente.observaciones || cliente.direccion || '-';
            }

            // Vincular botón de nuevo presupuesto
            const btnNuevoPresupuesto = document.querySelector('.section-header-flex a.btn-outline');
            if (btnNuevoPresupuesto) {
                btnNuevoPresupuesto.href = `Nuevo Presupuesto.html?id_cliente=${cliente.id}`;
            }

            // Listar presupuestos anteriores
            const contenedorPresupuestos = document.getElementById('detalle-presupuestos');
            if (contenedorPresupuestos) {
                let html = '<h3>Presupuestos anteriores</h3>';
                if (cliente.presupuestos && cliente.presupuestos.length > 0) {
                    cliente.presupuestos.forEach(p => {
                        let badgeBg = '#e0e0e0';
                        let badgeColor = '#616161';

                        if (p.estado === 'Aceptado') {
                            badgeBg = '#e8f5e9';
                            badgeColor = '#2e7d32';
                        } else if (p.estado === 'Rechazado') {
                            badgeBg = '#ffebee';
                            badgeColor = '#c62828';
                        } else if (p.estado === 'Enviado') {
                            badgeBg = '#e3f2fd';
                            badgeColor = '#1976d2';
                        }

                        // Número correlativo real (o fallback si es un registro antiguo)
                        const numeroMostrar = p.codigo || `N° ${String(p.id).padStart(4, '0')}`;

                        html += `
                            <div style="padding: 0.8rem 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                                <div style="cursor: pointer; flex-grow: 1;" onclick="verDetallePresupuesto(${p.id})" title="Ver detalle">
                                    <strong>${numeroMostrar}</strong> - $${Number(p.monto).toLocaleString()}
                                    <span style="background: ${badgeBg}; color: ${badgeColor}; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; margin-left: 6px;">${p.estado}</span>
                                </div>
                                <div style="display: flex; gap: 6px; align-items: center;">
                                    ${p.estado !== 'Aceptado' && p.estado !== 'Rechazado' ? `
                                        <button type="button" onclick="cambiarEstadoPresupuesto(event, ${p.id}, 3)" style="background-color: #2e7d32; color: white; border: none; border-radius: 4px; padding: 0.3rem 0.6rem; font-size: 0.8rem; cursor: pointer;" title="Aceptar">
                                            <i class="fa-solid fa-check"></i> Aceptar
                                        </button>
                                        <button type="button" onclick="cambiarEstadoPresupuesto(event, ${p.id}, 4)" style="background-color: #c62828; color: white; border: none; border-radius: 4px; padding: 0.3rem 0.6rem; font-size: 0.8rem; cursor: pointer;" title="Rechazar">
                                            <i class="fa-solid fa-xmark"></i> Rechazar
                                        </button>
                                    ` : `
                                        <span style="color: ${badgeColor}; font-size: 0.8rem; font-weight: bold;">
                                            <i class="fa-solid ${p.estado === 'Aceptado' ? 'fa-circle-check' : 'fa-circle-xmark'}"></i> ${p.estado}
                                        </span>
                                    `}
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html += `<p style="color: #666; font-size: 0.9rem; margin-top: 0.5rem;">Este cliente no registra presupuestos anteriores.</p>`;
                }
                contenedorPresupuestos.innerHTML = html;
            }
        })
        .catch(err => console.error('Error al obtener detalle del cliente:', err));
}

// 3. Función unificada para Aceptar (id_estado = 3) o Rechazar (id_estado = 4)
async function cambiarEstadoPresupuesto(event, idPresupuesto, idEstado) {
    event.stopPropagation();
    const accionTexto = idEstado === 3 ? 'ACEPTAR' : 'RECHAZAR';
    if (!confirm(`¿Deseas marcar este presupuesto como ${accionTexto}?`)) return;

    try {
        const res = await fetch('api.php?action=cambiar_estado_presupuesto', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_presupuesto: idPresupuesto, id_estado: idEstado })
        });
        const data = await res.json();
        if (data.success) {
            alert(`¡Presupuesto ${idEstado === 3 ? 'Aceptado' : 'Rechazado'} correctamente!`);
            if (window.idClienteActual) {
                seleccionarCliente(window.idClienteActual);
            }
        } else {
            alert('Error al actualizar: ' + (data.error || 'No se pudo procesar'));
        }
    } catch (err) {
        console.error('Error al cambiar estado:', err);
    }
}

// 4. Función para redirigir o ver el detalle del presupuesto seleccionado
function verDetallePresupuesto(idPresupuesto) {
    // Te redirige a la pantalla de presupuesto pasando el ID para ver o cargar sus datos
    window.location.href = `Nuevo Presupuesto.html?id_presupuesto=${idPresupuesto}`;
}