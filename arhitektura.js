/* Arkkitehtuurisivu: edistymispalkki, ilmestymiset, laskurit, sisällysluettelon korostus,
   sekvenssikaavion uudelleentoisto. Ilman tätä tiedostoa sivu toimii ja kaikki näkyy. */
(function () {
  'use strict';
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var root = document.documentElement;

  /* Lukemisen edistyminen */
  var bar = document.querySelector('.ar-progress span');
  var ticking = false;
  function progress() {
    var max = root.scrollHeight - root.clientHeight;
    bar.style.setProperty('--p', max > 0 ? Math.min(1, root.scrollTop / max) : 0);
    ticking = false;
  }
  if (bar) {
    window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(progress); } }, { passive: true });
    progress();
  }

  /* Laskuri 0 → luku */
  function countUp(el) {
    var target = parseInt(el.getAttribute('data-count'), 10);
    if (isNaN(target) || reduce || target === 0) { el.textContent = el.getAttribute('data-count'); return; }
    var start = null, dur = 1200;
    el.textContent = '0';
    function step(t) {
      if (!start) start = t;
      var k = Math.min(1, (t - start) / dur), e = 1 - Math.pow(1 - k, 3);
      el.textContent = String(Math.round(target * e));
      if (k < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  /* Ilmestymiset */
  var items = document.querySelectorAll('[data-reveal]');
  if (!('IntersectionObserver' in window)) {
    items.forEach(function (el) { el.classList.add('is-in'); });
  } else {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        var el = en.target;
        el.classList.add('is-in');
        setTimeout(function () { el.style.transitionDelay = '0s'; }, 1400);   /* hover-efektit ilman viivettä */
        var num = el.querySelector('[data-count]');
        if (num) countUp(num);
        io.unobserve(el);
      });
    }, { threshold: 0.18, rootMargin: '0px 0px -40px 0px' });
    items.forEach(function (el) { io.observe(el); });
  }

  /* Sisällysluettelon korostus */
  var links = {};
  document.querySelectorAll('[data-toc]').forEach(function (a) { links[a.getAttribute('data-toc')] = a; });
  var current = null;
  function setActive(id) {
    if (id === current || !links[id]) return;
    if (current && links[current]) links[current].classList.remove('is-active');
    links[id].classList.add('is-active');
    current = id;
    var ol = links[id].closest('ol');                 /* kapealla näytöllä vieritetään valittu siru näkyviin */
    if (ol && ol.scrollWidth > ol.clientWidth) {
      ol.scrollTo({ left: links[id].offsetLeft - 16, behavior: reduce ? 'auto' : 'smooth' });
    }
  }
  if ('IntersectionObserver' in window) {
    var spy = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) setActive(en.target.id); });
    }, { rootMargin: '-35% 0px -60% 0px' });
    document.querySelectorAll('.ar-sec[id]').forEach(function (s) { spy.observe(s); });
  }

  /* Sekvenssikaavion uudelleentoisto */
  document.querySelectorAll('[data-replay]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var fig = btn.closest('.ar-fig');
      fig.classList.remove('is-in');
      void fig.offsetWidth;                            /* pakottaa animaation alkamaan alusta */
      fig.classList.add('is-in');
      if (window.gtag) gtag('event', 'arkkitehtuuri_toisto');
    });
  });
})();
