<?php
/**
 * Tietokantayhteys — MALLI.
 * Kopioi tiedostoksi db_config.php ja täytä omat tunnukset.
 * db_config.php EI kuulu versionhallintaan (.gitignore).
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=TIETOKANTA;charset=utf8mb4',
            'KAYTTAJA',
            'SALASANA',
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }
    return $pdo;
}
