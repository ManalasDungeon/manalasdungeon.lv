<?php
/**
 * Palaute — yksityinen kommentti sivusta suoraan sähköpostiin (ei julkaista).
 *
 *  GET  palaute.php?token=1   → JSON {token} ponnahdusikkunalle (istunto alkaa vasta tässä,
 *                               joten etusivu pysyy evästeettömänä kunnes ikkuna avataan)
 *  GET  palaute.php?lang=fi   → oma sivu lomakkeella (toimii myös ilman JavaScriptiä)
 *  POST palaute.php           → käsittely; JSON jos Accept: application/json, muuten HTML
 *
 * Suojaukset: CSRF-tunnus istunnossa · hunajapurkki · aikaansa (alle 3 s = botti) ·
 * IP-kohtainen raja (1 / 60 s, 5 / vrk, tallennetaan vain hash) + koko sivuston katto 40 / vrk ·
 * linkkiraja · otsakeinjektion esto (nimi/sähköposti siivotaan ja koodataan) · htmlspecialchars.
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, kieli, <head>-palat

const PALAUTE_TO     = 'marko.nakari@manalasdungeon.lv';   // sama postilaatikko kuin vieraskirjan ilmoituksilla
const PALAUTE_FROM   = 'noreply@manalasdungeon.lv';
const MIN_SECONDS    = 3;      // lomakkeen avaamisesta lähetykseen vähintään
const IP_GAP         = 60;     // sama IP: vähintään 60 s viestien välillä
const IP_PER_DAY     = 5;      // sama IP: enintään 5 viestiä / vrk
const ALL_PER_DAY    = 40;     // koko sivusto: enintään 40 viestiä / vrk (postilaatikon suoja)
const MAX_NAME       = 60;
const MAX_EMAIL      = 120;
const MAX_MSG        = 1500;
const MAX_LINKS      = 2;

/* ---- Kieli ---- */
$supported = ['lv', 'en', 'fi'];
$lang = $_POST['lang'] ?? $_GET['lang'] ?? 'lv';
if (!is_string($lang) || !in_array($lang, $supported, true)) { $lang = 'lv'; }
$ALL = require __DIR__ . '/palaute_tekstit.php';
$P   = $ALL[$lang];

/* ---- Istunto (turvalliset evästeasetukset) ---- */
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https,
                               'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');


function issue_token(): string {
    if (empty($_SESSION['palaute_csrf'])) {
        $_SESSION['palaute_csrf'] = bin2hex(random_bytes(32));
    }
    $_SESSION['palaute_t'] = time();          // aikaansa alkaa lomakkeen avauksesta
    return $_SESSION['palaute_csrf'];
}

/** Poistaa ohjausmerkit (myös rivinvaihdot) — otsakkeisiin menevät kentät. */
function one_line(string $s): string {
    return trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $s) ?? '');
}

/** RFC 2047 -koodaus UTF-8:lle, pilkottuna alle 75 merkin osiin merkkirajoja rikkomatta. */
function enc_header(string $s): string {
    $parts = []; $buf = '';
    foreach (mb_str_split($s, 1, 'UTF-8') as $ch) {
        if (strlen($buf . $ch) > 45) { $parts[] = $buf; $buf = ''; }
        $buf .= $ch;
    }
    if ($buf !== '') { $parts[] = $buf; }
    return implode("\r\n ", array_map(fn($p) => '=?UTF-8?B?' . base64_encode($p) . '?=', $parts));
}

/** IP-kohtainen ja koko sivuston raja. Tiedosto web-juuren ULKOPUOLELLA; tallennetaan vain hash. */
function rate_take(string $ipHash): bool {
    $dir  = dirname(__DIR__);
    $file = (is_dir($dir) && is_writable($dir)) ? $dir . '/.manala_palaute_raja.json'
                                                : sys_get_temp_dir() . '/manala_palaute_raja.json';
    $fp = @fopen($file, 'c+');
    if ($fp === false) { return true; }                      // ei estetä jos tiedostoa ei saada auki
    try {
        flock($fp, LOCK_EX);
        $data = json_decode((string) stream_get_contents($fp), true);
        if (!is_array($data)) { $data = []; }
        $now = time();
        foreach ($data as $k => $list) {                      // vanhat (yli vrk) pois
            $data[$k] = array_values(array_filter((array) $list, fn($t) => is_int($t) && $t > $now - 86400));
            if (!$data[$k]) { unset($data[$k]); }
        }
        $mine = $data[$ipHash] ?? [];
        $all  = $data['_all'] ?? [];
        if (($mine && $now - max($mine) < IP_GAP) || count($mine) >= IP_PER_DAY || count($all) >= ALL_PER_DAY) {
            return false;
        }
        $data[$ipHash][] = $now;
        $data['_all'][]  = $now;
        ftruncate($fp, 0); rewind($fp);
        fwrite($fp, json_encode($data));
        fflush($fp);
        return true;
    } finally {
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}

/* ---- Tunnus ponnahdusikkunalle ---- */
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['token' => issue_token()]);
    exit;
}

/* ---- Käsittely ---- */
$wantsJson = str_contains((string)($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');
$errors = [];
$sent   = false;
$val    = ['name' => '', 'email' => '', 'msg' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!is_string($token) || empty($_SESSION['palaute_csrf']) || !hash_equals($_SESSION['palaute_csrf'], $token)) {
        $errors['_'] = $P['e_sess'];
    } elseif (!empty($_POST['website'])
           || time() - (int)($_SESSION['palaute_t'] ?? 0) < MIN_SECONDS) {
        $sent = true;                                         // botti: näytetään "kiitos", ei lähetetä mitään
    } else {
        $name  = one_line((string)($_POST['name'] ?? ''));
        $name  = trim(str_replace(['<', '>', '"'], '', $name));
        $email = one_line((string)($_POST['email'] ?? ''));
        $msg   = str_replace(["\r\n", "\r"], "\n", (string)($_POST['msg'] ?? ''));
        $msg   = trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $msg) ?? '');
        $val   = ['name' => $name, 'email' => $email, 'msg' => $msg];

        if ($name === '' || mb_strlen($name) > MAX_NAME) { $errors['name'] = $P['e_name']; }
        if ($email !== '' && (mb_strlen($email) > MAX_EMAIL || !filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $errors['email'] = $P['e_email'];
        }
        $len = mb_strlen($msg);
        if ($len < 3 || $len > MAX_MSG) {
            $errors['msg'] = $P['e_msg'];
        } elseif (preg_match_all('~https?://|www\.~i', $msg) > MAX_LINKS) {
            $errors['msg'] = $P['e_links'];
        }

        $last = (int)($_SESSION['palaute_last'] ?? 0);
        if (!$errors && time() - $last < IP_GAP) { $errors['_'] = $P['e_wait']; }
        if (!$errors) {
            $ipHash = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|manala_palaute');
            if (!rate_take($ipHash)) { $errors['_'] = $P['e_wait']; }
        }

        if (!$errors) {
            /* Sähköposti ylläpitäjälle (latviaksi, kuten vieraskirjan ilmoitukset).
               Lähettäjän nimi näkyy postilaatikossa sekä lähettäjänä että otsikossa. */
            $when = (new DateTime('now', new DateTimeZone('Europe/Helsinki')))->format('d.m.Y H:i');
            $body  = "Jauns komentārs no vietnes manalasdungeon.lv (netiek publicēts).\n\n";
            $body .= "Vārds:   {$name}\n";
            $body .= "E-pasts: " . ($email !== '' ? $email : '—') . "\n";
            $body .= "Valoda:  {$lang}\n";
            $body .= "Laiks:   {$when}\n\n";
            $body .= "Ziņa:\n{$msg}\n";
            if ($email !== '') { $body .= "\n—\nLai atbildētu, vienkārši nospied “Atbildēt”.\n"; }

            $headers  = 'From: ' . enc_header($name . ' · manalasdungeon.lv') . ' <' . PALAUTE_FROM . ">\r\n";
            if ($email !== '') { $headers .= 'Reply-To: ' . enc_header($name) . ' <' . $email . ">\r\n"; }
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $headers .= "Content-Transfer-Encoding: 8bit\r\n";
            $headers .= "X-Mailer: manalasdungeon.lv\r\n";

            $ok = @mail(PALAUTE_TO, enc_header('Komentārs no manalasdungeon.lv — ' . $name),
                        $body, $headers, '-f' . PALAUTE_FROM);
            if ($ok) {
                $sent = true;
                $_SESSION['palaute_last'] = time();
                $_SESSION['palaute_csrf'] = bin2hex(random_bytes(32));   // uusi tunnus seuraavaan kertaan
            } else {
                $errors['_'] = $P['e_send'];
            }
        }
    }

    if ($wantsJson) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($sent ? ['ok' => true] : ['ok' => false, 'errors' => $errors], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/* ---- Oma sivu (ilman JavaScriptiä tai suora osoite) ---- */
$csrf    = issue_token();
$homeUrl = 'https://manalasdungeon.lv/' . ($lang === 'lv' ? '' : '?lang=' . $lang);
$guest   = '<a href="viesu_gramata.php' . ($lang === 'lv' ? '' : '?lang=' . $lang) . '">' . h($P['guest_in']) . '</a>';
$fieldErr = fn(string $k) => isset($errors[$k]) ? '<p class="fb-err" id="fb-err-' . $k . '">' . h($errors[$k]) . '</p>' : '';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= h($P['title']) ?> — Manalas Dungeon</title>
    <?= head_fonts('playfair-display-latin-wght-normal.woff2', '    ') ?>    <link rel="stylesheet" href="<?= h(asset('manala.css')) ?>">
    <link rel="stylesheet" href="<?= h(asset('palaute.css')) ?>">
</head>
<body style="--hero-bg:url(<?= h(asset('images/info_bg.webp')) ?>); --hero-bg-m:url(<?= h(asset('images/info_bg_m.webp')) ?>)">
    <main class="wrap">
        <section class="card fb-page">
            <?php if ($sent): ?>
                <div class="fb-thanks">
                    <h1 class="fb-title"><?= h($P['thanks_h']) ?></h1>
                    <p class="fb-lead"><?= h($P['thanks']) ?></p>
                    <a class="fb-send" href="<?= h($homeUrl) ?>"><?= h($P['back']) ?></a>
                </div>
            <?php else: ?>
                <h1 class="fb-title"><?= h($P['title']) ?></h1>
                <p class="fb-lead"><?= h($P['lead']) ?></p>
                <?= isset($errors['_']) ? '<p class="fb-err fb-err--top" role="alert">' . h($errors['_']) . '</p>' : '' ?>
                <form class="fb-form" method="post" action="palaute.php" novalidate>
                    <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
                    <input type="hidden" name="lang" value="<?= h($lang) ?>">
                    <div class="fb-hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                    <label class="fb-field">
                        <span class="fb-label"><?= h($P['name']) ?> <span class="fb-req" aria-hidden="true">*</span></span>
                        <input type="text" name="name" required maxlength="<?= MAX_NAME ?>" autocomplete="name" value="<?= h($val['name']) ?>">
                        <?= $fieldErr('name') ?>
                    </label>
                    <label class="fb-field">
                        <span class="fb-label"><?= h($P['email']) ?> <span class="fb-hint">(<?= h($P['email_h']) ?>)</span></span>
                        <input type="email" name="email" maxlength="<?= MAX_EMAIL ?>" autocomplete="email" inputmode="email" value="<?= h($val['email']) ?>">
                        <?= $fieldErr('email') ?>
                    </label>
                    <label class="fb-field">
                        <span class="fb-label"><?= h($P['msg']) ?> <span class="fb-req" aria-hidden="true">*</span></span>
                        <textarea name="msg" rows="5" required minlength="3" maxlength="<?= MAX_MSG ?>"><?= h($val['msg']) ?></textarea>
                        <?= $fieldErr('msg') ?>
                    </label>
                    <div class="fb-actions">
                        <a class="fb-cancel" href="<?= h($homeUrl) ?>"><?= h($P['cancel']) ?></a>
                        <button class="fb-send" type="submit"><?= h($P['send']) ?></button>
                    </div>
                </form>
                <p class="fb-pub"><?= str_replace('{guest}', $guest, h($P['pub'])) ?></p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
