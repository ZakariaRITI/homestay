-- Migration: gestion des indisponibilites par periode (blocage calendrier)
-- Base: homestay2030

CREATE TABLE IF NOT EXISTS indisponibilites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  logement_id INT NOT NULL,
  date_debut DATE NOT NULL,
  date_fin DATE NOT NULL,
  motif VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_logement_dates (logement_id, date_debut, date_fin),
  CONSTRAINT fk_indispo_logement FOREIGN KEY (logement_id) REFERENCES logements(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
