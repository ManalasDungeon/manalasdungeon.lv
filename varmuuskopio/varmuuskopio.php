<?php
declare(strict_types=1);
/**
 * Tietokannan varmuuskopio cPanelin ajastetulle tehtävälle (cron).
 *
 * SIJOITUS: kotikansioon verkkojuuren ULKOPUOLELLE, esim. ~/varmuuskopio/varmuuskopio.php
 *           (ei public_html:ään — tiedostoa ei saa voida ajaa selaimesta).
 * AJO:      vain komentoriviltä (cron), myös CGI-versiolla. Selaimesta ajettuna se lopettaa heti.
 *           Cron-komento: php -q ~/varmuuskopio/varmuuskopio.php   (-q jättää CGI-otsakkeet pois)
 * TULOS:    ~/varmuuskopiot/manalas-VVVV-KK-PP.sql.gz  (oikeudet 600, vain sinä voit lukea)
 *           Säilyttää uusimmat KEEP kappaletta, vanhemmat poistetaan.
 *
 * Yhteys otetaan sivuston omalla db_config.php:llä, joten tunnuksia ei kopioida tähän tiedostoon.
 * Palautus: cPanel → phpMyAdmin → Tuo (Import) → valitse .sql.gz-tiedosto.
 */

// Vain komentorivi/cron. Palvelimen cron voi ajaa `php`-komennolla CGI-version (PHP_SAPI = cgi-fcgi),
// joten pelkkä PHP_SAPI-tarkistus esti myös oikean cron-ajon. Selainpyynnön tunnistaa
// verkkopalvelimen asettamista muuttujista (REQUEST_METHOD / REMOTE_ADDR).
if (PHP_SAPI !== 'cli' && (isset($_SERVER['REQUEST_METHOD']) || isset($_SERVER['REMOTE_ADDR']))) {
    http_response_code(404);
    exit;
}

// STDERR-vakio on olemassa vain CLI-versiossa; CGI-ajossa virheet kirjoitetaan php://stderr-virtaan.
function virhe(string $viesti): never {
    $e = defined('STDERR') ? STDERR : fopen('php://stderr', 'w');
    fwrite($e, $viesti . "\n");
    exit(1);
}

const KEEP = 8;                                    // montako viimeisintä varmuuskopiota säilytetään

$home      = dirname(__DIR__);                     // kotikansio (tämän kansion yläkansio)
$dbConfig  = getenv('MANALA_DB_CONFIG') ?: $home . '/public_html/db_config.php';
$outDir    = getenv('MANALA_BACKUP_DIR') ?: $home . '/varmuuskopiot';

if (!is_file($dbConfig)) {
    virhe("db_config.php puuttuu: $dbConfig");
}
require $dbConfig;

if (!is_dir($outDir) && !mkdir($outDir, 0700, true)) {
    virhe("Kansiota ei voitu luoda: $outDir");
}
@chmod($outDir, 0700);

$pdo  = db();
$file = $outDir . '/manalas-' . date('Y-m-d') . '.sql.gz';
$tmp  = $file . '.tmp';
$gz   = gzopen($tmp, 'wb6');
if ($gz === false) {
    virhe("Tiedostoa ei voitu avata: $tmp");
}

$out = static function (string $s) use ($gz): void { gzwrite($gz, $s); };

$out("-- manalasdungeon.lv tietokannan varmuuskopio " . date('c') . "\n");
$out("SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n");

$tables = $pdo->query('SHOW FULL TABLES')->fetchAll(PDO::FETCH_NUM);
$count  = 0;
foreach ($tables as [$table, $type]) {
    if ($type !== 'BASE TABLE') { continue; }
    $q = '`' . str_replace('`', '``', $table) . '`';

    $create = $pdo->query("SHOW CREATE TABLE $q")->fetch(PDO::FETCH_NUM)[1];
    $out("DROP TABLE IF EXISTS $q;\n$create;\n\n");

    $stmt = $pdo->query("SELECT * FROM $q", PDO::FETCH_NUM);
    $rows = [];
    foreach ($stmt as $row) {
        $vals = array_map(static fn($v) => $v === null ? 'NULL' : $pdo->quote((string)$v), $row);
        $rows[] = '(' . implode(',', $vals) . ')';
        if (count($rows) >= 200) {
            $out("INSERT INTO $q VALUES\n" . implode(",\n", $rows) . ";\n");
            $rows = [];
        }
        $count++;
    }
    if ($rows) { $out("INSERT INTO $q VALUES\n" . implode(",\n", $rows) . ";\n"); }
    $out("\n");
}
$out("SET FOREIGN_KEY_CHECKS=1;\n");
gzclose($gz);

chmod($tmp, 0600);
rename($tmp, $file);

/* Vanhimmat pois, uusimmat KEEP jäävät */
$all = glob($outDir . '/manalas-*.sql.gz') ?: [];
rsort($all);
foreach (array_slice($all, KEEP) as $old) { @unlink($old); }

echo 'OK ' . basename($file) . ' · ' . count($tables) . ' taulua · ' . $count . " riviä\n";
