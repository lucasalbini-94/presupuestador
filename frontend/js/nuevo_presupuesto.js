document.addEventListener('DOMContentLoaded', () => {
    // 1. Cargar los datos del cliente si viene pasado por URL (ej: ?id_cliente=3)
    const urlParams = new URLSearchParams(window.location.search);
    const idCliente = urlParams.get('id_cliente');

    if (idCliente) {
        fetch(`api.php?action=get_cliente&id=${idCliente}`)
            .then(res => res.json())
            .then(cliente => {
                if (cliente && !cliente.error) {
                    const inputNombre = document.getElementById('client-name');
                    const inputTelefono = document.getElementById('client-phone');
                    const inputEmail = document.getElementById('client-email');
                    const inputDireccion = document.getElementById('client-address');
                    const spanNumPresupuesto = document.getElementById('num-presupuesto');

                    const nombreCompleto = `${cliente.nombre || ''} ${cliente.apellido || ''}`.trim();

                    if (inputNombre) inputNombre.value = nombreCompleto;
                    if (inputTelefono) inputTelefono.value = cliente.telefono || '';
                    if (inputEmail) inputEmail.value = cliente.email || '';
                    if (inputDireccion) inputDireccion.value = cliente.direccion || '';

                    if (spanNumPresupuesto) {
                        spanNumPresupuesto.innerText = `Cliente N° ${cliente.id}`;
                    }
                }
            })
            .catch(err => console.error('Error al cargar los datos del cliente:', err));
    }

    const tablaTrabajos = document.getElementById('tabla-trabajos');
    const btnAddTrabajo = document.getElementById('btn-add-trabajo');
    const inputAjuste = document.getElementById('adjustment');
    const labelAjuste = document.getElementById('adjustment-val');
    const totalDisplay = document.getElementById('total-presupuesto');

    // 2. Recalcular Totales de la Cotización
    function calcularTotales() {
        let subtotalTrabajos = 0;
        if (!tablaTrabajos) return;

        const filas = tablaTrabajos.querySelectorAll('tr');
        filas.forEach(fila => {
            const precioInput = fila.querySelector('.input-precio');
            const cantInput = fila.querySelector('.input-cantidad');
            const subtotalTd = fila.querySelector('.celda-subtotal');

            const precio = parseFloat(precioInput?.value) || 0;
            const cant = parseInt(cantInput?.value) || 0;
            const subtotalRow = precio * cant;

            if (subtotalTd) subtotalTd.textContent = `$${subtotalRow.toFixed(2)}`;
            subtotalTrabajos += subtotalRow;
        });

        // Factor de Ajuste
        const porcentajeAjuste = inputAjuste ? (parseFloat(inputAjuste.value) / 100) : 1;
        if (labelAjuste && inputAjuste) labelAjuste.textContent = `${inputAjuste.value}%`;

        // Gastos Adicionales
        let totalGastos = 0;
        const itemsGastos = document.querySelectorAll('.expense-item');
        itemsGastos.forEach(item => {
            const chk = item.querySelector('.expense-check');
            if (chk && chk.checked) {
                const montoInput = item.querySelector('.input-monto');
                const diasInput = item.querySelector('.input-dias');
                const monto = parseFloat(montoInput?.value) || 0;
                const dias = diasInput ? (parseInt(diasInput.value) || 1) : 1;
                totalGastos += (monto * dias);
            }
        });

        const totalFinal = (subtotalTrabajos * porcentajeAjuste) + totalGastos;
        if (totalDisplay) totalDisplay.textContent = `Total $${totalFinal.toLocaleString('es-AR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }

    // Evento: Agregar nueva fila de trabajo
    if (btnAddTrabajo && tablaTrabajos) {
        btnAddTrabajo.addEventListener('click', (e) => {
            e.preventDefault();
            const nuevaFila = document.createElement('tr');
            nuevaFila.innerHTML = `
                <td>
                    <select class="input-table input-categoria">
                        <option value="" disabled selected>Seleccionar categoría ...</option>
                        <option value="Canalización">Canalización</option>
                        <option value="Cableado">Cableado</option>
                        <option value="Tableros">Tableros</option>
                        <option value="Colocacion artefactos">Colocación artefactos</option>
                        <option value="Boca Completa">Boca Completa</option>
                    </select>
                </td>
                <td>
                    <select class="input-table input-tipo">
                        <option value="" disabled selected>Seleccionar tipo ...</option>
                        <option value="Canalización embutida plastica">Canalización embutida plástica</option>
                        <option value="Canalización embutida Metal">Canalización embutida Metal</option>
                        <option value="De Aplique o empotrado">De Aplique o empotrado</option>
                    </select>
                </td>
                <td><input type="number" step="0.01" class="input-table input-precio" placeholder="$ 0,00"></td>
                <td><input type="number" class="input-table input-cantidad" placeholder="0"></td>
                <td class="text-bold celda-subtotal">$0.00</td>
                <td class="actions-cell">
                    <button type="button" class="action-btn text-danger btn-eliminar-fila"><i class="fa-regular fa-trash-can"></i></button>
                </td>
            `;
            tablaTrabajos.appendChild(nuevaFila);
        });
    }

    // Eventos de tabla
    if (tablaTrabajos) {
        tablaTrabajos.addEventListener('input', calcularTotales);
        tablaTrabajos.addEventListener('click', (e) => {
            if (e.target.closest('.btn-eliminar-fila')) {
                e.target.closest('tr').remove();
                calcularTotales();
            }
        });
    }

    // Habilitar / Deshabilitar Gastos Adicionales
    document.querySelectorAll('.expense-check').forEach(chk => {
        chk.addEventListener('change', (e) => {
            const container = e.target.closest('.expense-item');
            const inputs = container.querySelectorAll('input[type="number"]');
            inputs.forEach(inp => inp.disabled = !e.target.checked);
            calcularTotales();
        });
    });

    document.querySelectorAll('.additional-expenses-grid input[type="number"]').forEach(inp => {
        inp.addEventListener('input', calcularTotales);
    });

    if (inputAjuste) inputAjuste.addEventListener('input', calcularTotales);

    // 3. Función unificada para Guardar Presupuesto según el Estado
    async function guardarPresupuestoConEstado(idEstado) {
        const urlParams = new URLSearchParams(window.location.search);
        const idClienteUrl = urlParams.get('id_cliente');

        const payload = {
            id_cliente: idClienteUrl || null,
            cliente: {
                nombre: document.getElementById('client-name')?.value,
                telefono: document.getElementById('client-phone')?.value,
                email: document.getElementById('client-email')?.value,
                direccion: document.getElementById('client-address')?.value
            },
            ajuste_general: parseFloat(document.getElementById('adjustment')?.value || 100),
            total: parseFloat(document.getElementById('total-presupuesto')?.textContent.replace(/[^\d.]/g, '')) || 0,
            id_estado: idEstado, // 1 = Borrador, 2 = Enviado/Definitivo
            items: []
        };

        const tablaTrabajos = document.getElementById('tabla-trabajos');
        if (tablaTrabajos) {
            tablaTrabajos.querySelectorAll('tr').forEach(tr => {
                const cat = tr.querySelector('.input-categoria')?.value;
                const tipo = tr.querySelector('.input-tipo')?.value;
                const precio = parseFloat(tr.querySelector('.input-precio')?.value) || 0;
                const cant = parseInt(tr.querySelector('.input-cantidad')?.value) || 0;

                if (cat) {
                    payload.items.push({
                        categoria: cat,
                        tipo: tipo,
                        precio_unitario: precio,
                        cantidad: cant,
                        subtotal: precio * cant
                    });
                }
            });
        }

        try {
            const res = await fetch('api.php?action=guardar_presupuesto', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if (data.success) {
                const mensaje = idEstado === 1 
                    ? '¡Borrador guardado correctamente!' 
                    : '¡Presupuesto finalizado y enviado!';
                alert(mensaje);
                
                if (idEstado === 2) {
                    window.print(); // Solo abre el menú de impresión si es definitivo
                }

                window.location.href = 'cliente.html';
            } else {
                alert('Error al guardar: ' + (data.error || 'No se pudo procesar la solicitud'));
            }
        } catch (err) {
            console.error('Error de conexión al guardar presupuesto:', err);
            alert('Ocurrió un error al intentar guardar el presupuesto.');
        }
    }

    // 4. Asignar los eventos a tus dos botones HTML
    const btnBorrador = document.getElementById('btn-guardar-borrador');
    if (btnBorrador) {
        btnBorrador.addEventListener('click', () => guardarPresupuestoConEstado(1));
    }

    const btnFinalizar = document.getElementById('btn-finalizar-presupuesto');
    if (btnFinalizar) {
        btnFinalizar.addEventListener('click', () => guardarPresupuestoConEstado(2));
    }

    // Mandar por WhatsApp
    const btnEnviar = document.getElementById('btn-enviar-whatsapp');
    if (btnEnviar) {
        btnEnviar.addEventListener('click', () => {
            const nombre   = document.getElementById('client-name')?.value.trim() || 'cliente';
            const telefono = (document.getElementById('client-phone')?.value || '').replace(/\D/g, '');

            const mensaje = `Hola ${nombre}, te envío el presupuesto.`;
            window.open(`https://wa.me/${telefono}?text=${encodeURIComponent(mensaje)}`, '_blank');
        });
    }

    // Mandar por Email
    const btnEnviarMail = document.getElementById('btn-enviar-email');
    if (btnEnviarMail) {
        btnEnviarMail.addEventListener('click', () => {
            const nombre = document.getElementById('client-name')?.value.trim() || 'cliente';
            const email  = document.getElementById('client-email')?.value.trim() || '';

            const asunto = 'Presupuesto';
            const cuerpo = `Hola ${nombre}, te envío el presupuesto.`;
            const url = `mailto:${encodeURIComponent(email)}
                      + ?subject=${encodeURIComponent(asunto)}
                      + &body=${encodeURIComponent(cuerpo)}`;
            window.open(url, '_blank');
        });
    }
});
