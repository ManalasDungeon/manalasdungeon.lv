-- Aja tämä phpMyAdminissa (samassa tietokannassa kuin vieraskirja).
-- Luo uutistaulun "zinas".
CREATE TABLE IF NOT EXISTS zinas (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    virsraksts VARCHAR(160) NOT NULL,
    teksts     TEXT         NOT NULL,
    izveidots  DATETIME     NOT NULL,
    publicets  TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    INDEX idx_izveidots (izveidots)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
