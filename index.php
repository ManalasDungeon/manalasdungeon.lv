<?php
require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, kieli, <head>-palat

/* Samhain-aloitussivu — voimassa vain 31.10.2026 (Suomen aikaa).
   Näytetään kerran, ellei kävijä ole jo siirtynyt (?enter=1 tai eväste).
   Ohjaa normaalille etusivulle 30 s kuluttua. Poistuu käytöstä itsestään. */
try {
    $__samhain = (new DateTime('now', new DateTimeZone('Europe/Helsinki')))->format('Y-m-d') === '2027-10-31';
} catch (Throwable $e) {
    $__samhain = false;
}
if ($__samhain && !isset($_GET['enter']) && empty($_COOKIE['samhain_seen'])) {
    require __DIR__ . '/samhain.php';
    exit;
}

/* Latvijan itsenäisyyspäivä 18.11.2026 (Riian aikaa): avausanimaatio kerran + juhlakoristeet etusivulle.
   Poistuu käytöstä itsestään 19.11. Esikatselu minä päivänä tahansa: ?lv18preview=1
   Somen esikatselurobotit ja hakukoneet saavat suoraan normaalin etusivun. */
try {
    $__lv18 = (new DateTime('now', new DateTimeZone('Europe/Riga')))->format('Y-m-d') === '2026-11-18';
} catch (Throwable $e) {
    $__lv18 = false;
}
$__isBot = (bool) preg_match('/bot|crawl|spider|slurp|facebookexternalhit|embedly|preview/i', $_SERVER['HTTP_USER_AGENT'] ?? '');
if ((($__lv18 && !$__isBot && !isset($_GET['enter']) && empty($_COOKIE['lv18_seen'])) || isset($_GET['lv18preview']))) {
    require __DIR__ . '/latvija18.php';
    exit;
}

$supported = ['lv', 'en', 'fi'];
$default   = 'lv';
$lang = $_GET['lang'] ?? $default;
if (!in_array($lang, $supported, true)) { $lang = $default; }

/* Ikä lasketaan automaattisesti syntymäajasta — päivittyy itsestään joka vuosi. */
$SYNTYMAAIKA = '1971-12-12';
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
    'l_priv' => 'Privātums', 'l_guest' => 'Viesu grāmata',               'l_guest_sub' => 'Atstāj savu ierakstu',
    'l_vidzeme' => 'Piedzīvo Vidzemi',          'l_vidzeme_sub' => 'Cēsis, Salacgrīva un apkārtne',
    'l_bird_detail' => 'Putnu atpazīšanas spēle ir programmēšanas uzdevums, kurā apvienots frontend un backend. Pārlūkā darbojas pati spēle un saskarne, bet servera puse rūpējas par putnu attēliem, sugu datiem un rezultātiem.',
    'l_lib_detail'  => 'Manalas tiešsaistes bibliotēka ir programmēšanas uzdevums, kurā apvienots frontend, backend un datubāzu programmēšana. MySQL tabulas, PDO savienojumi un CRUD operācijas (pievienot, meklēt, labot, dzēst) veido pilnvērtīgu bibliotēkas sistēmu. Tas ir programmatūras izstrādātāja kvalifikācijas diplomdarbs (Vamia).',
    'detail_btn'    => 'Atvērt lapu',
    'l_yoga'  => 'Joga',  'l_yoga_title' => 'Joga — vēsture, filozofija un vinyasa',
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
    'l_priv' => 'Privacy', 'l_guest' => 'Guestbook',                    'l_guest_sub' => 'Leave a message',
    'l_vidzeme' => 'Experience Vidzeme',         'l_vidzeme_sub' => 'Cēsis, Salacgrīva & the coast',
    'l_bird_detail' => 'The bird identification game is a programming exercise that combines frontend and backend. The browser runs the game and interface, while the server side handles the bird images, species data and scores.',
    'l_lib_detail'  => 'The Manala online library is a programming exercise combining frontend, backend and database programming. MySQL tables, PDO connections and CRUD operations (create, read, update, delete) make up a complete library system. It is the final thesis for my software developer degree (Vamia).',
    'detail_btn'    => 'Open the page',
    'l_yoga'  => 'Yoga',  'l_yoga_title' => 'Yoga — history, philosophy and Vinyasa',
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
    'l_priv' => 'Tietosuoja', 'l_guest' => 'Vieraskirja',                 'l_guest_sub' => 'Jätä terveiset',
    'l_vidzeme' => 'Koe elämyksiä',             'l_vidzeme_sub' => 'Cēsis, Salacgrīva ja muu Vidzeme',
    'l_bird_detail' => 'Lintujen tunnistuspeli on ohjelmointiharjoitus, jossa yhdistyvät frontend ja backend. Selaimessa toimii itse peli ja käyttöliittymä, ja palvelinpuoli huolehtii lintukuvista, lajitiedoista ja tuloksista.',
    'l_lib_detail'  => 'Manalan verkkokirjasto on ohjelmointiharjoitus, jossa yhdistyvät frontend, backend ja tietokantaohjelmointi. MySQL-taulut, PDO-yhteydet ja CRUD-toiminnot (lisää, hae, muokkaa, poista) muodostavat toimivan kirjastojärjestelmän. Se on ohjelmistokehittäjän tutkinnon opinnäytetyö (Vamia).',
    'detail_btn'    => 'Siirry sivulle',
    'l_yoga'  => 'Jooga', 'l_yoga_title' => 'Jooga — historia, filosofia ja vinyasa',
    'contact' => 'Yhteystiedot',
  ],
];
$L = $T[$lang];
$L['bio'] = str_replace('{ika}', (string)$age, $L['bio']); // ikä esittelytekstiin

function purl(string $lg): string { return page_url('', $lg); }
$vitrupeUrl = $base . 'vitrupe/' . ($lang === 'lv' ? '' : $lang . '/');
$blogUrl    = $base . 'blogi.php' . ($lang === 'lv' ? '' : '?lang=' . $lang);
$vidzemeUrl = $base . 'vidzeme.php' . ($lang === 'lv' ? '' : '?lang=' . $lang);
$jogaUrl    = $base . 'joga.php' . ($lang === 'lv' ? '' : '?lang=' . $lang);
$guestUrl   = 'viesu_gramata.php' . ($lang === 'lv' ? '' : '?lang=' . $lang);
$fbUrl      = 'palaute.php' . ($lang === 'lv' ? '' : '?lang=' . $lang);
/* Palautelomakkeen tekstit (sama tiedosto kuin palaute.php:ssä) */
$P = (require __DIR__ . '/palaute_tekstit.php')[$lang];
$guestLink = fn(string $cls = '') => '<a' . ($cls ? ' class="' . $cls . '"' : '') . ' href="' . e($guestUrl) . '">' . e($P['guest_in']) . '</a>';

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
  'map'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>',
];
$links = [
  ['url' => 'https://www.manalasdungeon.lv/birdgame/',       'label' => $L['l_bird'],    'sub' => $L['l_bird_sub'],    'icon' => $icons['bird'], 'detail' => $L['l_bird_detail']],
  ['url' => 'https://www.manalasdungeon.lv/verkkokirjasto/', 'label' => $L['l_lib'],     'sub' => $L['l_lib_sub'],     'icon' => $icons['lib'],  'detail' => $L['l_lib_detail']],
  ['url' => $vitrupeUrl,                                      'label' => $L['l_vitrupe'], 'sub' => $L['l_vitrupe_sub'], 'icon' => $icons['wave']],
  ['url' => $blogUrl,                                         'label' => $L['l_blog'],    'sub' => $L['l_blog_sub'],    'icon' => $icons['news'], 'ts' => $latestTs, 'date' => $latestDate],
  ['url' => $vidzemeUrl,                                      'label' => $L['l_vidzeme'], 'sub' => $L['l_vidzeme_sub'], 'icon' => $icons['map']],
  ['url' => 'viesu_gramata.php',                              'label' => $L['l_guest'],   'sub' => $L['l_guest_sub'],   'icon' => $icons['pen']],
];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?= head_analytics('    ') ?>
    <title><?= e($L['title']) ?></title>

    <meta name="description" content="<?= e($L['desc']) ?>">
    <meta name="author" content="Marko Nakari">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#8d2027">
    <?= head_alternates('purl', $lang, '    ') ?>
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

    <link rel="preload" as="image" href="<?= e(asset('images/info_bg.webp')) ?>" media="(min-aspect-ratio: 3/4)">
    <link rel="preload" as="image" href="<?= e(asset('images/info_bg_m.webp')) ?>" media="(max-aspect-ratio: 3/4)">
    <?= head_fonts('playfair-display-latin-wght-normal.woff2', '    ') ?>    <link rel="stylesheet" href="<?= e(asset('manala.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('palaute.css')) ?>">
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
<?php if ($__lv18): ?>
    <style>
      /* 18.11. juhlakoristeet: lipun raita ylhäällä ja kultainen merkki kortissa */
      .lv18-stripe{position:fixed;left:0;right:0;top:0;height:8px;z-index:60;pointer-events:none;
        background:linear-gradient(#9E3039 0 40%,#fff 40% 60%,#9E3039 60% 100%);box-shadow:0 2px 10px rgba(0,0,0,.35)}
      .lv18-badge{display:inline-flex;align-items:center;gap:.55rem;margin:0 0 14px;padding:6px 14px 6px 8px;border-radius:999px;
        font-size:.95rem;font-weight:600;color:#fff;letter-spacing:.01em;
        background:linear-gradient(110deg,rgba(158,48,57,.85),rgba(110,18,35,.85));border:1px solid rgba(228,199,122,.6);
        box-shadow:0 0 0 0 rgba(228,199,122,.5);animation:lv18glow 3s ease-in-out infinite}
      .lv18-flag{width:26px;height:16px;border-radius:3px;flex:none;
        background:linear-gradient(#9E3039 0 40%,#fff 40% 60%,#9E3039 60% 100%);box-shadow:0 0 0 1px rgba(255,255,255,.35)}
      .lv18-date{color:#F3D98A;font-weight:700}
      @keyframes lv18glow{50%{box-shadow:0 0 18px 2px rgba(228,199,122,.35)}}
      @media (prefers-reduced-motion:reduce){.lv18-badge{animation:none}}
    </style>
<?php endif; ?>
</head>
<body style="--hero-bg:url(<?= asset('images/info_bg.webp') ?>); --hero-bg-m:url(<?= asset('images/info_bg_m.webp') ?>)">
<?php if ($__lv18): ?>    <div class="lv18-stripe" aria-hidden="true"></div>
<?php endif; ?>
    <!-- Kielivalitsin -->
    <nav class="lang" aria-label="Kieli">
        <?php foreach ($supported as $lg): ?>
        <a href="<?= e(purl($lg)) ?>" hreflang="<?= $lg ?>" class="<?= $lg === $lang ? 'active' : '' ?>"><span class="lang__flag"><?= $flag[$lg] ?></span> <?= strtoupper($lg) ?></a>
        <?php endforeach; ?>
    </nav>

    <!-- Joogasivu — nappi vasemmassa yläkulmassa (pari kielivalitsimelle) -->
    <a class="yoga-btn" href="<?= e($jogaUrl) ?>" title="<?= e($L['l_yoga_title']) ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20c-4.5 0-8-2.2-9-5.5 2.6-.4 5.6.4 7.4 2.4"/><path d="M12 20c4.5 0 8-2.2 9-5.5-2.6-.4-5.6.4-7.4 2.4"/><path d="M12 20c-2.2-2-3.2-4.6-3.2-7.4S10 7 12 4c2 3 3.2 5.8 3.2 8.6S14.2 18 12 20z"/></svg>
        <span class="yoga-btn__label"><?= e($L['l_yoga']) ?></span>
    </a>

    <main class="wrap">
        <section class="card">
            <?php if ($__lv18): /* juhlamerkki vain 18.11. */
                $__lv18txt = ['lv' => 'Daudz laimes dzimšanas dienā, Latvija!', 'en' => 'Happy Independence Day, Latvia!', 'fi' => 'Hyvää itsenäisyyspäivää, Latvia!'][$lang]; ?>
            <p class="lv18-badge"><span class="lv18-flag" aria-hidden="true"></span><span class="lv18-date">18.11.</span> <?= e($__lv18txt) ?></p>
            <?php endif; ?>
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

            <!-- Palaute: yksityinen kommentti sähköpostiin (vaihtoehto vieraskirjalle) -->
            <div class="feedback">
                <p class="feedback__text"><?= str_replace('{guest}', $guestLink(), e($P['line'])) ?></p>
                <a class="feedback__btn" href="<?= e($fbUrl) ?>" data-palaute aria-haspopup="dialog">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 9 9 0 0 1-3.9-.9L3 20.5l1.6-4.6A8.2 8.2 0 0 1 3 11.5 8.6 8.6 0 0 1 12 3a8.6 8.6 0 0 1 9 8.5z"/><path d="M8 10h8M8 14h5"/></svg>
                    <?= e($P['btn']) ?>
                </a>
            </div>

            <div class="contact">
                <span class="contact__label"><?= e($L['contact']) ?></span>
                <span class="contact__email">marko.nakari(at)inbox.lv</span>
            </div>
        </section>

        <aside class="detail" id="detail" aria-live="polite">
            <button class="detail__close" type="button" aria-label="×">&times;</button>
            <h2 class="detail__title"></h2>
            <p class="detail__text"></p>
            <a class="detail__btn" href="#"><span class="detail__btn-label"><?= e($L['detail_btn']) ?></span> <span aria-hidden="true">&rarr;</span></a>
        </aside>
    </main>

    <!-- Palaute-ponnahdusikkuna (avautuu Kommentoi-napista; ilman JS:ää nappi vie palaute.php-sivulle) -->
    <dialog class="fb-dialog" id="palaute" aria-labelledby="fb-title" data-token-url="palaute.php?token=1"
            data-e-name="<?= e($P['e_name']) ?>" data-e-email="<?= e($P['e_email']) ?>" data-e-msg="<?= e($P['e_msg']) ?>" data-e-net="<?= e($P['e_send']) ?>">
        <div class="fb-box">
            <button class="fb-x" type="button" data-fb-close aria-label="<?= e($P['close']) ?>">&times;</button>
            <div class="fb-view" data-fb-form-view>
                <h2 class="fb-title" id="fb-title"><?= e($P['title']) ?></h2>
                <p class="fb-lead"><?= e($P['lead']) ?></p>
                <p class="fb-err fb-err--top" data-fb-err="_" role="alert" hidden></p>
                <form class="fb-form" method="post" action="palaute.php" novalidate>
                    <input type="hidden" name="csrf" value="">
                    <input type="hidden" name="lang" value="<?= e($lang) ?>">
                    <div class="fb-hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                    <label class="fb-field">
                        <span class="fb-label"><?= e($P['name']) ?> <span class="fb-req" aria-hidden="true">*</span></span>
                        <input type="text" name="name" required maxlength="60" autocomplete="name">
                        <span class="fb-err" data-fb-err="name" hidden></span>
                    </label>
                    <label class="fb-field">
                        <span class="fb-label"><?= e($P['email']) ?> <span class="fb-hint">(<?= e($P['email_h']) ?>)</span></span>
                        <input type="email" name="email" maxlength="120" autocomplete="email" inputmode="email">
                        <span class="fb-err" data-fb-err="email" hidden></span>
                    </label>
                    <label class="fb-field">
                        <span class="fb-label"><?= e($P['msg']) ?> <span class="fb-req" aria-hidden="true">*</span> <span class="fb-count" data-fb-count>0 / 1500</span></span>
                        <textarea name="msg" rows="5" required minlength="3" maxlength="1500"></textarea>
                        <span class="fb-err" data-fb-err="msg" hidden></span>
                    </label>
                    <div class="fb-actions">
                        <button class="fb-cancel" type="button" data-fb-close><?= e($P['cancel']) ?></button>
                        <button class="fb-send" type="submit" data-sending="<?= e($P['sending']) ?>"><?= e($P['send']) ?></button>
                    </div>
                </form>
                <p class="fb-pub"><?= str_replace('{guest}', $guestLink(), e($P['pub'])) ?></p>
            </div>
            <div class="fb-view fb-thanks" data-fb-thanks-view hidden>
                <div class="fb-thanks__mark" aria-hidden="true">✓</div>
                <h2 class="fb-title"><?= e($P['thanks_h']) ?></h2>
                <p class="fb-lead"><?= e($P['thanks']) ?></p>
                <button class="fb-send" type="button" data-fb-close><?= e($P['close']) ?></button>
            </div>
        </div>
    </dialog>

    <!-- Alareuna -->
    <div class="pagefoot">
        <span id="date_time_container"><span id="date_time"></span></span>
        <span class="pagefoot__links">
            <a href="<?= e(page_url('privatums.php', $lang)) ?>" class="pagefoot__link"><?= e($L['l_priv']) ?></a>
            <a href="https://portal.nano.lv/aff.php?aff=291" target="_blank" rel="noopener" id="hosting_link">nano.lv</a>
        </span>
    </div>

    <script src="<?= e(asset('etusivu.js')) ?>" defer></script>
    <script src="<?= e(asset('palaute.js')) ?>" defer></script>
    <script type="text/javascript" src="<?= e(asset('main.js')) ?>"></script>
    <script type="text/javascript" src="<?= e(asset('app.js')) ?>"></script>

    <!-- © 2026 by marko.nakari(at)inbox.lv -->
</body>
</html>
