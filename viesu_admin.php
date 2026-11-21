<?php
/**
 * Viesu grāmatas administrācija — hyväksy / piilota / poista viestejä.
 * Passkey-suojattu (passkey/guard.php): pääsy vain rekisteröidyillä laitteilla.
 * Käyttää samaa db_config.php:tä kuin viesu_gramata.php.
 */
require __DIR__ . '/passkey/guard.php';
require __DIR__ . '/db_config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// CSRF-tunnus (toiminnot ja uloskirjautuminen)
if (empty($_SESSION['gb_csrf'])) {
    $_SESSION['gb_csrf'] = bin2hex(random_bytes(32));
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// Cache-busting: lisää tiedoston muokkausaika ?v=... -parametriksi.
function asset(string $path): string {
    $full = __DIR__ . '/' . ltrim($path, '/');
    $v = @filemtime($full) ?: time();
    return $path . '?v=' . $v;
}

// Näyttää linkit, jotta admin näkee mihin ne osoittavat
function linkify(string $text): string {
    $safe = h($text);
    $safe = preg_replace_callback('#\bhttps?://[^\s<]+#i', function ($m) {
        $url = $m[0]; $trail = '';
        while ($url !== '' && strpbrk(substr($url, -1), '.,;:!?)') !== false) {
            $trail = substr($url, -1) . $trail; $url = substr($url, 0, -1);
        }
        return '<a href="' . $url . '" target="_blank" rel="nofollow noopener">' . $url . '</a>' . $trail;
    }, $safe);
    return nl2br($safe);
}

$msg = ''; $err = false;

// Objektin tyyppivaihtoehdot (näytetään merkkinä)
$TYPES = [
    'pakalpojums' => 'Pakalpojums',
    'apskates'    => 'Apskates vieta',
    'turisms'     => 'Tūrisma objekts',
    'cits'        => 'Cits',
];

// ---- Uloskirjautuminen (passkey-istunto) — CSRF-tunnisteella ----
if (($_GET['logout'] ?? '') === '1') {
    if (hash_equals($_SESSION['gb_csrf'], (string)($_GET['t'] ?? ''))) {
        unset($_SESSION['gb_csrf']);
        pk_logout();
        header('Location: /passkey/login.php'); exit;
    }
    header('Location: viesu_admin.php'); exit;
}

// ---- Toiminnot (CSRF) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && in_array($_POST['action'] ?? '', ['approve', 'hide', 'delete'], true)) {

    if (!hash_equals($_SESSION['gb_csrf'], (string)($_POST['csrf'] ?? ''))) {
        $msg = 'Sesijas kļūda. Mēģiniet vēlreiz.'; $err = true;
    } else {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            if ($_POST['action'] === 'approve') {
                db()->prepare('UPDATE viesu_gramata SET apstiprinats = 1 WHERE id = ?')->execute([$id]);
                $msg = 'Ieraksts apstiprināts.';
            } elseif ($_POST['action'] === 'hide') {
                db()->prepare('UPDATE viesu_gramata SET apstiprinats = 0 WHERE id = ?')->execute([$id]);
                $msg = 'Ieraksts paslēpts.';
            } elseif ($_POST['action'] === 'delete') {
                db()->prepare('DELETE FROM viesu_gramata WHERE id = ?')->execute([$id]);
                $msg = 'Ieraksts dzēsts.';
            }
        }
    }
}

$csrf = $_SESSION['gb_csrf'];

// ---- Hae viestit ----
$pending  = db()->query('SELECT id, nosaukums, zina, saite, tips, izveidots FROM viesu_gramata WHERE apstiprinats = 0 ORDER BY izveidots DESC')->fetchAll();
$approved = db()->query('SELECT id, nosaukums, zina, saite, tips, izveidots FROM viesu_gramata WHERE apstiprinats = 1 ORDER BY izveidots DESC LIMIT 200')->fetchAll();
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Viesu grāmata — administrācija</title>
    <link rel="stylesheet" href="<?= h(asset('viesu_gramata.css')) ?>">
    <link rel="stylesheet" href="<?= h(asset('viesu_admin.css')) ?>">
</head>
<body class="gb-body ga-body">
    <div class="ga-wrap">

        <div class="ga-head">
            <h1>Viesu grāmata · administrācija</h1>
            <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap">
                <a class="ga-logout" href="/passkey/manage.php">Ierīces</a>
                <a class="ga-logout" href="?logout=1&amp;t=<?= h($csrf) ?>">Iziet</a>
            </div>
        </div>

        <?php if ($msg): ?>
            <p class="gb-msg <?= $err ? 'gb-msg-err' : 'gb-msg-ok' ?>"><?= h($msg) ?></p>
        <?php endif; ?>

        <section class="ga-section">
            <h2 class="gb-subtitle">Gaida apstiprināšanu <span class="gb-count">(<?= count($pending) ?>)</span></h2>
            <?php if (!$pending): ?>
                <p class="gb-empty">Nav jaunu ierakstu.</p>
            <?php else: foreach ($pending as $r): ?>
                <div class="ga-item ga-item--pending">
                    <div class="gb-entry-head">
                        <span class="gb-name"><?= h($r['nosaukums']) ?></span>
                        <span class="gb-date"><?= h(date('d.m.Y H:i', strtotime($r['izveidots']))) ?></span>
                    </div>
                    <?php if (!empty($r['tips']) && isset($TYPES[$r['tips']])): ?><span class="gb-badge"><?= h($TYPES[$r['tips']]) ?></span><?php endif; ?>
                    <p class="gb-text"><?= linkify($r['zina']) ?></p>
                    <?php if (!empty($r['saite'])): ?><a class="gb-entry-link" href="<?= h($r['saite']) ?>" target="_blank" rel="nofollow noopener ugc">Apmeklēt vietni &rarr;</a><?php endif; ?>
                    <div class="ga-actions">
                        <form method="post"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="ga-btn ga-btn--ok" name="action" value="approve">Apstiprināt</button></form>
                        <form method="post" onsubmit="return confirm('Dzēst šo ierakstu?');"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="ga-btn ga-btn--del" name="action" value="delete">Dzēst</button></form>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </section>

        <section class="ga-section">
            <h2 class="gb-subtitle">Publicētie <span class="gb-count">(<?= count($approved) ?>)</span></h2>
            <?php if (!$approved): ?>
                <p class="gb-empty">Nav publicētu ierakstu.</p>
            <?php else: foreach ($approved as $r): ?>
                <div class="ga-item">
                    <div class="gb-entry-head">
                        <span class="gb-name"><?= h($r['nosaukums']) ?></span>
                        <span class="gb-date"><?= h(date('d.m.Y H:i', strtotime($r['izveidots']))) ?></span>
                    </div>
                    <?php if (!empty($r['tips']) && isset($TYPES[$r['tips']])): ?><span class="gb-badge"><?= h($TYPES[$r['tips']]) ?></span><?php endif; ?>
                    <p class="gb-text"><?= linkify($r['zina']) ?></p>
                    <?php if (!empty($r['saite'])): ?><a class="gb-entry-link" href="<?= h($r['saite']) ?>" target="_blank" rel="nofollow noopener ugc">Apmeklēt vietni &rarr;</a><?php endif; ?>
                    <div class="ga-actions">
                        <form method="post"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="ga-btn" name="action" value="hide">Paslēpt</button></form>
                        <form method="post" onsubmit="return confirm('Dzēst šo ierakstu?');"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="ga-btn ga-btn--del" name="action" value="delete">Dzēst</button></form>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </section>

    </div>
</body>
</html>
