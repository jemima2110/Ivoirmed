<?php
require_once "config/auth.php";
require_once "config/database.php";
require_once "config/fonctions.php";
$message = ""; $erreur = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["ajouter_consultation"])) {
    $idDossier = (int)($_POST["id_dossier"] ?? 0);
    $idUtilisateur = (int)$_SESSION["utilisateur"]["id_utilisateur"];
    $idEtablissement = (int)$_SESSION["utilisateur"]["id_etablissement"];
    if (!$idDossier || trim($_POST["motif"] ?? "") === "") {
        $erreur = "Sélectionne un patient et renseigne le motif de consultation.";
    } else {
        $stmt = $connexion->prepare("INSERT INTO consultations (id_dossier,id_utilisateur,id_etablissement,motif,symptomes,temperature,tension_systolique,tension_diastolique,poids,taille,frequence_cardiaque,diagnostic,observations,traitement,conclusion) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $idDossier,$idUtilisateur,$idEtablissement,trim($_POST["motif"]),
            trim($_POST["symptomes"] ?? "") ?: null,
            $_POST["temperature"] !== "" ? $_POST["temperature"] : null,
            $_POST["tension_systolique"] !== "" ? $_POST["tension_systolique"] : null,
            $_POST["tension_diastolique"] !== "" ? $_POST["tension_diastolique"] : null,
            $_POST["poids"] !== "" ? $_POST["poids"] : null,
            $_POST["taille"] !== "" ? $_POST["taille"] : null,
            $_POST["frequence_cardiaque"] !== "" ? $_POST["frequence_cardiaque"] : null,
            trim($_POST["diagnostic"] ?? "") ?: null,
            trim($_POST["observations"] ?? "") ?: null,
            trim($_POST["traitement"] ?? "") ?: null,
            trim($_POST["conclusion"] ?? "") ?: null
        ]);
        $message = "Consultation enregistrée.";
    }
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["ajouter_prescription"])) {
    $stmt = $connexion->prepare("INSERT INTO prescriptions (id_consultation,id_utilisateur,nom_medicament,dosage,forme,posologie,voie_administration,frequence,duree_traitement,date_prescription) VALUES (?,?,?,?,?,?,?,?,?,CURDATE())");
    $stmt->execute([
        (int)$_POST["id_consultation"], (int)$_SESSION["utilisateur"]["id_utilisateur"],
        trim($_POST["nom_medicament"]), trim($_POST["dosage"] ?? ""),
        trim($_POST["forme"] ?? ""), trim($_POST["posologie"] ?? ""),
        trim($_POST["voie_administration"] ?? ""), trim($_POST["frequence"] ?? ""),
        trim($_POST["duree_traitement"] ?? "")
    ]);
    $message = "Prescription enregistrée.";
}
$patients = $connexion->query("SELECT d.id_dossier,p.nom,p.prenom,p.numero_identification FROM dossiers_medicaux d JOIN patients p ON p.id_patient=d.id_patient ORDER BY p.nom,p.prenom")->fetchAll();
$consultations = $connexion->query("SELECT c.id_consultation,c.date_consultation,c.motif,p.nom,p.prenom FROM consultations c JOIN dossiers_medicaux d ON d.id_dossier=c.id_dossier JOIN patients p ON p.id_patient=d.id_patient ORDER BY c.date_consultation DESC")->fetchAll();
$prescriptions = $connexion->query("SELECT pr.*, p.nom,p.prenom FROM prescriptions pr JOIN consultations c ON c.id_consultation=pr.id_consultation JOIN dossiers_medicaux d ON d.id_dossier=c.id_dossier JOIN patients p ON p.id_patient=d.id_patient ORDER BY pr.date_prescription DESC LIMIT 20")->fetchAll();
$totalConsult = (int)$connexion->query("SELECT COUNT(*) FROM consultations")->fetchColumn();
$totalPresc = (int)$connexion->query("SELECT COUNT(*) FROM prescriptions")->fetchColumn();
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Activités médicales | Ivoirmed</title><link rel="stylesheet" href="css/style.css"><script src="js/app.js" defer></script></head><body><div class="interface">
<?php require "config/menu.php"; ?><main class="contenu">
<header class="topbar"><div><h1>Activités médicales</h1><p>Suivi des consultations et prescriptions.</p></div><div class="profil"><div class="avatar"><?= e(mb_substr($_SESSION["utilisateur"]["prenom"],0,1)) ?></div><div><strong><?= e($nomAffiche) ?></strong><p><?= e($roleAffiche) ?></p></div></div></header>
<?php if($message): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?><?php if($erreur): ?><div class="alert error"><?= e($erreur) ?></div><?php endif; ?>
<section class="statistiques"><div class="carte-statistique"><h3>Consultations</h3><p class="nombre"><?= $totalConsult ?></p></div><div class="carte-statistique"><h3>Prescriptions</h3><p class="nombre"><?= $totalPresc ?></p></div></section>
<section class="panel"><div class="section-heading"><div><h2>Nouvelle consultation</h2><p>Enregistrer une consultation pour un patient existant.</p></div><button class="btn" type="button" onclick="basculer('formConsultation')">Afficher / masquer</button></div>
<form id="formConsultation" class="form-panel" method="POST"><div class="form-grid">
<div class="champ"><label>Dossier du patient *</label><select name="id_dossier" required><option value="">Choisir un patient</option><?php foreach($patients as $p): ?><option value="<?= (int)$p["id_dossier"] ?>"><?= e($p["prenom"]." ".$p["nom"]." — ".$p["numero_identification"]) ?></option><?php endforeach; ?></select></div>
<div class="champ"><label>Motif *</label><input name="motif" required></div>
<div class="champ span-2"><label>Symptômes</label><textarea name="symptomes"></textarea></div>
<div class="champ"><label>Température (°C)</label><input type="number" step="0.1" name="temperature"></div>
<div class="champ"><label>Tension systolique</label><input type="number" step="0.01" name="tension_systolique"></div>
<div class="champ"><label>Tension diastolique</label><input type="number" step="0.01" name="tension_diastolique"></div>
<div class="champ"><label>Poids (kg)</label><input type="number" step="0.01" name="poids"></div>
<div class="champ"><label>Taille (cm)</label><input type="number" step="0.01" name="taille"></div>
<div class="champ"><label>Fréquence cardiaque</label><input type="number" name="frequence_cardiaque"></div>
<div class="champ span-2"><label>Diagnostic</label><textarea name="diagnostic"></textarea></div>
<div class="champ span-2"><label>Observations</label><textarea name="observations"></textarea></div>
<div class="champ span-2"><label>Traitement</label><textarea name="traitement"></textarea></div>
<div class="champ span-2"><label>Conclusion</label><textarea name="conclusion"></textarea></div>
</div><button class="btn" name="ajouter_consultation" value="1">Enregistrer la consultation</button></form></section>
<section class="panel"><h2>Consultations récentes</h2><div class="table-container"><table><thead><tr><th>Date</th><th>Patient</th><th>Motif</th></tr></thead><tbody><?php if(!$consultations): ?><tr><td colspan="3" class="empty">Aucune consultation.</td></tr><?php else: foreach($consultations as $c): ?><tr><td><?= e(date("d/m/Y H:i",strtotime($c["date_consultation"]))) ?></td><td><?= e($c["prenom"]." ".$c["nom"]) ?></td><td><?= e($c["motif"]) ?></td></tr><?php endforeach; endif; ?></tbody></table></div></section>
<section class="panel"><div class="section-heading"><div><h2>Ajouter une prescription</h2><p>Associer un médicament à une consultation existante.</p></div><button class="btn" type="button" onclick="basculer('formPrescription')">Afficher / masquer</button></div>
<form id="formPrescription" class="form-panel hidden" method="POST"><div class="form-grid">
<div class="champ span-2"><label>Consultation *</label><select name="id_consultation" required><option value="">Choisir</option><?php foreach($consultations as $c): ?><option value="<?= (int)$c["id_consultation"] ?>">#<?= (int)$c["id_consultation"] ?> — <?= e($c["prenom"]." ".$c["nom"]." — ".$c["motif"]) ?></option><?php endforeach; ?></select></div>
<div class="champ"><label>Médicament *</label><input name="nom_medicament" required></div><div class="champ"><label>Dosage</label><input name="dosage"></div>
<div class="champ"><label>Forme</label><input name="forme" placeholder="Comprimé, sirop..."></div><div class="champ"><label>Posologie</label><input name="posologie"></div>
<div class="champ"><label>Voie d'administration</label><input name="voie_administration"></div><div class="champ"><label>Fréquence</label><input name="frequence"></div><div class="champ"><label>Durée du traitement</label><input name="duree_traitement"></div>
</div><button class="btn" name="ajouter_prescription" value="1">Enregistrer la prescription</button></form>
<div class="table-container"><table><thead><tr><th>Date</th><th>Patient</th><th>Médicament</th><th>Dosage</th><th>Posologie</th></tr></thead><tbody><?php if(!$prescriptions): ?><tr><td colspan="5" class="empty">Aucune prescription.</td></tr><?php else: foreach($prescriptions as $pr): ?><tr><td><?= e(date("d/m/Y",strtotime($pr["date_prescription"]))) ?></td><td><?= e($pr["prenom"]." ".$pr["nom"]) ?></td><td><?= e($pr["nom_medicament"]) ?></td><td><?= e($pr["dosage"]) ?></td><td><?= e($pr["posologie"]) ?></td></tr><?php endforeach; endif; ?></tbody></table></div></section>
</main></div></body></html>
