/* Testattavat sivut. Lisää uusi sivu tähän yhdellä rivillä.
 *  path        osoite (kieliversio lisätään automaattisesti ?lang=en / ?lang=fi)
 *  db          tarvitsee tietokannan (ohitetaan, jos testitietokantaa ei ole)
 *  noindex     ei vaadita canonical- eikä hreflang-merkintöjä
 *  langs       false = testataan vain oletuskielellä
 *  visual      false = ei kuvavertailua (satunnaisia animaatioita, esim. konfetti)
 *  h1          false = aloitussivu ilman h1-otsikkoa
 *  contains    tekstit, joiden pitää näkyä sivulla (oletuskielellä)
 *  notContains tekstit, jotka EIVÄT saa näkyä (luonnokset, moderoimattomat)
 *  notInHtml   raakaa HTML:ää, joka ei saa esiintyä suojaamattomana (XSS)
 */
module.exports = {
  pages: [
    { name: 'etusivu',     path: '/',                  db: true },
    { name: 'uutiset',     path: '/blogi.php',         db: true,
      contains: ['Marraskuinen aamu', 'Testiuutinen kaksi'],
      notContains: ['Piilotettu luonnos'], notInHtml: ['<script>alert(1)</script>', '<b>ei saa</b>'] },
    { name: 'matkablogi',  path: '/celojums.php',      db: true, contains: ['Valmiera'] },
    { name: 'vieraskirja', path: '/viesu_gramata.php', db: true,
      contains: ['Terveisiä Heinolasta'],
      notContains: ['Tämä odottaa hyväksyntää'], notInHtml: ['<img src=x onerror'] },
    { name: 'vidzeme',     path: '/vidzeme.php' },
    { name: 'jooga',       path: '/joga.php' },
    { name: 'portfolio',   path: '/portfolio.php' },
    { name: 'linkit',      path: '/linkit' },
    { name: 'arkkitehtuuri', path: '/arhitektura.php' },
    { name: 'tietosuoja',  path: '/privatums.php' },
    { name: 'palaute',     path: '/palaute.php', noindex: true },
    { name: 'latvija18',   path: '/?lv18preview=1', noindex: true, langs: false, visual: false },
    { name: 'samhain',     path: '/samhain.php',    noindex: true, langs: false, visual: false, h1: false },
    { name: 'passkey-kirjautuminen', path: '/passkey/login.php', noindex: true, langs: false, visual: false },
  ],
  /* Ylläpitosivujen pitää ohjata kirjautumiseen ilman passkeytä */
  protectedPages: ['/blogi_admin.php', '/viesu_admin.php', '/passkey/manage.php'],
  langs: ['lv', 'en', 'fi'],
  viewports: { tietokone: { width: 1366, height: 900 }, puhelin: { width: 390, height: 844 } },
  visualMaxDiff: 0.003,        // sallittu ero kuvavertailussa (0,3 % pikseleistä)
  visualMaxHeight: 3200,       // kuvavertailu sivun ylimmistä 3200 pikselistä
};
