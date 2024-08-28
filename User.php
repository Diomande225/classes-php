<?php
class User {
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create
    public function createUser($password) {
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("sssss", $this->login, $hashedPassword, $this->email, $this->firstname, $this->lastname);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Read
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Update
    public function updateUser($id, $password = null) {
        if($password) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $this->login, $hashedPassword, $this->email, $this->firstname, $this->lastname, $id);
        } else {
            $stmt = $this->conn->prepare("UPDATE utilisateurs SET login = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $this->login, $this->email, $this->firstname, $this->lastname, $id);
        }
        return $stmt->execute();
    }

    // Delete
    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Login
    public function login($login, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
?>
