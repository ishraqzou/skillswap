-- =====================================================================
-- SkillSwap — Script SQL de création de la base de données
-- Tâche 14 : Base de données SQL
-- Moteur cible : MySQL 8.0+ (compatible XAMPP)
-- Jeu de caractères : utf8mb4 / utf8mb4_unicode_ci
-- Cohérent avec : Tâche 11 (MCD), Tâche 12 (Schéma relationnel),
--                 Tâche 13 (Dictionnaire de données)
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS skillswap;
CREATE DATABASE skillswap
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE skillswap;

-- =====================================================================
-- SECTION 1 : COMPTES, COMPÉTENCES ET ÉCHANGES
-- =====================================================================

-- ---------------------------------------------------------------------
-- Table : compte
-- Table racine de la hiérarchie d'authentification (spécialisation
-- totale et disjointe en utilisateur / administrateur).
-- ---------------------------------------------------------------------
CREATE TABLE compte (
    idCompte        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(150)  NOT NULL,
    motDePasseHash  VARCHAR(255)  NOT NULL,
    dateCreation    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut          VARCHAR(20)   NOT NULL DEFAULT 'ACTIF',
    typeCompte      ENUM('UTILISATEUR','ADMIN') NOT NULL,
    CONSTRAINT uq_compte_email UNIQUE (email),
    CONSTRAINT chk_compte_statut CHECK (statut IN ('ACTIF','SUSPENDU','SUPPRIME'))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : utilisateur (sous-type de compte)
-- ---------------------------------------------------------------------
CREATE TABLE utilisateur (
    idCompte          INT UNSIGNED PRIMARY KEY,
    nom               VARCHAR(80)   NOT NULL,
    prenom            VARCHAR(80)   NOT NULL,
    photoProfil       VARCHAR(255)  NULL,
    biographie        TEXT          NULL,
    ville             VARCHAR(100)  NULL,
    languesParlees    VARCHAR(150)  NULL,
    soldePoints       INT           NOT NULL DEFAULT 0,
    noteMoyenne       DECIMAL(3,2)  NULL,
    visibiliteProfil  VARCHAR(20)   NOT NULL DEFAULT 'PUBLIQUE',
    CONSTRAINT fk_utilisateur_compte FOREIGN KEY (idCompte)
        REFERENCES compte(idCompte) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT chk_utilisateur_solde CHECK (soldePoints >= 0),
    CONSTRAINT chk_utilisateur_visibilite CHECK (visibiliteProfil IN ('PUBLIQUE','RESTREINTE')),
    CONSTRAINT chk_utilisateur_note CHECK (noteMoyenne IS NULL OR (noteMoyenne >= 0 AND noteMoyenne <= 5))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : administrateur (sous-type de compte)
-- ---------------------------------------------------------------------
CREATE TABLE administrateur (
    idCompte     INT UNSIGNED PRIMARY KEY,
    nomComplet   VARCHAR(150) NOT NULL,
    niveauAcces  VARCHAR(30)  NOT NULL DEFAULT 'STANDARD',
    CONSTRAINT fk_administrateur_compte FOREIGN KEY (idCompte)
        REFERENCES compte(idCompte) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT chk_administrateur_niveau CHECK (niveauAcces IN ('STANDARD','SUPERVISEUR'))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : categorie
-- ---------------------------------------------------------------------
CREATE TABLE categorie (
    idCategorie  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom          VARCHAR(80) NOT NULL,
    description  TEXT NULL,
    CONSTRAINT uq_categorie_nom UNIQUE (nom)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : competence
-- ---------------------------------------------------------------------
CREATE TABLE competence (
    idCompetence  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre         VARCHAR(150) NOT NULL,
    description   TEXT         NOT NULL,
    type          ENUM('OFFERTE','DEMANDEE') NOT NULL,
    niveau        VARCHAR(30)  NULL,
    motsCles      VARCHAR(255) NULL,
    statut        VARCHAR(20)  NOT NULL DEFAULT 'ACTIVE',
    dateCreation  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    idCompte      INT UNSIGNED NOT NULL,
    idCategorie   INT UNSIGNED NOT NULL,
    CONSTRAINT fk_competence_utilisateur FOREIGN KEY (idCompte)
        REFERENCES utilisateur(idCompte) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_competence_categorie FOREIGN KEY (idCategorie)
        REFERENCES categorie(idCategorie) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_competence_statut CHECK (statut IN ('ACTIVE','EN_PAUSE','ARCHIVEE')),
    CONSTRAINT chk_competence_description CHECK (CHAR_LENGTH(description) >= 20),
    INDEX idx_competence_statut (statut),
    INDEX idx_competence_type (type),
    FULLTEXT INDEX ftx_competence_recherche (titre, description, motsCles)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : demande_echange
-- ---------------------------------------------------------------------
CREATE TABLE demande_echange (
    idDemande              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dateCreation           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut                 VARCHAR(20)  NOT NULL DEFAULT 'EN_ATTENTE',
    dateReponse            DATETIME     NULL,
    idEmetteur             INT UNSIGNED NOT NULL,
    idDestinataire         INT UNSIGNED NOT NULL,
    idCompetenceProposee   INT UNSIGNED NOT NULL,
    idCompetenceSouhaitee  INT UNSIGNED NOT NULL,
    CONSTRAINT fk_demande_emetteur FOREIGN KEY (idEmetteur)
        REFERENCES utilisateur(idCompte) ON DELETE CASCADE ON UPDATE RESTRICT,
    CONSTRAINT fk_demande_destinataire FOREIGN KEY (idDestinataire)
        REFERENCES utilisateur(idCompte) ON DELETE CASCADE ON UPDATE RESTRICT,
    CONSTRAINT fk_demande_competence_proposee FOREIGN KEY (idCompetenceProposee)
        REFERENCES competence(idCompetence) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_demande_competence_souhaitee FOREIGN KEY (idCompetenceSouhaitee)
        REFERENCES competence(idCompetence) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_demande_statut CHECK (statut IN ('EN_ATTENTE','ACCEPTEE','REFUSEE','ANNULEE','EXPIREE')),
    CONSTRAINT chk_demande_utilisateurs_distincts CHECK (idEmetteur <> idDestinataire),
    INDEX idx_demande_statut (statut)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : seance
-- ---------------------------------------------------------------------
CREATE TABLE seance (
    idSeance         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dateHeure        DATETIME     NULL,
    duree            INT          NULL,
    statut           VARCHAR(20)  NOT NULL DEFAULT 'PROPOSEE',
    lieuModalite     VARCHAR(150) NULL,
    idDemande        INT UNSIGNED NOT NULL,
    idParticipantA   INT UNSIGNED NOT NULL,
    idParticipantB   INT UNSIGNED NOT NULL,
    CONSTRAINT fk_seance_demande FOREIGN KEY (idDemande)
        REFERENCES demande_echange(idDemande) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_seance_participantA FOREIGN KEY (idParticipantA)
        REFERENCES utilisateur(idCompte) ON DELETE CASCADE ON UPDATE RESTRICT,
    CONSTRAINT fk_seance_participantB FOREIGN KEY (idParticipantB)
        REFERENCES utilisateur(idCompte) ON DELETE CASCADE ON UPDATE RESTRICT,
    CONSTRAINT uq_seance_demande UNIQUE (idDemande),
    CONSTRAINT chk_seance_statut CHECK (statut IN ('PROPOSEE','CONFIRMEE','EN_COURS','TERMINEE','ANNULEE')),
    CONSTRAINT chk_seance_participants_distincts CHECK (idParticipantA <> idParticipantB),
    CONSTRAINT chk_seance_duree CHECK (duree IS NULL OR duree > 0),
    INDEX idx_seance_statut (statut),
    INDEX idx_seance_dateheure (dateHeure)
) ENGINE=InnoDB;

-- =====================================================================
-- SECTION 2 : AVIS, POINTS, BADGES ET CERTIFICATS
-- =====================================================================

-- ---------------------------------------------------------------------
-- Table : avis
-- ---------------------------------------------------------------------
CREATE TABLE avis (
    idAvis        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    note          TINYINT UNSIGNED NOT NULL,
    commentaire   TEXT NULL,
    dateCreation  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut        VARCHAR(20) NOT NULL DEFAULT 'VISIBLE',
    idSeance      INT UNSIGNED NOT NULL,
    idAuteur      INT UNSIGNED NOT NULL,
    idEvalue      INT UNSIGNED NOT NULL,
    CONSTRAINT fk_avis_seance FOREIGN KEY (idSeance)
        REFERENCES seance(idSeance) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_avis_auteur FOREIGN KEY (idAuteur)
        REFERENCES utilisateur(idCompte) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT fk_avis_evalue FOREIGN KEY (idEvalue)
        REFERENCES utilisateur(idCompte) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT uq_avis_seance_auteur UNIQUE (idSeance, idAuteur),
    CONSTRAINT chk_avis_note CHECK (note BETWEEN 1 AND 5),
    CONSTRAINT chk_avis_statut CHECK (statut IN ('VISIBLE','RETIRE')),
    CONSTRAINT chk_avis_utilisateurs_distincts CHECK (idAuteur <> idEvalue)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : badge
-- ---------------------------------------------------------------------
CREATE TABLE badge (
    idBadge      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom          VARCHAR(80) NOT NULL,
    description  TEXT NULL,
    seuilPoints  INT NOT NULL,
    icone        VARCHAR(255) NULL,
    CONSTRAINT uq_badge_nom UNIQUE (nom),
    CONSTRAINT chk_badge_seuil CHECK (seuilPoints > 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : point (historique des points)
-- ---------------------------------------------------------------------
CREATE TABLE point (
    idPoint          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    montant          INT NOT NULL,
    type             ENUM('ATTRIBUTION','AJUSTEMENT') NOT NULL,
    dateCreation     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    motif            VARCHAR(255) NULL,
    idUtilisateur    INT UNSIGNED NOT NULL,
    idSeance         INT UNSIGNED NULL,
    idAdministrateur INT UNSIGNED NULL,
    CONSTRAINT fk_point_utilisateur FOREIGN KEY (idUtilisateur)
        REFERENCES utilisateur(idCompte) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_point_seance FOREIGN KEY (idSeance)
        REFERENCES seance(idSeance) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_point_administrateur FOREIGN KEY (idAdministrateur)
        REFERENCES administrateur(idCompte) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT chk_point_motif_ajustement CHECK (type <> 'AJUSTEMENT' OR motif IS NOT NULL),
    INDEX idx_point_utilisateur (idUtilisateur)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : utilisateur_badge (table de jonction, association OBTENIR)
-- ---------------------------------------------------------------------
CREATE TABLE utilisateur_badge (
    idCompte       INT UNSIGNED NOT NULL,
    idBadge        INT UNSIGNED NOT NULL,
    dateObtention  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idCompte, idBadge),
    CONSTRAINT fk_utilisateurbadge_utilisateur FOREIGN KEY (idCompte)
        REFERENCES utilisateur(idCompte) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_utilisateurbadge_badge FOREIGN KEY (idBadge)
        REFERENCES badge(idBadge) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : certificat
-- ---------------------------------------------------------------------
CREATE TABLE certificat (
    idCertificat           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    identifiantUnique      VARCHAR(60)  NOT NULL,
    competenceAcquise      VARCHAR(150) NOT NULL,
    dateDelivrance         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    codeVerification       VARCHAR(60)  NOT NULL,
    signatureNumerique     VARCHAR(255) NOT NULL,
    statut                 VARCHAR(20)  NOT NULL DEFAULT 'ACTIF',
    idSeance               INT UNSIGNED NOT NULL,
    idApprenant            INT UNSIGNED NOT NULL,
    idFormateur            INT UNSIGNED NOT NULL,
    idAdministrateur       INT UNSIGNED NULL,
    idCertificatPrecedent  INT UNSIGNED NULL,
    CONSTRAINT uq_certificat_identifiant UNIQUE (identifiantUnique),
    CONSTRAINT uq_certificat_code UNIQUE (codeVerification),
    CONSTRAINT fk_certificat_seance FOREIGN KEY (idSeance)
        REFERENCES seance(idSeance) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_certificat_apprenant FOREIGN KEY (idApprenant)
        REFERENCES utilisateur(idCompte) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT fk_certificat_formateur FOREIGN KEY (idFormateur)
        REFERENCES utilisateur(idCompte) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT fk_certificat_administrateur FOREIGN KEY (idAdministrateur)
        REFERENCES administrateur(idCompte) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_certificat_precedent FOREIGN KEY (idCertificatPrecedent)
        REFERENCES certificat(idCertificat) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT chk_certificat_statut CHECK (statut IN ('ACTIF','REVOQUE','REMPLACE')),
    CONSTRAINT chk_certificat_utilisateurs_distincts CHECK (idApprenant <> idFormateur),
    INDEX idx_certificat_statut (statut)
) ENGINE=InnoDB;

-- =====================================================================
-- SECTION 3 : COMMUNICATION ET MODÉRATION
-- =====================================================================

-- ---------------------------------------------------------------------
-- Table : notification
-- ---------------------------------------------------------------------
CREATE TABLE notification (
    idNotification  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type            VARCHAR(50)  NOT NULL,
    contenu         VARCHAR(255) NOT NULL,
    dateCreation    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statutLecture   BOOLEAN      NOT NULL DEFAULT FALSE,
    canal           ENUM('INTERNE','EMAIL') NOT NULL,
    idDestinataire  INT UNSIGNED NOT NULL,
    CONSTRAINT fk_notification_destinataire FOREIGN KEY (idDestinataire)
        REFERENCES utilisateur(idCompte) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_notification_destinataire_lecture (idDestinataire, statutLecture)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : conversation
-- ---------------------------------------------------------------------
CREATE TABLE conversation (
    idConversation  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dateCreation    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    idParticipantA  INT UNSIGNED NOT NULL,
    idParticipantB  INT UNSIGNED NOT NULL,
    idDemande       INT UNSIGNED NULL,
    CONSTRAINT fk_conversation_participantA FOREIGN KEY (idParticipantA)
        REFERENCES utilisateur(idCompte) ON DELETE CASCADE ON UPDATE RESTRICT,
    CONSTRAINT fk_conversation_participantB FOREIGN KEY (idParticipantB)
        REFERENCES utilisateur(idCompte) ON DELETE CASCADE ON UPDATE RESTRICT,
    CONSTRAINT fk_conversation_demande FOREIGN KEY (idDemande)
        REFERENCES demande_echange(idDemande) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT uq_conversation_demande UNIQUE (idDemande),
    CONSTRAINT chk_conversation_participants_distincts CHECK (idParticipantA <> idParticipantB)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : message
-- ---------------------------------------------------------------------
CREATE TABLE message (
    idMessage       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    contenu         TEXT NOT NULL,
    dateEnvoi       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statutLecture   BOOLEAN NOT NULL DEFAULT FALSE,
    idConversation  INT UNSIGNED NOT NULL,
    idExpediteur    INT UNSIGNED NOT NULL,
    CONSTRAINT fk_message_conversation FOREIGN KEY (idConversation)
        REFERENCES conversation(idConversation) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_message_expediteur FOREIGN KEY (idExpediteur)
        REFERENCES utilisateur(idCompte) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_message_conversation (idConversation, dateEnvoi)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : signalement
-- ---------------------------------------------------------------------
CREATE TABLE signalement (
    idSignalement     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    motif             VARCHAR(255) NOT NULL,
    dateCreation      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut            VARCHAR(20) NOT NULL DEFAULT 'EN_ATTENTE',
    decision          VARCHAR(255) NULL,
    dateTraitement    DATETIME NULL,
    cibleType         VARCHAR(30) NOT NULL,
    cibleId           INT UNSIGNED NOT NULL,
    idAuteur          INT UNSIGNED NOT NULL,
    idAdministrateur  INT UNSIGNED NULL,
    CONSTRAINT fk_signalement_auteur FOREIGN KEY (idAuteur)
        REFERENCES utilisateur(idCompte) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_signalement_administrateur FOREIGN KEY (idAdministrateur)
        REFERENCES administrateur(idCompte) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT chk_signalement_statut CHECK (statut IN ('EN_ATTENTE','TRAITE','CLOTURE')),
    CONSTRAINT chk_signalement_cible CHECK (cibleType IN ('COMPETENCE','AVIS','MESSAGE','PROFIL')),
    INDEX idx_signalement_statut (statut)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- SECTION 4 : DONNÉES DE RÉFÉRENCE (SEED DATA)
-- =====================================================================

INSERT INTO categorie (nom, description) VALUES
('Langues', 'Apprentissage et pratique des langues étrangères ou régionales.'),
('Informatique', 'Programmation, bureautique, outils numériques et développement web.'),
('Artisanat', 'Savoir-faire manuels : couture, menuiserie, poterie, bricolage.'),
('Musique', 'Instruments de musique, chant, théorie musicale et production.'),
('Cuisine', 'Techniques culinaires, pâtisserie et cuisines du monde.'),
('Sport et bien-être', 'Activités physiques, fitness, yoga et bien-être.'),
('Gestion et entrepreneuriat', 'Comptabilité, marketing, gestion de projet et entrepreneuriat.'),
('Design et créativité', 'Design graphique, dessin, photographie et arts visuels.');

INSERT INTO badge (nom, description, seuilPoints, icone) VALUES
('Premier pas', 'Débloqué dès la première séance complétée.', 10, 'badges/premier_pas.svg'),
('Contributeur actif', 'Débloqué à partir de 50 points cumulés.', 50, 'badges/contributeur_actif.svg'),
('Membre de confiance', 'Débloqué à partir de 150 points cumulés.', 150, 'badges/membre_confiance.svg'),
('Expert SkillSwap', 'Débloqué à partir de 300 points cumulés.', 300, 'badges/expert_skillswap.svg'),
('Ambassadeur du partage', 'Débloqué à partir de 500 points cumulés.', 500, 'badges/ambassadeur_partage.svg');

-- Compte administrateur initial, avec un mot de passe de démonstration
-- FONCTIONNEL (à changer impérativement après la première connexion en
-- environnement réel) : identifiant admin@skillswap.local / mot de passe Admin1234
INSERT INTO compte (email, motDePasseHash, statut, typeCompte) VALUES
('admin@skillswap.local', '$2y$10$XJVWspfnOXtulQR7zLfQFe8gc8/b6WVaOFrgcRVD8SlHRSbAweeVS', 'ACTIF', 'ADMIN');

INSERT INTO administrateur (idCompte, nomComplet, niveauAcces) VALUES
(LAST_INSERT_ID(), 'Administrateur Principal', 'SUPERVISEUR');

-- Deux comptes utilisateurs de démonstration (mot de passe : Demo1234),
-- afin de pouvoir tester immédiatement le parcours d'échange complet
-- (recherche, demande, réservation, séance, avis, points, certificat).
INSERT INTO compte (email, motDePasseHash, statut, typeCompte) VALUES
('salma.demo@skillswap.local', '$2y$10$0jyugcVoKpdz8e7ReCdUTeA4pAXOx7m3m8vT02rDj3a2PgYwKUMZG', 'ACTIF', 'UTILISATEUR');
INSERT INTO utilisateur (idCompte, nom, prenom, ville, biographie, soldePoints, visibiliteProfil) VALUES
(LAST_INSERT_ID(), 'Layouni', 'Salma', 'Tunis', 'Passionnée de photographie et toujours partante pour apprendre une nouvelle langue.', 0, 'PUBLIQUE');

INSERT INTO compte (email, motDePasseHash, statut, typeCompte) VALUES
('karim.demo@skillswap.local', '$2y$10$0jyugcVoKpdz8e7ReCdUTeA4pAXOx7m3m8vT02rDj3a2PgYwKUMZG', 'ACTIF', 'UTILISATEUR');
INSERT INTO utilisateur (idCompte, nom, prenom, ville, biographie, soldePoints, visibiliteProfil) VALUES
(LAST_INSERT_ID(), 'Mansour', 'Karim', 'Sousse', 'Développeur web le jour, marmiton le soir.', 0, 'PUBLIQUE');

-- =====================================================================
-- SECTION 5 : VUES UTILITAIRES
-- =====================================================================

-- Vue : profil public d'un utilisateur (informations non sensibles)
CREATE OR REPLACE VIEW v_profil_public AS
SELECT
    u.idCompte,
    u.nom,
    u.prenom,
    u.ville,
    u.soldePoints,
    u.noteMoyenne,
    u.visibiliteProfil,
    c.statut AS statutCompte
FROM utilisateur u
INNER JOIN compte c ON c.idCompte = u.idCompte
WHERE c.statut = 'ACTIF';

-- Vue : compétences actives disponibles dans le catalogue public
CREATE OR REPLACE VIEW v_catalogue_competences AS
SELECT
    co.idCompetence,
    co.titre,
    co.description,
    co.type,
    co.niveau,
    ca.nom AS categorie,
    co.idCompte AS idAuteur
FROM competence co
INNER JOIN categorie ca ON ca.idCategorie = co.idCategorie
WHERE co.statut = 'ACTIVE';

-- Vue : synthèse des certificats actifs par utilisateur
CREATE OR REPLACE VIEW v_certificats_actifs AS
SELECT
    ce.idCertificat,
    ce.identifiantUnique,
    ce.competenceAcquise,
    ce.dateDelivrance,
    ce.idApprenant,
    ce.idFormateur
FROM certificat ce
WHERE ce.statut = 'ACTIF';

-- =====================================================================
-- FIN DU SCRIPT
-- =====================================================================
