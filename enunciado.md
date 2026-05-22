# 📚 Sistema de Gestión de Biblioteca

## 🗄️ Esquema SQL

```sql
CREATE DATABASE biblioteca;
USE biblioteca;

-- Usuarios
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Géneros
CREATE TABLE genres (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
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

-- Géneros por defecto
INSERT INTO genres (name) VALUES
('Ficción'), ('No ficción'), ('Ciencia ficción'),
('Fantasía'), ('Terror'), ('Romance'),
('Historia'), ('Tecnología'), ('Manga');
```

---

## 📁 Estructura de archivos

```
biblioteca/
├── db/
│   └── conn.php
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── books/
│   ├── index.php       (listar)
│   ├── create.php      (añadir)
│   ├── edit.php        (editar)
│   ├── delete.php      (borrar)
│   └── favourite.php   (marcar favorito)
├── includes/
│   ├── header.php
│   └── footer.php
├── css/
│   └── style.css
└── index.php           (redirige según sesión)
```

---

## 🔄 Flujo de la app

```
Usuario no logueado
        ↓
    login.php / register.php
        ↓
    Sesión iniciada
        ↓
    books/index.php → lista sus libros
        ↓
    ┌─────────────────────────┐
    │  Crear / Editar / Borrar│
    │  Filtrar por género     │
    │  Filtrar por estado     │
    │  Marcar favorito        │
    │  Ver estadísticas       │
    └─────────────────────────┘
```

---

## 📄 Detalle de cada archivo

### db/conn.php

```php
<?php
$conn = new mysqli("localhost", "root", "", "biblioteca");
if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}
```

### auth/register.php

- Formulario: username, email, password, confirmar password
- Validar que las contraseñas coinciden
- Hashear con `password_hash()`
- Insertar con prepared statement
- Redirigir a login

### auth/login.php

- Formulario: username, password
- Buscar usuario por username
- Verificar con `password_verify()`
- Guardar en sesión: `$_SESSION['user_id']` y `$_SESSION['username']`
- Redirigir a books/index.php

### books/index.php

- Comprobar sesión activa
- SELECT de todos los libros del usuario con JOIN a genres
- Filtros opcionales por GET: `?genre=3&status=leyendo`
- Mostrar estadísticas: total, leídos, leyendo, pendientes

### books/create.php

- Formulario: título, autor, género, año, estado, valoración
- INSERT con prepared statement
- Redirigir a index

### books/edit.php

- Recibe `?id=X` por GET
- Verificar que el libro pertenece al usuario (seguridad)
- Cargar datos actuales en el formulario
- UPDATE con prepared statement

### books/delete.php

- Recibe `?id=X` por GET
- Verificar que el libro pertenece al usuario
- DELETE con prepared statement
- Redirigir a index

### books/favourite.php

- Toggle del campo favourite
- `UPDATE books SET favourite = !favourite WHERE id = ? AND user_id = ?`

---

## 🔒 Seguridad importante

- **Siempre** verificar que `user_id` de la sesión coincide con el del libro antes de editar/borrar
- **Siempre** prepared statements, nunca concatenar variables en queries
- `session_start()` al principio de cada archivo que use sesiones
- Redirigir a login si no hay sesión activa

---

## 📊 Consultas SQL útiles

**Listar libros con género:**

```sql
SELECT b.*, g.name as genre_name
FROM books b
LEFT JOIN genres g ON b.genre_id = g.id
WHERE b.user_id = ?
ORDER BY b.created_at DESC
```

**Estadísticas:**

```sql
SELECT
  COUNT(*) as total,
  SUM(status = 'leido') as leidos,
  SUM(status = 'leyendo') as leyendo,
  SUM(status = 'pendiente') as pendientes,
  SUM(favourite = 1) as favoritos
FROM books
WHERE user_id = ?
```

**Filtrar por género y estado:**

```sql
SELECT b.*, g.name as genre_name
FROM books b
LEFT JOIN genres g ON b.genre_id = g.id
WHERE b.user_id = ?
AND b.genre_id = ?
AND b.status = ?
```

---

¡Ahí lo tienes todo! Con esto puedes construir la app entera sin improvisar nada 🎯

¿Por dónde empiezas, la BD o el login? 👀
