/* ============================================================
   Passkey (WebAuthn) — selainpuoli: kirjautuminen, rekisteröinti,
   laitteen poisto ja uloskirjautuminen. Painikkeet: data-pk-action.
   ============================================================ */
(function () {
  'use strict';

  function b64uToBuf(s) {
    s = s.replace(/-/g, '+').replace(/_/g, '/');
    while (s.length % 4) { s += '='; }
    var bin = atob(s), u = new Uint8Array(bin.length);
    for (var i = 0; i < bin.length; i++) { u[i] = bin.charCodeAt(i); }
    return u.buffer;
  }

  function bufToB64u(buf) {
    var u = new Uint8Array(buf), s = '';
    for (var i = 0; i < u.length; i++) { s += String.fromCharCode(u[i]); }
    return btoa(s).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
  }

  async function api(action, body) {
    var r = await fetch('/passkey/api.php?action=' + encodeURIComponent(action), {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', 'X-Passkey': '1' },
      body: JSON.stringify(body || {})
    });
    var j = await r.json().catch(function () { return { ok: false, error: 'Palvelimen vastaus oli virheellinen.' }; });
    if (!r.ok || !j.ok) { throw new Error(j.error || 'Virhe.'); }
    return j;
  }

  async function login(next) {
    var o = (await api('login_options')).options;
    o.challenge = b64uToBuf(o.challenge);
    o.allowCredentials = (o.allowCredentials || []).map(function (c) {
      return { type: c.type, id: b64uToBuf(c.id) };
    });
    var cred = await navigator.credentials.get({ publicKey: o });
    return api('login_verify', {
      next: next || '',
      id: cred.id,
      rawId: bufToB64u(cred.rawId),
      type: cred.type,
      response: {
        clientDataJSON: bufToB64u(cred.response.clientDataJSON),
        authenticatorData: bufToB64u(cred.response.authenticatorData),
        signature: bufToB64u(cred.response.signature),
        userHandle: cred.response.userHandle ? bufToB64u(cred.response.userHandle) : null
      }
    });
  }

  async function register(label, token) {
    var o = (await api('register_options', { setup_token: token || '' })).options;
    o.challenge = b64uToBuf(o.challenge);
    o.user.id = b64uToBuf(o.user.id);
    o.excludeCredentials = (o.excludeCredentials || []).map(function (c) {
      return { type: c.type, id: b64uToBuf(c.id) };
    });
    var cred = await navigator.credentials.create({ publicKey: o });
    return api('register_verify', {
      label: label || '',
      id: cred.id,
      rawId: bufToB64u(cred.rawId),
      type: cred.type,
      response: {
        clientDataJSON: bufToB64u(cred.response.clientDataJSON),
        attestationObject: bufToB64u(cred.response.attestationObject)
      }
    });
  }

  function msg(text, ok) {
    var m = document.getElementById('pkMsg');
    if (!m) { return; }
    m.textContent = text;
    m.className = 'pk-msg ' + (ok ? 'pk-msg--ok' : 'pk-msg--err');
    m.hidden = false;
  }

  function errText(e) {
    if (e && e.name === 'NotAllowedError')   { return 'Toiminto peruttiin tai aikakatkaistiin.'; }
    if (e && e.name === 'InvalidStateError') { return 'Tämä laite on jo rekisteröity.'; }
    if (e && e.name === 'SecurityError')     { return 'Turvallisuusvirhe — avaa sivu osoitteesta https://manalasdungeon.lv'; }
    return (e && e.message) ? e.message : 'Tuntematon virhe.';
  }

  function val(id) {
    var el = document.getElementById(id);
    return el ? el.value.trim() : '';
  }

  if (!(window.PublicKeyCredential && navigator.credentials)) {
    msg('Tämä selain ei tue passkey-kirjautumista. Käytä ajantasaista Chromea, Edgeä, Firefoxia tai Safaria.', false);
    document.querySelectorAll('[data-pk-action="login"],[data-pk-action="register"]').forEach(function (b) { b.disabled = true; });
    return;
  }

  document.addEventListener('click', async function (ev) {
    var b = ev.target.closest('[data-pk-action]');
    if (!b || b.disabled) { return; }
    ev.preventDefault();
    var act = b.getAttribute('data-pk-action');
    b.disabled = true;
    try {
      if (act === 'login') {
        var r = await login(b.getAttribute('data-next'));
        msg('Kirjautuminen onnistui…', true);
        location.href = r.next || '/passkey/manage.php';
        return;
      }
      if (act === 'register') {
        var label = val('pkLabel');
        if (!label) { throw new Error('Anna laitteelle nimi.'); }
        var tokenEl = document.getElementById('pkToken');
        if (tokenEl && !tokenEl.value.trim()) { throw new Error('Syötä asennusavain.'); }
        await register(label, tokenEl ? tokenEl.value.trim() : '');
        msg('Laite rekisteröity!', true);
        setTimeout(function () { location.href = '/passkey/manage.php'; }, 700);
        return;
      }
      if (act === 'delete') {
        if (!window.confirm('Poistetaanko laite ”' + (b.getAttribute('data-label') || '') + '”?')) {
          b.disabled = false;
          return;
        }
        await api('delete', { id: Number(b.getAttribute('data-id')) });
        location.reload();
        return;
      }
      if (act === 'logout') {
        await api('logout');
        location.href = '/passkey/login.php';
        return;
      }
    } catch (e) {
      msg(errText(e), false);
      b.disabled = false;
    }
  });
})();
