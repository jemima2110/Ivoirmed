<?php
$nomAffiche = $_SESSION["utilisateur"]["prenom"] . " " . $_SESSION["utilisateur"]["nom"];
$roleAffiche = $_SESSION["utilisateur"]["libelle_role"];
$pageActive = basename($_SERVER["PHP_SELF"]);
?>
<aside class="sidebar">
    <div class="sidebar-logo">

    <div class="logo-marque">
        <span class="logo-coeur">♥</span>
        <span class="logo-ondes"></span>
    </div>

    <div>
        <h2>Ivoirmed</h2>
        <p>La santé, mieux organisée.</p>
    </div>

    </div>
    <nav class="menu">
        <a class="menu-item <?= $pageActive === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Tableau de bord</a>
        <a class="menu-item <?= $pageActive === 'patients.php' || $pageActive === 'dossier.php' ? 'active' : '' ?>" href="patients.php">Patients</a>
        <a class="menu-item <?= $pageActive === 'activites.php' ? 'active' : '' ?>" href="activites.php">Activités médicales</a>
        <?php if ($roleAffiche === 'Administrateur'): ?>
        <a class="menu-item <?= $pageActive === 'administration.php' ? 'active' : '' ?>" href="administration.php">Administration</a>
        <a class="menu-item <?= $pageActive === 'parametres.php' ? 'active' : '' ?>" href="parametres.php">Paramètres</a>
        <?php endif; ?>
    </nav>
    <div class="sidebar-bottom"><a class="menu-item" href="deconnexion.php">Déconnexion</a></div>
</aside>
