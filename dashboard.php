<?php
require_once "config/auth.php";
require_once "config/database.php";
require_once "config/fonctions.php";

$totalPatients = (int)$connexion->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$totalConsultations = (int)$connexion->query("SELECT COUNT(*) FROM consultations")->fetchColumn();
$totalPersonnel = (int)$connexion->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$totalDossiers = (int)$connexion->query("SELECT COUNT(*) FROM dossiers_medicaux")->fetchColumn();

$dernières = $connexion->query(
    "SELECT c.date_consultation, c.motif, c.diagnostic, p.nom, p.prenom, u.nom AS nom_medecin, u.prenom AS prenom_medecin
     FROM consultations c
     JOIN dossiers_medicaux d ON d.id_dossier=c.id_dossier
     JOIN patients p ON p.id_patient=d.id_patient
     JOIN utilisateurs u ON u.id_utilisateur=c.id_utilisateur
     ORDER BY c.date_consultation DESC LIMIT 6"
)->fetchAll();
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Tableau de bord | Ivoirmed</title><link rel="stylesheet" href="css/style.css"></head><body>
<div class="interface">
<?php require "config/menu.php"; ?>
<main class="contenu">
<header class="topbar"><div><h1>Tableau de bord</h1><p>Vue générale de votre activité médicale.</p></div><div class="profil"><div class="avatar"><?= e(mb_substr($_SESSION["utilisateur"]["prenom"],0,1)) ?></div><div><strong><?= e($nomAffiche) ?></strong><p><?= e($roleAffiche) ?></p></div></div></header>
<section class="statistiques">
<div class="carte-statistique"><h3>Patients</h3><p class="nombre"><?= $totalPatients ?></p><span>Patients enregistrés</span></div>
<div class="carte-statistique"><h3>Consultations</h3><p class="nombre"><?= $totalConsultations ?></p><span>Consultations enregistrées</span></div>
<div class="carte-statistique"><h3>Personnel</h3><p class="nombre"><?= $totalPersonnel ?></p><span>Comptes utilisateurs</span></div>
<div class="carte-statistique"><h3>Dossiers médicaux</h3><p class="nombre"><?= $totalDossiers ?></p><span>Dossiers créés</span></div>
</section>
<section class="panel"><div class="section-heading"><div><h2>Dernières consultations</h2><p>Les consultations les plus récentes.</p></div><a class="btn" href="activites.php">Voir les activités</a></div>
<div class="table-container"><table><thead><tr><th>Date</th><th>Patient</th><th>Motif</th><th>Diagnostic</th><th>Médecin</th></tr></thead><tbody>
<?php if (!$dernières): ?><tr><td colspan="5" class="empty">Aucune consultation enregistrée.</td></tr><?php else: foreach($dernières as $ligne): ?>
<tr><td><?= e(date("d/m/Y H:i", strtotime($ligne["date_consultation"]))) ?></td><td><?= e($ligne["prenom"]." ".$ligne["nom"]) ?></td><td><?= e($ligne["motif"]) ?></td><td><?= e($ligne["diagnostic"]) ?></td><td><?= e($ligne["prenom_medecin"]." ".$ligne["nom_medecin"]) ?></td></tr>
<?php endforeach; endif; ?>
</tbody></table></div></section>
</main></div></body></html>
