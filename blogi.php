<?php
require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, kieli, <head>-palat
/* Ziņas / Uutiset — julkinen blogisivu. Taustana info_bg.jpg,
   uutiskortit liukuvat esiin oikealta, järjestys lisäyspäivän mukaan. */
require __DIR__ . '/db_config.php';

$supported = ['lv','en','fi'];
$default   = 'lv';
$lang = $_GET['lang'] ?? $default;
if (!in_array($lang, $supported, true)) { $lang = $default; }
$base = 'https://manalasdungeon.lv/';

$TT = [
  'lv' => ['title'=>'Manas ziņas','lead'=>'Jaunumi un pieraksti','back'=>'Sākums','empty'=>'Pagaidām nav ziņu.','loc'=>'lv_LV','fmt'=>'d.m.Y','celojums'=>'Ceļojums pa Latviju'],
  'en' => ['title'=>'My news','lead'=>'Updates and notes','back'=>'Home','empty'=>'No news yet.','loc'=>'en_US','fmt'=>'M j, Y','celojums'=>'Journey through Latvia'],
  'fi' => ['title'=>'Omat uutiset','lead'=>'Päivitykset ja merkinnät','back'=>'Etusivu','empty'=>'Ei vielä uutisia.','loc'=>'fi_FI','fmt'=>'d.m.Y','celojums'=>'Matka Latviassa'],
];
$T = $TT[$lang];

function home_url(string $lg): string { return page_url('', $lg); }
function blog_url(string $lg): string { return page_url('blogi.php', $lg); }
function celojums_url(string $lg): string { return page_url('celojums.php', $lg); }

/* Renderöi uutistekstin: suojaa HTML, muuttaa [kuva:tiedosto.jpg] kuvaksi. */
function render_teksts(string $t): string {
    $s = htmlspecialchars($t, ENT_QUOTES, 'UTF-8');
    $s = nl2br($s);
    $s = preg_replace_callback('/\[kuva:([A-Za-z0-9_\-.]+)\]/', function ($m) {
        $f = $m[1];
        if (strpos($f, '..') !== false) return '';
        return '<figure class="zina-fig"><img src="blogi_kuvat/' . rawurlencode($f) . '" alt="" loading="lazy"></figure>';
    }, $s);
    // siisti ylimääräiset rivinvaihdot kuvan ympäriltä
    $s = preg_replace('#(<br\s*/?>\s*)+<figure#', '<figure', $s);
    $s = preg_replace('#</figure>(\s*<br\s*/?>)+#', '</figure>', $s);
    return $s;
}

$rows = db()->query(
  "SELECT id, virsraksts, teksts, izveidots
   FROM zinas WHERE publicets = 1 AND kategorija = 'zina'
   ORDER BY izveidots DESC, id DESC LIMIT 100"
)->fetchAll();

/* Uusimman ziņan aikaleima — tallennetaan selaimeen, jotta etusivun
   "uusi"-merkki katoaa, kun kävijä on nähnyt uutiset. */
$latestTs = (int) db()->query(
  "SELECT COALESCE(UNIX_TIMESTAMP(MAX(izveidots)), 0)
   FROM zinas WHERE publicets = 1 AND kategorija = 'zina'"
)->fetchColumn();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?= head_analytics('') ?>
<title><?= e($T['title']) ?> — Manalas Dungeon</title>
<meta name="description" content="<?= e($T['title']) ?> — Manalas Dungeon">
<meta name="author" content="Marko Nakari">
<meta name="robots" content="index, follow">
<meta name="theme-color" content="#8d2027">
<?= head_alternates('blog_url', $lang, '') ?><meta property="og:type" content="website">
<meta property="og:site_name" content="Manalas Dungeon">
<meta property="og:title" content="<?= e($T['title']) ?> — Manalas Dungeon">
<meta property="og:url" content="<?= e(blog_url($lang)) ?>">
<meta property="og:image" content="<?= e($base) ?>images/blogi_bg.jpg">
<meta property="og:locale" content="<?= e($T['loc']) ?>">
<link rel="alternate" type="application/rss+xml" title="Manas ziņas — RSS" href="<?= e($base) ?>rss.php">
<?= head_fonts('playfair-display-latin-wght-normal.woff2', '') ?><link rel="stylesheet" href="<?= e(asset('blogi.css')) ?>">
</head>
<body data-zina-ts="<?= (int)$latestTs ?>" style="--blogi-bg:url(<?= asset('images/blogi_bg.webp') ?>); --blogi-bg-m:url(<?= asset('images/blogi_bg_m.webp') ?>)">

  <a class="zinas-back" href="<?= e(home_url($lang)) ?>">&larr; <?= e($T['back']) ?></a>

  <!-- Kielivalitsin -->
  <div class="zinas-lang">
    <?php foreach ($supported as $lg): ?>
      <a href="<?= e(blog_url($lg)) ?>" class="<?= $lg===$lang?'active':'' ?>"><?= strtoupper($lg) ?></a>
    <?php endforeach; ?>
  </div>

  <header class="zinas-hero">
    <h1><?= e($T['title']) ?></h1>
    <p class="zinas-lead"><?= e($T['lead']) ?></p>
  </header>

  <main class="zinas-wrap">
    <?php if (!$rows): ?>
      <p class="zinas-empty"><?= e($T['empty']) ?></p>
    <?php else: foreach ($rows as $r): ?>
      <article class="zina reveal" id="zina-<?= (int)$r['id'] ?>">
        <time class="zina-date"><?= e(date($T['fmt'], strtotime($r['izveidots']))) ?></time>
        <h2 class="zina-title"><?= e($r['virsraksts']) ?></h2>
        <div class="zina-text"><?= render_teksts($r['teksts']) ?></div>
      </article>
    <?php endforeach; endif; ?>
  </main>

  <div class="zinas-bottom">
    <a class="zinas-btn" href="<?= e(home_url($lang)) ?>"><span class="zinas-btn__arrow" aria-hidden="true">&larr;</span> <?= e($T['back']) ?></a>
    <a class="zinas-btn" href="<?= e(celojums_url($lang)) ?>"><?= e($T['celojums']) ?> <span class="zinas-btn__arrow zinas-btn__arrow--next" aria-hidden="true">&rarr;</span></a>
  </div>

  <footer class="zinas-footer">
    &copy; <a href="https://manalasdungeon.lv">Marko Nakari</a> &middot; Manalas Dungeon &middot; <a href="<?= e($base) ?>rss.php">RSS</a>
  </footer>

  <script src="<?= e(asset('blogi.js')) ?>" defer></script>
</body>
</html>
