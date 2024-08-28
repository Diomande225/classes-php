<?php
session_start();

// Configuration de la base de données
$servername = "localhost";
$username = "root"; // Remplacez par votre nom d'utilisateur MySQL
$password = ""; // Remplacez par votre mot de passe MySQL
$dbname = "classes";

// Connexion à la base de données avec PDO
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

include 'user-pdo.php';
$user = new Userpdo($conn);

$message = "";

// Inscription
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $user->login = $_POST['login'];
    $user->email = $_POST['email'];
    $user->firstname = $_POST['firstname'];
    $user->lastname = $_POST['lastname'];
    $password = $_POST['password'];

    if ($user->createUser($password)) {
        $message = "Vous êtes inscrit avec succès. Veuillez vous connecter.";
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
        header("Location: index.php"); // Rediriger pour éviter la soumission multiple
        exit();
    } else {
        $message = "Échec de la connexion.";
    }
}

// Mise à jour du profil de l'utilisateur
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $user->id = $_SESSION['user_id'];
    $user->login = $_POST['login'];
    $user->email = $_POST['email'];
    $user->firstname = $_POST['firstname'];
    $user->lastname = $_POST['lastname'];

    if ($user->updateUser()) {
        $_SESSION['login'] = $user->login;
        $_SESSION['firstname'] = $user->firstname;
        $_SESSION['lastname'] = $user->lastname;
        $_SESSION['email'] = $user->email;
        $message = "Profil mis à jour avec succès.";
    } else {
        $message = "Erreur lors de la mise à jour du profil.";
    }
}

// Si l'utilisateur est connecté, afficher la liste des utilisateurs et le formulaire de modification du profil
if (isset($_SESSION['user_id'])) {
    $users = $user->getAllUsers();
    
    // Remplir l'objet User avec les informations actuelles de l'utilisateur connecté
    $user->login = $_SESSION['login'];
    $user->email = $_SESSION['email'];
    $user->firstname = $_SESSION['firstname'];
    $user->lastname = $_SESSION['lastname'];
    
    // Suppression d'un utilisateur (uniquement l'utilisateur connecté)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
        $userId = $_POST['user_id'];
        if ($userId == $_SESSION['user_id']) { // Vérifier si c'est l'utilisateur connecté
            if ($user->deleteUser($userId)) {
                $message = "Votre compte a été supprimé avec succès.";
                session_destroy(); // Déconnecter l'utilisateur après suppression
                header("Location: index.php");
                exit();
            } else {
                $message = "Erreur lors de la suppression du compte.";
            }
        } else {
            $message = "Vous ne pouvez pas supprimer d'autres comptes.";
        }
    }

    // Récupérer les détails de l'utilisateur connecté via la méthode getAllInfos
    $userInfos = $user->getAllInfos();
} else {
    // Si l'utilisateur n'est pas connecté, afficher les formulaires d'inscription et de connexion
    $showRegistrationForm = true;
    $showLoginForm = true;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil utilisateur</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['user_id'])): ?>
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

    <div class="form-container">
        <h2>Connexion</h2>
        <form method="POST" action="">
            <input type="text" name="login" placeholder="Login" required><br>
            <input type="password" name="password" placeholder="Mot de passe" required><br>
            <input type="submit" name="login_btn" value="Se connecter">
        </form>
    </div>
<?php else: ?>
    <h2>Modifier votre profil</h2>
    <form method="POST" action="">
        <input type="text" name="login" placeholder="Login" value="<?php echo htmlspecialchars($userInfos['login']); ?>" required><br>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($userInfos['email']); ?>" required><br>
        <input type="text" name="firstname" placeholder="Prénom" value="<?php echo htmlspecialchars($userInfos['firstname']); ?>" required><br>
        <input type="text" name="lastname" placeholder="Nom" value="<?php echo htmlspecialchars($userInfos['lastname']); ?>" required><br>
        <input type="submit" name="update_profile" value="Mettre à jour le profil">
    </form>

    <h2>Liste des utilisateurs</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Login</th>
            <th>Email</th>
            <th>Prénom</th>
            <th>Nom</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?php echo $u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['login']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><?php echo htmlspecialchars($u['firstname']); ?></td>
            <td><?php echo htmlspecialchars($u['lastname']); ?></td>
            <td>
                <?php if ($u['id'] == $_SESSION['user_id']): // Permettre la suppression uniquement pour l'utilisateur connecté ?>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                    <input type="submit" name="delete_user" value="Supprimer">
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="logout.php">Déconnexion</a>
<?php endif; ?>

<p><?php echo $message; ?></p>

</body>
</html>
