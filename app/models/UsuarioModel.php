<?php

require_once ROOT . '/config/Database.php';

class UsuarioModel {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $result = $this->db->query(
            'SELECT u.id, u.nombre, u.email, u.rol, u.activo, u.created_at, u.sucursal_id,
                    v.nombre AS sucursal_nombre
             FROM usuarios u
             LEFT JOIN veterinarias v ON v.id = u.sucursal_id
             ORDER BY u.activo DESC, u.nombre ASC'
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getTotales(): array {
        $result = $this->db->query(
            "SELECT
                COUNT(*)                                                        AS total,
                SUM(activo = 1)                                                 AS activos,
                SUM(rol = 'admin'      AND activo = 1)                         AS admins,
                SUM(rol = 'veterinario' AND activo = 1)                        AS veterinarios,
                SUM(rol = 'recepcion'  AND activo = 1)                         AS recepcionistas
             FROM usuarios"
        );
        return $result ? ($result->fetch_assoc() ?? []) : [];
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare(
            'SELECT id, nombre, email, password, rol, ultima_veterinaria, sucursal_id FROM usuarios WHERE email = ? AND activo = 1'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result  = $stmt->get_result();
        $usuario = $result->num_rows === 1 ? $result->fetch_assoc() : null;
        $stmt->close();
        return $usuario;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT id, nombre, email, rol, activo, created_at FROM usuarios WHERE id = ?'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function emailExiste(string $email, int $excluirId = 0): bool {
        $stmt = $this->db->prepare(
            'SELECT id FROM usuarios WHERE email = ? AND id != ?'
        );
        $stmt->bind_param('si', $email, $excluirId);
        $stmt->execute();
        $existe = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $existe;
    }

    public function crear(array $d): bool {
        $hash      = password_hash($d['password'], PASSWORD_DEFAULT);
        $sucursal  = $d['sucursal_id'] ?: null;
        $stmt = $this->db->prepare(
            'INSERT INTO usuarios (nombre, email, password, rol, sucursal_id) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('ssssi', $d['nombre'], $d['email'], $hash, $d['rol'], $sucursal);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function actualizar(int $id, array $d): bool {
        $sucursal = $d['sucursal_id'] ?: null;
        $stmt = $this->db->prepare(
            'UPDATE usuarios SET nombre=?, email=?, rol=?, sucursal_id=? WHERE id=?'
        );
        $stmt->bind_param('sssii', $d['nombre'], $d['email'], $d['rol'], $sucursal, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function cambiarPassword(int $id, string $nueva): bool {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('UPDATE usuarios SET password=? WHERE id=?');
        $stmt->bind_param('si', $hash, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function toggleActivo(int $id): bool {
        $stmt = $this->db->prepare('UPDATE usuarios SET activo = 1 - activo WHERE id=?');
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function guardarUltimaVeterinaria(int $usuario_id, int $veterinaria_id): void {
        $stmt = $this->db->prepare('UPDATE usuarios SET ultima_veterinaria = ? WHERE id = ?');
        $stmt->bind_param('ii', $veterinaria_id, $usuario_id);
        $stmt->execute();
        $stmt->close();
    }

    public function verificarPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
}
