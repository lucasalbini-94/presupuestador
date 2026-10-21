function descargarPresupuestoPDF() {
    if (!window.jspdf?.jsPDF) {
        throw new Error("No se pudo cargar la librería para generar el PDF.");
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const selectorCliente = document.getElementById('id_cliente');
    const nombreCliente = selectorCliente?.selectedOptions[0]?.textContent.trim() || 'Cliente General';
    const numPresupuesto = document.getElementById('num-presupuesto')?.textContent.trim() || 'Presupuesto';
    const fechaActual = new Date().toLocaleDateString('es-AR');

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(18);
    doc.setTextColor(33, 37, 41);
    doc.text("PRESUPUESTO DE TRABAJO", 14, 20);

    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.setTextColor(100);
    doc.text(numPresupuesto, 14, 27);
    doc.text(`Fecha: ${fechaActual}`, 14, 32);
    doc.setDrawColor(220, 224, 230);
    doc.setFillColor(248, 249, 250);
    doc.roundedRect(14, 37, 182, 18, 3, 3, 'FD');
    doc.setFont('helvetica', 'bold');
    doc.setTextColor(33, 37, 41);
    doc.text("Datos del Cliente:", 18, 44);
    doc.setFont('helvetica', 'normal');
    doc.text(`Nombre: ${nombreCliente}`, 18, 51);

    const filasTabla = [];
    const tablaTrabajos = document.getElementById('tabla-trabajos');

    if (tablaTrabajos) {
        tablaTrabajos.querySelectorAll('tr').forEach((tr) => {
            const trabajo = tr.querySelector('.select-trabajo')?.selectedOptions[0]?.textContent.trim() || '';
            const precio = parseFloat(tr.querySelector('.input-precio')?.value) || 0;
            const cantidad = parseInt(tr.querySelector('.input-cantidad')?.value) || 0;
            const subtotal = precio * cantidad;

            if (trabajo && cantidad > 0) {
                filasTabla.push([
                    trabajo,
                    cantidad,
                    `$ ${precio.toLocaleString('es-AR', { minimumFractionDigits: 2 })}`,
                    `$ ${subtotal.toLocaleString('es-AR', { minimumFractionDigits: 2 })}`
                ]);
            }
        });
    }

    if (filasTabla.length === 0) {
        throw new Error("Agregá al menos un trabajo antes de descargar el presupuesto.");
    }

    if (typeof doc.autoTable !== 'function') {
        throw new Error("No se pudo cargar la tabla del presupuesto en PDF.");
    }

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

    let finalY = (doc.lastAutoTable?.finalY || 70) + 12;
    if (finalY > doc.internal.pageSize.getHeight() - 14) {
        doc.addPage();
        finalY = 20;
    }
    const totalTexto = document.getElementById('total-presupuesto')?.textContent || 'Total $0,00';
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(12);
    doc.setTextColor(33, 37, 41);
    doc.text(totalTexto, 196, finalY, { align: 'right' });

    const nombreArchivo = `${numPresupuesto.replace(/[^a-zA-Z0-9]/g, '_')}.pdf`;
    doc.save(nombreArchivo);
}

document.addEventListener('DOMContentLoaded', () => {
    const btnDescargarPDF = document.getElementById('btn-descargar-pdf');

    if (!btnDescargarPDF) {
        return;
    }

    btnDescargarPDF.addEventListener('click', () => {
        try {
            descargarPresupuestoPDF();
        } catch (error) {
            console.error('Error al generar el PDF:', error);
            window.alert(error.message || 'No se pudo generar el PDF.');
        }
    });
});