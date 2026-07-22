<?php

require_once ROOT . '/config/Database.php';

class DeudaModel {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(int $veterinaria_id): array {
        $stmt = $this->db->prepare(
            "SELECT d.*, CONCAT(c.nombre, ' ', c.apellido) AS cliente_nombre, c.telefono AS cliente_telefono
             FROM deudas d
             JOIN clientes c ON c.id = d.cliente_id
             WHERE d.veterinaria_id = ?
             ORDER BY (d.estado = 'pendiente') DESC, d.created_at DESC"
        );
        $stmt->bind_param('i', $veterinaria_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getTotales(int $veterinaria_id): array {
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(*)                                                          AS total_registros,
                SUM(estado = 'pendiente')                                         AS total_pendientes,
                COALESCE(SUM(CASE WHEN estado = 'pendiente' THEN monto ELSE 0 END), 0) AS monto_pendiente,
                COALESCE(SUM(CASE WHEN estado = 'pagada'
                                   AND MONTH(updated_at) = MONTH(NOW()) AND YEAR(updated_at) = YEAR(NOW())
                              THEN monto ELSE 0 END), 0)                          AS monto_pagado_mes
             FROM deudas WHERE veterinaria_id = ?"
        );
        $stmt->bind_param('i', $veterinaria_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? ($result->fetch_assoc() ?? []) : [];
    }

    public function crear(array $d): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO deudas (veterinaria_id, cliente_id, monto, notas, usuario_id)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('iidsi',
            $d['veterinaria_id'], $d['cliente_id'], $d['monto'], $d['notas'], $d['usuario_id']
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function marcarPagada(int $id, int $veterinaria_id): bool {
        $stmt = $this->db->prepare(
            "UPDATE deudas SET estado = 'pagada' WHERE id = ? AND veterinaria_id = ?"
        );
        $stmt->bind_param('ii', $id, $veterinaria_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function eliminar(int $id, int $veterinaria_id): bool {
        $stmt = $this->db->prepare('DELETE FROM deudas WHERE id = ? AND veterinaria_id = ?');
        $stmt->bind_param('ii', $id, $veterinaria_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
