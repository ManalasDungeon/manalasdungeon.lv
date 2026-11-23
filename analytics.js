/* Google Analytics 4 vain kävijän suostumuksella.
 *
 * - gtag() on aina olemassa, joten sivujen omat tapahtumat (linkkiklikkaukset, palaute) eivät kaadu.
 *   Ilman suostumusta tapahtumat jäävät vain selaimen muistiin eikä mitään lähetetä.
 * - Google-skripti ladataan vasta, kun kävijä on valinnut "Piekrītu / Accept / Hyväksyn".
 * - Valinta tallennetaan selaimeen 12 kuukaudeksi (localStorage, ei evästettä).
 * - Kieltäytyminen poistaa mahdolliset aiemmat _ga-evästeet.
 * - Tietosuojasivu kutsuu window.mdConsent.open(), jolla valinnan voi muuttaa.
 * Ei inline-skriptejä eikä style-attribuutteja → sopii CSP:hen. Ulkoasu: consent.css.
 */
(function () {
  'use strict';

  var GA_ID = 'G-0Q1HVYWR72';
  var KEY = 'md_consent';
  var MAX_AGE = 365 * 24 * 60 * 60 * 1000;

  window.dataLayer = window.dataLayer || [];
  window.gtag = function () { window.dataLayer.push(arguments); };

  var TXT = {
    lv: {
      label: 'Sīkdatņu izvēle',
      text: 'Es izmantoju Google Analytics, lai redzētu, cik daudz cilvēku apmeklē vietni un kuras lapas viņus interesē. Tas iestata sīkdatnes tikai tad, ja jūs piekrītat.',
      more: 'Privātuma politika',
      no: 'Atteikties',
      yes: 'Piekrītu'
    },
    en: {
      label: 'Cookie choice',
      text: 'I use Google Analytics to see how many people visit the site and which pages interest them. It sets cookies only if you accept.',
      more: 'Privacy policy',
      no: 'Decline',
      yes: 'Accept'
    },
    fi: {
      label: 'Evästevalinta',
      text: 'Käytän Google Analyticsia nähdäkseni, kuinka moni käy sivustolla ja mitkä sivut kiinnostavat. Se asettaa evästeitä vain, jos hyväksyt.',
      more: 'Tietosuojaseloste',
      no: 'Kieltäydyn',
      yes: 'Hyväksyn'
    }
  };

  function lang() {
    var l = (document.documentElement.getAttribute('lang') || 'lv').slice(0, 2).toLowerCase();
    return TXT[l] ? l : 'lv';
  }

  function read() {
    try {
      var v = JSON.parse(window.localStorage.getItem(KEY) || 'null');
      if (v && (v.c === 'yes' || v.c === 'no') && Date.now() - v.t < MAX_AGE) { return v.c; }
    } catch (e) { /* yksityinen ikkuna tai estetty tallennus */ }
    return null;
  }

  function save(choice) {
    try { window.localStorage.setItem(KEY, JSON.stringify({ c: choice, t: Date.now() })); } catch (e) {}
  }

  var loaded = false;
  function loadGA() {
    if (loaded) { return; }
    loaded = true;
    window.gtag('js', new Date());
    window.gtag('config', GA_ID);
    var s = document.createElement('script');
    s.async = true;
    s.src = 'https://www.googletagmanager.com/gtag/js?id=' + GA_ID;
    document.head.appendChild(s);
  }

  /* Poistaa Googlen _ga-evästeet kaikilla todennäköisillä domain-asetuksilla */
  function wipeCookies() {
    var host = window.location.hostname.replace(/^www\./, '');
    document.cookie.split(';').forEach(function (c) {
      var name = c.split('=')[0].trim();
      if (!/^_ga/.test(name)) { return; }
      ['', '; domain=' + host, '; domain=.' + host].forEach(function (d) {
        document.cookie = name + '=; Max-Age=0; path=/' + d;
      });
    });
  }

  function privacyUrl(l) {
    return '/privatums' + (l === 'lv' ? '' : '?lang=' + l);
  }

  function ensureCss() {
    if (document.getElementById('md-consent-css')) { return; }
    var link = document.createElement('link');
    link.id = 'md-consent-css';
    link.rel = 'stylesheet';
    link.href = '/consent.css?v=1';
    document.head.appendChild(link);
  }

  var box = null;
  function close() {
    if (box) { box.remove(); box = null; }
  }

  function open() {
    if (box) { return; }
    ensureCss();
    var l = lang(), t = TXT[l];

    box = document.createElement('section');
    box.className = 'md-consent';
    box.setAttribute('role', 'region');
    box.setAttribute('aria-label', t.label);

    var p = document.createElement('p');
    p.className = 'md-consent__text';
    p.textContent = t.text + ' ';
    var a = document.createElement('a');
    a.href = privacyUrl(l);
    a.textContent = t.more;
    p.appendChild(a);

    var row = document.createElement('div');
    row.className = 'md-consent__btns';
    var no = document.createElement('button');
    no.type = 'button';
    no.className = 'md-consent__btn md-consent__btn--no';
    no.textContent = t.no;
    var yes = document.createElement('button');
    yes.type = 'button';
    yes.className = 'md-consent__btn md-consent__btn--yes';
    yes.textContent = t.yes;
    row.appendChild(no);
    row.appendChild(yes);

    no.addEventListener('click', function () { apply('no'); });
    yes.addEventListener('click', function () { apply('yes'); });

    box.appendChild(p);
    box.appendChild(row);
    document.body.appendChild(box);
  }

  /* Valinnan asettaminen (ikkunan painikkeet ja tietosuojasivun kytkin) */
  function apply(choice) {
    save(choice);
    close();
    if (choice === 'yes') {
      window['ga-disable-' + GA_ID] = false;
      loadGA();
    } else {
      window['ga-disable-' + GA_ID] = true;   // jo ladattu Google-skripti lopettaa lähettämisen
      wipeCookies();
    }
    syncPanel();
  }

  /* Tietosuojasivun kytkin [data-consent-toggle] ja tilateksti [data-consent-status] */
  function syncPanel() {
    var c = read();
    document.querySelectorAll('[data-consent-toggle]').forEach(function (t) {
      t.disabled = false;
      t.checked = c === 'yes';
    });
    document.querySelectorAll('[data-consent-status]').forEach(function (el) {
      el.textContent = el.getAttribute(c === 'yes' ? 'data-yes' : c === 'no' ? 'data-no' : 'data-none') || '';
      el.setAttribute('data-state', c || 'none');
    });
  }
  document.addEventListener('change', function (e) {
    if (e.target && e.target.matches && e.target.matches('[data-consent-toggle]')) {
      apply(e.target.checked ? 'yes' : 'no');
    }
  });
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', syncPanel);
  } else {
    syncPanel();
  }

  /* Mikä tahansa [data-consent-open]-painike avaa valinnan uudelleen */
  document.addEventListener('click', function (e) {
    var el = e.target && e.target.closest ? e.target.closest('[data-consent-open]') : null;
    if (el) { e.preventDefault(); close(); open(); }
  });

  window.mdConsent = {
    open: open,
    status: read
  };

  var choice = read();
  if (choice === 'yes') {
    loadGA();
  } else {
    if (choice === 'no') { window['ga-disable-' + GA_ID] = true; wipeCookies(); }
    if (choice === null) {
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', open);
      } else {
        open();
      }
    }
  }
})();
