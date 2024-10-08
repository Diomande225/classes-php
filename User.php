<?php
class User {
    private $conn;

    public $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Méthode pour créer un utilisateur
    public function createUser($password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $this->login, $hashedPassword, $this->email, $this->firstname, $this->lastname);
        return $stmt->execute();
    }

    // Méthode pour se connecter
    public function login($login, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        } else {
            return false;
        }
    }

    // Méthode pour obtenir tous les utilisateurs
    public function getAllUsers() {
        $stmt = $this->conn->query("SELECT * FROM utilisateurs");
        return $stmt->fetch_all(MYSQLI_ASSOC);
    }

    // Méthode pour supprimer un utilisateur
    public function deleteUser($userId) {
        $stmt = $this->conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    // Méthode pour mettre à jour les informations de l'utilisateur
    public function updateUser() {
        $stmt = $this->conn->prepare("UPDATE utilisateurs SET login = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $this->login, $this->email, $this->firstname, $this->lastname, $this->id);
        return $stmt->execute();
    }

    // Méthode pour obtenir les détails d'un utilisateur par ID
    public function getUserById($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Méthode pour obtenir toutes les informations de l'utilisateur connecté
    public function getAllInfos() {
        return [
            'login' => $this->login,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname
        ];
    }

    // Méthode pour obtenir le login de l'utilisateur
    public function getLogin() {
        return $this->login;
    }

    // Méthode pour obtenir l'email de l'utilisateur
    public function getEmail() {
        return $this->email;
    }

    // Méthode pour obtenir le prénom de l'utilisateur
    public function getFirstname() {
        return $this->firstname;
    }

    // Méthode pour obtenir le nom de famille de l'utilisateur
    public function getLastname() {
        return $this->lastname;
    }
}
