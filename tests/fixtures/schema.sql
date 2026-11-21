-- Testitietokannan taulut (samat rakenteet kuin tuotannossa).
DROP TABLE IF EXISTS zinas, viesu_gramata, admin_passkeys;

CREATE TABLE zinas (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    virsraksts VARCHAR(160) NOT NULL,
    teksts     TEXT         NOT NULL,
    kategorija VARCHAR(20)  NOT NULL DEFAULT 'zina',
    izveidots  DATETIME     NOT NULL,
    publicets  TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    INDEX idx_izveidots (izveidots),
    INDEX idx_kategorija (kategorija)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE viesu_gramata (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nosaukums    VARCHAR(60)  NOT NULL,
    zina         TEXT         NOT NULL,
    saite        VARCHAR(255) NULL,
    tips         VARCHAR(20)  NULL,
    izveidots    DATETIME     NOT NULL,
    ip_hash      CHAR(64)     NOT NULL DEFAULT '',
    apstiprinats TINYINT(1)   NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    INDEX idx_izveidots (izveidots)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE admin_passkeys (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  credential_hash CHAR(64) CHARACTER SET ascii NOT NULL,
  credential_id   TEXT CHARACTER SET ascii NOT NULL,
  public_key      TEXT NOT NULL,
  sign_count      INT UNSIGNED NOT NULL DEFAULT 0,
  label           VARCHAR(100) NOT NULL DEFAULT '',
  created_at      DATETIME NOT NULL,
  last_used       DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_credential_hash (credential_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
