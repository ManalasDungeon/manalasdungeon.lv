-- ============================================================
-- Matkablogi: lisää kategoria-sarake olemassa olevaan zinas-tauluun.
-- Aja tämä KERRAN phpMyAdminissa (tietokanta: manalasd_Viesugramata).
-- Vanhat rivit saavat automaattisesti arvon 'zina' (= uutinen),
-- joten ne pysyvät Manas ziņas -sivulla. Matkamerkinnät tallennetaan
-- arvolla 'celojums' ja näkyvät uudella celojums.php-sivulla.
-- ============================================================

ALTER TABLE zinas
  ADD COLUMN kategorija VARCHAR(20) NOT NULL DEFAULT 'zina' AFTER teksts,
  ADD INDEX idx_kategorija (kategorija);
