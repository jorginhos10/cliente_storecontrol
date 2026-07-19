<?php

require_once ROOT . '/config/Database.php';

class DevolucionModel {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ── LISTADOS ──────────────────────────────────────────

    // Ventas de la sucursal disponibles para devolución (no anuladas)
    public function getVentas(int $veterinaria_id): array {
        $stmt = $this->db->prepare(
            "SELECT v.id, v.created_at, v.total,
                    TRIM(CONCAT(COALESCE(c.nombre,''),' ',COALESCE(c.apellido,''))) AS cliente_nombre,
                    u.nombre AS vendedor_nombre,
                    COUNT(d.id)     AS total_lineas,
                    SUM(d.cantidad) AS total_unidades,
                    (SELECT COALESCE(SUM(dd.cantidad), 0)
                       FROM devolucion_detalles dd
                       JOIN devoluciones dv ON dv.id = dd.devolucion_id
                      WHERE dv.venta_id = v.id) AS total_devuelto
             FROM ventas v
             LEFT JOIN clientes c ON c.id = v.cliente_id
             LEFT JOIN usuarios u ON u.id = v.usuario_id
             LEFT JOIN venta_detalles d ON d.venta_id = v.id
             WHERE v.veterinaria_id = ? AND v.estado != 'anulada'
             GROUP BY v.id
             ORDER BY v.created_at DESC"
        );
        $stmt->bind_param('i', $veterinaria_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Líneas de una venta con lo ya devuelto y lo disponible para devolver
    public function getLineasDisponibles(int $venta_id): array {
        $stmt = $this->db->prepare(
            "SELECT vd.producto_id, vd.cantidad, vd.precio_unitario,
                    p.nombre AS producto_nombre, p.codigo, p.unidad,
                    COALESCE((
                        SELECT SUM(dd.cantidad) FROM devolucion_detalles dd
                        JOIN devoluciones dv ON dv.id = dd.devolucion_id
                        WHERE dv.venta_id = vd.venta_id AND dd.producto_id = vd.producto_id
                    ), 0) AS cantidad_devuelta
             FROM venta_detalles vd
             JOIN productos p ON p.id = vd.producto_id
             WHERE vd.venta_id = ?"
        );
        $stmt->bind_param('i', $venta_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getHistorial(int $veterinaria_id): array {
        $stmt = $this->db->prepare(
            "SELECT dv.*, v.created_at AS venta_fecha,
                    TRIM(CONCAT(COALESCE(c.nombre,''),' ',COALESCE(c.apellido,''))) AS cliente_nombre,
                    us.nombre       AS usuario_nombre,
                    COUNT(dd.id)    AS total_lineas,
                    SUM(dd.cantidad) AS total_unidades
             FROM devoluciones dv
             JOIN ventas v ON v.id = dv.venta_id
             LEFT JOIN clientes c  ON c.id = v.cliente_id
             LEFT JOIN usuarios us ON us.id = dv.usuario_id
             LEFT JOIN devolucion_detalles dd ON dd.devolucion_id = dv.id
             WHERE dv.veterinaria_id = ?
             GROUP BY dv.id
             ORDER BY dv.created_at DESC"
        );
        $stmt->bind_param('i', $veterinaria_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getDetalleDevolucion(int $id): array {
        $stmt = $this->db->prepare(
            "SELECT dd.*, p.nombre AS producto_nombre, p.codigo, p.unidad
             FROM devolucion_detalles dd
             JOIN productos p ON p.id = dd.producto_id
             WHERE dd.devolucion_id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getTotales(int $veterinaria_id): array {
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(*) AS total_devoluciones,
                COALESCE(SUM(total), 0) AS valor_total,
                COALESCE(SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())
                              THEN total ELSE 0 END), 0) AS valor_mes
             FROM devoluciones WHERE veterinaria_id = ?"
        );
        $stmt->bind_param('i', $veterinaria_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc() ?? [];
        $stmt->close();

        $stmt2 = $this->db->prepare(
            "SELECT COALESCE(SUM(dd.cantidad), 0) AS total_unidades
             FROM devolucion_detalles dd
             JOIN devoluciones dv ON dv.id = dd.devolucion_id
             WHERE dv.veterinaria_id = ?"
        );
        $stmt2->bind_param('i', $veterinaria_id);
        $stmt2->execute();
        $row2 = $stmt2->get_result()->fetch_assoc() ?? [];
        $stmt2->close();

        return array_merge($row, $row2);
    }

    // ── ESCRITURA ─────────────────────────────────────────

    private function incrementarStock(int $veterinaria_id, int $producto_id, int $cantidad): void {
        $this->db->query(
            "INSERT INTO inventario (veterinaria_id, producto_id, stock)
             VALUES ($veterinaria_id, $producto_id, $cantidad)
             ON DUPLICATE KEY UPDATE stock = stock + $cantidad"
        );
    }

    public function crear(int $venta_id, int $veterinaria_id, int $usuario_id, string $motivo, array $lineas): array {
        $lineas = array_values(array_filter($lineas, fn($l) => (int)($l['cantidad'] ?? 0) > 0));
        if (empty($lineas)) {
            return ['ok' => false, 'error' => 'Selecciona al menos un producto con cantidad mayor a 0.'];
        }

        $disponibles = [];
        foreach ($this->getLineasDisponibles($venta_id) as $l) {
            $disponibles[(int)$l['producto_id']] = (int)$l['cantidad'] - (int)$l['cantidad_devuelta'];
        }

        $total = 0;
        foreach ($lineas as $l) {
            $pid  = (int)$l['producto_id'];
            $cant = (int)$l['cantidad'];
            if (!isset($disponibles[$pid])) {
                return ['ok' => false, 'error' => 'Uno de los productos no pertenece a esta venta.'];
            }
            if ($cant > $disponibles[$pid]) {
                return ['ok' => false, 'error' => "Cantidad a devolver mayor a la disponible (máx. {$disponibles[$pid]})."];
            }
            $total += $cant * (float)$l['precio_unitario'];
        }

        $this->db->begin_transaction();
        try {
            $tipo = 'parcial';
            $stmt = $this->db->prepare(
                'INSERT INTO devoluciones (venta_id, veterinaria_id, tipo, motivo, total, usuario_id)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param('iissdi', $venta_id, $veterinaria_id, $tipo, $motivo, $total, $usuario_id);
            $stmt->execute();
            $devolucion_id = (int)$this->db->insert_id;
            $stmt->close();

            $ins = $this->db->prepare(
                'INSERT INTO devolucion_detalles (devolucion_id, producto_id, cantidad, precio_unitario)
                 VALUES (?, ?, ?, ?)'
            );
            foreach ($lineas as $l) {
                $pid  = (int)$l['producto_id'];
                $cant = (int)$l['cantidad'];
                $prec = (float)$l['precio_unitario'];
                $ins->bind_param('iiid', $devolucion_id, $pid, $cant, $prec);
                $ins->execute();
                $this->incrementarStock($veterinaria_id, $pid, $cant);
            }
            $ins->close();

            // ¿Se devolvió todo lo que quedaba disponible de la venta?
            $devueltoAhora = [];
            foreach ($lineas as $l) { $devueltoAhora[(int)$l['producto_id']] = (int)$l['cantidad']; }

            $quedaAlgo = false;
            foreach ($disponibles as $pid => $disp) {
                $cubierto = $devueltoAhora[$pid] ?? 0;
                if ($disp - $cubierto > 0) { $quedaAlgo = true; break; }
            }
            if (!$quedaAlgo) {
                $this->db->query("UPDATE devoluciones SET tipo = 'total' WHERE id = $devolucion_id");
            }

            $this->db->commit();
            return ['ok' => true, 'error' => ''];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['ok' => false, 'error' => 'Error al registrar la devolución.'];
        }
    }
}
