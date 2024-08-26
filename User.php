<?php
class User {
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    private $conn;

    public function __construct($host = "localhost", $username = "root", $password = "", $dbname = "classes") {
        $this->conn = new mysqli($host, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function register($login, $password, $email, $firstname, $lastname) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $login, $password, $email, $firstname, $lastname);
        $stmt->execute();

        $this->id = $this->conn->insert_id;
        $this->login = $login;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;

        $stmt->close();

        return $this->getAllInfos();
    }

    public function connect($login, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $this->id = $user['id'];
                $this->login = $user['login'];
                $this->email = $user['email'];
                $this->firstname = $user['firstname'];
                $this->lastname = $user['lastname'];
            }
        }

        $stmt->close();
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
            $stmt->bind_param("i", $this->id);
            $stmt->execute();
            $stmt->close();
            $this->disconnect();
        }
    }

    public function update($login, $password, $email, $firstname, $lastname) {
        if ($this->id) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $login, $password, $email, $firstname, $lastname, $this->id);
            $stmt->execute();
            $stmt->close();

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
