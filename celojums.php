<?php
require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, kieli, <head>-palat
/* Ceļojums pa Latviju — matkablogi (17.–24.10.2026, Limbažu + Cēsu novads).
   Sama tietokanta ja sama zinas-taulu kuin uutisilla, suodatettu
   kategorialla 'celojums'. Kuvatuki [kuva:tiedosto.jpg] kuten blogissa.
   Hallinta: blogi_admin.php (valitse kategoriaksi "Ceļojums"). */
require __DIR__ . '/db_config.php';

$supported = ['lv','en','fi'];
$default   = 'lv';
$lang = $_GET['lang'] ?? $default;
if (!in_array($lang, $supported, true)) { $lang = $default; }
$base = 'https://manalasdungeon.lv/';

/* Matkan kiinteät tiedot */
$DATES  = '17.–24.10.2026';
$REGION = 'Limbažu novads · Cēsu novads';

$TT = [
  'lv' => ['title'=>'Ceļojums pa Latviju','lead'=>'Ceļojuma pieraksti un bildes','back'=>'Sākums','guest'=>'Atstāj šeit savu komentāru','empty'=>'Ceļojums vēl priekšā — pirmie ieraksti drīz.','loc'=>'lv_LV','fmt'=>'d.m.Y'],
  'en' => ['title'=>'A journey through Latvia','lead'=>'Travel notes and photos','back'=>'Home','guest'=>'Leave your comment here','empty'=>'The trip is still ahead — first entries coming soon.','loc'=>'en_US','fmt'=>'M j, Y'],
  'fi' => ['title'=>'Matka halki Latvian','lead'=>'Matkamerkinnät ja kuvat','back'=>'Etusivu','guest'=>'Jätä tänne kommenttisi','empty'=>'Matka on vielä edessä — ensimmäiset merkinnät pian.','loc'=>'fi_FI','fmt'=>'d.m.Y'],
];
$T = $TT[$lang];

function home_url(string $lg): string { return page_url('', $lg); }
function trip_url(string $lg): string { return page_url('celojums.php', $lg); }

/* Renderöi tekstin: suojaa HTML, muuttaa [kuva:tiedosto.jpg] kuvaksi. */
function render_teksts(string $t): string {
    $s = htmlspecialchars($t, ENT_QUOTES, 'UTF-8');
    $s = nl2br($s);
    // Muuta paljaat URL-osoitteet klikattaviksi linkeiksi
    $s = preg_replace_callback('#\bhttps?://[^\s<]+#', function ($m) {
        $url  = rtrim($m[0], '.,);!?');
        $tail = substr($m[0], strlen($url));
        return '<a href="' . $url . '" target="_blank" rel="noopener">' . $url . '</a>' . $tail;
    }, $s);
    $s = preg_replace_callback('/\[kuva:([A-Za-z0-9_\-.]+)\]/', function ($m) {
        $f = $m[1];
        if (strpos($f, '..') !== false) return '';
        return '<figure class="cel-fig"><img src="blogi_kuvat/' . rawurlencode($f) . '" alt="" loading="lazy"></figure>';
    }, $s);
    $s = preg_replace('#(<br\s*/?>\s*)+<figure#', '<figure', $s);
    $s = preg_replace('#</figure>(\s*<br\s*/?>)+#', '</figure>', $s);
    return $s;
}

/* Matkamerkinnät: uusin ylinnä. Vaihda ASC jos haluat kronologisen (päivä 1 -> N). */
$rows = db()->query(
  "SELECT virsraksts, teksts, izveidots
   FROM zinas WHERE publicets = 1 AND kategorija = 'celojums'
   ORDER BY izveidots DESC, id DESC LIMIT 200"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?= head_analytics('') ?>
<title><?= e($T['title']) ?> — Manalas Dungeon</title>
<meta name="description" content="<?= e($T['title']) ?> — <?= e($REGION) ?>, <?= e($DATES) ?>">
<meta name="author" content="Marko Nakari">
<meta name="robots" content="index, follow">
<meta name="theme-color" content="#8d2027">
<?= head_alternates('trip_url', $lang, '') ?><meta property="og:type" content="website">
<meta property="og:site_name" content="Manalas Dungeon">
<meta property="og:title" content="<?= e($T['title']) ?> — Manalas Dungeon">
<meta property="og:url" content="<?= e(trip_url($lang)) ?>">
<meta property="og:image" content="<?= e($base) ?>images/celojums_bg.jpg">
<meta property="og:locale" content="<?= e($T['loc']) ?>">
<?= head_fonts('playfair-display-latin-wght-normal.woff2', '') ?><link rel="stylesheet" href="<?= e(asset('celojums.css')) ?>">
</head>
<body>

  <a class="cel-back" href="<?= e(home_url($lang)) ?>">&larr; <?= e($T['back']) ?></a>

  <!-- Kielivalitsin -->
  <div class="cel-lang">
    <?php foreach ($supported as $lg): ?>
      <a href="<?= e(trip_url($lg)) ?>" class="<?= $lg===$lang?'active':'' ?>"><?= strtoupper($lg) ?></a>
    <?php endforeach; ?>
  </div>

  <header class="cel-hero">
    <p class="cel-eyebrow"><?= e($DATES) ?></p>
    <h1><?= e($T['title']) ?></h1>
    <p class="cel-lead"><?= e($T['lead']) ?></p>
    <p class="cel-region"><span class="cel-pin"></span><?= e($REGION) ?></p>
    <div class="cel-vaakunat">
      <a class="cel-vaakuna" href="https://www.limbazunovads.lv/lv" target="_blank" rel="noopener">
        <img src="<?= e(asset('images/limbazi.webp')) ?>" alt="Limbažu novada ģerbonis" loading="lazy">
        <span class="cel-vaakuna-name">Limbažu novads</span>
      </a>
      <a class="cel-vaakuna" href="https://www.cesis.lv/lv/" target="_blank" rel="noopener">
        <img src="<?= e(asset('images/cesis.webp')) ?>" alt="Cēsu novada ģerbonis" loading="lazy">
        <span class="cel-vaakuna-name">Cēsu novads</span>
      </a>
    </div>
  </header>

  <main class="cel-wrap">
    <?php if (!$rows): ?>
      <p class="cel-empty"><?= e($T['empty']) ?></p>
    <?php else: foreach ($rows as $r): ?>
      <article class="cel-zina reveal">
        <time class="cel-date"><?= e(date($T['fmt'], strtotime($r['izveidots']))) ?></time>
        <h2 class="cel-title"><?= e($r['virsraksts']) ?></h2>
        <div class="cel-text"><?= render_teksts($r['teksts']) ?></div>
      </article>
    <?php endforeach; endif; ?>
  </main>

  <div class="cel-bottom">
    <a class="cel-btn" href="<?= e($base) ?>viesu_gramata.php"><span class="cel-btn__arrow" aria-hidden="true">&#9998;</span> <?= e($T['guest']) ?></a>
  </div>

  <footer class="cel-footer">
    &copy; <a href="https://manalasdungeon.lv">Marko Nakari</a> &middot; Manalas Dungeon
  </footer>

  <script nonce="<?= csp_nonce() ?>">
  /* Ceļojums — reveal-animaatio + kuvan suurennus (lightbox).
     Inline + tyylit JS:ssä, joten toimii ilman celojums.js- tai celojums.css-riippuvuutta. */
  (function () {
    'use strict';

    var io = ('IntersectionObserver' in window) ? new IntersectionObserver(function (es) {
      es.forEach(function (x) { if (x.isIntersecting) { x.target.classList.add('vis'); io.unobserve(x.target); } });
    }, { threshold: 0.12 }) : null;
    document.querySelectorAll('.reveal').forEach(function (el, i) {
      el.style.transitionDelay = Math.min(i * 0.07, 0.4) + 's';
      if (io) { io.observe(el); } else { el.classList.add('vis'); }
    });

    var ov = document.createElement('div');
    ov.setAttribute('role', 'dialog');
    ov.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;z-index:1000;' +
      'display:none;align-items:center;justify-content:center;' +
      'background:rgba(10,6,6,.93);padding:20px;cursor:zoom-out';

    var big = document.createElement('img');
    big.alt = '';
    big.style.cssText = 'max-width:100%;max-height:100%;width:auto;height:auto;' +
      'border-radius:8px;box-shadow:0 12px 50px rgba(0,0,0,.7)';

    var btn = document.createElement('button');
    btn.type = 'button';
    btn.innerHTML = '&times;';
    btn.setAttribute('aria-label', 'Aizvērt');
    btn.style.cssText = 'position:fixed;top:14px;right:18px;z-index:1001;' +
      'background:rgba(20,10,10,.6);color:#fff;border:1px solid rgba(255,255,255,.35);' +
      'width:44px;height:44px;border-radius:50%;font-size:1.6rem;line-height:1;cursor:pointer;' +
      'display:flex;align-items:center;justify-content:center';

    ov.appendChild(big);
    ov.appendChild(btn);
    document.body.appendChild(ov);

    function openImg(src) { big.src = src; ov.style.display = 'flex'; }
    function closeImg() { ov.style.display = 'none'; big.src = ''; }

    document.addEventListener('click', function (e) {
      var im = e.target.closest ? e.target.closest('.cel-fig img') : null;
      if (im) { openImg(im.currentSrc || im.src); return; }
      if (e.target === ov || e.target === big || e.target === btn) { closeImg(); }
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeImg(); } });
  })();
  </script>
</body>
</html>
