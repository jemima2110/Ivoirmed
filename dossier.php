<?php
require_once "config/auth.php";
require_once "config/database.php";
require_once "config/fonctions.php";
$id = (int)($_GET["id"] ?? 0);
$stmt = $connexion->prepare("SELECT p.*, d.* FROM patients p LEFT JOIN dossiers_medicaux d ON d.id_patient=p.id_patient WHERE p.id_patient=?");
$stmt->execute([$id]); $patient = $stmt->fetch();
if (!$patient) { http_response_code(404); die("Patient introuvable. <a href='patients.php'>Retour aux patients</a>"); }

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["enregistrer_dossier"])) {
    $stmt = $connexion->prepare("UPDATE dossiers_medicaux SET groupe_sanguin=?, antecedents_medicaux=?, antecedents_chirurgicaux=?, antecedents_familiaux=?, allergies=?, maladies_chroniques=?, observations_generales=?, statut_dossier=? WHERE id_patient=?");
    $stmt->execute([
        $_POST["groupe_sanguin"] ?: null, trim($_POST["antecedents_medicaux"] ?? ""),
        trim($_POST["antecedents_chirurgicaux"] ?? ""), trim($_POST["antecedents_familiaux"] ?? ""),
        trim($_POST["allergies"] ?? ""), trim($_POST["maladies_chroniques"] ?? ""),
        trim($_POST["observations_generales"] ?? ""), $_POST["statut_dossier"] ?: "Ouvert", $id
    ]);
    $message = "Dossier médical mis à jour.";
    $stmt = $connexion->prepare("SELECT p.*, d.* FROM patients p LEFT JOIN dossiers_medicaux d ON d.id_patient=p.id_patient WHERE p.id_patient=?");
    $stmt->execute([$id]); $patient = $stmt->fetch();
}
$stmt = $connexion->prepare("SELECT c.*, u.nom AS nom_medecin, u.prenom AS prenom_medecin FROM consultations c JOIN utilisateurs u ON u.id_utilisateur=c.id_utilisateur WHERE c.id_dossier=? ORDER BY c.date_consultation DESC");
$stmt->execute([$patient["id_dossier"]]); $consultations = $stmt->fetchAll();
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Dossier médical | Ivoirmed</title><link rel="stylesheet" href="css/style.css"></head><body><div class="interface">
<?php require "config/menu.php"; ?><main class="contenu">
<header class="topbar"><div><h1>Dossier médical</h1><p>Informations et historique du patient.</p></div><div class="profil"><div class="avatar"><?= e(mb_substr($_SESSION["utilisateur"]["prenom"],0,1)) ?></div><div><strong><?= e($nomAffiche) ?></strong><p><?= e($roleAffiche) ?></p></div></div></header>
<a class="text-link back-link" href="patients.php">← Retour à la liste des patients</a>
<?php if($message): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?>
<section class="panel"><div class="patient-heading"><div class="patient-avatar"><?= e(mb_substr($patient["prenom"],0,1).mb_substr($patient["nom"],0,1)) ?></div><div><h2><?= e($patient["prenom"]." ".$patient["nom"]) ?></h2><p><?= e($patient["numero_identification"]) ?> · Dossier <?= e($patient["numero_dossier"]) ?></p></div></div>
<div class="info-grid"><div><span>Date de naissance</span><strong><?= e($patient["date_naissance"] ?: "Non renseignée") ?></strong></div><div><span>Sexe</span><strong><?= e($patient["sexe"] ?: "Non renseigné") ?></strong></div><div><span>Téléphone</span><strong><?= e($patient["telephone"] ?: "Non renseigné") ?></strong></div><div><span>Email</span><strong><?= e($patient["email"] ?: "Non renseigné") ?></strong></div></div></section>
<section class="panel"><div class="section-heading"><div><h2>Informations médicales</h2><p>Les données médicales doivent être renseignées par un professionnel habilité.</p></div></div>
<form method="POST"><div class="form-grid">
<div class="champ"><label>Groupe sanguin</label><select name="groupe_sanguin"><option value="">Non renseigné</option><?php foreach(["A+","A-","B+","B-","AB+","AB-","O+","O-"] as $g): ?><option <?= $patient["groupe_sanguin"]===$g?"selected":"" ?>><?= $g ?></option><?php endforeach; ?></select></div>
<div class="champ"><label>Statut du dossier</label><select name="statut_dossier"><option <?= $patient["statut_dossier"]==="Ouvert"?"selected":"" ?>>Ouvert</option><option <?= $patient["statut_dossier"]==="Fermé"?"selected":"" ?>>Fermé</option></select></div>
<?php foreach(["antecedents_medicaux"=>"Antécédents médicaux","antecedents_chirurgicaux"=>"Antécédents chirurgicaux","antecedents_familiaux"=>"Antécédents familiaux","allergies"=>"Allergies","maladies_chroniques"=>"Maladies chroniques","observations_generales"=>"Observations générales"] as $name=>$label): ?>
<div class="champ span-2"><label><?= e($label) ?></label><textarea name="<?= e($name) ?>" rows="2"><?= e($patient[$name]) ?></textarea></div>
<?php endforeach; ?>
</div><button class="btn" name="enregistrer_dossier" value="1">Enregistrer le dossier</button></form></section>
<section class="panel"><h2>Historique des consultations</h2><div class="table-container"><table><thead><tr><th>Date</th><th>Motif</th><th>Diagnostic</th><th>Médecin</th></tr></thead><tbody>
<?php if(!$consultations): ?><tr><td colspan="4" class="empty">Aucune consultation enregistrée.</td></tr><?php else: foreach($consultations as $c): ?><tr><td><?= e(date("d/m/Y H:i",strtotime($c["date_consultation"]))) ?></td><td><?= e($c["motif"]) ?></td><td><?= e($c["diagnostic"]) ?></td><td><?= e($c["prenom_medecin"]." ".$c["nom_medecin"]) ?></td></tr><?php endforeach; endif; ?>
</tbody></table></div></section>
</main></div></body></html>
