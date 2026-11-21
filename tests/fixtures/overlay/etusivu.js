/* Manalas Dungeon — etusivun skriptit.
   1) Ziņas-linkin kävijäkohtainen päiväysmerkki.
   2) Lintupeli-/kirjasto-linkin selostuskortti (avaus/sulku). */

/* 1) Näytä Ziņas-linkin päiväysmerkki vain, jos kävijä ei ole vielä avannut
      uusinta ziņaa. blogi.php tallentaa viimeksi nähdyn aikaleiman. */
(function () {
  'use strict';
  var news = document.querySelector('.link--news');
  if (!news) { return; }
  var ts = parseInt(news.getAttribute('data-zina-ts') || '0', 10);
  var badge = news.querySelector('.link__badge');
  if (!ts || !badge) { return; }
  var seen = 0;
  try { seen = parseInt(localStorage.getItem('zina_seen_ts') || '0', 10) || 0; } catch (e) {}
  if (ts > seen) { badge.hidden = false; news.classList.add('has-new'); }
  news.addEventListener('click', function () {
    try { localStorage.setItem('zina_seen_ts', String(ts)); } catch (e) {}
  });
})();

/* 2) Detail-kortti: klikkaa lintupeli-/kirjasto-linkkiä -> avautuu selostuskortti
      oikealle, jossa nappi varsinaiselle sivulle. Sulkeutuu ×, Esc tai taustaa
      klikkaamalla. */
(function () {
  'use strict';
  var detail = document.getElementById('detail');
  var wrap = document.querySelector('.wrap');
  if (!detail || !wrap) { return; }
  var dTitle = detail.querySelector('.detail__title');
  var dText  = detail.querySelector('.detail__text');
  var dBtn   = detail.querySelector('.detail__btn');

  function openDetail(link) {
    var label = link.querySelector('.link__label');
    dTitle.textContent = label ? label.textContent : '';
    dText.textContent  = link.getAttribute('data-detail-text') || '';
    dBtn.setAttribute('href', link.getAttribute('href'));
    wrap.classList.add('detail-visible');
    requestAnimationFrame(function () { detail.classList.add('open'); });
    document.querySelectorAll('.link.is-active').forEach(function (l) { l.classList.remove('is-active'); });
    link.classList.add('is-active');
  }
  function closeDetail() {
    detail.classList.remove('open');
    document.querySelectorAll('.link.is-active').forEach(function (l) { l.classList.remove('is-active'); });
    setTimeout(function () { if (!detail.classList.contains('open')) { wrap.classList.remove('detail-visible'); } }, 360);
  }

  document.querySelectorAll('.link[data-detail]').forEach(function (link) {
    link.addEventListener('click', function (e) { e.preventDefault(); openDetail(link); });
  });
  detail.querySelector('.detail__close').addEventListener('click', closeDetail);
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeDetail(); } });

  /* Sulje myös klikkaamalla korttien ulkopuolelle (taustakuvaa) */
  document.addEventListener('click', function (e) {
    if (!detail.classList.contains('open')) { return; }
    if (e.target.closest && (e.target.closest('.card') || e.target.closest('.detail'))) { return; }
    closeDetail();
  });
})();
