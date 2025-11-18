-- ============================================
-- Script de Base de Datos - Proyecto Justin
-- Sistema de Simulación de Pruebas de Admisión
-- ============================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS proyecto_justin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE proyecto_justin;

-- ============================================
-- Tabla: usuarios
-- ============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'estudiante') DEFAULT 'estudiante',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: universidades
-- ============================================
CREATE TABLE IF NOT EXISTS universidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    porc_examen INT NOT NULL DEFAULT 50,
    porc_grado INT NOT NULL DEFAULT 50,
    modalidad VARCHAR(50) DEFAULT 'Presencial',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: tipos_prueba_universidad
-- Tipos de prueba: razonamiento, conocimientos, generales
-- ============================================
CREATE TABLE IF NOT EXISTS tipos_prueba_universidad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    universidad_id INT NOT NULL,
    tipo_prueba VARCHAR(50) NOT NULL,
    FOREIGN KEY (universidad_id) REFERENCES universidades(id) ON DELETE CASCADE,
    INDEX idx_universidad (universidad_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: carreras
-- ============================================
CREATE TABLE IF NOT EXISTS carreras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    universidad_id INT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    modalidad VARCHAR(50) DEFAULT 'Presencial',
    matriz VARCHAR(100) DEFAULT 'Matriz Central',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (universidad_id) REFERENCES universidades(id) ON DELETE CASCADE,
    INDEX idx_universidad (universidad_id),
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: preguntas
-- ============================================
CREATE TABLE IF NOT EXISTS preguntas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    opcion1 VARCHAR(500) NOT NULL,
    opcion2 VARCHAR(500) NOT NULL,
    opcion3 VARCHAR(500) NOT NULL,
    opcion4 VARCHAR(500) NOT NULL,
    correcta INT NOT NULL CHECK (correcta >= 0 AND correcta <= 3),
    area VARCHAR(50) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_area (area)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: resultados
-- Historial de intentos de simuladores
-- ============================================
CREATE TABLE IF NOT EXISTS resultados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    universidad_id INT NOT NULL,
    correctas INT NOT NULL DEFAULT 0,
    incorrectas INT NOT NULL DEFAULT 0,
    total INT NOT NULL DEFAULT 0,
    tiempo VARCHAR(50),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (universidad_id) REFERENCES universidades(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_universidad (universidad_id),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATOS DE EJEMPLO
-- ============================================

-- Usuario administrador por defecto
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Administrador', 'admin@admin.com', '123456', 'admin');

-- Universidades de ejemplo
INSERT INTO universidades (nombre, porc_examen, porc_grado, modalidad) VALUES
('Universidad de Guayaquil', 60, 40, 'Presencial'),
('ESPOL', 50, 50, 'Presencial'),
('UCE', 65, 35, 'Virtual'),
('UNEMI', 70, 30, 'Presencial');

-- Tipos de prueba para cada universidad
INSERT INTO tipos_prueba_universidad (universidad_id, tipo_prueba) VALUES
(1, 'razonamiento'),
(1, 'conocimientos'),
(1, 'generales'),
(2, 'razonamiento'),
(2, 'conocimientos'),
(3, 'razonamiento'),
(3, 'generales'),
(4, 'razonamiento'),
(4, 'conocimientos'),
(4, 'generales');

-- Carreras de ejemplo
INSERT INTO carreras (universidad_id, nombre, modalidad, matriz) VALUES
(1, 'Ingeniería en Sistemas', 'Presencial', 'Matriz Central'),
(1, 'Medicina', 'Presencial', 'Matriz Central'),
(2, 'Ingeniería en Computación', 'Presencial', 'Campus Prosperina'),
(3, 'Derecho', 'Virtual', 'Matriz Central'),
(4, 'Administración de Empresas', 'Presencial', 'Matriz Central');

-- Preguntas de ejemplo
INSERT INTO preguntas (texto, opcion1, opcion2, opcion3, opcion4, correcta, area) VALUES
('¿Cuál es la capital de Ecuador?', 'Guayaquil', 'Quito', 'Cuenca', 'Ambato', 1, 'conocimientos'),
('Si 2x + 4 = 10, ¿cuánto vale x?', '2', '3', '4', '5', 1, 'razonamiento'),
('¿En qué año se fundó la Universidad de Guayaquil?', '1867', '1897', '1920', '1950', 0, 'generales'),
('¿Cuál es el resultado de 15 * 8?', '100', '110', '120', '130', 2, 'razonamiento');

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
