<?php
/**
 * Passkey-laitteiden hallinta.
 *  - Kirjautuneena: laitelista, uuden laitteen lisäys, poisto, uloskirjautuminen.
 *  - Kun yhtään laitetta ei ole: ensimmäisen laitteen rekisteröinti asennusavaimella.
 */
declare(strict_types=1);

require __DIR__ . '/lib.php';
pk_session();

$authed = pk_is_authenticated();

try {
    $count = pk_count();
} catch (Throwable $e) {
    error_log('passkey manage: ' . $e->getMessage());
    pk_page_start('Ylläpito');
    echo '<section class="pk-card"><h1 class="pk-title">Tietokantayhteys puuttuu</h1>'
       . '<p class="pk-msg pk-msg--err">Tietokantaan ei saada yhteyttä tai taulua <code>admin_passkeys</code> ei löydy. '
       . 'Tarkista <code>passkey/config.php</code>:n <code>db</code>-asetukset (käyttäjä, salasana, kannan oikeudet) '
       . 'ja että <code>schema.sql</code> on ajettu samaan kantaan.</p></section>';
    pk_page_end();
    exit;
}

if (!$authed && $count > 0) {
    header('Location: /passkey/login.php?next=' . rawurlencode('/passkey/manage.php'), true, 302);
    exit;
}

$cfg     = pk_config();
$devices = $authed ? pk_all() : [];
$current = (string)($_SESSION['pk_auth']['cred'] ?? '');

function pk_fmt_date(?string $d): string
{
    if (!$d) {
        return '—';
    }
    $t = strtotime($d);
    return $t ? date('d.m.Y H:i', $t) : '—';
}

pk_page_start($authed ? 'Laitteet' : 'Ensimmäinen laite');

if ($authed): ?>
<section class="pk-card pk-card--wide">
  <div class="pk-head">
    <div>
      <p class="pk-eyebrow">Manalas Dungeon · ylläpito</p>
      <h1 class="pk-title">Rekisteröidyt laitteet</h1>
    </div>
    <button class="pk-btn pk-btn--ghost" type="button" data-pk-action="logout">Kirjaudu ulos</button>
  </div>

  <?php if (!empty($cfg['admin_pages'])): ?>
  <nav class="pk-links" aria-label="Ylläpitosivut">
    <?php foreach ($cfg['admin_pages'] as $lbl => $url): ?>
      <a class="pk-chip" href="<?= pk_h((string)$url) ?>"><?= pk_h((string)$lbl) ?> &rarr;</a>
    <?php endforeach; ?>
  </nav>
  <?php endif; ?>

  <p class="pk-msg" id="pkMsg" role="status" hidden></p>

  <ul class="pk-devices">
    <?php foreach ($devices as $d): $isCur = hash_equals((string)$d['credential_id'], $current); ?>
    <li class="pk-device">
      <div class="pk-device__info">
        <span class="pk-device__name"><?= pk_h((string)$d['label']) ?></span>
        <?php if ($isCur): ?><span class="pk-badge">tämä istunto</span><?php endif; ?>
        <span class="pk-device__meta">Lisätty <?= pk_h(pk_fmt_date($d['created_at'])) ?> · Viimeksi käytetty <?= pk_h(pk_fmt_date($d['last_used'])) ?></span>
      </div>
      <button class="pk-btn pk-btn--small pk-btn--danger" type="button"
              data-pk-action="delete" data-id="<?= (int)$d['id'] ?>" data-label="<?= pk_h((string)$d['label']) ?>"
              <?= count($devices) <= 1 ? 'disabled title="Viimeistä laitetta ei voi poistaa"' : '' ?>>Poista</button>
    </li>
    <?php endforeach; ?>
  </ul>

  <div class="pk-add">
    <h2 class="pk-sub">Lisää uusi laite</h2>
    <p class="pk-note">Rekisteröi tämä laite — tai valitse selaimen ikkunasta <em>”Käytä puhelinta tai tablettia”</em>, niin voit tallentaa avaimen puhelimeen QR-koodilla.</p>
    <label class="pk-label" for="pkLabel">Laitteen nimi</label>
    <input class="pk-input" id="pkLabel" type="text" maxlength="100" placeholder="esim. Työkone, Puhelin, YubiKey" autocomplete="off">
    <button class="pk-btn" type="button" data-pk-action="register">Rekisteröi laite</button>
  </div>
</section>

<?php else: /* ensimmäinen laite */ ?>
<section class="pk-card">
  <div class="pk-icon" aria-hidden="true">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="4.5"/><path d="m10.7 12.3 9.8-9.8"/><path d="m15.5 7.5 3 3"/><path d="m18 5 2.5 2.5"/></svg>
  </div>
  <p class="pk-eyebrow">Manalas Dungeon · ylläpito</p>
  <h1 class="pk-title">Rekisteröi ensimmäinen laite</h1>

  <?php if (!pk_setup_configured()): ?>
    <p class="pk-msg pk-msg--err">Asennusavainta ei ole asetettu. Vaihda <code>passkey/config.php</code>:n <code>setup_token</code> pitkäksi satunnaiseksi merkkijonoksi (vähintään 20 merkkiä).</p>
  <?php else: ?>
    <p class="pk-lead">Syötä <code>config.php</code>:hen asettamasi asennusavain. Se toimii vain niin kauan, kuin yhtään laitetta ei ole rekisteröity.</p>
    <p class="pk-msg" id="pkMsg" role="status" hidden></p>
    <label class="pk-label" for="pkToken">Asennusavain</label>
    <input class="pk-input" id="pkToken" type="password" autocomplete="off" spellcheck="false">
    <label class="pk-label" for="pkLabel">Laitteen nimi</label>
    <input class="pk-input" id="pkLabel" type="text" maxlength="100" placeholder="esim. Työkone" autocomplete="off">
    <button class="pk-btn" type="button" data-pk-action="register">Rekisteröi tämä laite</button>
  <?php endif; ?>

  <p class="pk-foot"><a href="/">&larr; manalasdungeon.lv</a></p>
</section>
<?php endif;

pk_page_end();
