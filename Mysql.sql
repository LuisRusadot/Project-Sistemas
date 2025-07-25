CREATE DATABASE IF NOT EXISTS banco_hojas_vida;
USE banco_hojas_vida;

CREATE TABLE hoja_vida (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    tecnica VARCHAR(100) NOT NULL,
    perfil TEXT NOT NULL,
    idiomas TEXT,
    certificacion VARCHAR(255),
    institucion_curso VARCHAR(100),
    fecha_cert DATE,
    foto VARCHAR(255),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Agregar índice para búsquedas más rápidas
CREATE INDEX idx_nombre ON hoja_vida(nombre);
CREATE INDEX idx_tecnica ON hoja_vida(tecnica);