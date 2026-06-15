CREATE DATABASE rede_social;
USE rede_social;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(150) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_nascimento DATE NOT NULL,
    genero ENUM('Feminino','Masculino','Outro'),
    bio TEXT DEFAULT NULL,
    capa VARCHAR(255) DEFAULT NULL,
    foto LONGBLOB,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    conteudo TEXT,
    imagem VARCHAR(255) DEFAULT NULL,
    video VARCHAR(255) DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE curtidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    post_id INT,
    UNIQUE(usuario_id, post_id)
);

CREATE TABLE seguidores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seguidor_id INT,
    seguindo_id INT,
    UNIQUE(seguidor_id, seguindo_id)
);

CREATE TABLE recuperacao_senha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expira_em DATETIME NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);