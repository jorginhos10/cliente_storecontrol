<?php

require_once ROOT . '/config/Database.php';

class UsuarioModel {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(int $cuenta_id): array {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.nombre, u.email, u.rol, u.activo, u.created_at, u.sucursal_id,
                    v.nombre AS sucursal_nombre
             FROM usuarios u
             LEFT JOIN veterinarias v ON v.id = u.sucursal_id
             WHERE u.cuenta_id = ?
             ORDER BY u.activo DESC, u.nombre ASC'
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
                COUNT(*)                                                        AS total,
                SUM(activo = 1)                                                 AS activos,
                SUM(rol = 'admin'      AND activo = 1)                         AS admins,
                SUM(rol = 'veterinario' AND activo = 1)                        AS veterinarios,
                SUM(rol = 'recepcion'  AND activo = 1)                         AS recepcionistas
             FROM usuarios WHERE cuenta_id = ?"
        );
        $stmt->bind_param('i', $cuenta_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? ($result->fetch_assoc() ?? []) : [];
    }

    // Global a propósito: el login solo pide email+password, sin selector de cuenta.
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare(
            'SELECT id, nombre, email, password, rol, ultima_veterinaria, sucursal_id, cuenta_id FROM usuarios WHERE email = ? AND activo = 1'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result  = $stmt->get_result();
        $usuario = $result->num_rows === 1 ? $result->fetch_assoc() : null;
        $stmt->close();
        return $usuario;
    }

    public function findById(int $id, int $cuenta_id): ?array {
        $stmt = $this->db->prepare(
            'SELECT id, nombre, email, rol, activo, created_at FROM usuarios WHERE id = ? AND cuenta_id = ?'
        );
        $stmt->bind_param('ii', $id, $cuenta_id);
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

    public function crear(array $d, int $cuenta_id): bool {
        $hash      = password_hash($d['password'], PASSWORD_DEFAULT);
        $sucursal  = $d['sucursal_id'] ?: null;
        $stmt = $this->db->prepare(
            'INSERT INTO usuarios (nombre, email, password, rol, sucursal_id, cuenta_id) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('ssssii', $d['nombre'], $d['email'], $hash, $d['rol'], $sucursal, $cuenta_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function actualizar(int $id, array $d, int $cuenta_id): bool {
        $sucursal = $d['sucursal_id'] ?: null;
        $stmt = $this->db->prepare(
            'UPDATE usuarios SET nombre=?, email=?, rol=?, sucursal_id=? WHERE id=? AND cuenta_id=?'
        );
        $stmt->bind_param('sssiii', $d['nombre'], $d['email'], $d['rol'], $sucursal, $id, $cuenta_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function cambiarPassword(int $id, string $nueva, int $cuenta_id): bool {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('UPDATE usuarios SET password=? WHERE id=? AND cuenta_id=?');
        $stmt->bind_param('sii', $hash, $id, $cuenta_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function toggleActivo(int $id, int $cuenta_id): bool {
        $stmt = $this->db->prepare('UPDATE usuarios SET activo = 1 - activo WHERE id=? AND cuenta_id=?');
        $stmt->bind_param('ii', $id, $cuenta_id);
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
