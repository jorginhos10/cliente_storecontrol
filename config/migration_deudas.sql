-- Migración: módulo de Deudas (cuentas por cobrar a clientes)
-- Ejecutar en phpMyAdmin de producción antes de desplegar el código actualizado.

CREATE TABLE IF NOT EXISTS deudas (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    veterinaria_id INT NOT NULL,
    cliente_id    INT NOT NULL,
    monto         DECIMAL(10,2) NOT NULL,
    estado        ENUM('pendiente', 'pagada') NOT NULL DEFAULT 'pendiente',
    notas         VARCHAR(255) DEFAULT '',
    usuario_id    INT NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_veterinaria (veterinaria_id),
    INDEX idx_cliente (cliente_id)
);
