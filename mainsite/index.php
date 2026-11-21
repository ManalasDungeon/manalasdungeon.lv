<?php

/* Samhain-aloitussivu — voimassa vain 31.10.2026 (Suomen aikaa).
   Näytetään kerran, ellei kävijä ole jo siirtynyt (?enter=1 tai eväste).
   Ohjaa normaalille etusivulle 30 s kuluttua. Poistuu käytöstä itsestään. */
try {
    $__samhain = (new DateTime('now', new DateTimeZone('Europe/Helsinki')))->format('Y-m-d') === '2026-10-31';
} catch (Throwable $e) {
    $__samhain = false;
}
if ($__samhain && !isset($_GET['enter']) && empty($_COOKIE['samhain_seen'])) {
    require __DIR__ . '/samhain.php';
    exit;
}

$supported = ['lv', 'en', 'fi'];
$default   = 'lv';
$lang = $_GET['lang'] ?? $default;
if (!in_array($lang, $supported, true)) { $lang = $default; }

/* Ikä lasketaan automaattisesti syntymäajasta — päivittyy itsestään joka vuosi.
   VAIHDA alle oikea syntymäaika muodossa VVVV-KK-PP. */
$SYNTYMAAIKA = '1972-01-01';
try {
    $age = (new DateTime($SYNTYMAAIKA, new DateTimeZone('Europe/Helsinki')))
         ->diff(new DateTime('now', new DateTimeZone('Europe/Helsinki')))->y;
} catch (Throwable $e) {
    $age = 54; // varmuusarvo, jos päivä on virheellinen
}

$base    = 'https://manalasdungeon.lv/';
$locales = ['lv' => 'lv_LV', 'en' => 'en_US', 'fi' => 'fi_FI'];
$autonym = ['lv' => 'Latviešu', 'en' => 'English', 'fi' => 'Suomi'];
$flag    = ['lv' => '🇱🇻', 'en' => '🇬🇧', 'fi' => '🇫🇮'];

$T = [
  'lv' => [
    'title' => 'Mana — Heinola, Somija',
    'desc'  => 'Mana no Heinolas — programmatūras izstrādātājs un mediju instruktors. Projekti, bibliotēka un Vitrupes pludmale.',
    'loc'   => 'Heinola, Somija',
    'jobtitle' => 'Mediju instruktors',
    'org' => 'Heinolas bibliotēka',
    'bio'   => 'Man ir {ika} gadi, <strong>programmatūras izstrādātājs</strong> un mediju instruktors no Heinolas. Veidoju mājaslapas un lietotnes, un sirds mani atkal un atkal velk uz Latvijas Vidzemes krastu.',
    'l_bird' => 'Putnu atpazīšanas spēle',   'l_bird_sub' => 'Mācies atpazīt putnu sugas',
    'l_lib'  => 'Manalas bibliotēka',          'l_lib_sub'  => 'Pārlūko manu kolekciju tiešsaistē',
    'l_vitrupe' => 'Vitrupes Pludmale',        'l_vitrupe_sub' => 'Vidzemes krasta fotogalerija',
    'l_blog' => 'Ziņas',                        'l_blog_sub' => 'Jaunumi un pieraksti',
    'l_guest' => 'Viesu grāmata',               'l_guest_sub' => 'Atstāj savu ierakstu',
    'l_bird_detail' => 'Putnu atpazīšanas spēle ir programmēšanas uzdevums, kurā apvienots frontend un backend. Pārlūkā darbojas pati spēle un saskarne, bet servera puse rūpējas par putnu attēliem, sugu datiem un rezultātiem.',
    'l_lib_detail'  => 'Manalas tiešsaistes bibliotēka ir programmēšanas uzdevums, kurā apvienots frontend, backend un datubāzu programmēšana. MySQL tabulas, PDO savienojumi un CRUD operācijas (pievienot, meklēt, labot, dzēst) veido pilnvērtīgu bibliotēkas sistēmu. Tas ir programmatūras izstrādātāja kvalifikācijas diplomdarbs (Vamia).',
    'detail_btn'    => 'Atvērt lapu',
    'contact' => 'Kontaktinformācija',
  ],
  'en' => [
    'title' => 'Mana — Heinola, Finland',
    'desc'  => 'Mana from Heinola — software developer and media instructor. Projects, library and Vitrupe beach.',
    'loc'   => 'Heinola, Finland',
    'jobtitle' => 'Media instructor',
    'org' => 'Heinola library',
    'bio'   => "I'm {ika}, a <strong>software developer</strong> and media instructor from Heinola. I make websites and apps, and my heart keeps pulling me back to the Vidzeme coast of Latvia.",
    'l_bird' => 'Bird identification game',    'l_bird_sub' => 'Learn to identify bird species',
    'l_lib'  => 'Manala library',               'l_lib_sub'  => 'Browse my collection online',
    'l_vitrupe' => 'Vitrupes Pludmale',         'l_vitrupe_sub' => 'Photo gallery of the Vidzeme coast',
    'l_blog' => 'News',                          'l_blog_sub' => 'Updates and travel notes',
    'l_guest' => 'Guestbook',                    'l_guest_sub' => 'Leave a message',
    'l_bird_detail' => 'The bird identification game is a programming exercise that combines frontend and backend. The browser runs the game and interface, while the server side handles the bird images, species data and scores.',
    'l_lib_detail'  => 'The Manala online library is a programming exercise combining frontend, backend and database programming. MySQL tables, PDO connections and CRUD operations (create, read, update, delete) make up a complete library system. It is the final thesis for my software developer degree (Vamia).',
    'detail_btn'    => 'Open the page',
    'contact' => 'Contact',
  ],
  'fi' => [
    'title' => 'Mana — Heinola, Suomi',
    'desc'  => 'Mana Heinolasta — ohjelmistokehittäjä ja mediaohjaaja. Projektit, kirjasto ja Vitrupen ranta.',
    'loc'   => 'Heinola, Suomi',
    'jobtitle' => 'Medianeuvoja',
    'org' => 'Heinolan kirjasto',
    'bio'   => 'Olen {ika}-vuotias <strong>ohjelmistokehittäjä</strong> ja mediaohjaaja Heinolasta. Teen verkkosivuja ja sovelluksia, ja sydämeni vetää yhä uudelleen Latvian Vidzemen rannikolle.',
    'l_bird' => 'Lintujen tunnistuspeli',     'l_bird_sub' => 'Opettele tunnistamaan lintulajit',
    'l_lib'  => 'Manalan verkkokirjasto',      'l_lib_sub'  => 'Selaa kokoelmaani verkossa',
    'l_vitrupe' => 'Vitrupes Pludmale',        'l_vitrupe_sub' => 'Vidzemen rannikon kuvagalleria',
    'l_blog' => 'Uutiset',                      'l_blog_sub' => 'Päivitykset ja matkamerkinnät',
    'l_guest' => 'Vieraskirja',                 'l_guest_sub' => 'Jätä terveiset',
    'l_bird_detail' => 'Lintujen tunnistuspeli on ohjelmointiharjoitus, jossa yhdistyvät frontend ja backend. Selaimessa toimii itse peli ja käyttöliittymä, ja palvelinpuoli huolehtii lintukuvista, lajitiedoista ja tuloksista.',
    'l_lib_detail'  => 'Manalan verkkokirjasto on ohjelmointiharjoitus, jossa yhdistyvät frontend, backend ja tietokantaohjelmointi. MySQL-taulut, PDO-yhteydet ja CRUD-toiminnot (lisää, hae, muokkaa, poista) muodostavat toimivan kirjastojärjestelmän. Se on ohjelmistokehittäjän tutkinnon opinnäytetyö (Vamia).',
    'detail_btn'    => 'Siirry sivulle',
    'contact' => 'Yhteystiedot',
  ],
];
$L = $T[$lang];
$L['bio'] = str_replace('{ika}', (string)$age, $L['bio']); // ikä esittelytekstiin

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function purl(string $lg): string { global $base, $default; return $base . ($lg === $default ? '' : '?lang=' . $lg); }
function asset(string $path): string {
    $full = __DIR__ . '/' . ltrim($path, '/');
    $v = @filemtime($full) ?: time();
    return $path . '?v=' . $v;
}
$vitrupeUrl = $base . 'vitrupe/' . ($lang === 'lv' ? '' : $lang . '/');
$blogUrl    = $base . 'blogi.php' . ($lang === 'lv' ? '' : '?lang=' . $lang);

/* Uusimman julkaistun ziņan aikaleima — "uusi"-merkkiä varten. */
$latestTs = 0; $latestDate = '';
if (@is_file(__DIR__ . '/db_config.php')) {
    try {
        require_once __DIR__ . '/db_config.php';
        $latestTs = (int) db()->query(
            "SELECT COALESCE(UNIX_TIMESTAMP(MAX(izveidots)), 0)
             FROM zinas WHERE publicets = 1 AND kategorija = 'zina'"
        )->fetchColumn();
    } catch (Throwable $ex) {
        $latestTs = 0;
    }
}
if ($latestTs > 0) {
    $latestDate = date($lang === 'en' ? 'M j, Y' : 'd.m.Y', $latestTs);
}

/* Linkit ikoneineen (pienet inline-SVG:t) */
$icons = [
  'bird' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 7h.01"/><path d="M3.4 18H12a8 8 0 0 0 8-8V7a4 4 0 0 0-7.28-2.3L2 20"/><path d="m20 7 2 .5-2 .5"/><path d="M10 18v3"/><path d="M14 17.75V21"/><path d="M7 18a6 6 0 0 1-3.84-10.22"/></svg>',
  'lib'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>',
  'wave' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/></svg>',
  'news' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8V6z"/></svg>',
  'pen'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>',
];
$links = [
  ['url' => 'https://www.manalasdungeon.lv/birdgame/',       'label' => $L['l_bird'],    'sub' => $L['l_bird_sub'],    'icon' => $icons['bird'], 'detail' => $L['l_bird_detail']],
  ['url' => 'https://www.manalasdungeon.lv/verkkokirjasto/', 'label' => $L['l_lib'],     'sub' => $L['l_lib_sub'],     'icon' => $icons['lib'],  'detail' => $L['l_lib_detail']],
  ['url' => $vitrupeUrl,                                      'label' => $L['l_vitrupe'], 'sub' => $L['l_vitrupe_sub'], 'icon' => $icons['wave']],
  ['url' => $blogUrl,                                         'label' => $L['l_blog'],    'sub' => $L['l_blog_sub'],    'icon' => $icons['news'], 'ts' => $latestTs, 'date' => $latestDate],
  ['url' => 'viesu_gramata.php',                              'label' => $L['l_guest'],   'sub' => $L['l_guest_sub'],   'icon' => $icons['pen']],
];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-0Q1HVYWR72"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-0Q1HVYWR72');
    </script>

    <title><?= e($L['title']) ?></title>

    <meta name="description" content="<?= e($L['desc']) ?>">
    <meta name="author" content="Marko Nakari">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#8d2027">
    <link rel="canonical" href="<?= e(purl($lang)) ?>">
    <?php foreach ($supported as $lg): ?>
    <link rel="alternate" hreflang="<?= $lg ?>" href="<?= e(purl($lg)) ?>">
    <?php endforeach; ?>
    <link rel="alternate" hreflang="x-default" href="<?= e(purl('lv')) ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Manalas Dungeon">
    <meta property="og:title" content="<?= e($L['title']) ?>">
    <meta property="og:description" content="<?= e($L['desc']) ?>">
    <meta property="og:url" content="<?= e(purl($lang)) ?>">
    <meta property="og:image" content="<?= e($base) ?>images/info_bg.jpg">
    <meta property="og:locale" content="<?= e($locales[$lang]) ?>">
    <?php foreach ($supported as $lg): if ($lg !== $lang): ?>
    <meta property="og:locale:alternate" content="<?= e($locales[$lg]) ?>">
    <?php endif; endforeach; ?>

    <!-- Twitter / X -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($L['title']) ?>">
    <meta name="twitter:description" content="<?= e($L['desc']) ?>">
    <meta name="twitter:image" content="<?= e($base) ?>images/info_bg.jpg">

    <link rel="preload" as="image" href="<?= e(asset('images/info_bg.jpg')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=EB+Garamond:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('manala.css')) ?>">
    <script type="text/javascript" src="<?= e(asset('date_time.js')) ?>"></script>
<?php
$ld = [
    '@context'      => 'https://schema.org',
    '@type'         => 'Person',
    'name'          => 'Marko Nakari',
    'alternateName' => 'Mana',
    'url'           => $base,
    'image'         => $base . 'images/self.jpg',
    'jobTitle'      => $L['jobtitle'],
    'description'   => $L['desc'],
    'worksFor'      => ['@type' => 'Organization', 'name' => $L['org']],
    'address'       => ['@type' => 'PostalAddress', 'addressLocality' => 'Heinola', 'addressCountry' => 'FI'],
    'sameAs'        => ['https://github.com/ManalasDungeon'],
    'knowsLanguage' => ['lv', 'en', 'fi'],
];
?>
    <script type="application/ld+json">
    <?= json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>

    </script>
</head>
<body style="--hero-bg:url(<?= asset('images/info_bg.jpg') ?>)">

    <!-- Kielivalitsin -->
    <nav class="lang" aria-label="Kieli">
        <?php foreach ($supported as $lg): ?>
        <a href="<?= e(purl($lg)) ?>" hreflang="<?= $lg ?>" class="<?= $lg === $lang ? 'active' : '' ?>"><span class="lang__flag"><?= $flag[$lg] ?></span> <?= strtoupper($lg) ?></a>
        <?php endforeach; ?>
    </nav>

    <main class="wrap">
        <section class="card">
            <p class="eyebrow"><span class="dot"></span><?= e($L['loc']) ?></p>
            <h1 class="name">Marko Nakari <span class="alias">(Mana)</span></h1>
            <p class="bio"><?= $L['bio'] ?></p>

            <div class="divider"></div>

            <nav class="links">
                <?php foreach ($links as $lnk): $isNews = !empty($lnk['ts']); ?>
                <a class="link<?= $isNews ? ' link--news' : '' ?>" href="<?= e($lnk['url']) ?>"<?= $isNews ? ' data-zina-ts="' . (int)$lnk['ts'] . '"' : '' ?><?php if (!empty($lnk['detail'])): ?> data-detail="1" data-detail-text="<?= e($lnk['detail']) ?>"<?php endif; ?>>
                    <span class="link__icon" aria-hidden="true"><?= $lnk['icon'] ?></span>
                    <span class="link__text">
                        <span class="link__label"><?= e($lnk['label']) ?></span>
                        <span class="link__sub"><?= e($lnk['sub']) ?></span>
                    </span>
                    <?php if ($isNews): ?><span class="link__badge" hidden><?= e($lnk['date']) ?></span><?php endif; ?>
                    <span class="link__arrow" aria-hidden="true">&rarr;</span>
                </a>
                <?php endforeach; ?>
            </nav>

            <div class="contact">
                <span class="contact__label"><?= e($L['contact']) ?></span>
                <span class="contact__email">marko.nakari(at)manalasdungeon.lv</span>
            </div>
        </section>

        <aside class="detail" id="detail" aria-live="polite">
            <button class="detail__close" type="button" aria-label="×">&times;</button>
            <h2 class="detail__title"></h2>
            <p class="detail__text"></p>
            <a class="detail__btn" href="#"><span class="detail__btn-label"><?= e($L['detail_btn']) ?></span> <span aria-hidden="true">&rarr;</span></a>
        </aside>
    </main>

    <!-- Alareuna -->
    <div class="pagefoot">
        <span id="date_time_container"><span id="date_time"></span></span>
        <a href="https://portal.nano.lv/aff.php?aff=291" target="_blank" rel="noopener" id="hosting_link">nano.lv</a>
    </div>

    <script src="<?= e(asset('etusivu.js')) ?>" defer></script>
    <script type="text/javascript" src="<?= e(asset('main.js')) ?>"></script>
    <script type="text/javascript" src="<?= e(asset('app.js')) ?>"></script>

    <!-- © 2026 by marko.nakari(at)manalasdungeon.lv -->
</body>
</html>
