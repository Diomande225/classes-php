<?php
class Userpdo {
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    private $conn;

    public function __construct($host = "localhost", $username = "root", $password = "", $dbname = "classes") {
        try {
            $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function register($login, $password, $email, $firstname, $lastname) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$login, $password, $email, $firstname, $lastname]);

        $this->id = $this->conn->lastInsertId();
        $this->login = $login;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;

        return $this->getAllInfos();
    }

    public function connect($login, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->login = $user['login'];
            $this->email = $user['email'];
            $this->firstname = $user['firstname'];
            $this->lastname = $user['lastname'];
        }
    }

    public function disconnect() {
        $this->id = null;
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
    }

    public function delete() {
        if ($this->id) {
            $stmt = $this->conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->execute([$this->id]);
            $this->disconnect();
        }
    }

    public function update($login, $password, $email, $firstname, $lastname) {
        if ($this->id) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
            $stmt->execute([$login, $password, $email, $firstname, $lastname, $this->id]);

            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
        }
    }

    public function isConnected() {
        return isset($this->id);
    }

    public function getAllInfos() {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname
        ];
    }

    public function getLogin() {
        return $this->login;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function getLastname() {
        return $this->lastname;
    }
}
?>
