<?php
class Presupuesto {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function obtenerUltimos($id_usuario, $limite = 5) {
        $sql = "SELECT
                    p.id,
                    p.codigo AS nro,
                    CONCAT(c.nombre, ' ', c.apellido) AS cliente,
                    p.monto AS total,
                    e.descripcion AS estado
                FROM presupuesto p
                JOIN cliente c ON p.id_cliente = c.id
                JOIN estado e ON p.id_estado = e.id
                WHERE c.id_usuario = :id_usuario
                ORDER BY p.fecha_alta DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_usuario', (int)$id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(':limite', max(1, (int)$limite), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodos($id_usuario, $estado = null, $buscar = null) {
        $sql = "SELECT
                    p.id,
                    p.codigo AS nro,
                    CONCAT(c.nombre, ' ', c.apellido) AS cliente,
                    p.monto AS total,
                    e.descripcion AS estado
                FROM presupuesto p
                JOIN cliente c ON p.id_cliente = c.id
                JOIN estado e ON p.id_estado = e.id
                WHERE c.id_usuario = :id_usuario";

        $params = [':id_usuario' => $id_usuario];

        if ($estado && $estado !== 'todos') {
            $sql .= " AND LOWER(e.descripcion) = :estado";
            $params[':estado'] = mb_strtolower($estado);
        }

        if ($buscar) {
            $sql .= " AND (c.nombre LIKE :buscar_nombre OR c.apellido LIKE :buscar_apellido OR p.codigo LIKE :buscar_codigo)";
            $termino = '%' . $buscar . '%';
            $params[':buscar_nombre'] = $termino;
            $params[':buscar_apellido'] = $termino;
            $params[':buscar_codigo'] = $termino;
        }

        $sql .= " ORDER BY p.fecha_alta DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_usuario, $id_presupuesto) {
        $stmt = $this->db->prepare(
            "SELECT p.id, p.codigo AS nro, p.id_cliente, p.monto AS total,
                    e.descripcion AS estado,
                    c.nombre AS cliente_nombre, c.apellido AS cliente_apellido,
                    c.telefono AS cliente_telefono, c.email AS cliente_email,
                    c.direccion AS cliente_direccion
             FROM presupuesto p
             JOIN cliente c ON p.id_cliente = c.id
             JOIN estado e ON p.id_estado = e.id
             WHERE p.id = :id_presupuesto AND c.id_usuario = :id_usuario"
        );
        $stmt->execute([
            ':id_presupuesto' => (int)$id_presupuesto,
            ':id_usuario' => (int)$id_usuario
        ]);
        $presupuesto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$presupuesto) {
            return null;
        }

        $stmt = $this->db->prepare(
            "SELECT pt.id_trabajo, pt.cantidad, pt.precio_unitario,
                    t.nombre AS trabajo, t.id_categoria,
                    c.descripcion AS categoria, u.descripcion AS unidad
             FROM presupuesto_trabajo pt
             JOIN trabajo t ON t.id = pt.id_trabajo
             JOIN categoria c ON c.id = t.id_categoria
             JOIN unidad u ON u.id = t.id_unidad
             WHERE pt.id_presupuesto = :id_presupuesto
             ORDER BY c.id, t.nombre"
        );
        $stmt->execute([':id_presupuesto' => (int)$id_presupuesto]);
        $presupuesto['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $presupuesto;
    }

    public function guardar($id_usuario, $id_cliente, array $items, $id_presupuesto = null) {
        if (!$items) {
            throw new InvalidArgumentException('Agregá al menos un trabajo al presupuesto.');
        }

        $itemsPorTrabajo = [];
        foreach ($items as $item) {
            $idTrabajo = (int)($item['id_trabajo'] ?? 0);
            $cantidad = (int)($item['cantidad'] ?? 0);
            if ($idTrabajo < 1 || $cantidad < 1) {
                throw new InvalidArgumentException('Revisá los trabajos y sus cantidades.');
            }
            $cantidadAcumulada = $itemsPorTrabajo[$idTrabajo] ?? 0;
            if ($cantidad > 2147483647 - $cantidadAcumulada) {
                throw new InvalidArgumentException('La cantidad supera el máximo permitido.');
            }
            $itemsPorTrabajo[$idTrabajo] = $cantidadAcumulada + $cantidad;
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "SELECT id FROM cliente WHERE id = :id_cliente AND id_usuario = :id_usuario"
            );
            $stmt->execute([
                ':id_cliente' => (int)$id_cliente,
                ':id_usuario' => (int)$id_usuario
            ]);
            if (!$stmt->fetchColumn()) {
                throw new InvalidArgumentException('El cliente seleccionado no está disponible.');
            }

            if ($id_presupuesto !== null) {
                $stmt = $this->db->prepare(
                    "SELECT p.id
                     FROM presupuesto p
                     JOIN cliente c ON c.id = p.id_cliente
                     WHERE p.id = :id_presupuesto AND c.id_usuario = :id_usuario"
                );
                $stmt->execute([
                    ':id_presupuesto' => (int)$id_presupuesto,
                    ':id_usuario' => (int)$id_usuario
                ]);
                if (!$stmt->fetchColumn()) {
                    throw new InvalidArgumentException('El presupuesto seleccionado no está disponible.');
                }
            }

            $marcadores = implode(',', array_fill(0, count($itemsPorTrabajo), '?'));
            $stmt = $this->db->prepare(
                "SELECT t.id, COALESCE(ut.precio_modificado, t.precio_base) AS precio
                 FROM trabajo t
                 LEFT JOIN usuario_trabajo ut
                   ON ut.id_trabajo = t.id AND ut.id_usuario = ?
                 WHERE t.id IN ($marcadores)"
            );
            $stmt->execute(array_merge([(int)$id_usuario], array_keys($itemsPorTrabajo)));
            $precios = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
                $precios[(int)$fila['id']] = (int)round((float)$fila['precio'] * 100);
            }

            if (count($precios) !== count($itemsPorTrabajo)) {
                throw new InvalidArgumentException('Uno o más trabajos ya no están disponibles.');
            }

            $totalCentavos = 0;
            foreach ($itemsPorTrabajo as $idTrabajo => $cantidad) {
                if ($precios[$idTrabajo] < 0) {
                    throw new InvalidArgumentException('Los precios del catálogo deben ser válidos.');
                }
                if ($precios[$idTrabajo] > 0 &&
                    $cantidad > intdiv(9999999999 - $totalCentavos, $precios[$idTrabajo])) {
                    throw new InvalidArgumentException('El total supera el máximo permitido.');
                }
                $totalCentavos += $precios[$idTrabajo] * $cantidad;
            }
            $total = intdiv($totalCentavos, 100) . '.' .
                str_pad((string)($totalCentavos % 100), 2, '0', STR_PAD_LEFT);

            if ($id_presupuesto === null) {
                $stmt = $this->db->prepare(
                    "INSERT INTO presupuesto (codigo, monto, fecha_alta, id_cliente, id_estado)
                     VALUES ('TEMP', :monto, NOW(), :id_cliente, 1)"
                );
                $stmt->execute([':monto' => $total, ':id_cliente' => (int)$id_cliente]);
                $id_presupuesto = (int)$this->db->lastInsertId();
                $codigo = 'EL' . strtoupper(str_pad(base_convert((string)$id_presupuesto, 10, 36), 6, '0', STR_PAD_LEFT));
                $stmt = $this->db->prepare("UPDATE presupuesto SET codigo = :codigo WHERE id = :id");
                $stmt->execute([':codigo' => $codigo, ':id' => $id_presupuesto]);
            } else {
                $stmt = $this->db->prepare(
                    "UPDATE presupuesto SET monto = :monto, id_cliente = :id_cliente
                     WHERE id = :id_presupuesto"
                );
                $stmt->execute([
                    ':monto' => $total,
                    ':id_cliente' => (int)$id_cliente,
                    ':id_presupuesto' => (int)$id_presupuesto
                ]);
                $stmt = $this->db->prepare(
                    "DELETE FROM presupuesto_trabajo WHERE id_presupuesto = :id_presupuesto"
                );
                $stmt->execute([':id_presupuesto' => (int)$id_presupuesto]);
            }

            $stmt = $this->db->prepare(
                "INSERT INTO presupuesto_trabajo (id_presupuesto, id_trabajo, cantidad, precio_unitario)
                 VALUES (:id_presupuesto, :id_trabajo, :cantidad, :precio_unitario)"
            );
            foreach ($itemsPorTrabajo as $idTrabajo => $cantidad) {
                $stmt->execute([
                    ':id_presupuesto' => (int)$id_presupuesto,
                    ':id_trabajo' => $idTrabajo,
                    ':cantidad' => $cantidad,
                    ':precio_unitario' => intdiv($precios[$idTrabajo], 100) . '.' .
                        str_pad((string)($precios[$idTrabajo] % 100), 2, '0', STR_PAD_LEFT)
                ]);
            }

            $this->db->commit();
            return (int)$id_presupuesto;
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $error;
        }
    }
}
