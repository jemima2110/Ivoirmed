<?php
require_once "config/auth.php";
require_once "config/database.php";
require_once "config/fonctions.php";
if ($_SESSION["utilisateur"]["libelle_role"] !== "Administrateur") { http_response_code(403); die("Accès réservé à l'administrateur."); }
$message=""; $erreur="";
$idEtab=(int)$_SESSION["utilisateur"]["id_etablissement"];
$stmt=$connexion->prepare("SELECT * FROM etablissements WHERE id_etablissement=?"); $stmt->execute([$idEtab]); $etablissement=$stmt->fetch();
if($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["modifier_etablissement"])) {
    $stmt=$connexion->prepare("UPDATE etablissements SET nom_etablissement=?,type_etablissement=?,adresse=?,telephone=?,email=? WHERE id_etablissement=?");
    $stmt->execute([trim($_POST["nom_etablissement"]),$_POST["type_etablissement"]?:null,trim($_POST["adresse"]??""),trim($_POST["telephone"]??""),trim($_POST["email"]??""),$idEtab]);
    $message="Informations de l'établissement mises à jour.";
    $stmt=$connexion->prepare("SELECT * FROM etablissements WHERE id_etablissement=?"); $stmt->execute([$idEtab]); $etablissement=$stmt->fetch();
}
if($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["modifier_mot_de_passe"])) {
    $ancien=$_POST["ancien_mot_de_passe"]??""; $nouveau=$_POST["nouveau_mot_de_passe"]??""; $confirmation=$_POST["confirmation"]??"";
    $stmt=$connexion->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id_utilisateur=?"); $stmt->execute([$_SESSION["utilisateur"]["id_utilisateur"]]); $hash=$stmt->fetchColumn();
    if(!password_verify($ancien,$hash)) $erreur="Le mot de passe actuel est incorrect.";
    elseif(strlen($nouveau)<8) $erreur="Le nouveau mot de passe doit contenir au moins 8 caractères.";
    elseif($nouveau!==$confirmation) $erreur="Les deux nouveaux mots de passe ne correspondent pas.";
    else { $stmt=$connexion->prepare("UPDATE utilisateurs SET mot_de_passe=? WHERE id_utilisateur=?"); $stmt->execute([password_hash($nouveau,PASSWORD_DEFAULT),$_SESSION["utilisateur"]["id_utilisateur"]]); $message="Mot de passe modifié avec succès."; }
}
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Paramètres | Ivoirmed</title><link rel="stylesheet" href="css/style.css"></head><body><div class="interface">
<?php require "config/menu.php"; ?><main class="contenu">
<header class="topbar"><div><h1>Paramètres</h1><p>Configuration de votre espace Ivoirmed.</p></div><div class="profil"><div class="avatar">A</div><div><strong><?= e($nomAffiche) ?></strong><p><?= e($roleAffiche) ?></p></div></div></header>
<?php if($message): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?><?php if($erreur): ?><div class="alert error"><?= e($erreur) ?></div><?php endif; ?>
<section class="panel"><h2>Informations de l'établissement</h2><p class="muted">Ces informations concernent l'établissement associé à ton compte.</p>
<form method="POST"><div class="form-grid">
<div class="champ"><label>Nom de l'établissement</label><input name="nom_etablissement" value="<?= e($etablissement["nom_etablissement"]) ?>" required></div>
<div class="champ"><label>Type</label><input name="type_etablissement" value="<?= e($etablissement["type_etablissement"]) ?>"></div>
<div class="champ"><label>Téléphone</label><input name="telephone" value="<?= e($etablissement["telephone"]) ?>"></div>
<div class="champ"><label>Email</label><input type="email" name="email" value="<?= e($etablissement["email"]) ?>"></div>
<div class="champ span-2"><label>Adresse</label><input name="adresse" value="<?= e($etablissement["adresse"]) ?>"></div>
</div><button class="btn" name="modifier_etablissement" value="1">Enregistrer les informations</button></form></section>
<section class="panel"><h2>Sécurité du compte</h2><p class="muted">Choisis un mot de passe d'au moins 8 caractères.</p>
<form method="POST"><div class="form-grid">
<div class="champ span-2"><label>Mot de passe actuel</label><input type="password" name="ancien_mot_de_passe" required></div>
<div class="champ"><label>Nouveau mot de passe</label><input type="password" name="nouveau_mot_de_passe" minlength="8" required></div>
<div class="champ"><label>Confirmation</label><input type="password" name="confirmation" minlength="8" required></div>
</div><button class="btn" name="modifier_mot_de_passe" value="1">Modifier le mot de passe</button></form></section>
</main></div></body></html>
