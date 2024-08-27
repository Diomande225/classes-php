<?php
// Inclusion de la classe User (MySQLi) ou Userpdo (PDO)
require_once 'User.php';
// require_once 'Userpdo.php'; // Décommentez cette ligne et commentez l'autre pour utiliser PDO

// Initialisation de l'objet User
$user = new User('localhost', 'root', '', 'classes');

// Test de l'enregistrement d'un nouvel utilisateur
$registerInfo = $user->register("login", "password", "email", "firstname", "lastname");
echo "User registered: ";
print_r($registerInfo);
echo "<br>";

// Test de la connexion de l'utilisateur
$user->connect("johndoe", "password123");
if ($user->isConnected()) {
    echo "User connected: ";
    print_r($user->getAllInfos());
    echo "<br>";
}

// Test de la mise à jour de l'utilisateur
$user->update("johnupdated", "newpassword123", "johnupdated@example.com", "John", "Updated");
echo "User updated: ";
print_r($user->getAllInfos());
echo "<br>";

// Test de la déconnexion de l'utilisateur
$user->disconnect();
echo "User disconnected: " . ($user->isConnected() ? "Yes" : "No") . "<br>";

// Test de la suppression de l'utilisateur
$user->connect("johnupdated", "newpassword123");
if ($user->isConnected()) {
    echo "Deleting user...";
    $user->delete();
    echo "User deleted. ";
    echo "User connected: " . ($user->isConnected() ? "Yes" : "No") . "<br>";
}
?>
