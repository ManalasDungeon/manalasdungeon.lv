<?php
/* PHP:n kehityspalvelimen reititin: jäljittelee .htaccess-sääntöjä, joita php -S ei lue. */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$root = $_SERVER['DOCUMENT_ROOT'];
if (preg_match('#^/linkit/?$#', $path)) {            // RewriteRule ^linkit/?$ linkit.php [L,QSA]
    $_SERVER['SCRIPT_NAME'] = '/linkit.php';
    require $root . '/linkit.php';
    return true;
}
if ($path === '/') { require $root . '/index.php'; return true; }

/* vitrupe/.htaccess: kieliprefiksit /vitrupe/en/ ja /vitrupe/fi/ */
if (preg_match('#^/vitrupe/(en|fi)/(images|css|js|audio)/(.+)$#', $path, $m)) {   // resurssit oikeisiin kansioihin
    $file = $root . '/vitrupe/' . $m[2] . '/' . $m[3];
    if (!is_file($file)) { http_response_code(404); return true; }
    $types = ['css' => 'text/css', 'js' => 'application/javascript', 'webp' => 'image/webp', 'jpg' => 'image/jpeg',
              'png' => 'image/png', 'mp3' => 'audio/mpeg', 'ogg' => 'audio/ogg'];
    header('Content-Type: ' . ($types[strtolower(pathinfo($file, PATHINFO_EXTENSION))] ?? 'application/octet-stream'));
    readfile($file);
    return true;
}
if (preg_match('#^/vitrupe/(en|fi)/?$#', $path, $m)) {                             // /vitrupe/en/ -> index.php?lang=en
    $_GET['lang'] = $m[1];
    require $root . '/vitrupe/index.php';
    return true;
}
if (preg_match('#^/vitrupe/(en|fi)/([A-Za-z0-9_-]+\.php)$#', $path, $m)) {        // /vitrupe/en/kuvat.php -> kuvat.php?lang=en
    $_GET['lang'] = $m[1];
    require $root . '/vitrupe/' . $m[2];
    return true;
}
if ($path === '/vitrupe/' || $path === '/vitrupe') { require $root . '/vitrupe/index.php'; return true; }
return false;                                         // muut tiedostot sellaisenaan
