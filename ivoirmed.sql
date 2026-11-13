CREATE DATABASE IF NOT EXISTS ivoirmed
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ivoirmed;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS prescriptions;
DROP TABLE IF EXISTS consultations;
DROP TABLE IF EXISTS dossiers_medicaux;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS utilisateurs;
DROP TABLE IF EXISTS etablissements;
DROP TABLE IF EXISTS roles;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE roles (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    libelle_role VARCHAR(50) NOT NULL,
    description VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE etablissements (
    id_etablissement INT AUTO_INCREMENT PRIMARY KEY,
    nom_etablissement VARCHAR(150) NOT NULL,
    type_etablissement VARCHAR(100) DEFAULT NULL,
    adresse VARCHAR(255) DEFAULT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    statut VARCHAR(30) DEFAULT 'Actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    id_role INT NOT NULL,
    id_etablissement INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    sexe VARCHAR(20) DEFAULT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    adresse VARCHAR(255) DEFAULT NULL,
    specialite VARCHAR(100) DEFAULT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    statut_compte VARCHAR(30) DEFAULT 'Actif',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_utilisateur_role FOREIGN KEY (id_role) REFERENCES roles(id_role),
    CONSTRAINT fk_utilisateur_etablissement FOREIGN KEY (id_etablissement) REFERENCES etablissements(id_etablissement)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE patients (
    id_patient INT AUTO_INCREMENT PRIMARY KEY,
    numero_identification VARCHAR(50) NOT NULL UNIQUE,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE DEFAULT NULL,
    sexe VARCHAR(20) DEFAULT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    adresse VARCHAR(255) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    contact_urgence VARCHAR(100) DEFAULT NULL,
    date_enregistrement TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE dossiers_medicaux (
    id_dossier INT AUTO_INCREMENT PRIMARY KEY,
    id_patient INT NOT NULL UNIQUE,
    numero_dossier VARCHAR(50) NOT NULL UNIQUE,
    date_creation DATE NOT NULL,
    groupe_sanguin VARCHAR(5) DEFAULT NULL,
    antecedents_medicaux VARCHAR(1000) DEFAULT NULL,
    antecedents_chirurgicaux VARCHAR(1000) DEFAULT NULL,
    antecedents_familiaux VARCHAR(1000) DEFAULT NULL,
    allergies VARCHAR(1000) DEFAULT NULL,
    maladies_chroniques VARCHAR(1000) DEFAULT NULL,
    observations_generales VARCHAR(1000) DEFAULT NULL,
    statut_dossier VARCHAR(30) DEFAULT 'Ouvert',
    CONSTRAINT fk_dossier_patient FOREIGN KEY (id_patient) REFERENCES patients(id_patient)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE consultations (
    id_consultation INT AUTO_INCREMENT PRIMARY KEY,
    id_dossier INT NOT NULL,
    id_utilisateur INT NOT NULL,
    id_etablissement INT NOT NULL,
    date_consultation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    motif VARCHAR(255) DEFAULT NULL,
    symptomes VARCHAR(1000) DEFAULT NULL,
    temperature DECIMAL(4,1) DEFAULT NULL,
    tension_systolique DECIMAL(5,2) DEFAULT NULL,
    tension_diastolique DECIMAL(5,2) DEFAULT NULL,
    poids DECIMAL(5,2) DEFAULT NULL,
    taille DECIMAL(5,2) DEFAULT NULL,
    frequence_cardiaque INT DEFAULT NULL,
    diagnostic VARCHAR(1000) DEFAULT NULL,
    observations VARCHAR(1000) DEFAULT NULL,
    traitement VARCHAR(1000) DEFAULT NULL,
    conclusion VARCHAR(1000) DEFAULT NULL,
    CONSTRAINT fk_consultation_dossier FOREIGN KEY (id_dossier) REFERENCES dossiers_medicaux(id_dossier),
    CONSTRAINT fk_consultation_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur),
    CONSTRAINT fk_consultation_etablissement FOREIGN KEY (id_etablissement) REFERENCES etablissements(id_etablissement)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prescriptions (
    id_prescription INT AUTO_INCREMENT PRIMARY KEY,
    id_consultation INT NOT NULL,
    id_utilisateur INT NOT NULL,
    nom_medicament VARCHAR(150) NOT NULL,
    dosage VARCHAR(100) DEFAULT NULL,
    forme VARCHAR(100) DEFAULT NULL,
    posologie VARCHAR(255) DEFAULT NULL,
    voie_administration VARCHAR(100) DEFAULT NULL,
    frequence VARCHAR(100) DEFAULT NULL,
    duree_traitement VARCHAR(100) DEFAULT NULL,
    date_prescription DATE NOT NULL,
    CONSTRAINT fk_prescription_consultation FOREIGN KEY (id_consultation) REFERENCES consultations(id_consultation),
    CONSTRAINT fk_prescription_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO roles (libelle_role, description) VALUES
('Administrateur', 'Gestion complète de la plateforme'),
('Médecin', 'Consultation et suivi médical'),
('Infirmier', 'Suivi des soins'),
('Secrétaire', 'Gestion administrative des patients');

INSERT INTO etablissements (nom_etablissement, type_etablissement, adresse, statut)
VALUES ('Établissement principal', 'Centre médical', 'Côte d’Ivoire', 'Actif');
