/* Testattavat sivut. Lisää uusi sivu tähän yhdellä rivillä.
 *  path        osoite (kieliversio lisätään automaattisesti ?lang=en / ?lang=fi)
 *  db          tarvitsee tietokannan (ohitetaan, jos testitietokantaa ei ole)
 *  noindex     ei vaadita canonical- eikä hreflang-merkintöjä
 *  langs       false = testataan vain oletuskielellä
 *  langPrefix  kieliversiot polkuna (esim. /vitrupe/en/) eikä ?lang=-parametrina
 *  visual      false = ei kuvavertailua (satunnaisia animaatioita, esim. konfetti)
 *  h1          false = aloitussivu ilman h1-otsikkoa
 *  contains    tekstit, joiden pitää näkyä sivulla (oletuskielellä)
 *  notContains tekstit, jotka EIVÄT saa näkyä (luonnokset, moderoimattomat)
 *  notInHtml   raakaa HTML:ää, joka ei saa esiintyä suojaamattomana (XSS)
 */
module.exports = {
  pages: [
    { name: 'etusivu',     path: '/',                  db: true },
    { name: 'uutiset',     path: '/blogi',         db: true,
      contains: ['Marraskuinen aamu', 'Testiuutinen kaksi'],
      notContains: ['Piilotettu luonnos'], notInHtml: ['<script>alert(1)</script>', '<b>ei saa</b>'] },
    { name: 'matkablogi',  path: '/celojums',      db: true, contains: ['Valmiera'] },
    { name: 'vieraskirja', path: '/viesu_gramata', db: true,
      contains: ['Terveisiä Heinolasta'],
      notContains: ['Tämä odottaa hyväksyntää'], notInHtml: ['<img src=x onerror'] },
    { name: 'vidzeme',     path: '/vidzeme' },
    { name: 'jooga',       path: '/joga' },
    { name: 'portfolio',   path: '/portfolio' },
    { name: 'linkit',      path: '/linkit' },
    { name: 'arkkitehtuuri', path: '/arhitektura' },
    { name: 'tietosuoja',  path: '/privatums' },
    { name: 'palaute',     path: '/palaute', noindex: true },
    { name: 'latvija18',   path: '/?lv18preview=1', noindex: true, langs: false, visual: false },
    { name: 'samhain',     path: '/samhain.php',    noindex: true, langs: false, visual: false, h1: false },
    { name: 'vitrupe',         path: '/vitrupe/',          langPrefix: '/vitrupe/', visual: false },
    { name: 'vitrupe-galleria', path: '/vitrupe/kuvat.php', langPrefix: '/vitrupe/' },
    { name: 'vitrupe-galleria2', path: '/vitrupe/kuvat2.php', langPrefix: '/vitrupe/' },
    { name: 'vitrupe-galleria3', path: '/vitrupe/kuvat3.php', langPrefix: '/vitrupe/' },
    { name: 'passkey-kirjautuminen', path: '/passkey/login.php', noindex: true, langs: false, visual: false },
  ],
  /* Ylläpitosivujen pitää ohjata kirjautumiseen ilman passkeytä */
  protectedPages: ['/blogi_admin.php', '/viesu_admin.php', '/passkey/manage.php'],
  langs: ['lv', 'en', 'fi'],
  viewports: { tietokone: { width: 1366, height: 900 }, puhelin: { width: 390, height: 844 } },
  visualMaxDiff: 0.003,        // sallittu ero kuvavertailussa (0,3 % pikseleistä)
  visualMaxHeight: 3200,       // kuvavertailu sivun ylimmistä 3200 pikselistä
};
