<?php
/**
 * Yhteinen kerros kaikille sivuille: kieli, suojaus, osoitteet, <head>-palat ja turvaotsakkeet.
 * Rivien sisennys: ensimmäinen rivi tulee sivulta, muut parametrista $indent.
 * Käyttö sivun alussa:  require_once __DIR__ . '/includes/bootstrap.php';
 *
 * Funktiot:
 *   site_lang()                       kieli ?lang=-parametrista (sallittujen lista, oletus lv)
 *   h($s) / e($s)                     HTML-suojaus (htmlspecialchars, UTF-8)
 *   asset('tiedosto.css')             linkki + ?v=<muokkausaika> (välimuistin ohitus)
 *   page_url('sivu.php', $lang)       kanoninen osoite kieliversiolle ('' = etusivu)
 *   head_alternates($urlFn, $lang)    canonical + hreflang (lv, en, fi, x-default)
 *   head_fonts($preload)              omat fontit (fonts.css) + tärkeimmän fontin preload
 *   head_analytics()                  Google Analytics 4 (ulkoinen tiedosto, ei inline-skriptiä)
 *   csp_nonce()                       sivukohtainen tunniste inline-skripteille: <script nonce="<?= csp_nonce() ?>">
 *
 * Turvaotsakkeet (CSP ym.) lähetetään automaattisesti, kun tämä tiedosto ladataan.
 */
if (defined('MANALA_BOOT')) { return; }
define('MANALA_BOOT', true);

const SITE_URL          = 'https://manalasdungeon.lv/';
const SITE_LANGS        = ['lv', 'en', 'fi'];
const SITE_DEFAULT_LANG = 'lv';
const GA_ID             = 'G-0Q1HVYWR72';

/* Content-Security-Policy:
 *   false = Report-Only: rikkeet kirjataan lokiin (csp-raportti.php), mitään ei estetä
 *   true  = sääntö on voimassa ja selain estää kaiken muun
 * Ota käyttöön (true), kun loki on ollut tyhjä viikon tai pari. */
const CSP_ENFORCE       = true;
define('SITE_ROOT', dirname(__DIR__));

function site_lang(): string
{
    $l = $_GET['lang'] ?? '';
    return (is_string($l) && in_array($l, SITE_LANGS, true)) ? $l : SITE_DEFAULT_LANG;
}

function h(?string $s): string { return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function e(?string $s): string { return h($s); }

/** Tiedoston osoite + muokkausaika, jotta selain hakee uuden version heti muutoksen jälkeen. */
function asset(string $path): string
{
    $v = @filemtime(SITE_ROOT . '/' . ltrim($path, '/')) ?: 1;
    return $path . '?v=' . $v;
}

/**
 * Kanoninen osoite: page_url('joga', 'en') → https://manalasdungeon.lv/joga?lang=en
 * Sivut ovat siistein osoittein ilman .php-päätettä (.htaccess: /joga avaa joga.php:n).
 * Jos kutsussa on vielä vanha muoto 'joga.php', pääte jätetään pois.
 */
const CLEAN_PAGES = ['arhitektura', 'blogi', 'celojums', 'joga', 'linkit', 'palaute', 'portfolio',
                     'privatums', 'rss', 'vidzeme', 'viesu_gramata'];
function page_url(string $file, string $lang): string
{
    $sivu = preg_replace('/\.php$/', '', $file);
    if (in_array($sivu, CLEAN_PAGES, true)) $file = $sivu;
    return SITE_URL . $file . ($lang === SITE_DEFAULT_LANG ? '' : '?lang=' . $lang);
}

/** canonical + hreflang kaikille kielille + x-default. $url = sivun oma osoitefunktio, esim. 'jg_url'. */
function head_alternates(callable $url, string $lang, string $indent = '    '): string
{
    $lines = ['<link rel="canonical" href="' . h($url($lang)) . '">'];
    foreach (SITE_LANGS as $lg) {
        $lines[] = '<link rel="alternate" hreflang="' . $lg . '" href="' . h($url($lg)) . '">';
    }
    $lines[] = '<link rel="alternate" hreflang="x-default" href="' . h($url(SITE_DEFAULT_LANG)) . '">';
    return implode("\n" . $indent, $lines) . "\n";
}

/** Omat fontit: preload otsikkofontille (näkyy heti) + kaikki @font-face-määrittelyt. */
function head_fonts(string $preload = 'playfair-display-latin-wght-normal.woff2', string $indent = '    '): string
{
    $lines = [];
    if ($preload !== '') {
        $lines[] = '<link rel="preload" href="/fonts/' . h($preload) . '" as="font" type="font/woff2" crossorigin>';
    }
    $lines[] = '<link rel="stylesheet" href="/' . h(asset('fonts.css')) . '">';
    return implode("\n" . $indent, $lines) . "\n";
}

/** Google Analytics 4: gtag.js + asetukset tiedostosta analytics.js (ei inline-koodia → tiukempi CSP). */
function head_analytics(string $indent = '    '): string
{
    // Google-skripti ladataan vasta suostumuksen jälkeen (analytics.js näyttää valinnan).
    return '<script src="/' . h(asset('analytics.js')) . '"></script>' . "\n";
}

/** Ylläpidon sähköpostiosoite (palautteet ja vieraskirjan ilmoitukset).
 *  Osoite on palvelimen mail_config.php-tiedostossa, ei versionhallinnassa (julkinen repo).
 *  Palauttaa tyhjän merkkijonon, jos tiedostoa ei ole tai osoite ei kelpaa. */
function admin_mail(): string
{
    static $to = null;
    if ($to === null) {
        $file = SITE_ROOT . '/mail_config.php';
        $cfg  = is_file($file) ? (require $file) : [];
        $to   = (is_array($cfg) && filter_var($cfg['to'] ?? '', FILTER_VALIDATE_EMAIL)) ? (string)$cfg['to'] : '';
    }
    return $to;
}

/** Satunnainen tunniste (nonce) tämän sivunlatauksen inline-skripteille. */
function csp_nonce(): string
{
    static $nonce = null;
    return $nonce ??= base64_encode(random_bytes(16));
}

/** CSP ja muut turvaotsakkeet. Kutsutaan automaattisesti alla. */
function send_security_headers(): void
{
    if (headers_sent()) { return; }
    $n = csp_nonce();
    $policy = [
        "default-src 'self'",
        "script-src 'self' 'nonce-{$n}' https://*.googletagmanager.com",
        "style-src 'self' 'unsafe-inline'",                // style="--i:…" -attribuutit ja <style>-lohkot
        "img-src 'self' data: https://*.google-analytics.com https://*.googletagmanager.com https://*.g.doubleclick.net",
        "font-src 'self'",
        "connect-src 'self' https://*.google-analytics.com https://analytics.google.com https://*.analytics.google.com https://*.googletagmanager.com https://*.g.doubleclick.net",   // analytics.google.com ei kuulu *.analytics.google.com -merkintään
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self'",
    ];
    if (CSP_ENFORCE) { $policy[] = "frame-ancestors 'self'"; }   // ei toimi Report-Only-tilassa
    $policy[] = 'report-uri /csp-raportti.php';
    header((CSP_ENFORCE ? 'Content-Security-Policy: ' : 'Content-Security-Policy-Report-Only: ') . implode('; ', $policy));
    header('X-Frame-Options: SAMEORIGIN');                    // estää sivun upottamisen muille sivustoille
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
}
send_security_headers();
