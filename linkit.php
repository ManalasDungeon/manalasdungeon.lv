<?php
require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, kieli, <head>-palat
/**
 * Linkit / Links / Saites — linkkipuu (link in bio) manalasdungeon.lv
 * Kolmikielinen: ?lang=en / ?lang=fi, oletus lv. Siisti osoite: /linkit (.htaccess).
 * Linkit ovat taulukossa $GROUPS — uusi linkki = yksi rivi.
 * Klikkaukset näkyvät GA4:ssä tapahtumana "link_click" (link_id).
 */

$supported = ['lv', 'en', 'fi'];
$default   = 'lv';
$lang = $_GET['lang'] ?? $default;
if (!in_array($lang, $supported, true)) { $lang = $default; }
$base = 'https://manalasdungeon.lv/';
$q    = $lang === $default ? '' : '?lang=' . $lang;

function lk_url(string $lg): string { return page_url('linkit', $lg); }

$TT = [
  'lv' => [
    'loc' => 'lv_LV',
    'title' => 'Marko Nakari — saites',
    'desc'  => 'Visas Marko Nakari saites vienuviet: portfolio, LinkedIn, GitHub un manalasdungeon.lv lapas.',
    'role'  => 'Programmatūras izstrādātājs · PHP / MySQL',
    'tagline' => 'Somija ↔ Vidzeme. Tīmekļa vietnes, drošība un ceļojumi.',
    'g_work' => 'Darbs', 'g_site' => 'Vietnē', 'g_contact' => 'Kontakti',
    'portfolio' => 'Portfolio', 'portfolio_sub' => 'Projekts, tehnoloģijas un drošība',
    'linkedin_sub' => 'Profesionālais profils', 'github_sub' => 'Kods un projekti',
    'home' => 'Sākums', 'home_sub' => 'manalasdungeon.lv',
    'blog' => 'Ziņas', 'blog_sub' => 'Jaunumi un pieraksti',
    'vidzeme' => 'Piedzīvo Vidzemi', 'vidzeme_sub' => 'Cēsis, Salacgrīva un apkārtne',
    'yoga' => 'Joga', 'yoga_sub' => 'Vēsture, filozofija un vinyasa',
    'vitrupe' => 'Vitrupes Pludmale', 'vitrupe_sub' => 'Vidzemes krasta fotogalerija',
    'bird' => 'Putnu atpazīšanas spēle', 'bird_sub' => 'Mācies atpazīt putnu sugas',
    'lib' => 'Manalas bibliotēka', 'lib_sub' => 'Pārlūko manu kolekciju',
    'guest' => 'Viesu grāmata', 'guest_sub' => 'Atstāj savu ierakstu',
    'email_label' => 'E-pasts', 'copy' => 'Kopēt', 'copied' => 'Nokopēts',
    'qr' => 'Skenē un saglabā šo lapu',
  ],
  'en' => [
    'loc' => 'en_US',
    'title' => 'Marko Nakari — links',
    'desc'  => 'All of Marko Nakari’s links in one place: portfolio, LinkedIn, GitHub and the pages of manalasdungeon.lv.',
    'role'  => 'Software developer · PHP / MySQL',
    'tagline' => 'Finland ↔ Vidzeme. Websites, security and travel.',
    'g_work' => 'Work', 'g_site' => 'On the site', 'g_contact' => 'Contact',
    'portfolio' => 'Portfolio', 'portfolio_sub' => 'Project, tech stack and security',
    'linkedin_sub' => 'Professional profile', 'github_sub' => 'Code and projects',
    'home' => 'Home', 'home_sub' => 'manalasdungeon.lv',
    'blog' => 'News', 'blog_sub' => 'Updates and travel notes',
    'vidzeme' => 'Experience Vidzeme', 'vidzeme_sub' => 'Cēsis, Salacgrīva & the coast',
    'yoga' => 'Yoga', 'yoga_sub' => 'History, philosophy and Vinyasa',
    'vitrupe' => 'Vitrupes Pludmale', 'vitrupe_sub' => 'Photo gallery of the Vidzeme coast',
    'bird' => 'Bird identification game', 'bird_sub' => 'Learn to identify bird species',
    'lib' => 'Manala library', 'lib_sub' => 'Browse my collection',
    'guest' => 'Guestbook', 'guest_sub' => 'Leave a message',
    'email_label' => 'Email', 'copy' => 'Copy', 'copied' => 'Copied',
    'qr' => 'Scan to save this page',
  ],
  'fi' => [
    'loc' => 'fi_FI',
    'title' => 'Marko Nakari – linkit',
    'desc'  => 'Kaikki Marko Nakarin linkit yhdessä paikassa: portfolio, LinkedIn, GitHub ja manalasdungeon.lv-sivuston sivut.',
    'role'  => 'Ohjelmistokehittäjä · PHP / MySQL',
    'tagline' => 'Suomi ↔ Vidzeme. Verkkosivustot, tietoturva ja matkat.',
    'g_work' => 'Työ', 'g_site' => 'Sivustolla', 'g_contact' => 'Yhteystiedot',
    'portfolio' => 'Portfolio', 'portfolio_sub' => 'Projekti, tekniikat ja tietoturva',
    'linkedin_sub' => 'Ammatillinen profiili', 'github_sub' => 'Koodi ja projektit',
    'home' => 'Etusivu', 'home_sub' => 'manalasdungeon.lv',
    'blog' => 'Uutiset', 'blog_sub' => 'Päivitykset ja matkamerkinnät',
    'vidzeme' => 'Koe elämyksiä', 'vidzeme_sub' => 'Cēsis, Salacgrīva ja muu Vidzeme',
    'yoga' => 'Jooga', 'yoga_sub' => 'Historia, filosofia ja vinyasa',
    'vitrupe' => 'Vitrupes Pludmale', 'vitrupe_sub' => 'Vidzemen rannikon kuvagalleria',
    'bird' => 'Lintujen tunnistuspeli', 'bird_sub' => 'Opettele tunnistamaan lintulajit',
    'lib' => 'Manalan verkkokirjasto', 'lib_sub' => 'Selaa kokoelmaani',
    'guest' => 'Vieraskirja', 'guest_sub' => 'Jätä terveiset',
    'email_label' => 'Sähköposti', 'copy' => 'Kopioi', 'copied' => 'Kopioitu',
    'qr' => 'Skannaa ja tallenna tämä sivu',
  ],
];
$T = $TT[$lang];

/* Ikonit: samat kuin etusivulla + muutama oma */
$icons = [
  'bird' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 7h.01"/><path d="M3.4 18H12a8 8 0 0 0 8-8V7a4 4 0 0 0-7.28-2.3L2 20"/><path d="m20 7 2 .5-2 .5"/><path d="M10 18v3"/><path d="M14 17.75V21"/><path d="M7 18a6 6 0 0 1-3.84-10.22"/></svg>',
  'lib'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>',
  'wave' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/></svg>',
  'news' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8V6z"/></svg>',
  'pen'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>',
  'map'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>',
  'case' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><path d="M2 13h20"/></svg>',
  'in'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>',
  'git'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>',
  'home' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 9-7 9 7v10a2 2 0 0 1-2 2h-4v-7H9v7H5a2 2 0 0 1-2-2z"/></svg>',
  'lotus'=> '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20c-4.5 0-8-2.2-9-5.5 2.6-.4 5.6.4 7.4 2.4"/><path d="M12 20c4.5 0 8-2.2 9-5.5-2.6-.4-5.6.4-7.4 2.4"/><path d="M12 20c-2.2-2-3.2-4.6-3.2-7.4S10 7 12 4c2 3 3.2 5.8 3.2 8.6S14.2 18 12 20z"/></svg>',
  'mail' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg>',
];

$vitrupeUrl = $base . 'vitrupe/' . ($lang === $default ? '' : $lang . '/');

/* [id GA4:lle, otsikko, alarivi, osoite, ikoni, ulkoinen?] */
$GROUPS = [
  $T['g_work'] => [
    ['portfolio', $T['portfolio'], $T['portfolio_sub'], $base . 'portfolio.php' . $q, 'case', false],
    ['linkedin',  'LinkedIn',      $T['linkedin_sub'],  'https://www.linkedin.com/in/manalainen', 'in', true],
    ['github',    'GitHub',        $T['github_sub'],    'https://github.com/ManalasDungeon', 'git', true],
  ],
  $T['g_site'] => [
    ['home',     $T['home'],    $T['home_sub'],    $base . $q,                    'home',  false],
    ['blog',     $T['blog'],    $T['blog_sub'],    $base . 'blogi.php' . $q,      'news',  false],
    ['vidzeme',  $T['vidzeme'], $T['vidzeme_sub'], $base . 'vidzeme.php' . $q,    'map',   false],
    ['yoga',     $T['yoga'],    $T['yoga_sub'],    $base . 'joga.php' . $q,       'lotus', false],
    ['vitrupe',  $T['vitrupe'], $T['vitrupe_sub'], $vitrupeUrl,                   'wave',  false],
    ['birdgame', $T['bird'],    $T['bird_sub'],    $base . 'birdgame/',           'bird',  false],
    ['library',  $T['lib'],     $T['lib_sub'],     $base . 'verkkokirjasto/',     'lib',   false],
    ['guestbook',$T['guest'],   $T['guest_sub'],   $base . 'viesu_gramata.php' . $q, 'pen', false],
  ],
];
$ogImage = $base . 'images/linkit_og.jpg';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

    <?= head_analytics('    ') ?>
    <title><?= h($T['title']) ?></title>
    <meta name="description" content="<?= h($T['desc']) ?>">
    <meta name="author" content="Marko Nakari">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#3a0913">
    <?= head_alternates('lk_url', $lang, '    ') ?>
    <!-- Open Graph (name= myös LinkedInille) -->
    <meta property="og:type" content="profile">
    <meta property="og:site_name" content="Manalas Dungeon">
    <meta name="title" property="og:title" content="<?= h($T['title']) ?>">
    <meta property="og:description" content="<?= h($T['desc']) ?>">
    <meta property="og:url" content="<?= h(lk_url($lang)) ?>">
    <meta name="image" property="og:image" content="<?= h($ogImage) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="<?= h($T['loc']) ?>">
    <meta property="profile:first_name" content="Marko">
    <meta property="profile:last_name" content="Nakari">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="<?= h($ogImage) ?>">

    <?= head_fonts('playfair-display-latin-wght-normal.woff2', '    ') ?>    <link rel="stylesheet" href="<?= h(asset('linkit.css')) ?>">
</head>
<body class="lk-body" style="--lk-bg:url(<?= h(asset('images/linkit_bg.webp')) ?>)">

    <nav class="lk-lang" aria-label="Language">
        <?php foreach ($supported as $lg): ?>
        <a href="<?= h(lk_url($lg)) ?>" hreflang="<?= $lg ?>" class="<?= $lg === $lang ? 'active' : '' ?>"><?= strtoupper($lg) ?></a>
        <?php endforeach; ?>
    </nav>

    <main class="lk-card">
        <header class="lk-head">
            <img class="lk-cover" src="<?= h(asset('images/linkit_cover.webp')) ?>" alt="Latvija — Cēsu pils saulrietā" width="1200" height="360">
            <div class="lk-avatar" aria-hidden="true"><span>M</span></div>
            <h1 class="lk-name">Marko Nakari</h1>
            <p class="lk-role"><?= h($T['role']) ?></p>
            <p class="lk-tagline"><?= h($T['tagline']) ?></p>
        </header>

        <?php foreach ($GROUPS as $groupName => $items): ?>
        <section class="lk-group">
            <h2 class="lk-group__title"><?= h($groupName) ?></h2>
            <ul class="lk-list">
                <?php foreach ($items as [$id, $label, $sub, $url, $icon, $ext]): ?>
                <li>
                    <a class="lk-link<?= $id === 'portfolio' ? ' lk-link--main' : '' ?>" href="<?= h($url) ?>" data-id="<?= h($id) ?>"<?= $ext ? ' target="_blank" rel="noopener"' : '' ?>>
                        <span class="lk-link__icon" aria-hidden="true"><?= $icons[$icon] ?></span>
                        <span class="lk-link__text">
                            <span class="lk-link__label"><?= h($label) ?></span>
                            <span class="lk-link__sub"><?= h($sub) ?></span>
                        </span>
                        <span class="lk-link__arrow" aria-hidden="true"><?= $ext ? '↗' : '→' ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php endforeach; ?>

        <section class="lk-group">
            <h2 class="lk-group__title"><?= h($T['g_contact']) ?></h2>
            <div class="lk-mail">
                <span class="lk-link__icon" aria-hidden="true"><?= $icons['mail'] ?></span>
                <span class="lk-mail__text">
                    <span class="lk-link__sub"><?= h($T['email_label']) ?></span>
                    <!-- osoite osina roskapostibotteja vastaan; JS kokoaa sen kopiointia varten -->
                    <span class="lk-mail__addr" data-u="marko.nakari" data-d="inbox.lv">marko.nakari(at)<wbr>inbox.lv</span>
                </span>
                <button type="button" class="lk-copy" data-copied="<?= h($T['copied']) ?>"><?= h($T['copy']) ?></button>
            </div>
        </section>

        <figure class="lk-qr">
            <img src="<?= h(asset('images/linkit_qr.png')) ?>" alt="QR: manalasdungeon.lv/linkit" width="152" height="152" loading="lazy">
            <figcaption><?= h($T['qr']) ?></figcaption>
        </figure>

        <footer class="lk-foot">&copy; <?= date('Y') ?> Marko Nakari · <a href="<?= h($base . $q) ?>">manalasdungeon.lv</a></footer>
    </main>

    <script nonce="<?= csp_nonce() ?>">
    // GA4: mitä linkkejä klikataan
    document.querySelectorAll('.lk-link').forEach(function (a) {
      a.addEventListener('click', function () {
        if (typeof gtag === 'function') {
          gtag('event', 'link_click', { link_id: a.dataset.id, link_url: a.href, transport_type: 'beacon' });
        }
      });
    });
    // Sähköpostin kopiointi (osoite kootaan vasta tässä)
    (function () {
      var btn = document.querySelector('.lk-copy'), el = document.querySelector('.lk-mail__addr');
      if (!btn || !el) return;
      var addr = el.dataset.u + '@' + el.dataset.d, label = btn.textContent;
      btn.addEventListener('click', function () {
        var done = function () {
          btn.textContent = btn.dataset.copied; btn.classList.add('is-done');
          setTimeout(function () { btn.textContent = label; btn.classList.remove('is-done'); }, 1800);
          if (typeof gtag === 'function') gtag('event', 'link_click', { link_id: 'email_copy' });
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(addr).then(done, function () { el.textContent = addr; });
        } else { el.textContent = addr; }
      });
    })();
    </script>
</body>
</html>
