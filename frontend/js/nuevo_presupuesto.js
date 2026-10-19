document.addEventListener('DOMContentLoaded', () => {
    // 1. Cargar los datos del cliente si viene pasado por URL (ej: ?id_cliente=3)
    const urlParams = new URLSearchParams(window.location.search);
    const idCliente = urlParams.get('id_cliente');
    const URL_API = '../api/presupuestos.php';

    if (idCliente) {
        fetch(`${URL_API}?action=get_cliente&id=${idCliente}`)
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

    function actualizarTrabajosDeFila(fila, reiniciarSeleccion = true) {
        const categoria = fila.querySelector('.select-categoria');
        const trabajo = fila.querySelector('.select-trabajo');
        const precio = fila.querySelector('.input-precio');
        if (!categoria || !trabajo || !precio) return;

        if (reiniciarSeleccion) {
            trabajo.value = '';
            precio.value = '';
        }
        trabajo.disabled = categoria.value === '';

        trabajo.querySelectorAll('option[data-categoria]').forEach(opcion => {
            const disponible = opcion.dataset.categoria === categoria.value;
            opcion.hidden = !disponible;
            opcion.disabled = !disponible;
        });
    }

    // Evento: Agregar una fila con las categorías y trabajos del catálogo.
    if (btnAddTrabajo && tablaTrabajos) {
        btnAddTrabajo.addEventListener('click', (e) => {
            e.preventDefault();
            const nuevaFila = tablaTrabajos.querySelector('tr').cloneNode(true);
            const categoria = nuevaFila.querySelector('.select-categoria');
            const trabajo = nuevaFila.querySelector('.select-trabajo');
            const precio = nuevaFila.querySelector('.input-precio');
            const cantidad = nuevaFila.querySelector('.input-cantidad');

            categoria.value = '';
            trabajo.value = '';
            trabajo.disabled = true;
            trabajo.querySelectorAll('option[data-categoria]').forEach(opcion => {
                opcion.hidden = false;
                opcion.disabled = false;
            });
            precio.value = '';
            cantidad.value = '1';
            nuevaFila.querySelector('.celda-subtotal').textContent = '$0.00';
            tablaTrabajos.appendChild(nuevaFila);
        });
    }

    // Eventos de tabla
    if (tablaTrabajos) {
        tablaTrabajos.addEventListener('input', calcularTotales);
        tablaTrabajos.addEventListener('change', (e) => {
            const fila = e.target.closest('tr');
            if (!fila) return;

            if (e.target.matches('.select-categoria')) {
                actualizarTrabajosDeFila(fila);
            } else if (e.target.matches('.select-trabajo')) {
                const seleccion = e.target.selectedOptions[0];
                const precio = fila.querySelector('.input-precio');
                precio.value = seleccion?.dataset.precio || '';
            }
            calcularTotales();
        });
        tablaTrabajos.addEventListener('click', (e) => {
            if (e.target.closest('.btn-eliminar-fila')) {
                e.target.closest('tr').remove();
                calcularTotales();
            }
        });
        tablaTrabajos.querySelectorAll('tr').forEach(fila => {
            actualizarTrabajosDeFila(fila, false);
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
    calcularTotales();

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
