CREATE DATABASE Coperativa;
USE Coperativa;

CREATE TABLE IF NOT EXISTS usuarios (
  id_usuario INT PRIMARY KEY AUTO_INCREMENT,
  usuario TEXT NOT NULL,
  ci TEXT NOT NULL UNIQUE,
  estado TEXT NOT NULL,
  rol ENUM('socio', 'administrador') NOT NULL,
  direccion TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  contrasena TEXT NOT NULL,
  telefono TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE administradores (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS horas_trabajadas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    inicio_horario TIME NOT NULL,
    final_horario TIME NOT NULL,
    fecha_trabajo DATE NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS comprobantes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    numero_referencia VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    estado ENUM('pendiente', 'verificado') DEFAULT 'pendiente',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;