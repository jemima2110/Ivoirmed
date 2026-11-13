<?php
require_once "config/auth.php";
require_once "config/database.php";
require_once "config/fonctions.php";

$message = "";
$erreur = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["ajouter_patient"])) {
    $nom = trim($_POST["nom"] ?? "");
    $prenom = trim($_POST["prenom"] ?? "");
    if ($nom === "" || $prenom === "") {
        $erreur = "Le nom et le prénom sont obligatoires.";
    } else {
        try {
            $connexion->beginTransaction();
            $numero = "IVO-" . date("ymd") . "-" . strtoupper(bin2hex(random_bytes(3)));
            $stmt = $connexion->prepare("INSERT INTO patients (numero_identification, nom, prenom, date_naissance, sexe, telephone, adresse, email, contact_urgence) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $numero, $nom, $prenom,
                $_POST["date_naissance"] ?: null,
                $_POST["sexe"] ?: null,
                trim($_POST["telephone"] ?? "") ?: null,
                trim($_POST["adresse"] ?? "") ?: null,
                trim($_POST["email"] ?? "") ?: null,
                trim($_POST["contact_urgence"] ?? "") ?: null
            ]);
            $idPatient = (int)$connexion->lastInsertId();
            $numeroDossier = "DOS-" . date("ymd") . "-" . $idPatient;
            $stmt = $connexion->prepare("INSERT INTO dossiers_medicaux (id_patient, numero_dossier, date_creation, statut_dossier) VALUES (?, ?, CURDATE(), 'Ouvert')");
            $stmt->execute([$idPatient, $numeroDossier]);
            $connexion->commit();
            $message = "Patient et dossier médical créés avec succès.";
        } catch (Throwable $ex) {
            if ($connexion->inTransaction()) $connexion->rollBack();
            $erreur = "Une erreur est survenue lors de l'enregistrement. Vérifie les informations.";
        }
    }
}
$recherche = trim($_GET["q"] ?? "");
$sql = "SELECT p.*, d.id_dossier FROM patients p LEFT JOIN dossiers_medicaux d ON d.id_patient=p.id_patient";
$params = [];
if ($recherche !== "") {
    $sql .= " WHERE p.nom LIKE ? OR p.prenom LIKE ? OR p.numero_identification LIKE ? OR p.telephone LIKE ?";
    $mot = "%".$recherche."%";
    $params = [$mot,$mot,$mot,$mot];
}
$sql .= " ORDER BY p.date_enregistrement DESC";
$stmt = $connexion->prepare($sql); $stmt->execute($params); $patients = $stmt->fetchAll();
$total = (int)$connexion->query("SELECT COUNT(*) FROM patients")->fetchColumn();
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Patients | Ivoirmed</title><link rel="stylesheet" href="css/style.css"><script src="js/app.js" defer></script></head><body><div class="interface">
<?php require "config/menu.php"; ?>
<main class="contenu">
<header class="topbar"><div><h1>Patients</h1><p>Enregistrement et suivi des patients.</p></div><div class="profil"><div class="avatar"><?= e(mb_substr($_SESSION["utilisateur"]["prenom"],0,1)) ?></div><div><strong><?= e($nomAffiche) ?></strong><p><?= e($roleAffiche) ?></p></div></div></header>
<?php if($message): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?><?php if($erreur): ?><div class="alert error"><?= e($erreur) ?></div><?php endif; ?>
<section class="statistiques"><div class="carte-statistique"><h3>Total des patients</h3><p class="nombre"><?= $total ?></p><span>Patients enregistrés</span></div></section>
<section class="panel">
<div class="section-heading"><div><h2>Liste des patients</h2><p>Recherchez ou ajoutez un patient.</p></div><button class="btn" type="button" onclick="afficherFormulaire()">+ Ajouter un patient</button></div>
<form class="search-form" method="GET"><input type="search" name="q" value="<?= e($recherche) ?>" placeholder="Rechercher par nom, numéro ou téléphone"><button class="btn" type="submit">Rechercher</button><?php if($recherche): ?><a class="btn secondary" href="patients.php">Réinitialiser</a><?php endif; ?></form>
<form id="formulairePatient" class="form-panel hidden" method="POST">
<h3>Nouveau patient</h3><div class="form-grid">
<div class="champ"><label>Nom *</label><input name="nom" required></div>
<div class="champ"><label>Prénom *</label><input name="prenom" required></div>
<div class="champ"><label>Date de naissance</label><input type="date" name="date_naissance"></div>
<div class="champ"><label>Sexe</label><select name="sexe"><option value="">Sélectionner</option><option>Féminin</option><option>Masculin</option><option>Autre</option></select></div>
<div class="champ"><label>Téléphone</label><input name="telephone" type="tel"></div>
<div class="champ"><label>Email</label><input name="email" type="email"></div>
<div class="champ"><label>Contact d'urgence</label><input name="contact_urgence"></div>
<div class="champ"><label>Adresse</label><input name="adresse"></div>
</div><div class="form-actions"><button class="btn" name="ajouter_patient" value="1">Enregistrer</button><button class="btn secondary" type="button" onclick="masquerFormulaire()">Annuler</button></div>
</form>
<div class="table-container"><table><thead><tr><th>N° identification</th><th>Nom et prénom</th><th>Date de naissance</th><th>Téléphone</th><th>Date d'enregistrement</th><th>Dossier</th></tr></thead><tbody>
<?php if(!$patients): ?><tr><td colspan="6" class="empty">Aucun patient trouvé.</td></tr><?php else: foreach($patients as $p): ?>
<tr><td><?= e($p["numero_identification"]) ?></td><td><?= e($p["prenom"]." ".$p["nom"]) ?></td><td><?= e($p["date_naissance"] ? date("d/m/Y",strtotime($p["date_naissance"])) : "—") ?></td><td><?= e($p["telephone"]) ?></td><td><?= e(date("d/m/Y",strtotime($p["date_enregistrement"]))) ?></td><td><?php if($p["id_dossier"]): ?><a class="text-link" href="dossier.php?id=<?= (int)$p["id_patient"] ?>">Consulter</a><?php else: ?>Non créé<?php endif; ?></td></tr>
<?php endforeach; endif; ?>
</tbody></table></div>
</section></main></div></body></html>
