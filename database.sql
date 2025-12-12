-- 1. Inicialización de la Base de Datos
DROP DATABASE IF EXISTS cajas_sorpresa_db;
CREATE DATABASE cajas_sorpresa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cajas_sorpresa_db;

-- 2. Tabla de Categorías (Normal, Geek, +18)
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT
) ENGINE=InnoDB;

-- 3. Tabla de Productos (Datos extraídos con Web Scraping)
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    imagen_url VARCHAR(500),
    url_origen VARCHAR(500) NOT NULL,
    
    -- Campos específicos para tu lógica de negocio (Fase 5)
    condicion VARCHAR(50) NULL,     -- Para Cajas Normales (Nuevo/Usado de eBay)
    franquicia VARCHAR(100) NULL,   -- Para Cajas Geek (Ej: 'Dragon Ball')
    puntuacion DECIMAL(3, 1) NULL,  -- Para Cajas +18 (Filtrar por calidad)
    
    categoria_id INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Clave foránea para relacionar con Categorías
    CONSTRAINT fk_producto_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id)
) ENGINE=InnoDB;

-- 4. Tabla de Cajas (El producto final generado)
CREATE TABLE cajas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,       -- Ej: 'Caja Otaku Premium'
    precio_venta DECIMAL(10, 2) NOT NULL,
    descripcion_ia TEXT,                -- Descripción generada por IA (Opcional/Valorado)
    categoria_id INT NOT NULL,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_caja_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id)
) ENGINE=InnoDB;

-- 5. Tabla Pivote (Relación N:M -> Una caja tiene muchos productos)
CREATE TABLE cajas_productos (
    caja_id INT NOT NULL,
    producto_id INT NOT NULL,
    PRIMARY KEY (caja_id, producto_id),
    CONSTRAINT fk_cp_caja FOREIGN KEY (caja_id) REFERENCES cajas(id) ON DELETE CASCADE,
    CONSTRAINT fk_cp_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Datos Iniciales (Seeders)
INSERT INTO categorias (nombre, descripcion) VALUES 
('Normal', 'Gadgets y curiosidades aleatorias de eBay'),
('Geek', 'Anime, Manga y Coleccionables de AkatsukiAnime'),
('Adultos', 'Juguetes y accesorios +18 de Platanomelon');