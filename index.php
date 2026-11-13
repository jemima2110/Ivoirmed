<?php
session_start();
require_once "config/database.php";

$erreur = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $mot_de_passe = $_POST["mot_de_passe"] ?? "";

    $requete = $connexion->prepare(
        "SELECT u.id_utilisateur, u.nom, u.prenom, u.email, u.mot_de_passe,
                u.statut_compte, r.libelle_role, u.id_etablissement
         FROM utilisateurs u
         INNER JOIN roles r ON r.id_role = u.id_role
         WHERE u.email = ? LIMIT 1"
    );
    $requete->execute([$email]);
    $utilisateur = $requete->fetch();

    if ($utilisateur && $utilisateur["statut_compte"] === "Actif"
        && password_verify($mot_de_passe, $utilisateur["mot_de_passe"])) {
        session_regenerate_id(true);
        unset($utilisateur["mot_de_passe"]);
        $_SESSION["utilisateur"] = $utilisateur;
        header("Location: dashboard.php");
        exit;
    }
    $erreur = "Adresse e-mail ou mot de passe incorrect, ou compte inactif.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion | Ivoirmed</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
<main class="login-card">
    <div class="brand"><h1>Ivoirmed</h1><p>La santé, mieux organisée.</p></div>
    <h2>Bienvenue</h2>
    <p class="muted">Connectez-vous à votre espace professionnel.</p>
    <?php if ($erreur): ?><div class="alert error"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>
    <form method="POST">
        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" placeholder="exemple@ivoirmed.ci" required>
        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        <button class="btn full" type="submit">Se connecter</button>
    </form>
    <p class="login-foot">© 2026 Ivoirmed - Tous droits réservés.</p>
</main>
</body>
</html>
