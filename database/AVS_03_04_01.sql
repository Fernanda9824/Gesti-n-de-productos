-- Base de datos para la práctica AVS.03.04.01
CREATE DATABASE IF NOT EXISTS AVS_03_04_01
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE AVS_03_04_01;

-- Tabla de categorías: catálogo que clasifica los productos.
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    descripcion VARCHAR(30) NOT NULL,
    PRIMARY KEY (id_categoria),
    UNIQUE KEY uq_categorias_descripcion (descripcion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla principal de productos.
CREATE TABLE IF NOT EXISTS productos (
    id_producto INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(30) NOT NULL,
    precio DOUBLE NOT NULL,
    stock INT(100) UNSIGNED NOT NULL DEFAULT 0,
    foto_p VARCHAR(100) NOT NULL,
    categoria_id INT(8) UNSIGNED NOT NULL,
    PRIMARY KEY (id_producto),
    KEY idx_productos_categoria (categoria_id),
    CONSTRAINT fk_productos_categorias
      FOREIGN KEY (categoria_id)
      REFERENCES categorias (id_categoria)
      ON UPDATE CASCADE
      ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categorías iniciales para poder seleccionar una al registrar productos.
INSERT IGNORE INTO categorias (descripcion) VALUES
('Electrónica'),
('Papelería'),
('Accesorios'),
('Hogar');
