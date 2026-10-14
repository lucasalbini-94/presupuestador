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
-- 2. CATÁLOGO GLOBAL DE TRABAJOS (Precios base del sistema)
-- =====================================================

INSERT INTO trabajo (id, nombre, precio_base, id_categoria, id_unidad) VALUES
-- Categoría 1: Cableado
(1, 'Cableado de circuito de iluminación / tomas', 3500.00, 1, 1),
(2, 'Pasado de conductores por línea troncal', 800.00, 1, 2),

-- Categoría 2: Canalización
(3, 'Canalización embutida en mampostería', 2000.00, 2, 2),
(4, 'Tendido de cañería a la vista / cablecanal', 1400.00, 2, 2),

-- Categoría 3: Instalación luminarias
(5, 'Colocación de panel LED embutir / plafón', 4200.00, 3, 3),
(6, 'Montaje de artefacto colgante / araña', 7500.00, 3, 3),

-- Categoría 4: Instalación artefactos
(7, 'Instalación circuito y conexión de aire acondicionado', 15000.00, 4, 3),
(8, 'Colocación de extractor de baño / campana', 6000.00, 4, 3),

-- Categoría 5: Tableros y PE
(9, 'Armado y conexionado de Tablero (hasta 6 módulos)', 24000.00, 5, 3),
(10, 'Instalación de jabalina con cámara y medición de PAT', 18000.00, 5, 3);


-- =====================================================
-- 3. USUARIOS Y CONFIGURACIONES
-- =====================================================

INSERT INTO usuario (id, nombre, apellido, telefono, email, pass) VALUES
(1, 'Martín', 'Sosa', '+54 11 4455-6677', 'martin.sosa@example.com', '$2y$10$e8wWwN3Z0b0n0X0p9aBCdeFGHIJKLMNOPQRSTUVWXYZabcdefghijkl'),
(2, 'Federico', 'Ríos', '+54 11 5566-7788', 'federico.rios@example.com', '$2y$10$x9yYzM2A1c1o1Y1q8bCDeFGHIJKLMNOPQRSTUVWXYZabcdefghijkl');

INSERT INTO configuracion (id, id_usuario, validez_presupuesto, matricula, nro_matricula, porcentaje_matricula) VALUES
(1, 1, 15, 1, 54210, 15),  -- Matriculado, recargo del 15%
(2, 2, 30, 0, NULL, NULL); -- No matriculado


-- =====================================================
-- 4. PRECIOS PERSONALIZADOS POR USUARIO (usuario_trabajo)
-- Solo se guardan los que el electricista modificó respecto a la base
-- =====================================================

INSERT INTO usuario_trabajo (id_usuario, id_trabajo, precio) VALUES
-- Martín (usuario 1) ajustó algunos precios al alza:
(1, 1, 3800.00),   -- Boca de cableado: base $3500 -> cobra $3800
(1, 4, 1500.00),   -- Cañería a la vista: base $1400 -> cobra $1500
(1, 5, 4500.00),   -- Panel LED: base $4200 -> cobra $4500
(1, 9, 26000.00),  -- Tablero: base $24000 -> cobra $26000
-- (Nota: para el trabajo 10 usa el precio_base de $18000 porque no lo modificó)

-- Federico (usuario 2) cobra más barato el cableado y más el tablero:
(2, 1, 3200.00),   -- Boca de cableado: base $3500 -> cobra $3200
(2, 9, 28000.00);  -- Tablero: base $24000 -> cobra $28000


-- =====================================================
-- 5. CLIENTES
-- =====================================================

INSERT INTO cliente (id, nombre, apellido, direccion, telefono, email, id_usuario) VALUES
(1, 'Estudio Jurídico Díaz & Asoc.', 'Av. Corrientes 1400 4B, CABA', '+54 11 4111-2233', 'contacto@diazasoc.com', 1),
(2, 'Luciana', 'Blanco', 'Calle Mendoza 320, Quilmes', '+54 11 4222-3344', 'luciana.blanco@gmail.com', 1),
(3, 'Taller Mecánico El Rayo', 'Av. Hipólito Yrigoyen 8900, Lomas', '+54 11 4333-4455', 'taller.elrayo@yahoo.com', 2);


-- =====================================================
-- 6. PRESUPUESTOS
-- =====================================================

INSERT INTO presupuesto (id, codigo, monto, fecha_alta, id_cliente, id_estado) VALUES
-- Presupuesto 1 (Martín para Estudio Díaz): Aprobado
(1, 'EL-0001', 95100.00, '2026-10-02 09:30:00', 1, 3),

-- Presupuesto 2 (Martín para Luciana Blanco): Pendiente
(2, 'EL-0002', 44000.00, '2026-10-09 14:15:00', 2, 2),

-- Presupuesto 3 (Federico para Taller El Rayo): Borrador
(3, 'EL-0003', 25600.00, '2026-10-11 16:00:00', 3, 1);


-- =====================================================
-- 7. DETALLE DE ÍTEMS DEL PRESUPUESTO
-- Registra el precio unitario congelado al momento de crear el presupuesto
-- =====================================================

INSERT INTO presupuesto_trabajo (id_presupuesto, id_trabajo, cantidad, precio_unitario) VALUES
-- Presupuesto 1:
(1, 1, 12.00, 3800.00),   -- 12 bocas de cableado ($45.600) -> precio personalizado
(1, 4, 15.00, 1500.00),   -- 15 metros cablecanal ($22.500) -> precio personalizado
(1, 5, 6.00, 4500.00),    -- 6 paneles LED ($27.000)       -> precio personalizado

-- Presupuesto 2:
(2, 9, 1.00, 26000.00),   -- 1 armado tablero ($26.000)    -> precio personalizado
(2, 10, 1.00, 18000.00),  -- 1 jabalina + PAT ($18.000)    -> precio base del catálogo

-- Presupuesto 3:
(3, 1, 8.00, 3200.00);    -- 8 bocas de cableado ($25.600)  -> precio personalizado de Federico