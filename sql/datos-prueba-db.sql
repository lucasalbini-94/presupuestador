-- =====================================================
-- 1. CATÁLOGOS BASE (Tablas maestras)
-- =====================================================

INSERT INTO estado (id, descripcion) VALUES
(1, 'Borrador'),
(2, 'Pendiente'),
(3, 'Aprobado'),
(4, 'Rechazado'),
(5, 'Facturado');

INSERT INTO categoria (id, descripcion) VALUES
(1, 'Cableado'),
(2, 'Canalización'),
(3, 'Instalación luminarias'),
(4, 'Instalación artefactos'),
(5, 'Tableros y PE');

INSERT INTO unidad (id, descripcion) VALUES
(1, 'Boca'),
(2, 'Metro lineal (m)'),
(3, 'Unidad'),
(4, 'Módulo'),
(5, 'Global / Paquete');


-- =====================================================
-- 2. USUARIOS Y CONFIGURACIONES
-- =====================================================

-- Contraseña de prueba para ambos usuarios: Demo1234! (solo desarrollo).
INSERT INTO usuario (id, nombre, apellido, telefono, email, pass) VALUES
(1, 'Martín', 'Sosa', '+54 11 4455-6677', 'martin.sosa@example.com', '$2y$10$TV6a50eDn9KqV9iRnjKq.eAZgeFu89HB9qxujOuHkvjvLhrepjoKa'),
(2, 'Federico', 'Ríos', '+54 11 5566-7788', 'federico.rios@example.com', '$2y$10$fFIdlGhSt4oawhw03C0PqeJfsTl5I8YYgf2t21rMKKnDxdyg25fga');

INSERT INTO configuracion (id, id_usuario, validez_presupuesto, matricula, nro_matricula, porcentaje_matricula) VALUES
(1, 1, 15, 1, 54210, 15),  -- Matriculado, recargo del 15%
(2, 2, 30, 0, NULL, NULL); -- No matriculado


-- =====================================================
-- 3. CLIENTES
-- =====================================================

INSERT INTO cliente (id, nombre, apellido, direccion, telefono, email, id_usuario) VALUES
(1, 'Estudio Jurídico Díaz & Asoc.', '', 'Av. Corrientes 1400 4B, CABA', '+54 11 4111-2233', 'contacto@diazasoc.com', 1),
(2, 'Luciana', 'Blanco', 'Calle Mendoza 320, Quilmes', '+54 11 4222-3344', 'luciana.blanco@gmail.com', 1),
(3, 'Taller Mecánico El Rayo', '', 'Av. Hipólito Yrigoyen 8900, Lomas', '+54 11 4333-4455', 'taller.elrayo@yahoo.com', 2);


-- =====================================================
-- 4. TRABAJOS Y PRECIOS POR USUARIO
-- Cada trabajo pertenece directamente al usuario con su respectivo precio
-- =====================================================

INSERT INTO trabajo (id, nombre, precio, id_categoria, id_unidad, id_usuario) VALUES
-- Trabajos de Martín (id_usuario = 1)
(1, 'Cableado de circuito de iluminación / tomas', 3800.00, 1, 1, 1),
(2, 'Pasado de conductores por línea troncal', 800.00, 1, 2, 1),
(3, 'Canalización embutida en mampostería', 2000.00, 2, 2, 1),
(4, 'Tendido de cañería a la vista / cablecanal', 1500.00, 2, 2, 1),
(5, 'Colocación de panel LED embutir / plafón', 4500.00, 3, 3, 1),
(6, 'Montaje de artefacto colgante / araña', 7500.00, 3, 3, 1),
(7, 'Instalación circuito y conexión de aire acondicionado', 15000.00, 4, 3, 1),
(8, 'Colocación de extractor de baño / campana', 6000.00, 4, 3, 1),
(9, 'Armado y conexionado de Tablero (hasta 6 módulos)', 26000.00, 5, 3, 1),
(10, 'Instalación de jabalina con cámara y medición de PAT', 18000.00, 5, 3, 1),

-- Trabajos de Federico (id_usuario = 2)
(11, 'Cableado de circuito de iluminación / tomas', 3200.00, 1, 1, 2),
(12, 'Armado y conexionado de Tablero (hasta 6 módulos)', 28000.00, 5, 3, 2);


-- =====================================================
-- 5. PRESUPUESTOS
-- =====================================================

INSERT INTO presupuesto (id, codigo, monto, fecha_alta, id_cliente, id_estado) VALUES
-- Presupuesto 1 (Martín para Estudio Díaz): Aprobado
(1, 'EL-0001', 95100.00, '2026-10-02 09:30:00', 1, 3),

-- Presupuesto 2 (Martín para Luciana Blanco): Pendiente
(2, 'EL-0002', 44000.00, '2026-10-09 14:15:00', 2, 2),

-- Presupuesto 3 (Federico para Taller El Rayo): Borrador
(3, 'EL-0003', 25600.00, '2026-10-11 16:00:00', 3, 1);


-- =====================================================
-- 6. DETALLE DE ÍTEMS DEL PRESUPUESTO
-- =====================================================

INSERT INTO presupuesto_trabajo (id_presupuesto, id_trabajo, cantidad, precio_unitario) VALUES
-- Presupuesto 1 (Martín):
(1, 1, 12.00, 3800.00),   -- 12 bocas de cableado ($45.600)
(1, 4, 15.00, 1500.00),   -- 15 metros cablecanal ($22.500)
(1, 5, 6.00, 4500.00),    -- 6 paneles LED ($27.000)

-- Presupuesto 2 (Martín):
(2, 9, 1.00, 26000.00),   -- 1 armado tablero ($26.000)
(2, 10, 1.00, 18000.00),  -- 1 jabalina + PAT ($18.000)

-- Presupuesto 3 (Federico):
(3, 11, 8.00, 3200.00);   -- 8 bocas de cableado con el trabajo id=11 perteneciente a Federico ($25.600)