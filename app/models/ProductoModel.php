<?php

require_once ROOT . '/config/Database.php';

class ProductoModel {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Catálogo de la cuenta; stock por veterinaria desde inventario
    public function getAll(int $cuenta_id, int $veterinaria_id = 0): array {
        if ($veterinaria_id > 0) {
            $stmt = $this->db->prepare(
                'SELECT p.*, c.nombre AS categoria, COALESCE(inv.stock, 0) AS stock
                 FROM productos p
                 LEFT JOIN categorias c  ON c.id = p.categoria_id
                 LEFT JOIN inventario inv ON inv.producto_id = p.id AND inv.veterinaria_id = ?
                 WHERE p.cuenta_id = ?
                 ORDER BY p.activo DESC, p.nombre ASC'
            );
            $stmt->bind_param('ii', $veterinaria_id, $cuenta_id);
        } else {
            $stmt = $this->db->prepare(
                'SELECT p.*, c.nombre AS categoria
                 FROM productos p
                 LEFT JOIN categorias c ON c.id = p.categoria_id
                 WHERE p.cuenta_id = ?
                 ORDER BY p.activo DESC, p.nombre ASC'
            );
            $stmt->bind_param('i', $cuenta_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Totales usando el stock de inventario de la veterinaria seleccionada
    public function getTotales(int $cuenta_id, int $veterinaria_id = 0): array {
        $joinVet = $veterinaria_id > 0 ? "AND inv.veterinaria_id = $veterinaria_id" : '';
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(p.id)                                                                AS total_productos,
                COALESCE(SUM(COALESCE(inv.stock, 0)), 0)                                   AS total_stock,
                COUNT(DISTINCT p.categoria_id)                                             AS total_categorias,
                COALESCE(SUM(COALESCE(inv.stock, 0) * p.precio_compra), 0)                AS valor_inventario,
                SUM(CASE WHEN COALESCE(inv.stock, 0) <= p.stock_minimo THEN 1 ELSE 0 END) AS stock_bajo
             FROM productos p
             LEFT JOIN inventario inv ON inv.producto_id = p.id $joinVet
             WHERE p.activo = 1 AND p.cuenta_id = ?"
        );
        $stmt->bind_param('i', $cuenta_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? ($result->fetch_assoc() ?? []) : [];
    }

    public function getCategorias(int $cuenta_id): array {
        $stmt = $this->db->prepare(
            'SELECT id, nombre FROM categorias WHERE activo = 1 AND cuenta_id = ? ORDER BY nombre ASC'
        );
        $stmt->bind_param('i', $cuenta_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function crear(array $datos, int $cuenta_id): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO productos (nombre, codigo, codigo_barras, descripcion, imagen, categoria_id, unidad, precio_compra, precio_venta, stock, stock_minimo, cuenta_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)'
        );
        $stmt->bind_param(
            'sssssisddii',
            $datos['nombre'],
            $datos['codigo'],
            $datos['codigo_barras'],
            $datos['descripcion'],
            $datos['imagen'],
            $datos['categoria_id'],
            $datos['unidad'],
            $datos['precio_compra'],
            $datos['precio_venta'],
            $datos['stock_minimo'],
            $cuenta_id
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function existeCodigoBarras(string $codigo, int $cuenta_id, int $excluirId = 0): bool {
        $stmt = $this->db->prepare('SELECT id FROM productos WHERE codigo_barras = ? AND cuenta_id = ? AND id != ?');
        $stmt->bind_param('sii', $codigo, $cuenta_id, $excluirId);
        $stmt->execute();
        $existe = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $existe;
    }

    public function findById(int $id, int $cuenta_id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM productos WHERE id = ? AND cuenta_id = ? AND activo = 1');
        $stmt->bind_param('ii', $id, $cuenta_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function actualizar(int $id, array $datos, int $cuenta_id): bool {
        $stmt = $this->db->prepare(
            'UPDATE productos SET nombre=?, codigo=?, codigo_barras=?, descripcion=?, imagen=?, categoria_id=?, unidad=?, precio_compra=?, precio_venta=?, stock_minimo=?
             WHERE id=? AND cuenta_id=?'
        );
        $stmt->bind_param(
            'sssssisddiii',
            $datos['nombre'],
            $datos['codigo'],
            $datos['codigo_barras'],
            $datos['descripcion'],
            $datos['imagen'],
            $datos['categoria_id'],
            $datos['unidad'],
            $datos['precio_compra'],
            $datos['precio_venta'],
            $datos['stock_minimo'],
            $id,
            $cuenta_id
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function toggleActivo(int $id, int $cuenta_id): bool {
        $stmt = $this->db->prepare('UPDATE productos SET activo = 1 - activo WHERE id=? AND cuenta_id=?');
        $stmt->bind_param('ii', $id, $cuenta_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
