CREATE DATABASE biblioteca;
USE biblioteca;

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name ENUM('admin', 'user') DEFAULT 'user'
);
-- Usuarios
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) not null unique,
  email varchar(50) not null unique,
  role INT DEFAULT 2,
  password varchar(255) not null, -- Cuando este hecha la app cambiar al hash
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role) REFERENCES roles(id)
);

-- Géneros
CREATE TABLE genres (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) not null unique
);

-- Libros
CREATE TABLE books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  genre_id INT,
  title VARCHAR(150) NOT NULL,
  author VARCHAR(100) NOT NULL,
  year YEAR,
  status ENUM('pendiente', 'leyendo', 'leido') DEFAULT 'pendiente',
  rating TINYINT CHECK (rating BETWEEN 1 AND 5),
  favourite BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE SET NULL
);

INSERT INTO genres (name) VALUES
('Ficción'), ('No ficción'), ('Ciencia ficción'),
('Fantasía'), ('Terror'), ('Romance'),
('Historia'), ('Tecnología'), ('Manga');
