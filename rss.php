<?php
require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, turvaotsakkeet
/**
 * RSS 2.0 -syöte uutisille (Ziņas). Osoite: https://manalasdungeon.lv/rss
 * 20 uusinta julkaistua uutista, koko teksti kuvineen. Linkit osoittavat blogi.php-sivun
 * kyseiseen uutiseen (#zina-ID). Luonnoksia ja matkablogin merkintöjä ei näytetä.
 */
require __DIR__ . '/db_config.php';

$rows = db()->query(
  "SELECT id, virsraksts, teksts, izveidots
   FROM zinas WHERE publicets = 1 AND kategorija = 'zina'
   ORDER BY izveidots DESC, id DESC LIMIT 20"
)->fetchAll();

$tz = new DateTimeZone('Europe/Riga');

/** Uutisteksti HTML:ksi syötteenlukijoille: suojattu teksti, rivinvaihdot ja kuvat absoluuttisin osoittein. */
function rss_html(string $t): string {
    $s = nl2br(htmlspecialchars($t, ENT_QUOTES, 'UTF-8'));
    return preg_replace_callback('/\[kuva:([A-Za-z0-9_\-.]+)\]/', static function ($m) {
        if (strpos($m[1], '..') !== false) { return ''; }
        return '<p><img src="' . SITE_URL . 'blogi_kuvat/' . rawurlencode($m[1]) . '" alt=""></p>';
    }, $s) ?? $s;
}

/** XML-turvallinen teksti (myös ohjausmerkit pois). */
function x(string $s): string {
    $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $s) ?? '';
    return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

$last = $rows ? new DateTimeImmutable($rows[0]['izveidots'], $tz) : new DateTimeImmutable('now', $tz);

header('Content-Type: application/rss+xml; charset=utf-8');
header('Cache-Control: public, max-age=900');
echo '<?xml version="1.0" encoding="UTF-8"?>', "\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
  <title>Manas ziņas — Manalas Dungeon</title>
  <link><?= x(SITE_URL . 'blogi') ?></link>
  <atom:link href="<?= x(SITE_URL . 'rss') ?>" rel="self" type="application/rss+xml"/>
  <description>Marko Nakari (Mana) — jaunumi un pieraksti no Heinolas un Vidzemes.</description>
  <language>lv</language>
  <lastBuildDate><?= $last->format(DATE_RSS) ?></lastBuildDate>
<?php foreach ($rows as $r):
    $url  = SITE_URL . 'blogi#zina-' . (int)$r['id'];
    $date = new DateTimeImmutable($r['izveidots'], $tz); ?>
  <item>
    <title><?= x($r['virsraksts']) ?></title>
    <link><?= x($url) ?></link>
    <guid isPermaLink="true"><?= x($url) ?></guid>
    <pubDate><?= $date->format(DATE_RSS) ?></pubDate>
    <description><?= x(rss_html($r['teksts'])) ?></description>
  </item>
<?php endforeach; ?>
</channel>
</rss>
