<?php
/**
 * Passkey (WebAuthn / FIDO2) -kirjautuminen Manalas Dungeonin admin-sivuille.
 *
 * Ydinkirjasto ilman ulkoisia riippuvuuksia (PHP 8.1+, openssl, PDO):
 *  - minimaalinen CBOR-dekooderi (attestationObject ja COSE-avaimet)
 *  - COSE → PEM (ES256 / P-256 ja RS256)
 *  - rekisteröinnin ja kirjautumisen (assertion) tarkistus
 *  - istunto: aikakatkaisut ja session-ID:n uusinta kirjautuessa
 *
 * HUOM: guard.php käyttää vain istuntoa — tietokantaa EI avata admin-sivun
 * pyynnössä, jottei db_config.php latautuisi kahteen kertaan (db() uudelleenmäärittely).
 */
declare(strict_types=1);

/** Käyttäjälle näytettävä virhe (muut poikkeukset kirjataan lokiin ja näytetään yleisenä). */
final class PkError extends RuntimeException {}

/** CBOR-tavujono (erotetaan tekstistä). */
final class PkCborBytes
{
    public function __construct(public readonly string $b) {}
}

/** Minimaalinen CBOR-dekooderi (RFC 8949) — riittää WebAuthn-datalle. */
final class PkCbor
{
    private int $p = 0;
    private int $depth = 0;

    private function __construct(private readonly string $d) {}

    /** Dekoodaa koko datan (ylimääräinen data = virhe). */
    public static function decode(string $d): mixed
    {
        $c = new self($d);
        $v = $c->item();
        if ($c->p !== strlen($d)) {
            throw new PkError('CBOR: ylimääräistä dataa.');
        }
        return $v;
    }

    /** Dekoodaa ensimmäisen alkion (authDatan COSE-avain, jota voi seurata extensions-data). */
    public static function decodeFirst(string $d): mixed
    {
        return (new self($d))->item();
    }

    private function byte(): int
    {
        if ($this->p >= strlen($this->d)) {
            throw new PkError('CBOR: data loppui kesken.');
        }
        return ord($this->d[$this->p++]);
    }

    private function take(int $n): string
    {
        if ($n < 0 || $this->p + $n > strlen($this->d)) {
            throw new PkError('CBOR: data loppui kesken.');
        }
        $s = substr($this->d, $this->p, $n);
        $this->p += $n;
        return $s;
    }

    private function arg(int $ai): int
    {
        if ($ai < 24)   { return $ai; }
        if ($ai === 24) { return $this->byte(); }
        if ($ai === 25) { return unpack('n', $this->take(2))[1]; }
        if ($ai === 26) { return unpack('N', $this->take(4))[1]; }
        if ($ai === 27) {
            $v = unpack('J', $this->take(8))[1];
            if ($v < 0) {
                throw new PkError('CBOR: liian suuri kokonaisluku.');
            }
            return $v;
        }
        throw new PkError('CBOR: tukematon pituuskoodaus.');
    }

    private function item(): mixed
    {
        if (++$this->depth > 16) {
            throw new PkError('CBOR: liian syvä rakenne.');
        }
        $ib = $this->byte();
        $mt = $ib >> 5;
        $ai = $ib & 0x1f;

        switch ($mt) {
            case 0: $v = $this->arg($ai); break;
            case 1: $v = -1 - $this->arg($ai); break;
            case 2: $v = new PkCborBytes($this->take($this->arg($ai))); break;
            case 3: $v = $this->take($this->arg($ai)); break;
            case 4:
                $n = $this->arg($ai);
                if ($n > 1024) { throw new PkError('CBOR: liian suuri taulukko.'); }
                $v = [];
                for ($i = 0; $i < $n; $i++) { $v[] = $this->item(); }
                break;
            case 5:
                $n = $this->arg($ai);
                if ($n > 1024) { throw new PkError('CBOR: liian suuri map.'); }
                $v = [];
                for ($i = 0; $i < $n; $i++) {
                    $k = $this->item();
                    if (!is_int($k) && !is_string($k)) {
                        throw new PkError('CBOR: tukematon avaintyyppi.');
                    }
                    $v[$k] = $this->item();
                }
                break;
            case 7:
                $v = match ($ai) {
                    20 => false,
                    21 => true,
                    22, 23 => null,
                    default => throw new PkError('CBOR: tukematon arvo.'),
                };
                break;
            default:
                throw new PkError('CBOR: tukematon tyyppi.');
        }
        $this->depth--;
        return $v;
    }
}

/* =====================================================================
 *  Perusapurit
 * ===================================================================== */

function pk_config(): array
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require __DIR__ . '/config.php';
    }
    return $cfg;
}

function pk_h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function pk_b64u_enc(string $bin): string
{
    return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
}

function pk_b64u_dec(string $s): string
{
    if ($s === '' || !preg_match('/^[A-Za-z0-9_-]+$/', $s)) {
        throw new PkError('Virheellinen koodaus.');
    }
    $s = strtr($s, '-_', '+/');
    $pad = strlen($s) % 4;
    if ($pad) {
        $s .= str_repeat('=', 4 - $pad);
    }
    $r = base64_decode($s, true);
    if ($r === false) {
        throw new PkError('Virheellinen koodaus.');
    }
    return $r;
}

/** Sivun sisäinen paluuosoite — estää avoimen uudelleenohjauksen muille sivustoille. */
function pk_safe_next(mixed $n): string
{
    $n = is_string($n) ? $n : '';
    if ($n === '' || $n[0] !== '/' || str_starts_with($n, '//') || str_contains($n, '\\')
        || preg_match('/[\x00-\x1f\x7f]/', $n)) {
        return '/';
    }
    return $n;
}

/** Ylläpitäjän käyttäjätunniste (ei salainen, pysyvä per asennus). */
function pk_user_id(): string
{
    return substr(hash('sha256', 'manala-admin|' . pk_config()['rp_id'], true), 0, 16);
}

function pk_asset(string $file): string
{
    $v = @filemtime(__DIR__ . '/' . $file) ?: time();
    return '/passkey/' . $file . '?v=' . $v;
}

/* =====================================================================
 *  Istunto
 * ===================================================================== */

function pk_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $c = pk_config();
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => (bool)($c['cookie_secure'] ?? true),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function pk_is_authenticated(): bool
{
    pk_session();
    $a = $_SESSION['pk_auth'] ?? null;
    if (!is_array($a) || !isset($a['at'], $a['last'])) {
        return false;
    }
    $c   = pk_config();
    $now = time();
    if ($now - (int)$a['at'] > (int)$c['absolute_timeout'] || $now - (int)$a['last'] > (int)$c['idle_timeout']) {
        unset($_SESSION['pk_auth']);
        return false;
    }
    $_SESSION['pk_auth']['last'] = $now;
    return true;
}

function pk_login_session(string $credB64u): void
{
    session_regenerate_id(true);
    $_SESSION['pk_auth'] = ['cred' => $credB64u, 'at' => time(), 'last' => time()];
}

function pk_logout(): void
{
    pk_session();
    unset($_SESSION['pk_auth'], $_SESSION['pk_chal'], $_SESSION['pk_reg_ok']);
    session_regenerate_id(true);
}

function pk_new_challenge(string $kind): string
{
    $c = random_bytes(32);
    $_SESSION['pk_chal'] = ['v' => pk_b64u_enc($c), 'kind' => $kind, 'exp' => time() + 300];
    return $c;
}

/** Haaste kulutetaan aina (kertakäyttöinen), onnistui tarkistus tai ei. */
function pk_take_challenge(string $kind): string
{
    $c = $_SESSION['pk_chal'] ?? null;
    unset($_SESSION['pk_chal']);
    if (!is_array($c) || ($c['kind'] ?? '') !== $kind || time() > (int)($c['exp'] ?? 0)) {
        throw new PkError('Haaste vanhentui — yritä uudelleen.');
    }
    return pk_b64u_dec((string)$c['v']);
}

/* =====================================================================
 *  WebAuthn-tarkistukset
 * ===================================================================== */

function pk_check_client_data(string $cdj, string $type, string $challenge): void
{
    $c = json_decode($cdj, true, 8);
    if (!is_array($c)) {
        throw new PkError('Virheellinen clientData.');
    }
    if (($c['type'] ?? '') !== $type) {
        throw new PkError('Väärä toimintotyyppi.');
    }
    if (!is_string($c['challenge'] ?? null) || !hash_equals(pk_b64u_enc($challenge), $c['challenge'])) {
        throw new PkError('Haaste ei täsmää.');
    }
    if (!in_array($c['origin'] ?? '', pk_config()['origins'], true)) {
        throw new PkError('Väärä alkuperä (origin).');
    }
    if (!empty($c['crossOrigin'])) {
        throw new PkError('Upotettu (cross-origin) pyyntö estetty.');
    }
}

function pk_parse_authdata(string $ad): array
{
    if (strlen($ad) < 37) {
        throw new PkError('authenticatorData liian lyhyt.');
    }
    $r = [
        'rpIdHash'  => substr($ad, 0, 32),
        'flags'     => ord($ad[32]),
        'signCount' => unpack('N', substr($ad, 33, 4))[1],
    ];
    if ($r['flags'] & 0x40) { // AT: attested credential data
        if (strlen($ad) < 55) {
            throw new PkError('Avaintieto puuttuu.');
        }
        $len = unpack('n', substr($ad, 53, 2))[1];
        if ($len < 1 || $len > 1023 || strlen($ad) < 55 + $len + 1) {
            throw new PkError('Virheellinen avaintunniste.');
        }
        $r['credId'] = substr($ad, 55, $len);
        $r['cose']   = PkCbor::decodeFirst(substr($ad, 55 + $len));
    }
    return $r;
}

function pk_check_authdata_common(array $ad): void
{
    if (!hash_equals(hash('sha256', pk_config()['rp_id'], true), $ad['rpIdHash'])) {
        throw new PkError('Väärä palvelin (rpId).');
    }
    if (!($ad['flags'] & 0x01)) { // UP
        throw new PkError('Käyttäjän läsnäoloa ei vahvistettu.');
    }
    if (!($ad['flags'] & 0x04)) { // UV
        throw new PkError('Laite ei tunnistanut käyttäjää (PIN tai biometria vaaditaan).');
    }
}

/* ---- DER / PEM ---- */

function pk_der_len(int $n): string
{
    if ($n < 0x80) {
        return chr($n);
    }
    $s = '';
    while ($n > 0) {
        $s = chr($n & 0xff) . $s;
        $n >>= 8;
    }
    return chr(0x80 | strlen($s)) . $s;
}

function pk_der_int(string $b): string
{
    $b = ltrim($b, "\x00");
    if ($b === '') {
        $b = "\x00";
    }
    if (ord($b[0]) & 0x80) {
        $b = "\x00" . $b;
    }
    return "\x02" . pk_der_len(strlen($b)) . $b;
}

function pk_der_seq(string $c): string
{
    return "\x30" . pk_der_len(strlen($c)) . $c;
}

function pk_pem(string $der): string
{
    return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($der), 64, "\n") . "-----END PUBLIC KEY-----\n";
}

/** COSE-julkinen avain → PEM (SubjectPublicKeyInfo). Tuetaan ES256 (P-256) ja RS256. */
function pk_cose_to_pem(mixed $k): string
{
    if (!is_array($k)) {
        throw new PkError('Virheellinen julkinen avain.');
    }
    $kty = $k[1] ?? null;
    $alg = $k[3] ?? null;

    if ($kty === 2 && $alg === -7) {             // EC2 + ES256
        if (($k[-1] ?? null) !== 1) {
            throw new PkError('Tukematon elliptinen käyrä.');
        }
        $x = $k[-2] ?? null;
        $y = $k[-3] ?? null;
        if (!$x instanceof PkCborBytes || !$y instanceof PkCborBytes || strlen($x->b) !== 32 || strlen($y->b) !== 32) {
            throw new PkError('Virheellinen EC-avain.');
        }
        // SPKI: id-ecPublicKey + prime256v1, BIT STRING (pakkaamaton piste 04||X||Y)
        $der = hex2bin('3059301306072a8648ce3d020106082a8648ce3d030107034200') . "\x04" . $x->b . $y->b;
    } elseif ($kty === 3 && $alg === -257) {     // RSA + RS256
        $n = $k[-1] ?? null;
        $e = $k[-2] ?? null;
        if (!$n instanceof PkCborBytes || !$e instanceof PkCborBytes || strlen($n->b) < 256) {
            throw new PkError('Virheellinen RSA-avain.');
        }
        $rsa   = pk_der_seq(pk_der_int($n->b) . pk_der_int($e->b));
        $algId = pk_der_seq("\x06\x09\x2a\x86\x48\x86\xf7\x0d\x01\x01\x01" . "\x05\x00");
        $der   = pk_der_seq($algId . "\x03" . pk_der_len(strlen($rsa) + 1) . "\x00" . $rsa);
    } else {
        throw new PkError('Tukematon avaintyyppi (vain ES256 ja RS256).');
    }

    $pem = pk_pem($der);
    if (openssl_pkey_get_public($pem) === false) {
        throw new PkError('Julkista avainta ei voitu lukea.');
    }
    return $pem;
}

/**
 * Tarkistaa rekisteröinnin (navigator.credentials.create -vastaus).
 * Palauttaa tallennettavat tiedot. Attestaatiota ei vaadita ('none') —
 * kyse on omien laitteiden rekisteröinnistä.
 */
function pk_verify_registration(array $p, string $challenge): array
{
    $resp = is_array($p['response'] ?? null) ? $p['response'] : [];
    $cdj  = pk_b64u_dec((string)($resp['clientDataJSON'] ?? ''));
    pk_check_client_data($cdj, 'webauthn.create', $challenge);

    $att = PkCbor::decode(pk_b64u_dec((string)($resp['attestationObject'] ?? '')));
    if (!is_array($att) || !(($att['authData'] ?? null) instanceof PkCborBytes)) {
        throw new PkError('Virheellinen attestaatio.');
    }
    $ad = pk_parse_authdata($att['authData']->b);
    pk_check_authdata_common($ad);
    if (!isset($ad['credId'], $ad['cose'])) {
        throw new PkError('Avaintieto puuttuu.');
    }
    $rawId = pk_b64u_dec((string)($p['rawId'] ?? ''));
    if (!hash_equals($ad['credId'], $rawId)) {
        throw new PkError('Avaintunnisteet eivät täsmää.');
    }

    return [
        'raw_id'        => $ad['credId'],
        'credential_id' => pk_b64u_enc($ad['credId']),
        'public_key'    => pk_cose_to_pem($ad['cose']),
        'sign_count'    => (int)$ad['signCount'],
    ];
}

/**
 * Tarkistaa kirjautumisen (navigator.credentials.get -vastaus) tallennettua avainta vasten.
 * Palauttaa uuden allekirjoituslaskurin arvon.
 */
function pk_verify_assertion(array $p, string $challenge, array $stored): int
{
    $resp = is_array($p['response'] ?? null) ? $p['response'] : [];
    $cdj  = pk_b64u_dec((string)($resp['clientDataJSON'] ?? ''));
    pk_check_client_data($cdj, 'webauthn.get', $challenge);

    $authData = pk_b64u_dec((string)($resp['authenticatorData'] ?? ''));
    $sig      = pk_b64u_dec((string)($resp['signature'] ?? ''));
    $ad       = pk_parse_authdata($authData);
    pk_check_authdata_common($ad);

    if (!empty($resp['userHandle'])) {
        if (!hash_equals(pk_user_id(), pk_b64u_dec((string)$resp['userHandle']))) {
            throw new PkError('Käyttäjätunniste ei täsmää.');
        }
    }

    $ok = openssl_verify($authData . hash('sha256', $cdj, true), $sig, (string)$stored['public_key'], OPENSSL_ALGO_SHA256);
    if ($ok !== 1) {
        throw new PkError('Allekirjoitus ei kelpaa.');
    }

    // Laskuri: jos laite käyttää laskuria, sen on kasvettava (kloonattu avain paljastuu).
    $new = (int)$ad['signCount'];
    $old = (int)$stored['sign_count'];
    if (($new !== 0 || $old !== 0) && $new <= $old) {
        throw new PkError('Allekirjoituslaskuri ei kasvanut — mahdollinen kopioitu avain.');
    }
    return $new;
}

/* =====================================================================
 *  Tietokanta (vain api.php / login.php / manage.php — EI guardissa)
 * ===================================================================== */

/**
 * Tietokantayhteys. Jos config.php:n 'db' => ['dsn' => ...] on täytetty, avataan
 * oma yhteys (erillinen kanta, esim. manalasd_passkey). Muuten käytetään sivuston
 * db_config.php:n db()-yhteyttä.
 */
function pk_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $c = pk_config()['db'] ?? null;
    if (is_array($c) && trim((string)($c['dsn'] ?? '')) !== '') {
        $pdo = new PDO((string)$c['dsn'], (string)($c['user'] ?? ''), (string)($c['pass'] ?? ''), [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    }
    if (!function_exists('db')) {
        require_once dirname(__DIR__) . '/db_config.php';
    }
    return $pdo = db();
}

function pk_table(): string
{
    $t = (string)pk_config()['table'];
    if (!preg_match('/^[A-Za-z0-9_]+$/', $t)) {
        throw new LogicException('Virheellinen taulun nimi.');
    }
    return $t;
}

function pk_cred_hash(string $rawId): string
{
    return hash('sha256', $rawId);
}

function pk_count(): int
{
    return (int)pk_db()->query('SELECT COUNT(*) FROM ' . pk_table())->fetchColumn();
}

function pk_all(): array
{
    return pk_db()->query(
        'SELECT id, credential_id, label, sign_count, created_at, last_used FROM ' . pk_table() . ' ORDER BY id'
    )->fetchAll(PDO::FETCH_ASSOC);
}

function pk_find(string $rawId): ?array
{
    $s = pk_db()->prepare('SELECT * FROM ' . pk_table() . ' WHERE credential_hash = ?');
    $s->execute([pk_cred_hash($rawId)]);
    $r = $s->fetch(PDO::FETCH_ASSOC);
    return $r ?: null;
}

function pk_insert(array $c, string $label): void
{
    $s = pk_db()->prepare(
        'INSERT INTO ' . pk_table() . ' (credential_hash, credential_id, public_key, sign_count, label, created_at)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $s->execute([pk_cred_hash($c['raw_id']), $c['credential_id'], $c['public_key'], $c['sign_count'], $label, date('Y-m-d H:i:s')]);
}

function pk_touch(int $id, int $count): void
{
    $s = pk_db()->prepare('UPDATE ' . pk_table() . ' SET sign_count = ?, last_used = ? WHERE id = ?');
    $s->execute([$count, date('Y-m-d H:i:s'), $id]);
}

function pk_delete(int $id): void
{
    $s = pk_db()->prepare('DELETE FROM ' . pk_table() . ' WHERE id = ?');
    $s->execute([$id]);
}

/* ---- Ensimmäisen laitteen asennusavain ---- */

function pk_setup_configured(): bool
{
    $t = (string)(pk_config()['setup_token'] ?? '');
    return strlen($t) >= 20 && !str_contains($t, 'VAIHDA');
}

/** Asennusavain kelpaa VAIN kun yhtään laitetta ei ole rekisteröity. */
function pk_setup_allowed(string $token): bool
{
    if (!pk_setup_configured() || pk_count() > 0) {
        return false;
    }
    return hash_equals((string)pk_config()['setup_token'], $token);
}

/* =====================================================================
 *  Sivupohja (login.php / manage.php)
 * ===================================================================== */

function pk_page_start(string $title): void
{
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: same-origin');
    ?>
<!DOCTYPE html>
<html lang="fi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?= pk_h($title) ?> — Manalas Dungeon</title>
<link rel="stylesheet" href="/fonts.css?v=<?= @filemtime(dirname(__DIR__) . '/fonts.css') ?: 1 ?>">
<link rel="stylesheet" href="<?= pk_h(pk_asset('passkey.css')) ?>">
</head>
<body class="pk-body">
<main class="pk-wrap">
<?php
}

function pk_page_end(): void
{
    ?>
</main>
<script src="<?= pk_h(pk_asset('passkey.js')) ?>" defer></script>
</body>
</html>
<?php
}

/** Uloskirjautumisnappi admin-sivuille: <?= pk_logout_button() ?> */
function pk_logout_button(string $label = 'Kirjaudu ulos'): string
{
    $js = "fetch('/passkey/api.php?action=logout',{method:'POST',credentials:'same-origin',"
        . "headers:{'X-Passkey':'1','Content-Type':'application/json'},body:'{}'})"
        . ".then(function(){location.href='/passkey/login.php'})";
    return '<button type="button" class="pk-logout" onclick="' . pk_h($js) . '">' . pk_h($label) . '</button>';
}
