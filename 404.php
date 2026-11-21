<?php
require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, turvaotsakkeet
/**
 * Oma 404-sivu (.htaccess: ErrorDocument 404 /404.php).
 * Kieli: ?lang= → selaimen kieliasetus → latvia. Palauttaa aina tilakoodin 404.
 */
http_response_code(404);
header('Cache-Control: no-store');

$lang = $_GET['lang'] ?? '';
if (!is_string($lang) || !in_array($lang, SITE_LANGS, true)) {
    $lang = SITE_DEFAULT_LANG;
    $al = strtolower((string)($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''));
    if (preg_match_all('/\b(lv|fi|en)\b/', $al, $m)) { $lang = $m[1][0]; }
}

$TT = [
  'lv' => ['title' => 'Lapa nav atrasta', 'lead' => 'Šķiet, ka šī taka pazudusi kāpās. Varbūt saite ir novecojusi vai adresē ir drukas kļūda.',
           'home' => 'Uz sākumlapu', 'news' => 'Ziņas', 'vidz' => 'Piedzīvo Vidzemi', 'guest' => 'Viesu grāmata'],
  'en' => ['title' => 'Page not found', 'lead' => 'This path seems to have vanished into the dunes. The link may be out of date, or there may be a typo in the address.',
           'home' => 'Go to the home page', 'news' => 'News', 'vidz' => 'Explore Vidzeme', 'guest' => 'Guestbook'],
  'fi' => ['title' => 'Sivua ei löytynyt', 'lead' => 'Tämä polku näyttää kadonneen dyyneihin. Linkki voi olla vanhentunut tai osoitteessa on kirjoitusvirhe.',
           'home' => 'Etusivulle', 'news' => 'Uutiset', 'vidz' => 'Koe Vidzeme', 'guest' => 'Vieraskirja'],
];
$T = $TT[$lang];
$q = $lang === SITE_DEFAULT_LANG ? '' : '?lang=' . $lang;
?>
<!DOCTYPE html>
<html lang="<?= h($lang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($T['title']) ?> — Manalas Dungeon</title>
<meta name="robots" content="noindex">
<meta name="theme-color" content="#9E1B32">
<?= head_fonts('playfair-display-latin-wght-normal.woff2', '') ?>
<link rel="stylesheet" href="/<?= h(asset('404.css')) ?>">
</head>
<body>
  <main class="nf">
    <p class="nf-code" aria-hidden="true">404</p>
    <h1><?= h($T['title']) ?></h1>
    <p class="nf-lead"><?= h($T['lead']) ?></p>
    <p><a class="nf-btn" href="/<?= h($q) ?>"><?= h($T['home']) ?> →</a></p>
    <nav class="nf-links" aria-label="<?= h($T['title']) ?>">
      <a href="/blogi.php<?= h($q) ?>"><?= h($T['news']) ?></a>
      <a href="/vidzeme.php<?= h($q) ?>"><?= h($T['vidz']) ?></a>
      <a href="/viesu_gramata.php<?= h($q) ?>"><?= h($T['guest']) ?></a>
    </nav>
    <p class="nf-langs">
      <?php foreach (SITE_LANGS as $lg): if ($lg === $lang) { continue; } ?>
      <a href="?lang=<?= $lg ?>" hreflang="<?= $lg ?>"><?= h($TT[$lg]['title']) ?></a>
      <?php endforeach; ?>
    </p>
  </main>
</body>
</html>
