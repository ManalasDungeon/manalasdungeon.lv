<?php
/**
 * Kielivalinta ja käännösten lataus.
 * Sisällytä TÄMÄ jokaisen sivun alkuun ENNEN mitään tulostusta.
 */

$supported = ['lv', 'en', 'fi'];
$default   = 'lv';

// Kieli tulee VAIN osoitteesta: /en/ ja /fi/ ohjautuvat .htaccess:ssa muotoon ?lang=xx.
// Juuri (ilman prefiksia/parametria) = latvia. Ei evastetta -> paluu latviaan toimii aina.
$lang = $_GET['lang'] ?? $default;
if (!in_array($lang, $supported, true)) {
    $lang = $default;
}

$L    = require __DIR__ . "/lang/$lang.php";
$BASE = 'https://manalasdungeon.lv/vitrupe/';

/** Käännös avaimella, esim. t('index.lead'). Palauttaa avaimen jos puuttuu. */
function t(string $key): string {
    global $L;
    $v = $L;
    foreach (explode('.', $key) as $k) {
        if (!is_array($v) || !array_key_exists($k, $v)) return $key;
        $v = $v[$k];
    }
    return is_string($v) ? $v : $key;
}

/** HTML-turvallinen tulostus (attribuutteihin). */
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/** Absoluuttinen URL sivulle: lv juuressa, en/fi prefiksillä (BASE/en/polku). */
function page_url(string $path, string $lang): string {
    global $BASE;
    $prefix = ($lang === 'lv') ? '' : $lang . '/';
    return $BASE . $prefix . $path;
}

/** Sisäinen linkki NYKYISELLÄ kielellä (navi/footer säilyttää kielen). */
function lurl(string $path): string {
    global $lang;
    return page_url($path, $lang);
}

/** hreflang-tagit hakukoneille. */
function hreflang_tags(string $path): string {
    global $supported;
    $out = '';
    foreach ($supported as $lg) {
        $out .= '<link rel="alternate" hreflang="' . $lg . '" href="' . e(page_url($path, $lg)) . "\">\n    ";
    }
    $out .= '<link rel="alternate" hreflang="x-default" href="' . e(page_url($path, 'lv')) . '">';
    return $out;
}

/** Cache-busting: lisää tiedoston muokkausaika ?v=... -parametriksi. */
function asset(string $path): string {
    $full = __DIR__ . '/' . ltrim($path, '/');
    $v = @filemtime($full) ?: time();
    return $path . '?v=' . $v;
}
