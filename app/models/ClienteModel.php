<?php

require_once ROOT . '/config/Database.php';

class ClienteModel {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(int $cuenta_id): array {
        $stmt = $this->db->prepare(
            'SELECT *, 0 AS total_mascotas FROM clientes WHERE activo = 1 AND cuenta_id = ? ORDER BY apellido ASC, nombre ASC'
        );
        $stmt->bind_param('i', $cuenta_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getTotales(int $cuenta_id): array {
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()) THEN 1 ELSE 0 END) AS nuevos_mes
             FROM clientes WHERE activo = 1 AND cuenta_id = ?"
        );
        $stmt->bind_param('i', $cuenta_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? ($result->fetch_assoc() ?? []) : [];
    }

    public function findById(int $id, int $cuenta_id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM clientes WHERE id = ? AND cuenta_id = ? AND activo = 1');
        $stmt->bind_param('ii', $id, $cuenta_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function crear(array $d, int $cuenta_id): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO clientes (nombre, apellido, dni, email, telefono, direccion, notas, cuenta_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('sssssssi',
            $d['nombre'], $d['apellido'], $d['dni'],
            $d['email'],  $d['telefono'], $d['direccion'], $d['notas'], $cuenta_id
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function actualizar(int $id, array $d, int $cuenta_id): bool {
        $stmt = $this->db->prepare(
            'UPDATE clientes SET nombre=?, apellido=?, dni=?, email=?, telefono=?, direccion=?, notas=?
             WHERE id=? AND cuenta_id=?'
        );
        $stmt->bind_param('sssssssii',
            $d['nombre'], $d['apellido'], $d['dni'],
            $d['email'],  $d['telefono'], $d['direccion'], $d['notas'], $id, $cuenta_id
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function eliminar(int $id, int $cuenta_id): bool {
        $stmt = $this->db->prepare('UPDATE clientes SET activo=0 WHERE id=? AND cuenta_id=?');
        $stmt->bind_param('ii', $id, $cuenta_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
