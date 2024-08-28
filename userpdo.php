<?php

class Userpdo {
    private $pdo;

    public function __construct($host, $user, $password, $dbname) {
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
            $this->pdo = new PDO($dsn, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }

    // Méthode pour créer un utilisateur
    public function create($login, $password, $email, $firstname, $lastname) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (:login, :password, :email, :firstname, :lastname)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            die('Erreur lors de la création de l\'utilisateur : ' . $e->getMessage());
        }
    }

    // Méthode pour lire un utilisateur par ID
    public function read($id) {
        try {
            $sql = "SELECT * FROM utilisateurs WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Erreur lors de la lecture de l\'utilisateur : ' . $e->getMessage());
        }
    }

    // Méthode pour lire un utilisateur par login
    public function readByLogin($login) {
        try {
            $sql = "SELECT * FROM utilisateurs WHERE login = :login";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Erreur lors de la lecture de l\'utilisateur : ' . $e->getMessage());
        }
    }

    // Méthode pour mettre à jour un utilisateur
    public function update($id, $login, $email, $firstname, $lastname) {
        try {
            $sql = "UPDATE utilisateurs SET login = :login, email = :email, firstname = :firstname, lastname = :lastname WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            die('Erreur lors de la mise à jour de l\'utilisateur : ' . $e->getMessage());
        }
    }

    // Méthode pour supprimer un utilisateur
    public function delete($id) {
        try {
            $sql = "DELETE FROM utilisateurs WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            die('Erreur lors de la suppression de l\'utilisateur : ' . $e->getMessage());
        }
    }

    public function close() {
        $this->pdo = null; // Ferme la connexion en détruisant l'objet PDO
    }
}
?>
