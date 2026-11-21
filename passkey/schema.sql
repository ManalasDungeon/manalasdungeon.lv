-- Passkey-kirjautuminen: aja KERRAN phpMyAdminissa (valitse tietokanta → SQL-välilehti).
-- credential_hash = SHA-256 avaintunnisteesta (yksilöivä haku, toimii kaikilla MySQL/MariaDB-versioilla).

CREATE TABLE IF NOT EXISTS admin_passkeys (
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
