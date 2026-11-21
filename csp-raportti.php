<?php
declare(strict_types=1);
/**
 * Vastaanottaa selainten Content-Security-Policy-rikeilmoitukset (report-uri).
 * Kirjaa ne lyhyesti lokitiedostoon verkkojuuren ulkopuolelle.
 * Loki: yksi JSON-rivi per ilmoitus. Tiedoston koko ja ilmoitusmäärä on rajattu,
 * joten roskapostittaja ei voi täyttää levyä.
 */
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

$raw = file_get_contents('php://input', false, null, 0, 8192);
if ($raw === false || $raw === '') { http_response_code(204); exit; }
$data = json_decode($raw, true);
if (!is_array($data)) { http_response_code(204); exit; }

/* Vanha muoto: {"csp-report": {...}}  –  uusi Reporting API: [{"type":"csp-violation","body":{...}}] */
$reports = [];
if (isset($data['csp-report']) && is_array($data['csp-report'])) {
    $reports[] = $data['csp-report'];
} elseif (array_is_list($data)) {
    foreach (array_slice($data, 0, 10) as $r) {
        if (is_array($r) && is_array($r['body'] ?? null)) { $reports[] = $r['body']; }
    }
}
if (!$reports) { http_response_code(204); exit; }

$dir      = dirname(__DIR__);
$logFile  = $dir . '/.manala_csp.log';
$rateFile = $dir . '/.manala_csp_raja.json';
$maxLog   = 256 * 1024;   // lokin enimmäiskoko
$perIp    = 30;           // ilmoituksia / IP / tunti
$total    = 500;          // ilmoituksia / vuorokausi koko sivustolle

/* Rajoitus: IP tallennetaan vain tiivisteenä */
$ip  = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|csp');
$now = time();
$fh  = @fopen($rateFile, 'c+');
if ($fh && flock($fh, LOCK_EX)) {
    $st = json_decode((string)stream_get_contents($fh), true);
    if (!is_array($st) || ($st['day'] ?? '') !== gmdate('Y-m-d')) { $st = ['day' => gmdate('Y-m-d'), 'n' => 0, 'ip' => []]; }
    $st['ip'] = array_filter($st['ip'], fn($v) => is_array($v) && $now - ($v[0] ?? 0) < 3600);
    $mine = $st['ip'][$ip] ?? [$now, 0];
    if ($mine[1] >= $perIp || $st['n'] >= $total) {
        flock($fh, LOCK_UN); fclose($fh);
        http_response_code(204); exit;
    }
    $st['ip'][$ip] = [$mine[0], $mine[1] + 1];
    $st['n']++;
    ftruncate($fh, 0); rewind($fh);
    fwrite($fh, json_encode($st));
    fflush($fh); flock($fh, LOCK_UN); fclose($fh);
} else {
    http_response_code(204); exit;
}

$clip = fn($v, int $n = 300) => mb_substr(preg_replace('/[\x00-\x1F\x7F]/u', ' ', (string)$v) ?? '', 0, $n);
$lines = '';
foreach ($reports as $r) {
    $lines .= json_encode([
        'aika'     => gmdate('c'),
        'sivu'     => $clip(strtok((string)($r['document-uri'] ?? $r['documentURL'] ?? ''), '?#')),
        'saanto'   => $clip($r['effective-directive'] ?? $r['effectiveDirective'] ?? $r['violated-directive'] ?? '', 60),
        'estetty'  => $clip($r['blocked-uri'] ?? $r['blockedURL'] ?? ''),
        'tiedosto' => $clip($r['source-file'] ?? $r['sourceFile'] ?? ''),
        'rivi'     => (int)($r['line-number'] ?? $r['lineNumber'] ?? 0),
        'ote'      => $clip($r['script-sample'] ?? $r['sample'] ?? '', 80),
        'tila'     => $clip($r['disposition'] ?? '', 10),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
}

/* Kun loki on täynnä, vanha siirretään .1-tiedostoksi (vain yksi varakopio) */
clearstatcache();
if (is_file($logFile) && filesize($logFile) > $maxLog) { @rename($logFile, $logFile . '.1'); }
@file_put_contents($logFile, $lines, FILE_APPEND | LOCK_EX);
@chmod($logFile, 0600);

http_response_code(204);
