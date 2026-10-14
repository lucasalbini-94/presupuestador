<?php
class Presupuesto {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Obtener los últimos presupuestos (Tabla de inicio)
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
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los presupuestos con filtros opcionales
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
            $params[':estado'] = strtolower($estado);
        }

        if ($buscar) {
            $sql .= " AND (c.nombre LIKE :buscar OR c.apellido LIKE :buscar OR p.codigo LIKE :buscar)";
            $params[':buscar'] = "%$buscar%";
        }

        $sql .= " ORDER BY p.fecha_alta DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}