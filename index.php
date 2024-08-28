<?php
session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "root"; // Remplacez par votre nom d'utilisateur MySQL
$password = ""; // Remplacez par votre mot de passe MySQL
$dbname = "classes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include 'User.php';
$user = new User($conn);

$message = "";
$showRegistrationForm = true;
$showLoginForm = true;

// Inscription
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $user->login = $_POST['login'];
    $user->email = $_POST['email'];
    $user->firstname = $_POST['firstname'];
    $user->lastname = $_POST['lastname'];
    $password = $_POST['password'];

    if ($user->createUser($password)) {
        $message = "Vous êtes inscrit avec succès. Veuillez vous connecter.";
        $showRegistrationForm = false; // Cache le formulaire d'inscription après l'inscription
    } else {
        $message = "Erreur lors de l'inscription.";
    }
}

// Connexion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_btn'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $user_data = $user->login($login, $password);

    if ($user_data) {
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['login'] = $user_data['login'];
        $_SESSION['firstname'] = $user_data['firstname'];
        $_SESSION['lastname'] = $user_data['lastname'];
        $_SESSION['email'] = $user_data['email'];
        $message = "Connexion réussie. Bienvenue " . $user_data['firstname'];
        $showRegistrationForm = false;
        $showLoginForm = false;
    } else {
        $message = "Échec de la connexion.";
    }
}

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    $showRegistrationForm = false;
    $showLoginForm = false;
    $message = "Vous êtes connecté en tant que " . $_SESSION['firstname'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription et Connexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .form-container {
            margin-bottom: 20px;
        }
        form input {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<?php if ($showRegistrationForm): ?>
    <div class="form-container">
        <h2>Inscription</h2>
        <form method="POST" action="">
            <input type="text" name="login" placeholder="Login" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="text" name="firstname" placeholder="Prénom" required><br>
            <input type="text" name="lastname" placeholder="Nom" required><br>
            <input type="password" name="password" placeholder="Mot de passe" required><br>
            <input type="submit" name="register" value="S'inscrire">
        </form>
    </div>
<?php endif; ?>

<?php if ($showLoginForm || isset($_SESSION['user_id'])): ?>
    <div class="form-container">
        <h2>Connexion</h2>
        <form method="POST" action="">
            <input type="text" name="login" placeholder="Login" required><br>
            <input type="password" name="password" placeholder="Mot de passe" required><br>
            <input type="submit" name="login_btn" value="Se connecter">
        </form>
    </div>
<?php endif; ?>

<p><?php echo $message; ?></p>

<?php if (isset($_SESSION['user_id'])): ?>
    <a href="logout.php">Déconnexion</a>
<?php endif; ?>

</body>
</html>
