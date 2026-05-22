<?php

class Genres {
  private $conn;
  private $userModel;
  public $name;

  public function __construct($conn, $userModel) {
    $this->conn = $conn;
    $this->userModel = $userModel;
  }

  public function GetAllGenres() {
    $stmt = $this->conn->prepare("SELECT * FROM genres");
    $stmt->execute();
    $result = $stmt->get_result();
    $genres = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $genres;
  }

  public function GetGenre($id) {
    $stmt = $this->conn->prepare("SELECT * FROM genres WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $genre = $result->fetch_assoc();
    $stmt->close();
    return $genre ?? null;
  }

  public function CreateGenre($name, $userId) {
    if (!$this->userModel->isAdmin($userId)) {
      return false; // No es admin
    }
    $stmt = $this->conn->prepare("INSERT INTO genres (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->close();
    return true;
  }

  public function DeleteGenre($id, $userId) {
    if (!$this->userModel->isAdmin($userId)) {
      return false;
    }
    $stmt = $this->conn->prepare("DELETE FROM genres WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    return true;
  }
}
