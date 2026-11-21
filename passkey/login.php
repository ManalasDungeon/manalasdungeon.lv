<?php
/** Passkey-kirjautumissivu ylläpitoon. */
declare(strict_types=1);

require __DIR__ . '/lib.php';
pk_session();

$next = pk_safe_next($_GET['next'] ?? null);
if ($next === '/') {
    $next = '/passkey/manage.php';
}
if (pk_is_authenticated()) {
    header('Location: ' . $next, true, 302);
    exit;
}

$count = null;
try {
    $count = pk_count();
} catch (Throwable $e) {
    error_log('passkey login: ' . $e->getMessage());
}

pk_page_start('Kirjaudu');
?>
<section class="pk-card">
  <div class="pk-icon" aria-hidden="true">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="4.5"/><path d="m10.7 12.3 9.8-9.8"/><path d="m15.5 7.5 3 3"/><path d="m18 5 2.5 2.5"/></svg>
  </div>
  <p class="pk-eyebrow">Manalas Dungeon · ylläpito</p>
  <h1 class="pk-title">Kirjaudu passkeyllä</h1>
  <p class="pk-lead">Kirjautuminen tapahtuu tälle laitteelle tallennetulla avaimella — sormenjälki, kasvot, laitteen PIN tai turva-avain. Salasanaa ei tarvita.</p>

  <p class="pk-msg" id="pkMsg" role="status" hidden></p>

  <?php if ($count === null): ?>
    <p class="pk-msg pk-msg--err">Tietokantaan ei saada yhteyttä tai taulua <code>admin_passkeys</code> ei löydy. Tarkista <code>passkey/config.php</code>:n <code>db</code>-asetukset (käyttäjä, salasana, kannan oikeudet) ja että <code>schema.sql</code> on ajettu samaan kantaan.</p>
  <?php elseif ($count === 0): ?>
    <p class="pk-note">Yhtään laitetta ei ole vielä rekisteröity.</p>
    <a class="pk-btn" href="/passkey/manage.php">Rekisteröi ensimmäinen laite</a>
  <?php else: ?>
    <button class="pk-btn" type="button" data-pk-action="login" data-next="<?= pk_h($next) ?>">Kirjaudu sisään</button>
  <?php endif; ?>

  <p class="pk-foot"><a href="/">&larr; manalasdungeon.lv</a></p>
</section>
<?php
pk_page_end();
