<?php
/**
 * Passkey-portti admin-sivuille.
 *
 * Lisää jokaisen admin-tiedoston ALKUUN (heti <?php-rivin jälkeen):
 *     require __DIR__ . '/passkey/guard.php';
 *
 * Päästää läpi vain passkeyllä kirjautuneen istunnon; muuten ohjaa
 * kirjautumissivulle ja palaa kirjautumisen jälkeen samaan osoitteeseen.
 * Ei avaa tietokantaa (vain istunto).
 */
declare(strict_types=1);

require_once __DIR__ . '/lib.php';

pk_session();

if (!pk_is_authenticated()) {
    header('Cache-Control: no-store');
    header('Location: /passkey/login.php?next=' . rawurlencode(pk_safe_next($_SERVER['REQUEST_URI'] ?? '/')), true, 302);
    exit;
}

header('Cache-Control: no-store, private');
header('X-Frame-Options: DENY');
