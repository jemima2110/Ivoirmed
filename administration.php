<?php
require_once "config/auth.php";
require_once "config/database.php";
require_once "config/fonctions.php";
if ($_SESSION["utilisateur"]["libelle_role"] !== "Administrateur") { http_response_code(403); die("Accès réservé à l'administrateur."); }
$message=""; $erreur="";
if ($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["ajouter_utilisateur"])) {
    $nom=trim($_POST["nom"]??""); $prenom=trim($_POST["prenom"]??""); $email=trim($_POST["email"]??"");
    $mdp=$_POST["mot_de_passe"]??""; $idRole=(int)($_POST["id_role"]??0); $idEtab=(int)($_POST["id_etablissement"]??0);
    if(!$nom||!$prenom||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($mdp)<8||!$idRole||!$idEtab) {
        $erreur="Vérifie les champs obligatoires. Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        try {
            $stmt=$connexion->prepare("INSERT INTO utilisateurs (id_role,id_etablissement,nom,prenom,sexe,telephone,adresse,specialite,email,mot_de_passe,statut_compte) VALUES (?,?,?,?,?,?,?,?,?,?, 'Actif')");
            $stmt->execute([$idRole,$idEtab,$nom,$prenom,$_POST["sexe"]?:null,$_POST["telephone"]?:null,$_POST["adresse"]?:null,$_POST["specialite"]?:null,$email,password_hash($mdp,PASSWORD_DEFAULT)]);
            $message="Utilisateur ajouté.";
        } catch(PDOException $ex) { $erreur="Impossible d'ajouter cet utilisateur. Vérifie que son adresse e-mail n'est pas déjà utilisée."; }
    }
}
if ($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["changer_statut"])) {
    $id=(int)$_POST["id_utilisateur"];
    if($id !== (int)$_SESSION["utilisateur"]["id_utilisateur"]) {
        $stmt=$connexion->prepare("UPDATE utilisateurs SET statut_compte=IF(statut_compte='Actif','Inactif','Actif') WHERE id_utilisateur=?");
        $stmt->execute([$id]); $message="Statut du compte modifié.";
    } else { $erreur="Tu ne peux pas désactiver ton propre compte."; }
}
$roles=$connexion->query("SELECT * FROM roles ORDER BY id_role")->fetchAll();
$etablissements=$connexion->query("SELECT * FROM etablissements ORDER BY nom_etablissement")->fetchAll();
$utilisateurs=$connexion->query("SELECT u.*,r.libelle_role,e.nom_etablissement FROM utilisateurs u JOIN roles r ON r.id_role=u.id_role JOIN etablissements e ON e.id_etablissement=u.id_etablissement ORDER BY u.date_creation DESC")->fetchAll();
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Administration | Ivoirmed</title><link rel="stylesheet" href="css/style.css"></head><body><div class="interface">
<?php require "config/menu.php"; ?><main class="contenu">
<header class="topbar"><div><h1>Administration</h1><p>Gestion des utilisateurs et des établissements.</p></div><div class="profil"><div class="avatar">A</div><div><strong><?= e($nomAffiche) ?></strong><p><?= e($roleAffiche) ?></p></div></div></header>
<?php if($message): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?><?php if($erreur): ?><div class="alert error"><?= e($erreur) ?></div><?php endif; ?>
<section class="panel"><div class="section-heading"><div><h2>Ajouter un utilisateur</h2><p>Créer un compte pour un membre du personnel.</p></div></div>
<form method="POST"><div class="form-grid">
<div class="champ"><label>Nom *</label><input name="nom" required></div><div class="champ"><label>Prénom *</label><input name="prenom" required></div>
<div class="champ"><label>Email *</label><input type="email" name="email" required></div><div class="champ"><label>Mot de passe provisoire *</label><input type="password" name="mot_de_passe" minlength="8" required></div>
<div class="champ"><label>Rôle *</label><select name="id_role" required><option value="">Choisir</option><?php foreach($roles as $r): ?><option value="<?= (int)$r["id_role"] ?>"><?= e($r["libelle_role"]) ?></option><?php endforeach; ?></select></div>
<div class="champ"><label>Établissement *</label><select name="id_etablissement" required><option value="">Choisir</option><?php foreach($etablissements as $et): ?><option value="<?= (int)$et["id_etablissement"] ?>"><?= e($et["nom_etablissement"]) ?></option><?php endforeach; ?></select></div>
<div class="champ"><label>Téléphone</label><input name="telephone"></div><div class="champ"><label>Spécialité</label><input name="specialite"></div>
<div class="champ span-2"><label>Adresse</label><input name="adresse"></div>
</div><button class="btn" name="ajouter_utilisateur" value="1">Créer le compte</button></form></section>
<section class="panel"><h2>Personnel enregistré</h2><div class="table-container"><table><thead><tr><th>Nom</th><th>Rôle</th><th>Établissement</th><th>Email</th><th>Statut</th><th>Action</th></tr></thead><tbody>
<?php foreach($utilisateurs as $u): ?><tr><td><?= e($u["prenom"]." ".$u["nom"]) ?></td><td><?= e($u["libelle_role"]) ?></td><td><?= e($u["nom_etablissement"]) ?></td><td><?= e($u["email"]) ?></td><td><?= e($u["statut_compte"]) ?></td><td><?php if((int)$u["id_utilisateur"] !== (int)$_SESSION["utilisateur"]["id_utilisateur"]): ?><form method="POST" class="inline-form"><input type="hidden" name="id_utilisateur" value="<?= (int)$u["id_utilisateur"] ?>"><button class="btn small secondary" name="changer_statut" value="1">Activer / désactiver</button></form><?php else: ?>Compte actuel<?php endif; ?></td></tr><?php endforeach; ?>
</tbody></table></div></section>
<section class="panel"><h2>Établissements enregistrés</h2><div class="table-container"><table><thead><tr><th>Nom</th><th>Type</th><th>Adresse</th><th>Téléphone</th><th>Statut</th></tr></thead><tbody><?php foreach($etablissements as $et): ?><tr><td><?= e($et["nom_etablissement"]) ?></td><td><?= e($et["type_etablissement"]) ?></td><td><?= e($et["adresse"]) ?></td><td><?= e($et["telephone"]) ?></td><td><?= e($et["statut"]) ?></td></tr><?php endforeach; ?></tbody></table></div></section>
</main></div></body></html>
