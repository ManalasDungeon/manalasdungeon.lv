<?php
/* Testitietokanta — testiajo kopioi tämän sivuston kopioon nimellä db_config.php.
   EI KOSKAAN tuotantoon. Asetukset ympäristömuuttujista tai oletukset. */
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . (getenv('TEST_DB_HOST') ?: '127.0.0.1') . ';dbname=' . (getenv('TEST_DB_NAME') ?: 'manalas_test') . ';charset=utf8mb4',
            getenv('TEST_DB_USER') ?: 'manalas_test',
            getenv('TEST_DB_PASS') ?: 'manalas_test',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
        );
    }
    return $pdo;
}
