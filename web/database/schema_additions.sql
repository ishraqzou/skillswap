-- =====================================================================
-- SkillSwap — Compléments au schéma de la Tâche 14
-- À exécuter APRÈS skillswap_database.sql
--
-- Ajoute le suivi de la confirmation individuelle de chaque participant
-- sur une séance, nécessaire à l'implémentation précise de la règle
-- métier BR-026 (une séance n'est complétée que lorsque les DEUX
-- participants l'ont confirmée). Cette granularité n'était pas portée
-- par le schéma conceptuel (Tâche 11), qui modélisait le résultat
-- (statut de la séance) sans le détail de son obtention.
-- =====================================================================

USE skillswap;

ALTER TABLE seance
    ADD COLUMN confirmationParticipantA DATETIME NULL AFTER lieuModalite,
    ADD COLUMN confirmationParticipantB DATETIME NULL AFTER confirmationParticipantA;
