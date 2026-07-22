-- Migración: cuentas independientes (multi-tenant)
-- Ejecutar en phpMyAdmin de producción ANTES de desplegar el código actualizado.

CREATE TABLE IF NOT EXISTS cuentas (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(150) NOT NULL,
    activo     TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO cuentas (id, nombre) VALUES (1, 'Cuenta Principal');

ALTER TABLE veterinarias ADD COLUMN cuenta_id INT NOT NULL DEFAULT 1, ADD INDEX idx_cuenta (cuenta_id);
ALTER TABLE usuarios     ADD COLUMN cuenta_id INT NOT NULL DEFAULT 1, ADD INDEX idx_cuenta (cuenta_id);
ALTER TABLE clientes     ADD COLUMN cuenta_id INT NOT NULL DEFAULT 1, ADD INDEX idx_cuenta (cuenta_id);
ALTER TABLE proveedores  ADD COLUMN cuenta_id INT NOT NULL DEFAULT 1, ADD INDEX idx_cuenta (cuenta_id);
ALTER TABLE categorias   ADD COLUMN cuenta_id INT NOT NULL DEFAULT 1, ADD INDEX idx_cuenta (cuenta_id);
ALTER TABLE productos    ADD COLUMN cuenta_id INT NOT NULL DEFAULT 1, ADD INDEX idx_cuenta (cuenta_id);
