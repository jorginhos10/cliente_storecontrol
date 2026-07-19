<?php

require_once ROOT . '/config/Database.php';

class CategoriaModel {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $result = $this->db->query(
            'SELECT c.*, COUNT(p.id) AS total_productos
             FROM categorias c
             LEFT JOIN productos p ON p.categoria_id = c.id AND p.activo = 1
             GROUP BY c.id
             ORDER BY c.activo DESC, c.nombre ASC'
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getActivas(): array {
        $result = $this->db->query(
            'SELECT id, nombre FROM categorias WHERE activo = 1 ORDER BY nombre ASC'
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getTotales(): array {
        $result = $this->db->query(
            "SELECT
                COUNT(*)        AS total,
                SUM(activo = 1) AS activas,
                SUM(activo = 0) AS inactivas
             FROM categorias"
        );
        return $result ? ($result->fetch_assoc() ?? []) : [];
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM categorias WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function crear(array $d): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)'
        );
        $stmt->bind_param('ss', $d['nombre'], $d['descripcion']);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function actualizar(int $id, array $d): bool {
        $stmt = $this->db->prepare(
            'UPDATE categorias SET nombre = ?, descripcion = ? WHERE id = ?'
        );
        $stmt->bind_param('ssi', $d['nombre'], $d['descripcion'], $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function toggleActivo(int $id): bool {
        $stmt = $this->db->prepare('UPDATE categorias SET activo = 1 - activo WHERE id = ?');
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function existeNombre(string $nombre, int $excluirId = 0): bool {
        $stmt = $this->db->prepare('SELECT id FROM categorias WHERE nombre = ? AND id != ?');
        $stmt->bind_param('si', $nombre, $excluirId);
        $stmt->execute();
        $existe = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $existe;
    }
}
