-- Migration pour activer l'espace Admin + gestion des disponibilités/réservations
-- À exécuter dans la base homestay2030 (phpMyAdmin / mysql)

-- 1) Logements : disponibilité
ALTER TABLE logements
  ADD COLUMN disponible TINYINT(1) NOT NULL DEFAULT 1 AFTER prix_suggere_ia;

-- 2) Réservations : statut
ALTER TABLE reservations
  ADD COLUMN statut ENUM('EN_ATTENTE','CONFIRMEE','ANNULEE') NOT NULL DEFAULT 'EN_ATTENTE' AFTER date_fin;

-- Index utile
CREATE INDEX idx_reservations_statut ON reservations(statut);
