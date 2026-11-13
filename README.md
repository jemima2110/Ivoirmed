# Ivoirmed
IVOIRMED — version PHP/MySQL de départ

ARBORESCENCE
IVOIRMED/
  config/database.php
  config/auth.php
  config/fonctions.php
  config/menu.php
  css/style.css
  js/app.js
  index.php
  dashboard.php
  patients.php
  dossier.php
  activites.php
  administration.php
  parametres.php
  deconnexion.php
  creer_admin.php
  ivoirmed.sql

INSTALLATION
1. Copie le dossier IVOIRMED dans C:\xampp\htdocs\
2. Démarre Apache et MySQL dans XAMPP.
3. Ouvre phpMyAdmin et importe ivoirmed.sql.
   ATTENTION : le script SQL réinitialise les tables portant ces noms dans la base ivoirmed.
4. Vérifie les identifiants MySQL dans config/database.php (XAMPP par défaut : root, mot de passe vide).
5. Ouvre http://localhost/IVOIRMED/creer_admin.php et crée le premier compte administrateur.
6. Connecte-toi depuis http://localhost/IVOIRMED/
7. SUPPRIME creer_admin.php après avoir créé le compte.

FONCTIONNALITÉS
- Connexion avec password_hash/password_verify et session.
- Tableau de bord alimenté par les comptes, patients, dossiers et consultations.
- Ajout/recherche des patients et création automatique du dossier médical.
- Consultation et mise à jour d'un dossier médical.
- Saisie des consultations et prescriptions.
- Gestion des comptes utilisateurs et activation/désactivation.
- Mise à jour de l'établissement et du mot de passe.

IMPORTANT
- Le script SQL contient les 7 tables conservées : roles, etablissements, utilisateurs, patients, dossiers_medicaux, consultations, prescriptions.
- Les tables examens_medicaux et hospitalisations ne sont pas créées.
- Les données médicales affichées doivent être fictives pendant les tests. Cette base est un prototype pédagogique, pas un système prêt pour des données de santé réelles.
- Avant toute utilisation réelle : ajouter protections CSRF, journalisation, règles d'accès détaillées, sauvegardes, HTTPS, validations renforcées et audit de sécurité.
