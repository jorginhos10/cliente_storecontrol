<?php

require_once ROOT . '/config/Database.php';

class CategoriaModel {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(int $cuenta_id): array {
        $stmt = $this->db->prepare(
            'SELECT c.*, COUNT(p.id) AS total_productos
             FROM categorias c
             LEFT JOIN productos p ON p.categoria_id = c.id AND p.activo = 1
             WHERE c.cuenta_id = ?
             GROUP BY c.id
             ORDER BY c.activo DESC, c.nombre ASC'
        );
        $stmt->bind_param('i', $cuenta_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getActivas(int $cuenta_id): array {
        $stmt = $this->db->prepare(
            'SELECT id, nombre FROM categorias WHERE activo = 1 AND cuenta_id = ? ORDER BY nombre ASC'
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
                COUNT(*)        AS total,
                SUM(activo = 1) AS activas,
                SUM(activo = 0) AS inactivas
             FROM categorias WHERE cuenta_id = ?"
        );
        $stmt->bind_param('i', $cuenta_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? ($result->fetch_assoc() ?? []) : [];
    }

    public function findById(int $id, int $cuenta_id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM categorias WHERE id = ? AND cuenta_id = ?');
        $stmt->bind_param('ii', $id, $cuenta_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function crear(array $d, int $cuenta_id): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO categorias (nombre, descripcion, cuenta_id) VALUES (?, ?, ?)'
        );
        $stmt->bind_param('ssi', $d['nombre'], $d['descripcion'], $cuenta_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function actualizar(int $id, array $d, int $cuenta_id): bool {
        $stmt = $this->db->prepare(
            'UPDATE categorias SET nombre = ?, descripcion = ? WHERE id = ? AND cuenta_id = ?'
        );
        $stmt->bind_param('ssii', $d['nombre'], $d['descripcion'], $id, $cuenta_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function toggleActivo(int $id, int $cuenta_id): bool {
        $stmt = $this->db->prepare('UPDATE categorias SET activo = 1 - activo WHERE id = ? AND cuenta_id = ?');
        $stmt->bind_param('ii', $id, $cuenta_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function existeNombre(string $nombre, int $cuenta_id, int $excluirId = 0): bool {
        $stmt = $this->db->prepare('SELECT id FROM categorias WHERE nombre = ? AND cuenta_id = ? AND id != ?');
        $stmt->bind_param('sii', $nombre, $cuenta_id, $excluirId);
        $stmt->execute();
        $existe = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $existe;
    }
}
