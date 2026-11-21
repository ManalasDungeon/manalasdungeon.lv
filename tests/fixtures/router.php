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
return false;                                         // muut tiedostot sellaisenaan
