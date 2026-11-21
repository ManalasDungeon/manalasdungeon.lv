/* Palaute-ponnahdusikkuna (etusivu).
   - Kommentoi-nappi avaa <dialog>-ikkunan; ilman JS:ää nappi vie palaute.php-sivulle.
   - CSRF-tunnus haetaan vasta kun ikkuna avataan, joten etusivu ei aseta evästeitä turhaan.
   - Lähetys fetchillä (Accept: application/json), virheet näytetään kenttien alla. */
(function () {
  'use strict';
  var dlg = document.getElementById('palaute');
  var openers = document.querySelectorAll('[data-palaute]');
  if (!dlg || !openers.length || typeof dlg.showModal !== 'function') return; // vanha selain → linkki toimii sellaisenaan

  var form     = dlg.querySelector('form');
  var formView = dlg.querySelector('[data-fb-form-view]');
  var thanks   = dlg.querySelector('[data-fb-thanks-view]');
  var sendBtn  = form.querySelector('.fb-send');
  var sendText = sendBtn.textContent;
  var msg      = form.elements.msg;
  var counter  = dlg.querySelector('[data-fb-count]');
  var tokenUrl = dlg.getAttribute('data-token-url');
  var lastOpener = null;

  function errEl(key) { return dlg.querySelector('[data-fb-err="' + key + '"]'); }

  function clearErrors() {
    dlg.querySelectorAll('[data-fb-err]').forEach(function (el) { el.hidden = true; el.textContent = ''; });
    form.querySelectorAll('[aria-invalid]').forEach(function (el) {
      el.removeAttribute('aria-invalid'); el.removeAttribute('aria-describedby');
    });
  }

  function showErrors(errors) {
    var first = null;
    Object.keys(errors || {}).forEach(function (key) {
      var el = errEl(key);
      if (!el) return;
      el.textContent = errors[key];
      el.hidden = false;
      var input = form.elements[key];
      if (input) {
        el.id = 'fb-err-' + key;
        input.setAttribute('aria-invalid', 'true');
        input.setAttribute('aria-describedby', el.id);
        if (!first) first = input;
      }
    });
    if (first) first.focus();
  }

  function fetchToken() {
    return fetch(tokenUrl, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (d) { form.elements.csrf.value = d.token || ''; })
      .catch(function () { /* lähetys kertoo istuntovirheestä tarvittaessa */ });
  }

  function updateCount() {
    var n = msg.value.length, max = parseInt(msg.getAttribute('maxlength'), 10) || 1500;
    counter.textContent = n + ' / ' + max;
    counter.classList.toggle('is-near', n > max * 0.9);
  }

  function open(e) {
    e.preventDefault();
    lastOpener = e.currentTarget;
    if (!thanks.hidden) {                 // edellinen viesti lähetetty → tyhjä lomake
      form.reset(); updateCount();
      thanks.hidden = true; formView.hidden = false;
    }
    clearErrors();
    dlg.showModal();
    fetchToken();
    setTimeout(function () { form.elements.name.focus(); }, 30);
    if (window.gtag) gtag('event', 'palaute_avattu');
  }

  function close() { dlg.close(); }

  openers.forEach(function (a) { a.addEventListener('click', open); });
  dlg.querySelectorAll('[data-fb-close]').forEach(function (b) { b.addEventListener('click', close); });

  // Klikkaus taustalle (ikkunan ulkopuolelle) sulkee. Kirjoitettu teksti säilyy, jos avaa uudelleen.
  dlg.addEventListener('click', function (e) { if (e.target === dlg) close(); });
  dlg.addEventListener('close', function () { if (lastOpener) lastOpener.focus(); });

  msg.addEventListener('input', updateCount);

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    clearErrors();

    // Selaimen oma tarkistus ensin (pakolliset kentät, sähköpostin muoto, pituudet)
    var bad = {};
    ['name', 'email', 'msg'].forEach(function (k) {
      var el = form.elements[k];
      if (!el.checkValidity()) bad[k] = dlg.getAttribute('data-e-' + k) || el.validationMessage; // sivun kielellä
    });
    if (Object.keys(bad).length) { showErrors(bad); return; }

    sendBtn.disabled = true;
    sendBtn.textContent = sendBtn.getAttribute('data-sending');

    fetch(form.getAttribute('action'), {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json' },
      body: new FormData(form)
    })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (d.ok) {
          formView.hidden = true;
          thanks.hidden = false;
          thanks.querySelector('.fb-send').focus();
          if (window.gtag) gtag('event', 'palaute_lahetetty');
        } else {
          showErrors(d.errors);
        }
      })
      .catch(function () {
        // Verkkovirhe: lähetetään tavallisena lomakkeena (palaute.php näyttää tuloksen)
        form.submit();
      })
      .then(function () {
        sendBtn.disabled = false;
        sendBtn.textContent = sendText;
      });
  });
})();
