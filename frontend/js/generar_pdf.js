function descargarPresupuestoPDF() {
    // Verificar que jsPDF esté disponible
    if (!window.jspdf || !window.jspdf.jsPDF) {
        console.error("La librería jsPDF no está cargada correctamente.");
        return;
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // 1. Obtener datos del cliente del formulario
    const nombreCliente = document.getElementById('client-name')?.value || 'Cliente General';
    const telefonoCliente = document.getElementById('client-phone')?.value || '-';
    const emailCliente = document.getElementById('client-email')?.value || '-';
    const direccionCliente = document.getElementById('client-address')?.value || '-';
    const numPresupuesto = document.getElementById('num-presupuesto')?.textContent || 'Presupuesto';
    const fechaActual = new Date().toLocaleDateString('es-AR');

    // 2. Encabezado del Documento
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(18);
    doc.setTextColor(33, 37, 41);
    doc.text("PRESUPUESTO DE TRABAJO", 14, 20);

    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.setTextColor(100);
    doc.text(`${numPresupuesto}`, 14, 27);
    doc.text(`Fecha: ${fechaActual}`, 14, 32);

    // Recuadro de datos del cliente
    doc.setDrawColor(220, 224, 230);
    doc.setFillColor(248, 249, 250);
    doc.roundedRect(14, 37, 182, 26, 3, 3, 'FD');

    doc.setFont('helvetica', 'bold');
    doc.setTextColor(33, 37, 41);
    doc.text("Datos del Cliente:", 18, 44);

    doc.setFont('helvetica', 'normal');
    doc.text(`Nombre: ${nombreCliente}`, 18, 51);
    doc.text(`Teléfono: ${telefonoCliente}`, 18, 57);
    doc.text(`Email: ${emailCliente}`, 100, 51);
    doc.text(`Dirección: ${direccionCliente}`, 100, 57);

    // 3. Extraer las filas de la tabla de trabajos
    const filasTabla = [];
    const tablaTrabajos = document.getElementById('tabla-trabajos');

    if (tablaTrabajos) {
        tablaTrabajos.querySelectorAll('tr').forEach(tr => {
            const cat = tr.querySelector('.input-categoria')?.value || tr.querySelector('.select-trabajo option:checked')?.text || '';
            const tipo = tr.querySelector('.input-tipo')?.value || '';
            
            const descripcion = tipo ? `${cat} - ${tipo}` : cat;
            
            const precio = parseFloat(tr.querySelector('.input-precio')?.value) || 0;
            const cantidad = parseInt(tr.querySelector('.input-cantidad')?.value) || 0;
            const subtotal = precio * cantidad;

            if (descripcion && cantidad > 0) {
                filasTabla.push([
                    descripcion,
                    cantidad,
                    `$ ${precio.toLocaleString('es-AR', { minimumFractionDigits: 2 })}`,
                    `$ ${subtotal.toLocaleString('es-AR', { minimumFractionDigits: 2 })}`
                ]);
            }
        });
    }

    // NUEVO: Extraer los Gastos Adicionales seleccionados (Viáticos, Ayudante, etc.)
    const itemsGastos = document.querySelectorAll('.expense-item');
    itemsGastos.forEach(item => {
        const chk = item.querySelector('.expense-check');
        if (chk && chk.checked) {
            // Obtenemos el nombre del gasto (ej. "Viáticos")
            const nombreGasto = item.querySelector('span')?.textContent.replace(':', '').trim() || 'Gasto Adicional';
            const montoInput = item.querySelector('.input-monto');
            const diasInput = item.querySelector('.input-dias');
            
            const monto = parseFloat(montoInput?.value) || 0;
            const dias = diasInput ? (parseInt(diasInput.value) || 1) : 1;
            const subtotalGasto = monto * dias;

            if (monto > 0) {
                let descripcionGasto = diasInput ? `${nombreGasto} (${dias} días)` : nombreGasto;
                let precioUnitario = diasInput ? monto : subtotalGasto;
                let cantidadDias = diasInput ? dias : 1;

                filasTabla.push([
                    descripcionGasto,
                    cantidadDias,
                    `$ ${precioUnitario.toLocaleString('es-AR', { minimumFractionDigits: 2 })}`,
                    `$ ${subtotalGasto.toLocaleString('es-AR', { minimumFractionDigits: 2 })}`
                ]);
            }
        }
    });

    // 4. Generar la tabla con AutoTable (incluyendo trabajos y gastos)
    doc.autoTable({
        startY: 68,
        head: [['Descripción / Trabajo', 'Cant.', 'Precio Unit.', 'Subtotal']],
        body: filasTabla,
        theme: 'striped',
        headStyles: { fillColor: [41, 128, 185], textColor: 255, fontStyle: 'bold' },
        styles: { fontSize: 9, cellPadding: 4 },
        columnStyles: {
            0: { cellWidth: 90 },
            1: { halign: 'center' },
            2: { halign: 'right' },
            3: { halign: 'right' }
        }
    });

    // 5. Total
    const finalY = (doc.lastAutoTable?.finalY || 70) + 12;
    const totalTexto = document.getElementById('total-presupuesto')?.textContent || 'Total $0,00';

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(12);
    doc.setTextColor(33, 37, 41);
    doc.text(totalTexto, 196, finalY, { align: 'right' });

    // 6. Descargar el PDF directamente
    const nombreArchivo = `${numPresupuesto.replace(/[^a-zA-Z0-9]/g, '_')}.pdf`;
    doc.save(nombreArchivo);
}

// Escuchar el botón cuando la página cargue
document.addEventListener('DOMContentLoaded', () => {
    const btnFinalizar = document.getElementById('btn-finalizar-presupuesto');
    
    if (btnFinalizar) {
        btnFinalizar.addEventListener('click', () => {
            descargarPresupuestoPDF();
        });
    }
});