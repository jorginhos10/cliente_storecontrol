<?php

require_once ROOT . '/config/Database.php';

class ProveedorModel {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(int $cuenta_id): array {
        $stmt = $this->db->prepare(
            'SELECT * FROM proveedores WHERE cuenta_id = ? ORDER BY activo DESC, nombre ASC'
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
                COUNT(*)            AS total,
                SUM(activo = 1)     AS activos,
                SUM(activo = 0)     AS inactivos
             FROM proveedores WHERE cuenta_id = ?"
        );
        $stmt->bind_param('i', $cuenta_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? ($result->fetch_assoc() ?? []) : [];
    }

    public function findById(int $id, int $cuenta_id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM proveedores WHERE id = ? AND cuenta_id = ?');
        $stmt->bind_param('ii', $id, $cuenta_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function crear(array $d, int $cuenta_id): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO proveedores (nombre, contacto, telefono, email, direccion, notas, cuenta_id)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('ssssssi',
            $d['nombre'], $d['contacto'], $d['telefono'],
            $d['email'],  $d['direccion'], $d['notas'], $cuenta_id
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function actualizar(int $id, array $d, int $cuenta_id): bool {
        $stmt = $this->db->prepare(
            'UPDATE proveedores SET nombre=?, contacto=?, telefono=?, email=?, direccion=?, notas=?
             WHERE id=? AND cuenta_id=?'
        );
        $stmt->bind_param('ssssssii',
            $d['nombre'], $d['contacto'], $d['telefono'],
            $d['email'],  $d['direccion'], $d['notas'], $id, $cuenta_id
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function toggleActivo(int $id, int $cuenta_id): bool {
        $stmt = $this->db->prepare('UPDATE proveedores SET activo = 1 - activo WHERE id=? AND cuenta_id=?');
        $stmt->bind_param('ii', $id, $cuenta_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function tieneIngresos(int $id, int $cuenta_id): bool {
        $prov = $this->findById($id, $cuenta_id);
        if (!$prov) return false;
        $nombre = $this->db->real_escape_string($prov['nombre']);
        $r = $this->db->query("SELECT COUNT(*) FROM ingresos WHERE proveedor = '$nombre'");
        return $r && (int)$r->fetch_row()[0] > 0;
    }
}
