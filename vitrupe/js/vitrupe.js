/* ============================================================
   Vitrupes Pludmale — yhteinen skripti
   Navipalkin tila, scroll-reveal, äänipainike, kellonaika ja
   gallerian lightbox kuvanavigoinnilla (nuolet / näppäimistö / pyyhkäisy).
   ============================================================ */

/* Lightboxin tila. Kuvat avataan tapahtumankäsittelijöillä (ei inline-onclickiä → sopii CSP:hen). */
var _lbImgs = [];
var _lbIdx = 0;
var _lbOpener = null;   /* kuva, josta lightbox avattiin: kohdistus palaa siihen suljettaessa */
var _LB_TXT = {
  lv: { prev: 'Iepriekšējais attēls', next: 'Nākamais attēls' },
  en: { prev: 'Previous image',       next: 'Next image' },
  fi: { prev: 'Edellinen kuva',       next: 'Seuraava kuva' }
};
function _lbLang() {
  var l = (document.documentElement.getAttribute('lang') || 'lv').slice(0, 2);
  return _LB_TXT[l] ? l : 'lv';
}

function _lbCollect() {
  _lbImgs = Array.prototype.slice.call(document.querySelectorAll('.gallery__media img'));
}
function _lbShow() {
  var img = document.getElementById('lightbox-img'),
      cap = document.getElementById('lightbox-cap'),
      el  = _lbImgs[_lbIdx];
  if (!img || !el) return;
  img.src = el.getAttribute('data-full') || el.currentSrc || el.src;   /* täysikokoinen kuva, ruudukossa pikkukuva */
  img.alt = el.alt || '';
  var box = el.closest('.gallery'), d = box ? box.querySelector('.desc') : null;
  if (cap) cap.textContent = d ? d.textContent : (el.alt || '');
}
function _lbNav(dir) {
  if (!_lbImgs.length) return;
  _lbIdx = (_lbIdx + dir + _lbImgs.length) % _lbImgs.length;
  _lbShow();
}

function openImg(el) {
  var lb = document.getElementById('lightbox');
  if (!lb) return;
  if (!_lbImgs.length) _lbCollect();
  _lbIdx = _lbImgs.indexOf(el);
  if (_lbIdx < 0) _lbIdx = 0;
  _lbOpener = el;
  _lbShow();
  lb.classList.add('open');
  document.body.style.overflow = 'hidden';
  var close = lb.querySelector('.lightbox__close');
  if (close) close.focus();
}
function closeImg() {
  var lb = document.getElementById('lightbox');
  if (!lb || !lb.classList.contains('open')) return;
  lb.classList.remove('open');
  document.body.style.overflow = '';
  if (_lbOpener && _lbOpener.focus) _lbOpener.focus();
}

(function () {
  /* Navipalkki: läpinäkyvä hero:n päällä, kiinteä vieritettäessä */
  var nav = document.getElementById('siteNav');
  function onScroll() { if (nav) nav.classList.toggle('is-solid', window.scrollY > 40); }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* Scroll-reveal */
  var io = ('IntersectionObserver' in window) ? new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
    });
  }, { threshold: 0.12 }) : null;
  document.querySelectorAll('.reveal').forEach(function (el) {
    if (io) io.observe(el); else el.classList.add('is-visible');
  });

  /* Hero: vaihtuva taustakuva (ristiinhäivytys). Aktivoituu vain
     elementille #heroBg, jolla on data-hero-lista (esim. "ranta1,ranta2").
     Galleriasivujen sub-herot eivät muutu. Ei pyöri, jos käyttäjä on
     valinnut vähennetyn liikkeen. */
  var hero = document.getElementById('heroBg');
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (hero && hero.getAttribute('data-hero') && !reduceMotion) {
    var heroNames = hero.getAttribute('data-hero').split(',')
      .map(function (s) { return s.trim(); })
      .filter(function (s) { return s; });
    if (heroNames.length > 1) {
      /* Esiladataan vain seuraava kuva (ei kaikkia kerralla) — säästää mobiilidataa */
      var preload = function (i) { var im = new Image(); im.src = 'images/' + heroNames[i] + '.webp'; };
      preload(1);
      var layer = document.createElement('div');
      layer.className = hero.className;       /* sama tyyli: cover + scale */
      layer.style.opacity = '0';
      layer.style.transition = 'opacity 1.4s ease';
      hero.parentNode.insertBefore(layer, hero.nextSibling);
      var hi = 0;
      setInterval(function () {
        hi = (hi + 1) % heroNames.length;
        var url = "url('images/" + heroNames[hi] + ".webp')";
        layer.style.backgroundImage = url;
        layer.style.opacity = '1';
        setTimeout(function () {
          hero.style.backgroundImage = url;
          layer.style.opacity = '0';
          preload((hi + 1) % heroNames.length);
        }, 1400);
      }, 6000);
    }
  }

  /* Lightboxin kuvanavigointi: nuolet + pyyhkäisy (injektoidaan tähän) */
  var lb = document.getElementById('lightbox');
  if (lb) {
    _lbCollect();

    /* Kuvan avaus hiirellä tai näppäimistöllä (Enter / välilyönti) */
    document.addEventListener('click', function (e) {
      var img = e.target.closest ? e.target.closest('.gallery__media img') : null;
      if (img) openImg(img);
    });
    document.addEventListener('keydown', function (e) {
      if ((e.key === 'Enter' || e.key === ' ') && e.target.matches && e.target.matches('.gallery__media img')) {
        e.preventDefault();
        openImg(e.target);
      }
    });
    /* Taustan tai ×-painikkeen klikkaus sulkee (kuva ja nuolet pysäyttävät tapahtuman) */
    lb.addEventListener('click', closeImg);

    /* Tyylit asetetaan JS:ssä, jotta toimii vaikka kuvatyylit.css olisi
       vanha välimuistissa (ei riippuvuutta .lightbox__nav-sääntöihin). */
    lb.style.touchAction = 'none';

    /* Kuvan klikkaus ei sulje — vain tausta, ×-painike tai Esc */
    var lbImg = document.getElementById('lightbox-img');
    if (lbImg) {
      lbImg.style.touchAction = 'none';
      lbImg.addEventListener('click', function (e) { e.stopPropagation(); });
    }

    var navBase = 'position:absolute;top:50%;transform:translateY(-50%);z-index:3;' +
      'width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;' +
      'background:rgba(20,10,8,.5);color:#fff;border:1px solid rgba(255,255,255,.4);' +
      'font-size:2rem;line-height:1;font-family:sans-serif;cursor:pointer;' +
      '-webkit-user-select:none;user-select:none;-webkit-tap-highlight-color:transparent';

    var prev = document.createElement('button');
    prev.type = 'button';
    prev.className = 'lightbox__nav lightbox__nav--prev';
    prev.setAttribute('aria-label', _LB_TXT[_lbLang()].prev);
    prev.innerHTML = '&#8249;';
    prev.style.cssText = navBase + ';left:14px';
    prev.addEventListener('click', function (e) { e.stopPropagation(); _lbNav(-1); });

    var next = document.createElement('button');
    next.type = 'button';
    next.className = 'lightbox__nav lightbox__nav--next';
    next.setAttribute('aria-label', _LB_TXT[_lbLang()].next);
    next.innerHTML = '&#8250;';
    next.style.cssText = navBase + ';right:14px';
    next.addEventListener('click', function (e) { e.stopPropagation(); _lbNav(1); });

    lb.appendChild(prev);
    lb.appendChild(next);

    /* Pyyhkäisy mobiilissa. Vaakaeleellä estetään selaimen oma ele
       (preventDefault), jotta touchend laukeaa ja kuva vaihtuu. */
    var x0 = null, y0 = null;
    lb.addEventListener('touchstart', function (e) {
      if (e.touches.length !== 1) { x0 = null; return; }
      x0 = e.touches[0].clientX;
      y0 = e.touches[0].clientY;
    }, { passive: true });
    lb.addEventListener('touchmove', function (e) {
      if (x0 === null) return;
      var dx = e.touches[0].clientX - x0, dy = e.touches[0].clientY - y0;
      if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 10 && e.cancelable) {
        e.preventDefault();
      }
    }, { passive: false });
    lb.addEventListener('touchend', function (e) {
      if (x0 === null) return;
      var dx = e.changedTouches[0].clientX - x0;
      if (Math.abs(dx) > 40) _lbNav(dx < 0 ? 1 : -1);
      x0 = null;
    }, { passive: true });
  }

  /* Näppäimistö: Esc sulkee, ←/→ selaa */
  document.addEventListener('keydown', function (e) {
    var box = document.getElementById('lightbox');
    if (!box || !box.classList.contains('open')) return;
    if (e.key === 'Escape') closeImg();
    else if (e.key === 'ArrowLeft') _lbNav(-1);
    else if (e.key === 'ArrowRight') _lbNav(1);
  });

  /* Kelluva äänipainike */
  var audio = document.getElementById('beachAudio'), btn = document.getElementById('soundBtn');
  if (btn && audio) {
    btn.addEventListener('click', function () {
      if (audio.paused) { audio.play(); btn.classList.add('is-on'); btn.setAttribute('aria-pressed', 'true'); }
      else { audio.pause(); btn.classList.remove('is-on'); btn.setAttribute('aria-pressed', 'false'); }
    });
  }

  /* Kellonaika footeriin */
  if (typeof date_time === 'function') { try { date_time('date_time'); } catch (e) {} }
})();
