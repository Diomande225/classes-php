<?php
class Userpdo {
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
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (:login, :password, :email, :firstname, :lastname)");
        return $stmt->execute([
            'login' => $this->login,
            'password' => $hashedPassword,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname
        ]);
    }

    // Méthode pour se connecter
    public function login($login, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE login = :login");
        $stmt->execute(['login' => $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        } else {
            return false;
        }
    }

    // Méthode pour obtenir tous les utilisateurs
    public function getAllUsers() {
        $stmt = $this->conn->query("SELECT * FROM utilisateurs");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour supprimer un utilisateur
    public function deleteUser($userId) {
        $stmt = $this->conn->prepare("DELETE FROM utilisateurs WHERE id = :id");
        return $stmt->execute(['id' => $userId]);
    }

    // Méthode pour mettre à jour les informations de l'utilisateur
    public function updateUser() {
        $stmt = $this->conn->prepare("UPDATE utilisateurs SET login = :login, email = :email, firstname = :firstname, lastname = :lastname WHERE id = :id");
        return $stmt->execute([
            'login' => $this->login,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'id' => $this->id
        ]);
    }

    // Méthode pour obtenir les détails d'un utilisateur par ID
    public function getUserById($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
