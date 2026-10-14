CREATE DATABASE IF NOT EXISTS presupuestador;
USE presupuestador;

CREATE TABLE IF NOT EXISTS usuario (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NULL,
    apellido VARCHAR(100) NULL,
    telefono VARCHAR(20) NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS cliente (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    direccion VARCHAR(150) NULL,
    telefono VARCHAR(20) NULL,
    email VARCHAR(150) NULL,
    id_usuario INT NOT NULL,
    CONSTRAINT FK_cliente_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id)
);

CREATE TABLE IF NOT EXISTS estado (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS presupuesto (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(8) NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    fecha_alta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_cliente INT NOT NULL,
    id_estado INT NOT NULL,
    CONSTRAINT FK_presupuesto_cliente FOREIGN KEY (id_cliente) REFERENCES cliente(id),
    CONSTRAINT FK_presupuesto_estado FOREIGN KEY (id_estado) REFERENCES estado(id)
);

CREATE TABLE IF NOT EXISTS categoria (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS unidad (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS trabajo (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    id_categoria INT NOT NULL,
    id_unidad INT NOT NULL,
    CONSTRAINT FK_trabajo_categoria FOREIGN KEY (id_categoria) REFERENCES categoria(id),
    CONSTRAINT FK_trabajo_unidad FOREIGN KEY (id_unidad) REFERENCES unidad(id)
);

CREATE IF NOT EXISTS TABLE usuario_trabajo(
    id_usuario INT NOT NULL,
    id_trabajo INT NOT NULL,
    precio_modificado DECIMAL(10, 2) NOT NULL,
    CONSTRAINT FK_usuartrab_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id),
    CONSTRAINT FK_usuartrab_trabajo FOREIGN KEY (id_trabajo) REFERENCES trabajo(id)
);

CREATE TABLE IF NOT EXISTS presupuesto_trabajo (
    id_presupuesto INT NOT NULL,
    id_trabajo INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (id_presupuesto, id_trabajo),
    CONSTRAINT FK_presuptrab_presupuesto FOREIGN KEY (id_presupuesto) REFERENCES presupuesto(id),
    CONSTRAINT FK_presuptrab_trabajo FOREIGN KEY (id_trabajo) REFERENCES trabajo(id)
);

CREATE TABLE IF NOT EXISTS configuracion (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    validez_presupuesto INT NULL,
    matricula BIT DEFAULT 0,
    nro_matricula INT NULL,
    porcentaje_matricula INT NULL,
    CONSTRAINT FK_configuracion_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id)
);