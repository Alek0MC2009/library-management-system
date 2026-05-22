<?php
// require('../db/conn.php');

class User {
    public $name;
    public $email;
    public $role;
    public $password;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function CreateUser($name, $email, $role, $password) {
        // $conn → $this->conn ❌
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, role, password) VALUES (?, ?, ?, ?)");
        // role es INT, bind_param "ssis" no "ssss" ❌
        $stmt->bind_param("ssis", $name, $email, $role, $password);
        $stmt->execute();
        $stmt->close();
    }

    public function DeleteUser($id){
      $stmt = $this->conn->prepare("DELETE FROM users where id = ?");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $stmt->close();
    }
    // Obtener usuario por ID
    public function GetUser($id) {
        $stmt = $this->conn->prepare("SELECT id, username, email, role, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user ?? null; // Si no existe devolvemos null
     }

    // Obtener usuario por username (para el login)
    public function GetUserByUsername($username) {
        $stmt = $this->conn->prepare("SELECT id, username, email, role, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    // Obtener todos los usuarios (para el admin)
    public function GetAllUsers() {
        $stmt = $this->conn->prepare("SELECT id, username, email, role, created_at FROM users");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $users;
    }

    // Actualizar usuario
    public function UpdateUser($id, $name, $email, $role) {
        $stmt = $this->conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssii", $name, $email, $role, $id);
        $stmt->execute();
        $stmt->close();
    }

    public function isAdmin($id) {
        $user = $this->GetUser($id);
        return $user && $user['role'] == 1; // 1 = admin
    }
}
