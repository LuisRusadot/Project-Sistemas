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

CREATE TABLE experiencia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_hoja_vida INT NOT NULL,
    empresa VARCHAR(100),
    inicio DATE,
    fin DATE,
    puesto VARCHAR(100),
    funciones TEXT,
    FOREIGN KEY (id_hoja_vida) REFERENCES hoja_vida(id)
);

CREATE TABLE educacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_hoja_vida INT NOT NULL,
    institucion VARCHAR(100),
    ubicacion VARCHAR(100),
    titulo VARCHAR(100),
    fecha DATE,
    FOREIGN KEY (id_hoja_vida) REFERENCES hoja_vida(id)
);

CREATE INDEX idx_nombre ON hoja_vida(nombre);
CREATE INDEX idx_tecnica ON hoja_vida(tecnica);
