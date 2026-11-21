/* Ziņas / Uutiset — blogisivun skriptit.
   1) Merkitään uusin ziņa nähdyksi (etusivun päiväysmerkki katoaa).
   2) Uutiskorttien reveal-animaatio vierityksen mukaan. */
(function () {
  'use strict';

  // 1) Tallenna viimeksi nähty aikaleima (luetaan body[data-zina-ts]:stä)
  var ts = document.body.getAttribute('data-zina-ts');
  if (ts) {
    try { localStorage.setItem('zina_seen_ts', String(parseInt(ts, 10) || 0)); } catch (e) {}
  }

  // 2) Reveal-animaatio
  var io = ('IntersectionObserver' in window)
    ? new IntersectionObserver(function (entries) {
        entries.forEach(function (x) {
          if (x.isIntersecting) { x.target.classList.add('vis'); io.unobserve(x.target); }
        });
      }, { threshold: 0.12 })
    : null;

  document.querySelectorAll('.reveal').forEach(function (el, i) {
    el.style.transitionDelay = Math.min(i * 0.07, 0.4) + 's';
    if (io) { io.observe(el); } else { el.classList.add('vis'); }
  });
})();

/* 3) Kuvan suurennus (lightbox): klikkaa uutiskuvaa -> täyskuva.
   Tyylit JS:ssä, tapahtumadelegointi, joten toimii myös lazy-load-kuville. */
(function () {
  'use strict';

  var ov = document.createElement('div');
  ov.setAttribute('role', 'dialog');
  ov.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;z-index:1000;' +
    'display:none;align-items:center;justify-content:center;' +
    'background:rgba(10,6,6,.93);padding:20px;cursor:zoom-out;touch-action:none';

  var big = document.createElement('img');
  big.alt = '';
  big.style.cssText = 'max-width:100%;max-height:100%;width:auto;height:auto;' +
    'border-radius:8px;box-shadow:0 12px 50px rgba(0,0,0,.7)';

  var btn = document.createElement('button');
  btn.type = 'button';
  btn.innerHTML = '&times;';
  btn.setAttribute('aria-label', 'Aizvērt');
  btn.style.cssText = 'position:fixed;top:14px;right:18px;z-index:1001;' +
    'background:rgba(20,10,10,.6);color:#fff;border:1px solid rgba(255,255,255,.35);' +
    'width:44px;height:44px;border-radius:50%;font-size:1.6rem;line-height:1;cursor:pointer;' +
    'display:flex;align-items:center;justify-content:center';

  ov.appendChild(big);
  ov.appendChild(btn);
  document.body.appendChild(ov);

  function openImg(src) { big.src = src; ov.style.display = 'flex'; }
  function closeImg() { ov.style.display = 'none'; big.src = ''; }

  document.addEventListener('click', function (e) {
    var im = e.target.closest ? e.target.closest('.zina-fig img') : null;
    if (im) { openImg(im.currentSrc || im.src); return; }
    if (e.target === ov || e.target === big || e.target === btn) { closeImg(); }
  });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeImg(); } });
})();
