<?php

require_once ROOT . '/config/Database.php';

class VeterinariaModel {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public static function generarIniciales(string $nombre): string {
        // Quitar la palabra "veterinaria"
        $n = preg_replace('/\bveterinaria\b/i', '', $nombre);
        // Quitar artículos y preposiciones cortas
        $n = preg_replace('/\b(la|el|los|las|de|del|y|e|the)\b/i', '', $n);
        $palabras = array_values(array_filter(explode(' ', trim($n))));
        if (empty($palabras)) return 'VC';
        if (count($palabras) === 1) return strtoupper(substr($palabras[0], 0, 2));
        return strtoupper(substr($palabras[0], 0, 1) . substr($palabras[1], 0, 1));
    }

    public static function serialVenta(string $sucursal, int $id, string $fecha = ''): string {
        $ini  = strtoupper(self::generarIniciales($sucursal));
        $date = $fecha ? date('dmY', strtotime($fecha)) : date('dmY');
        return $ini . '-' . $date . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
    }

    public static function serialIngreso(string $sucursal, int $id, string $fecha = ''): string {
        $ini  = strtoupper(self::generarIniciales($sucursal));
        $date = $fecha ? date('dmY', strtotime($fecha)) : date('dmY');
        return $ini . '-' . $date . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
    }

    public function getAll(int $cuenta_id): array {
        $stmt = $this->db->prepare(
            'SELECT * FROM veterinarias WHERE activo = 1 AND cuenta_id = ? ORDER BY id ASC'
        );
        $stmt->bind_param('i', $cuenta_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function findById(int $id, int $cuenta_id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM veterinarias WHERE id = ? AND cuenta_id = ? AND activo = 1');
        $stmt->bind_param('ii', $id, $cuenta_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function crear(array $d, int $cuenta_id): int {
        $stmt = $this->db->prepare(
            'INSERT INTO veterinarias (nombre, ruc, telefono, email, direccion, horario, sitio_web, cuenta_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('sssssssi',
            $d['nombre'], $d['ruc'], $d['telefono'],
            $d['email'],  $d['direccion'], $d['horario'], $d['sitio_web'], $cuenta_id
        );
        $stmt->execute();
        $id = (int)$this->db->insert_id;
        $stmt->close();
        return $id;
    }

    public function actualizar(int $id, array $d, int $cuenta_id): bool {
        $stmt = $this->db->prepare(
            'UPDATE veterinarias SET nombre=?, ruc=?, telefono=?, email=?, direccion=?, horario=?, sitio_web=?
             WHERE id=? AND cuenta_id=?'
        );
        $stmt->bind_param('sssssssii',
            $d['nombre'], $d['ruc'], $d['telefono'],
            $d['email'],  $d['direccion'], $d['horario'], $d['sitio_web'], $id, $cuenta_id
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function tieneDatos(int $id): bool {
        $tablas = [
            "SELECT COUNT(*) FROM ventas    WHERE veterinaria_id = $id",
            "SELECT COUNT(*) FROM ingresos  WHERE veterinaria_id = $id",
            "SELECT COUNT(*) FROM perdidas  WHERE veterinaria_id = $id",
            "SELECT COUNT(*) FROM inventario WHERE veterinaria_id = $id AND stock > 0",
            "SELECT COUNT(*) FROM usuarios  WHERE sucursal_id = $id AND activo = 1",
        ];
        foreach ($tablas as $sql) {
            $r = $this->db->query($sql);
            if ($r && (int)$r->fetch_row()[0] > 0) return true;
        }
        return false;
    }

    public function eliminar(int $id, int $cuenta_id): bool {
        if ($this->tieneDatos($id)) return false;
        $stmt = $this->db->prepare('UPDATE veterinarias SET activo=0 WHERE id=? AND cuenta_id=?');
        $stmt->bind_param('ii', $id, $cuenta_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
