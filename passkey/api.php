<?php
/**
 * Passkey-API (JSON). Kutsutaan vain passkey.js:stä.
 * Toiminnot: login_options, login_verify, register_options, register_verify, delete, logout.
 */
declare(strict_types=1);

require __DIR__ . '/lib.php';
pk_session();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function pk_out(array $d, int $code = 200): never
{
    http_response_code($code);
    echo json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---- Pyynnön perustarkistukset (CSRF-suoja) ---- */
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    pk_out(['ok' => false, 'error' => 'Vain POST.'], 405);
}
if (($_SERVER['HTTP_X_PASSKEY'] ?? '') !== '1') {
    pk_out(['ok' => false, 'error' => 'Kielletty.'], 403);
}
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '' && !in_array($origin, pk_config()['origins'], true)) {
    pk_out(['ok' => false, 'error' => 'Kielletty alkuperä.'], 403);
}

$raw = file_get_contents('php://input');
$in  = json_decode($raw !== false && $raw !== '' ? $raw : '{}', true, 8);
if (!is_array($in)) {
    $in = [];
}
$cfg    = pk_config();
$action = (string)($_GET['action'] ?? '');

try {
    switch ($action) {

        /* ---------- Kirjautuminen ---------- */
        case 'login_options': {
            $all = pk_all();
            if (!$all) {
                throw new PkError('Yhtään laitetta ei ole rekisteröity.');
            }
            $ch = pk_new_challenge('auth');
            pk_out(['ok' => true, 'options' => [
                'challenge'        => pk_b64u_enc($ch),
                'rpId'             => $cfg['rp_id'],
                'timeout'          => 60000,
                'userVerification' => 'required',
                'allowCredentials' => array_map(
                    fn(array $r) => ['type' => 'public-key', 'id' => $r['credential_id']],
                    $all
                ),
            ]]);
        }

        case 'login_verify': {
            $ch     = pk_take_challenge('auth');
            $rawId  = pk_b64u_dec((string)($in['rawId'] ?? ''));
            $stored = pk_find($rawId);
            if (!$stored) {
                usleep(300000);
                throw new PkError('Tuntematon laite.');
            }
            $count = pk_verify_assertion($in, $ch, $stored);
            pk_touch((int)$stored['id'], $count);
            pk_login_session((string)$stored['credential_id']);
            $next = pk_safe_next($in['next'] ?? null);
            pk_out(['ok' => true, 'next' => $next === '/' ? '/passkey/manage.php' : $next]);
        }

        /* ---------- Rekisteröinti ---------- */
        case 'register_options': {
            if (!pk_is_authenticated() && !pk_setup_allowed((string)($in['setup_token'] ?? ''))) {
                usleep(800000); // hidastaa arvailua
                throw new PkError('Rekisteröinti ei sallittu: väärä asennusavain, tai laitteita on jo rekisteröity (kirjaudu ensin).');
            }
            $_SESSION['pk_reg_ok'] = time();
            $ch = pk_new_challenge('reg');
            pk_out(['ok' => true, 'options' => [
                'challenge' => pk_b64u_enc($ch),
                'rp'        => ['id' => $cfg['rp_id'], 'name' => $cfg['rp_name']],
                'user'      => [
                    'id'          => pk_b64u_enc(pk_user_id()),
                    'name'        => $cfg['user_name'],
                    'displayName' => $cfg['user_display'],
                ],
                'pubKeyCredParams' => [
                    ['type' => 'public-key', 'alg' => -7],    // ES256
                    ['type' => 'public-key', 'alg' => -257],  // RS256 (Windows Hello)
                ],
                'timeout'                => 120000,
                'attestation'            => 'none',
                'authenticatorSelection' => [
                    'residentKey'      => 'preferred',
                    'userVerification' => 'required',
                ],
                'excludeCredentials' => array_map(
                    fn(array $r) => ['type' => 'public-key', 'id' => $r['credential_id']],
                    pk_all()
                ),
            ]]);
        }

        case 'register_verify': {
            $okAt = (int)($_SESSION['pk_reg_ok'] ?? 0);
            unset($_SESSION['pk_reg_ok']);
            if ($okAt === 0 || time() - $okAt > 300) {
                throw new PkError('Rekisteröintilupa vanhentui — aloita alusta.');
            }
            $authed = pk_is_authenticated();
            if (!$authed && pk_count() > 0) {           // kilpailutilanne: joku ehti ensin
                throw new PkError('Kirjaudu ensin olemassa olevalla laitteella.');
            }
            $ch = pk_take_challenge('reg');
            $c  = pk_verify_registration($in, $ch);
            if (pk_find($c['raw_id'])) {
                throw new PkError('Tämä laite on jo rekisteröity.');
            }
            $label = trim(mb_substr(strip_tags((string)($in['label'] ?? '')), 0, 100));
            pk_insert($c, $label !== '' ? $label : 'Laite');
            if (!$authed) {
                pk_login_session($c['credential_id']);  // ensimmäinen laite: kirjataan suoraan sisään
            }
            pk_out(['ok' => true]);
        }

        /* ---------- Hallinta ---------- */
        case 'delete': {
            if (!pk_is_authenticated()) {
                pk_out(['ok' => false, 'error' => 'Kirjaudu ensin.'], 401);
            }
            if (pk_count() <= 1) {
                throw new PkError('Viimeistä laitetta ei voi poistaa — rekisteröi ensin toinen.');
            }
            pk_delete((int)($in['id'] ?? 0));
            pk_out(['ok' => true]);
        }

        case 'logout': {
            pk_logout();
            pk_out(['ok' => true]);
        }

        default:
            pk_out(['ok' => false, 'error' => 'Tuntematon toiminto.'], 404);
    }
} catch (PkError $e) {
    pk_out(['ok' => false, 'error' => $e->getMessage()], 400);
} catch (Throwable $e) {
    error_log('passkey api: ' . $e->getMessage());
    pk_out(['ok' => false, 'error' => 'Tarkistus epäonnistui.'], 400);
}
