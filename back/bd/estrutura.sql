CREATE DATABASE IF NOT EXISTS acme CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE acme;

CREATE TABLE usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    matricula VARCHAR(04) NOT NULL UNIQUE,
    nome VARCHAR(60) NOT NULL,
    email VARCHAR(60) NOT NULL UNIQUE,
    senha_hash CHAR(128) NOT NULL,
    sal CHAR(32) NOT NULL,
    tipo_usuario ENUM('ALUNO', 'FUNCIONARIO') NOT NULL
) ENGINE=InnoDB;

CREATE TABLE aluno (
    id_usuario INT PRIMARY KEY,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE funcionario (
    id_usuario INT PRIMARY KEY,
    tipo_funcionario ENUM('TECNICO', 'PROFESSOR') NOT NULL,
    cargo_gestao BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tipo_espaco (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE espaco (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo_sala CHAR(04),
    nome VARCHAR(100) NOT NULL UNIQUE,
    id_tipo INT NOT NULL,
    FOREIGN KEY (id_tipo) REFERENCES tipo_espaco(id)
      ON UPDATE CASCADE
      ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE reserva (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_espaco INT NOT NULL,
    data_reserva DATETIME NOT NULL,
    inicio DATETIME NOT NULL,
    fim DATETIME NOT NULL,
    justificativa VARCHAR(200),
    estado ENUM('MARCADA', 'CANCELADA') NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (id_espaco) REFERENCES espaco(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;