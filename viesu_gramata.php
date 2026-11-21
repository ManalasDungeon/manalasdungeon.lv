<?php
require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, kieli, <head>-palat
/**
 * Viesu grāmata — vieraskirja (PHP + MySQL), kolmikielinen (lv/en/fi).
 * Vasen palsta: viestilomake. Oikea palsta: viestit.
 * Kieli: ?lang=en / ?lang=fi, oletus lv.
 */

require __DIR__ . '/db_config.php';

session_start();

/* =====================================================================
 *  Sähköposti-ilmoitus uudesta vieraskirjaviestistä. (Admin-posti latviaksi.)
 * ===================================================================== */
// Ilmoitusten vastaanottaja: admin_mail() (palvelimen mail_config.php)
const GB_NOTIFY_FROM = 'noreply@manalasdungeon.lv';

/* ---- Kieli ---- */
$supported = ['lv', 'en', 'fi'];
$default   = 'lv';
$lang = $_GET['lang'] ?? $default;
if (!in_array($lang, $supported, true)) { $lang = $default; }
$base = 'https://manalasdungeon.lv/';

/* ---- Käännökset ---- */
$TT = [
  'lv' => [
    'title'    => 'Viesu grāmata — Manalasdungeon.lv',
    'h_form'   => 'Atstāj ziņu',
    'invite'   => 'Vai vēlaties, lai jūsu pakalpojums, apskates vieta vai tūrisma objekts būtu redzams mūsu vietnē? Atstājiet ziņu un iekļaujiet saiti — mēs to labprāt pieminēsim.',
    'l_name'   => 'Vārds',
    'l_msg'    => 'Ziņa',
    'opt_head' => 'Piedāvā pakalpojumu vai objektu? (nav obligāts)',
    'l_type'   => 'Veids',
    'l_link'   => 'Saite (URL)',
    'choose'   => '— izvēlies —',
    'send'     => 'Nosūtīt',
    'entries'  => 'Ieraksti',
    'empty'    => 'Vēl nav neviena ieraksta. Esi pirmais!',
    'visit'    => 'Apmeklēt vietni',
    'back'     => 'Atpakaļ uz Vitrupes Pludmali',
    'role'     => 'Programmatūras izstrādātājs 2026',
    'ok'       => 'Paldies! Tava ziņa ir saņemta un tiks parādīta pēc apstiprināšanas.',
    'e_sess'   => 'Sesijas kļūda. Lūdzu, mēģiniet vēlreiz.',
    'e_name'   => 'Vārds ir obligāts (maks. 60 rakstzīmes).',
    'e_msg'    => 'Ziņa ir obligāta (maks. 1000 rakstzīmes).',
    'e_link'   => 'Saite nav derīga (jāsākas ar http:// vai https://).',
    'e_wait'   => 'Lūdzu, uzgaidiet brīdi pirms nākamās ziņas.',
    'loc'      => 'lv_LV',
    'fmt'      => 'd.m.Y H:i',
  ],
  'en' => [
    'title'    => 'Guestbook — Manalasdungeon.lv',
    'h_form'   => 'Leave a message',
    'invite'   => 'Would you like your service, sight or tourist attraction to appear on our site? Leave a message and include a link — we’ll gladly mention it.',
    'l_name'   => 'Name',
    'l_msg'    => 'Message',
    'opt_head' => 'Offering a service or place? (optional)',
    'l_type'   => 'Type',
    'l_link'   => 'Link (URL)',
    'choose'   => '— choose —',
    'send'     => 'Send',
    'entries'  => 'Entries',
    'empty'    => 'No entries yet. Be the first!',
    'visit'    => 'Visit the site',
    'back'     => 'Back to Vitrupes Pludmale',
    'role'     => 'Software developer 2026',
    'ok'       => 'Thank you! Your message has been received and will appear after approval.',
    'e_sess'   => 'Session error. Please try again.',
    'e_name'   => 'Name is required (max 60 characters).',
    'e_msg'    => 'Message is required (max 1000 characters).',
    'e_link'   => 'The link is not valid (it must start with http:// or https://).',
    'e_wait'   => 'Please wait a moment before sending another message.',
    'loc'      => 'en_US',
    'fmt'      => 'M j, Y H:i',
  ],
  'fi' => [
    'title'    => 'Vieraskirja — Manalasdungeon.lv',
    'h_form'   => 'Jätä viesti',
    'invite'   => 'Haluaisitko palvelusi, nähtävyytesi tai matkailukohteesi näkyvän sivustollamme? Jätä viesti ja liitä linkki — mainitsemme sen mielellämme.',
    'l_name'   => 'Nimi',
    'l_msg'    => 'Viesti',
    'opt_head' => 'Tarjoatko palvelua tai kohdetta? (valinnainen)',
    'l_type'   => 'Tyyppi',
    'l_link'   => 'Linkki (URL)',
    'choose'   => '— valitse —',
    'send'     => 'Lähetä',
    'entries'  => 'Viestit',
    'empty'    => 'Ei vielä yhtään viestiä. Ole ensimmäinen!',
    'visit'    => 'Käy sivustolla',
    'back'     => 'Takaisin Vitrupes Pludmaleen',
    'role'     => 'Ohjelmistokehittäjä 2026',
    'ok'       => 'Kiitos! Viestisi on vastaanotettu ja näkyy hyväksynnän jälkeen.',
    'e_sess'   => 'Istuntovirhe. Yritä uudelleen.',
    'e_name'   => 'Nimi on pakollinen (enintään 60 merkkiä).',
    'e_msg'    => 'Viesti on pakollinen (enintään 1000 merkkiä).',
    'e_link'   => 'Linkki ei kelpaa (sen on alettava http:// tai https://).',
    'e_wait'   => 'Odota hetki ennen seuraavan viestin lähettämistä.',
    'loc'      => 'fi_FI',
    'fmt'      => 'd.m.Y H:i',
  ],
];
$T = $TT[$lang];

/* Objektin tyypit: avaimet kiinteät (kannassa), nimet käännetään. */
$TYPE_KEYS   = ['pakalpojums', 'apskates', 'turisms', 'cits'];
$TYPE_LABELS = [
  'lv' => ['pakalpojums'=>'Pakalpojums','apskates'=>'Apskates vieta','turisms'=>'Tūrisma objekts','cits'=>'Cits'],
  'en' => ['pakalpojums'=>'Service','apskates'=>'Sight','turisms'=>'Tourist attraction','cits'=>'Other'],
  'fi' => ['pakalpojums'=>'Palvelu','apskates'=>'Nähtävyys','turisms'=>'Matkailukohde','cits'=>'Muu'],
];
$TYPES = $TYPE_LABELS[$lang]; // nykyisen kielen nimet

// --- CSRF-tunnus ---
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$errors  = [];
$success = false;

// --- Lomakkeen käsittely ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        $errors[] = $T['e_sess'];
    }

    // Hunajapurkki (bottien ansa)
    if (!empty($_POST['website'])) {
        $success = true;
    } elseif (!$errors) {
        $nosaukums = trim($_POST['nosaukums'] ?? '');
        $zina      = trim($_POST['zina'] ?? '');

        if ($nosaukums === '' || mb_strlen($nosaukums) > 60) {
            $errors[] = $T['e_name'];
        }
        if ($zina === '' || mb_strlen($zina) > 1000) {
            $errors[] = $T['e_msg'];
        }

        $saite = trim($_POST['saite'] ?? '');
        $tips  = trim($_POST['tips'] ?? '');
        if ($saite !== '') {
            if (mb_strlen($saite) > 255
                || !preg_match('#^https?://#i', $saite)
                || !filter_var($saite, FILTER_VALIDATE_URL)) {
                $errors[] = $T['e_link'];
            }
        }
        if ($tips !== '' && !in_array($tips, $TYPE_KEYS, true)) {
            $tips = '';
        }

        // Spam-rajoitus: sama IP enintään kerran / 30 s
        $ipHash = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|manala_salt');
        if (!$errors) {
            $stmt = db()->prepare(
                'SELECT COUNT(*) FROM viesu_gramata
                 WHERE ip_hash = ? AND izveidots > (NOW() - INTERVAL 30 SECOND)'
            );
            $stmt->execute([$ipHash]);
            if ((int)$stmt->fetchColumn() > 0) {
                $errors[] = $T['e_wait'];
            }
        }

        if (!$errors) {
            $stmt = db()->prepare(
                'INSERT INTO viesu_gramata (nosaukums, zina, saite, tips, izveidots, ip_hash, apstiprinats)
                 VALUES (?, ?, ?, ?, NOW(), ?, 0)'
            );
            $stmt->execute([
                $nosaukums,
                $zina,
                ($saite !== '' ? $saite : null),
                ($tips !== '' ? $tips : null),
                $ipHash,
            ]);
            $success = true;
            $_SESSION['csrf'] = bin2hex(random_bytes(32));

            // --- Ilmoitus ylläpitäjälle (latviaksi) ---
            $mailBody  = "Vietnē manalasdungeon.lv ir jauna viesu grāmatas ziņa (gaida apstiprināšanu).\n\n";
            $mailBody .= "Vārds: {$nosaukums}\n";
            if ($tips !== '')  { $mailBody .= "Veids: " . ($TYPE_LABELS['lv'][$tips] ?? $tips) . "\n"; }
            if ($saite !== '') { $mailBody .= "Saite: {$saite}\n"; }
            $mailBody .= "Ziņa:\n{$zina}\n\n";
            $mailBody .= "Apstiprini vai dzēs šeit:\nhttps://manalasdungeon.lv/viesu_admin.php\n";

            $mailSubject = '=?UTF-8?B?' . base64_encode('Jauna viesu grāmatas ziņa') . '?=';
            $mailHeaders  = 'From: Manalas Dungeon <' . GB_NOTIFY_FROM . ">\r\n";
            $mailHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $mailHeaders .= "X-Mailer: PHP\r\n";

            if (admin_mail() !== '') @mail(admin_mail(), $mailSubject, $mailBody, $mailHeaders, '-f' . GB_NOTIFY_FROM);
        }
    }
}

// --- Hae viestit ---
$rows = db()->query(
    'SELECT nosaukums, zina, saite, tips, izveidots
     FROM viesu_gramata
     WHERE apstiprinats = 1
     ORDER BY izveidots DESC
     LIMIT 100'
)->fetchAll();

// Turvallinen tulostus
// Sivun URL kielellä (lv juuressa, en/fi parametrilla)
function gb_url(string $lg): string { return page_url('viesu_gramata.php', $lg); }
// http/https-osoitteet klikattaviksi linkeiksi (teksti suojataan ensin)
function linkify(string $text): string {
    $safe = h($text);
    $safe = preg_replace_callback('#\bhttps?://[^\s<]+#i', function ($m) {
        $url = $m[0]; $trail = '';
        while ($url !== '' && strpbrk(substr($url, -1), '.,;:!?)') !== false) {
            $trail = substr($url, -1) . $trail;
            $url   = substr($url, 0, -1);
        }
        return '<a href="' . $url . '" target="_blank" rel="nofollow noopener ugc">' . $url . '</a>' . $trail;
    }, $safe);
    return nl2br($safe);
}
$csrf = $_SESSION['csrf'];
$formAction = 'viesu_gramata.php' . ($lang === $default ? '' : '?lang=' . $lang);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?= head_analytics('    ') ?>
    <title><?= h($T['title']) ?></title>
    <meta name="description" content="<?= h($T['title']) ?>">
    <meta name="robots" content="index, follow">
    <?= head_alternates('gb_url', $lang, '    ') ?>    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Manalas Dungeon">
    <meta property="og:title" content="<?= h($T['title']) ?>">
    <meta property="og:url" content="<?= h(gb_url($lang)) ?>">
    <meta property="og:locale" content="<?= h($T['loc']) ?>">

    <link rel="stylesheet" href="<?= h(asset('manala.css')) ?>">
    <?= head_fonts('playfair-display-latin-wght-normal.woff2', '    ') ?>    <link rel="stylesheet" href="<?= h(asset('viesu_gramata.css')) ?>">
</head>
<body class="gb-body">

    <header class="gb-hero">
        <!-- Otsikko on kuvassa; h1 ruudunlukijoille ja hakukoneille -->
        <h1 class="gb-sr"><?= h(explode(' — ', $T['title'])[0]) ?></h1>
        <a class="gb-back" href="/">&larr; Manalasdungeon.lv</a>
        <!-- Kielivalitsin -->
        <div class="gb-lang">
            <?php foreach ($supported as $lg): ?>
            <a href="<?= h(gb_url($lg)) ?>" hreflang="<?= $lg ?>" class="<?= $lg === $lang ? 'active' : '' ?>"><?= strtoupper($lg) ?></a>
            <?php endforeach; ?>
        </div>
    </header>

    <main class="gb-layout">

        <!-- VASEN: lomake -->
        <section class="gb-form-col">
            <div class="card gb-card">
                <h2 class="gb-subtitle"><?= h($T['h_form']) ?></h2>
                <p class="gb-invite"><?= h($T['invite']) ?></p>

                <?php if ($success): ?>
                    <p class="gb-msg gb-msg-ok"><?= h($T['ok']) ?></p>
                <?php endif; ?>

                <?php foreach ($errors as $e): ?>
                    <p class="gb-msg gb-msg-err"><?= h($e) ?></p>
                <?php endforeach; ?>

                <form method="post" action="<?= h($formAction) ?>" class="gb-form" autocomplete="off">
                    <input type="hidden" name="csrf" value="<?= h($csrf) ?>">

                    <!-- hunajapurkki -->
                    <div class="gb-hp" aria-hidden="true">
                        <label>Neaizpildīt: <input type="text" name="website" tabindex="-1"></label>
                    </div>

                    <label class="gb-label" for="nosaukums"><?= h($T['l_name']) ?></label>
                    <input class="gb-input" type="text" id="nosaukums" name="nosaukums"
                           maxlength="60" required
                           value="<?= isset($_POST['nosaukums']) && !$success ? h($_POST['nosaukums']) : '' ?>">

                    <label class="gb-label" for="zina"><?= h($T['l_msg']) ?></label>
                    <textarea class="gb-input gb-textarea" id="zina" name="zina"
                              maxlength="1000" rows="6" required><?= isset($_POST['zina']) && !$success ? h($_POST['zina']) : '' ?></textarea>

                    <div class="gb-optional">
                        <p class="gb-optional-head"><?= h($T['opt_head']) ?></p>

                        <label class="gb-label" for="tips"><?= h($T['l_type']) ?></label>
                        <select class="gb-input" id="tips" name="tips">
                            <option value=""><?= h($T['choose']) ?></option>
                            <?php foreach ($TYPES as $k => $lbl): ?>
                            <option value="<?= h($k) ?>" <?= (($_POST['tips'] ?? '') === $k && !$success) ? 'selected' : '' ?>><?= h($lbl) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label class="gb-label" for="saite"><?= h($T['l_link']) ?></label>
                        <input class="gb-input" type="url" id="saite" name="saite" maxlength="255" placeholder="https://..."
                               value="<?= isset($_POST['saite']) && !$success ? h($_POST['saite']) : '' ?>">
                    </div>

                    <button class="gb-submit" type="submit"><?= h($T['send']) ?></button>
                </form>
            </div>
        </section>

        <!-- OIKEA: viestit -->
        <section class="gb-list-col">
            <div class="card gb-card">
                <h2 class="gb-subtitle"><?= h($T['entries']) ?> <span class="gb-count">(<?= count($rows) ?>)</span></h2>

                <?php if (!$rows): ?>
                    <p class="gb-empty"><?= h($T['empty']) ?></p>
                <?php else: ?>
                    <ul class="gb-entries">
                        <?php foreach ($rows as $r): ?>
                            <li class="gb-entry">
                                <div class="gb-entry-head">
                                    <span class="gb-name"><?= h($r['nosaukums']) ?></span>
                                    <span class="gb-date"><?= h(date($T['fmt'], strtotime($r['izveidots']))) ?></span>
                                </div>
                                <?php if (!empty($r['tips']) && isset($TYPES[$r['tips']])): ?>
                                    <span class="gb-badge"><?= h($TYPES[$r['tips']]) ?></span>
                                <?php endif; ?>
                                <p class="gb-text"><?= linkify($r['zina']) ?></p>
                                <?php if (!empty($r['saite'])): ?>
                                    <a class="gb-entry-link" href="<?= h($r['saite']) ?>" target="_blank" rel="nofollow noopener ugc"><?= h($T['visit']) ?> &rarr;</a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <footer class="gb-footer">
        <a class="gb-footer-link" href="vitrupe/"><span class="gb-arrow" aria-hidden="true">&larr;</span> <?= h($T['back']) ?></a>
        <p class="gb-footer-copy">&copy; <a href="https://manalasdungeon.lv">Marko Nakari</a> &middot; <?= h($T['role']) ?></p>
    </footer>

</body>
</html>
