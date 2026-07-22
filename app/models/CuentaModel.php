<?php

require_once ROOT . '/config/Database.php';

class CuentaModel {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crea, en una sola transacción, la cuenta, su primer comercio (veterinaria)
    // y el usuario admin dueño de la cuenta. Devuelve los ids generados o false.
    public function crearCuentaConAdmin(string $nombreNegocio, string $nombreAdmin, string $email, string $password): array|false {
        $this->db->begin_transaction();
        try {
            $stmt = $this->db->prepare('INSERT INTO cuentas (nombre) VALUES (?)');
            $stmt->bind_param('s', $nombreNegocio);
            $stmt->execute();
            $cuenta_id = (int)$this->db->insert_id;
            $stmt->close();

            $stmt = $this->db->prepare(
                'INSERT INTO veterinarias (nombre, ruc, telefono, email, direccion, horario, sitio_web, cuenta_id)
                 VALUES (?, \'\', \'\', \'\', \'\', \'\', \'\', ?)'
            );
            $stmt->bind_param('si', $nombreNegocio, $cuenta_id);
            $stmt->execute();
            $veterinaria_id = (int)$this->db->insert_id;
            $stmt->close();

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $rol  = 'admin';
            $stmt = $this->db->prepare(
                'INSERT INTO usuarios (nombre, email, password, rol, cuenta_id) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->bind_param('ssssi', $nombreAdmin, $email, $hash, $rol, $cuenta_id);
            $stmt->execute();
            $usuario_id = (int)$this->db->insert_id;
            $stmt->close();

            $this->db->commit();
            return ['cuenta_id' => $cuenta_id, 'veterinaria_id' => $veterinaria_id, 'usuario_id' => $usuario_id];
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM cuentas WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }
}
