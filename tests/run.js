#!/usr/bin/env node
/* manalasdungeon.lv — automaattiset testit
 *
 *   node run.js              kaikki testit
 *   node run.js --update     päivitä kuvavertailun pohjakuvat (tarkoituksellisen ulkoasumuutoksen jälkeen)
 *   node run.js --no-visual  ilman kuvavertailua (nopea)
 *   node run.js --only=uutiset,linkit
 *
 * Testit ajetaan sivuston KOPIOTA vastaan väliaikaiskansiossa: kopioon lisätään testitietokannan
 * db_config.php, joten oikeisiin tiedostoihin ei kosketa. Ulkoiset pyynnöt (Google Fonts, GA4)
 * estetään, jotta tulokset ovat toistettavia.
 *
 * Ympäristömuuttujat (valinnaiset):
 *   TEST_DB_HOST, TEST_DB_NAME, TEST_DB_USER, TEST_DB_PASS   testitietokanta (oletus manalas_test)
 *   TEST_BASELINE  kuvavertailun pohjakuvien kansio (oletus tests/baseline)
 *   TEST_OVERLAY   kansio, jonka tiedostot kopioidaan sivuston kopion päälle (esim. palvelimella
 *                  olevat kuvat, joita ei ole repossa)
 */
'use strict';
const fs = require('fs');
const os = require('os');
const path = require('path');
const { spawn, execFileSync } = require('child_process');
const { chromium, request } = require('playwright');
const CFG = require('./pages');

const args = process.argv.slice(2);
const UPDATE = args.includes('--update');
const VISUAL = !args.includes('--no-visual');
const A11Y = !args.includes('--no-a11y');
const AXE_SRC = A11Y ? fs.readFileSync(require.resolve('axe-core/axe.min.js'), 'utf8') : '';
const ONLY = (args.find(a => a.startsWith('--only=')) || '').slice(7).split(',').filter(Boolean);

const SRC = fs.realpathSync(path.resolve(__dirname, '..'));
const FIX = path.join(__dirname, 'fixtures');
const BASE_DIR = process.env.TEST_BASELINE || path.join(__dirname, 'baseline');
const OUT_DIR = path.join(__dirname, 'output');
const PORT = 8000 + Math.floor(Math.random() * 900);
const ORIGIN = `http://127.0.0.1:${PORT}`;

const results = [];   // { group, name, ok, detail }
function rec(group, name, ok, detail = '') { results.push({ group, name, ok: !!ok, detail }); }

/* ---------- 1. Sivuston kopio (alkuperäiseen ei kirjoiteta koskaan) ---------- */
function makeSiteCopy() {
  const tmp = fs.realpathSync(fs.mkdtempSync(path.join(os.tmpdir(), 'manala-test-')));
  if (tmp.startsWith(SRC + path.sep)) throw new Error('Väliaikaiskansio ei saa olla sivuston sisällä');
  const site = path.join(tmp, 'public_html');
  const skip = new Set(['tests', 'node_modules', '.git', 'github_profile']);
  fs.cpSync(SRC, site, {
    recursive: true, dereference: true,
    filter: (src) => {
      const rel = path.relative(SRC, src);
      if (!rel) return true;
      if (skip.has(rel.split(path.sep)[0])) return false;
      if (/\.(zip|mp4|webm)$|_esikatselu/.test(rel)) return false;
      if (rel === 'db_config.php') return false;           // oikeaa yhteyttä ei koskaan testeihin
      if (rel === path.join('passkey', 'config.php')) return false;   // eikä oikeaa asennusavainta
      return true;
    },
  });
  if (process.env.TEST_OVERLAY) fs.cpSync(process.env.TEST_OVERLAY, site, { recursive: true, dereference: true });
  // Passkeyn testiasetukset (oikea config.php ei ole versionhallinnassa eikä testikopiossa)
  fs.copyFileSync(path.join(FIX, 'passkey_config.test.php'), path.join(site, 'passkey', 'config.php'));
  return { tmp, site };
}

/* ---------- 2. Testitietokanta ---------- */
function mysqlArgs(extra = []) {
  return ['--default-character-set=utf8mb4', '-h', process.env.TEST_DB_HOST || '127.0.0.1', '-u', process.env.TEST_DB_USER || 'manalas_test',
          ...extra, process.env.TEST_DB_NAME || 'manalas_test'];
}
const mysqlEnv = () => ({ ...process.env, MYSQL_PWD: process.env.TEST_DB_PASS || 'manalas_test' });
function dbQuery(sql) {
  return execFileSync('mysql', mysqlArgs(['-N', '-B', '-e', sql]), { env: mysqlEnv() }).toString().trim();
}
function setupDb(site) {
  try {
    for (const f of ['schema.sql', 'seed.sql']) {
      execFileSync('mysql', mysqlArgs(), { input: fs.readFileSync(path.join(FIX, f)), env: mysqlEnv(), stdio: ['pipe', 'ignore', 'pipe'] });
    }
    fs.copyFileSync(path.join(FIX, 'db_config.test.php'), path.join(site, 'db_config.php'));
    return true;
  } catch (e) {
    console.log('  ! Testitietokantaa ei saatu käyttöön — tietokantasivut ohitetaan.\n    ' + String(e.message).split('\n')[0]);
    return false;
  }
}

/* ---------- 3. PHP-syntaksi ---------- */
function phpLint(site) {
  const files = [];
  (function walk(d) {
    for (const e of fs.readdirSync(d, { withFileTypes: true })) {
      const p = path.join(d, e.name);
      if (e.isDirectory()) walk(p); else if (e.name.endsWith('.php')) files.push(p);
    }
  })(site);
  let bad = 0;
  for (const f of files) {
    try { execFileSync('php', ['-l', f], { stdio: 'pipe' }); }
    catch (e) { bad++; rec('php -l', path.relative(site, f), false, String(e.stdout || e.message).trim()); }
  }
  rec('php -l', `${files.length} tiedostoa`, bad === 0, bad ? `${bad} virheellistä` : '');
}

/* ---------- 4. Palvelin ---------- */
function startServer(site, mailDir) {
  return spawn('php', ['-S', `127.0.0.1:${PORT}`, '-t', site,
    '-d', 'display_errors=1', '-d', 'error_reporting=E_ALL',
    '-d', `sendmail_path=sh ${path.join(FIX, 'sendmail.sh')}`,      // sh: toimii myös ilman suoritusoikeutta
    path.join(FIX, 'router.php')], { env: { ...process.env, MAIL_DIR: mailDir }, stdio: 'ignore' });
}
async function waitForServer() {
  for (let i = 0; i < 50; i++) {
    try { const r = await fetch(ORIGIN + '/robots.txt'); if (r.status) return; } catch (e) { /* odotetaan */ }
    await new Promise(r => setTimeout(r, 100));
  }
  throw new Error('PHP-palvelin ei käynnistynyt');
}

/* ---------- 5. Sivutestit ---------- */
const PHP_ERR = /(<b>)?(Warning|Notice|Deprecated|Fatal error|Parse error|Uncaught)(<\/b>)?:\s/;
function urlFor(p, lang) {
  if (lang === 'lv') return p;
  return p + (p.includes('?') ? '&' : '?') + 'lang=' + lang;
}

async function checkPage(browser, page, lang, vpName, vp, linkSet) {
  const label = `${page.name} · ${lang} · ${vpName}`;
  const ctx = await browser.newContext({ viewport: vp, deviceScaleFactor: 1, reducedMotion: 'reduce' });
  const tab = await ctx.newPage();
  const errors = [], failed = [];
  await tab.addInitScript(() => {
    // Evästevalinta valmiiksi tehty ("ei"), jotta ilmoitus ei peitä sivua kuvavertailussa
    try { localStorage.setItem('md_consent', JSON.stringify({ c: 'no', t: Date.now() })); } catch (e) {}
    window.__csp = [];
    document.addEventListener('securitypolicyviolation', e => window.__csp.push(e.violatedDirective + ' ' + e.blockedURI));
  });
  await tab.route(u => !String(u).startsWith(ORIGIN), r => r.abort());    // ei ulkoisia pyyntöjä
  tab.on('pageerror', e => errors.push('JS: ' + e.message));
  tab.on('console', m => {
    const t = m.text();
    if (m.type() === 'error' && !/Failed to load resource/.test(t)) errors.push('console: ' + t);
  });
  tab.on('response', r => { if (r.url().startsWith(ORIGIN) && r.status() >= 400) failed.push(r.status() + ' ' + r.url().slice(ORIGIN.length).replace(/\?v=\d+$/, '')); });

  const problems = [];
  let resp;
  try { resp = await tab.goto(ORIGIN + urlFor(page.path, lang), { waitUntil: 'load', timeout: 20000 }); }
  catch (e) { rec('sivut', label, false, 'ei latautunut: ' + e.message); await ctx.close(); return; }
  if (!resp || resp.status() !== 200) problems.push('HTTP ' + (resp && resp.status()));

  const html = await tab.content();
  const m = html.match(PHP_ERR);
  if (m) problems.push('PHP-virhe sivulla: ' + html.slice(html.indexOf(m[0]), html.indexOf(m[0]) + 160).replace(/<[^>]+>/g, ''));

  // Vieritetään loppuun asti, jotta laiskasti latautuvat kuvat ja ilmestymiset laukeavat
  await tab.evaluate(async () => {
    for (let y = 0; y < document.documentElement.scrollHeight; y += 500) { window.scrollTo(0, y); await new Promise(r => setTimeout(r, 40)); }
    window.scrollTo(0, 0);
  });
  await tab.waitForTimeout(400);

  const info = await tab.evaluate(() => ({
    lang: document.documentElement.lang,
    title: document.title.trim(),
    h1: [...document.querySelectorAll('h1')].filter(h => h.getClientRects().length > 0).length,
    canonical: (document.querySelector('link[rel=canonical]') || {}).href || '',
    hreflang: [...document.querySelectorAll('link[rel=alternate][hreflang]')].map(l => l.hreflang),
    overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
    brokenImgs: [...document.images].filter(i => i.complete && i.naturalWidth === 0 && i.currentSrc).map(i => i.currentSrc.replace(location.origin, '')),
    noAlt: [...document.images].filter(i => !i.hasAttribute('alt')).map(i => i.src.replace(location.origin, '')),
    links: [...document.querySelectorAll('a[href]')].map(a => a.href).filter(h => h.startsWith(location.origin)),
    text: document.body.innerText,
    csp: window.__csp || [],
  }));

  if (page.langs !== false && info.lang !== lang) problems.push(`<html lang="${info.lang}">, odotettiin ${lang}`);
  if (!info.title) problems.push('tyhjä <title>');
  if (page.h1 !== false && info.h1 !== 1) problems.push(`h1-otsikoita ${info.h1} (pitäisi olla 1)`);
  if (!page.noindex) {
    if (!info.canonical.startsWith('https://manalasdungeon.lv/')) problems.push('canonical puuttuu tai väärä: ' + info.canonical);
    else if (lang !== 'lv' && !info.canonical.includes('lang=' + lang)) problems.push('canonical ei ole kieliversion: ' + info.canonical);
    for (const l of ['lv', 'en', 'fi', 'x-default']) if (!info.hreflang.includes(l)) problems.push('hreflang puuttuu: ' + l);
  }
  if (info.overflow > 1) problems.push(`vaakasuuntainen ylivuoto ${info.overflow}px`);
  if (info.brokenImgs.length) problems.push('rikkinäiset kuvat: ' + info.brokenImgs.join(', '));
  if (info.noAlt.length) problems.push('kuvilta puuttuu alt: ' + info.noAlt.slice(0, 3).join(', '));
  if (failed.length) problems.push('epäonnistuneet pyynnöt: ' + [...new Set(failed)].join(', '));
  if (errors.length) problems.push([...new Set(errors)].join(' | '));
  if (info.csp.length) problems.push('CSP-rikkeet: ' + [...new Set(info.csp)].join(', '));
  if (lang === 'lv' || page.langs === false) {
    for (const t of page.contains || []) if (!info.text.includes(t)) problems.push(`puuttuu teksti "${t}"`);
    for (const t of page.notContains || []) if (info.text.includes(t)) problems.push(`EI saisi näkyä: "${t}"`);
    for (const t of page.notInHtml || []) if (html.includes(t)) problems.push(`suojaamaton HTML sivulla: "${t}"`);
  }
  info.links.forEach(l => linkSet.add(l.split('#')[0]));

  /* Saavutettavuus (axe-core, WCAG 2.1 A/AA): vakavat ja kriittiset löydökset kaatavat testin.
     Ajetaan oletuskielellä tietokoneen koossa (sama HTML kaikilla kielillä, vain tekstit vaihtuvat).
     evaluate() ohittaa sivun CSP:n, joten axe ei aiheuta CSP-rikettä. */
  if (A11Y && vpName === 'tietokone' && (lang === 'lv' || page.langs === false)) {
    await tab.evaluate(AXE_SRC);
    const res = await tab.evaluate(() => window.axe.run(document, {
      runOnly: { type: 'tag', values: ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'] },
      resultTypes: ['violations'],
    }));
    const bad = res.violations.filter(v => v.impact === 'serious' || v.impact === 'critical');
    for (const v of bad) {
      const where = v.nodes.slice(0, 2).map(n => n.target.join(' ')).join(', ');
      problems.push(`saavutettavuus: ${v.id} (${v.nodes.length} kpl, esim. ${where})`);
    }
  }

  /* Kuvavertailu */
  if (VISUAL && page.visual !== false) {
    await tab.addStyleTag({ content: '*,*::before,*::after{animation:none!important;transition:none!important;caret-color:transparent!important}' });
    await tab.evaluate(() => document.querySelectorAll('[data-reveal]').forEach(e => e.classList.add('is-in')));
    await tab.waitForTimeout(150);
    const full = await tab.evaluate(() => document.documentElement.scrollHeight);
    const shot = await tab.screenshot({ clip: { x: 0, y: 0, width: vp.width, height: Math.min(full, CFG.visualMaxHeight) }, fullPage: true });
    const v = compareVisual(shot, `${page.name}_${lang}_${vpName}.png`);
    if (v) problems.push(v);
  }

  rec('sivut', label, problems.length === 0, problems.join('; '));
  await ctx.close();
}

function compareVisual(buf, file) {
  const { PNG } = require('pngjs');
  const pixelmatch = require('pixelmatch');
  const basePath = path.join(BASE_DIR, file);
  if (UPDATE || !fs.existsSync(basePath)) {
    fs.mkdirSync(BASE_DIR, { recursive: true });
    fs.writeFileSync(basePath, buf);
    return '';                                                   // uusi pohjakuva
  }
  const a = PNG.sync.read(fs.readFileSync(basePath)), b = PNG.sync.read(buf);
  if (a.width !== b.width || a.height !== b.height) {
    fs.mkdirSync(path.join(OUT_DIR, 'diff'), { recursive: true });
    fs.writeFileSync(path.join(OUT_DIR, 'diff', file.replace('.png', '_uusi.png')), buf);
    return `kuvavertailu: koko muuttui ${a.width}×${a.height} → ${b.width}×${b.height} (ks. tests/output/diff/)`;
  }
  const diff = new PNG({ width: a.width, height: a.height });
  const n = pixelmatch(a.data, b.data, diff.data, a.width, a.height, { threshold: 0.12 });
  const ratio = n / (a.width * a.height);
  if (ratio > CFG.visualMaxDiff) {
    fs.mkdirSync(path.join(OUT_DIR, 'diff'), { recursive: true });
    fs.writeFileSync(path.join(OUT_DIR, 'diff', file), PNG.sync.write(diff));
    fs.writeFileSync(path.join(OUT_DIR, 'diff', file.replace('.png', '_uusi.png')), buf);
    return `kuvavertailu: ${(ratio * 100).toFixed(2)} % pikseleistä muuttui (ks. tests/output/diff/${file})`;
  }
  return '';
}

/* ---------- 6. Linkit ---------- */
async function checkLinks(api, linkSet) {
  const bad = [];
  for (const l of linkSet) {
    try {
      const r = await api.get(l, { maxRedirects: 5 });
      if (r.status() >= 400) bad.push(r.status() + ' ' + l.replace(ORIGIN, ''));
    } catch (e) { bad.push('virhe ' + l.replace(ORIGIN, '')); }
  }
  rec('linkit', `${linkSet.size} sisäistä linkkiä`, bad.length === 0, bad.join(', '));
}

/* ---------- 7. Suojatut sivut ---------- */
async function checkProtected(api) {
  for (const p of CFG.protectedPages) {
    const r = await api.get(ORIGIN + p, { maxRedirects: 0 });
    const loc = r.headers()['location'] || '';
    rec('ylläpito', p + ' ohjaa kirjautumiseen', [301, 302, 303].includes(r.status()) && loc.includes('/passkey/login.php'),
        `HTTP ${r.status()} ${loc}`);
  }
}

/* ---------- 8. Lomakkeet ---------- */
async function checkForms(mailDir, haveDb) {
  const mails = () => fs.readdirSync(mailDir).filter(f => f.endsWith('.eml'));
  const json = { Accept: 'application/json' };
  const sleep = ms => new Promise(res => setTimeout(res, ms));

  /* Palaute */
  let api = await request.newContext({ baseURL: ORIGIN });
  let t = (await (await api.get('/palaute.php?token=1')).json()).token;
  let r = await (await api.post('/palaute.php', { headers: json, form: { csrf: 'väärä', lang: 'fi', name: 'A', msg: 'hei hei' } })).json();
  rec('palaute', 'väärä CSRF-tunniste hylätään', r.ok === false && r.errors && r.errors._);
  const seen = new Set(mails());
  r = await (await api.post('/palaute.php', { headers: json, form: { csrf: t, lang: 'fi', name: 'Botti', msg: 'roskaa', website: 'spam.example' } })).json();
  rec('palaute', 'ansakenttä: näennäinen kiitos, ei postia', r.ok === true && mails().length === seen.size);
  await sleep(3200);                                               // aikaraja 3 s
  r = await (await api.post('/palaute.php', { headers: json, form: { csrf: t, lang: 'fi', name: '', email: 'ei-osoite', msg: 'x' } })).json();
  rec('palaute', 'virheelliset kentät hylätään', r.ok === false && r.errors && r.errors.name && r.errors.email && r.errors.msg);
  r = await (await api.post('/palaute.php', { headers: json, form: { csrf: t, lang: 'lv', name: 'Jānis\r\nBcc: evil@example.com', email: 'janis@example.lv', msg: 'Labdien! Ļoti jauka lapa.' } })).json();
  const newMail = mails().filter(f => !seen.has(f)).map(f => fs.readFileSync(path.join(mailDir, f), 'utf8')).join('\n');
  rec('palaute', 'kelvollinen viesti lähtee postiin', r.ok === true && newMail.includes('Labdien'), JSON.stringify(r));
  rec('palaute', 'otsakeinjektio ei onnistu', newMail !== '' && !/^Bcc:/mi.test(newMail));
  t = (await (await api.get('/palaute.php?token=1')).json()).token;
  await sleep(3200);
  r = await (await api.post('/palaute.php', { headers: json, form: { csrf: t, lang: 'fi', name: 'A', msg: 'toinen viesti heti perään' } })).json();
  rec('palaute', 'lähetysraja estää heti seuraavan viestin', r.ok === false && r.errors && r.errors._);
  await api.dispose();

  /* Vieraskirja */
  if (!haveDb) { rec('vieraskirja', 'ohitettu (ei testitietokantaa)', true); return; }
  api = await request.newContext({ baseURL: ORIGIN });
  const page = await (await api.get('/viesu_gramata.php?lang=fi')).text();
  const csrf = (page.match(/name="csrf" value="([^"]+)"/) || [])[1];
  rec('vieraskirja', 'lomakkeessa on CSRF-tunniste', !!csrf);
  const countRows = () => Number(dbQuery('SELECT COUNT(*) FROM viesu_gramata'));
  const n0 = countRows();
  await api.post('/viesu_gramata.php?lang=fi', { form: { csrf: 'väärä', nosaukums: 'X', zina: 'Väärällä tunnisteella' } });
  rec('vieraskirja', 'väärä CSRF-tunniste ei tallenna', countRows() === n0);
  const res = await api.post('/viesu_gramata.php?lang=fi', { form: { csrf, nosaukums: 'Testaaja', zina: 'Uusi testiviesti odottaa moderointia', saite: '', tips: '', website: '' } });
  rec('vieraskirja', 'lähetys tallentuu odottamaan (HTTP ' + res.status() + ')', res.status() === 200 && countRows() === n0 + 1);
  const after = await (await api.get('/viesu_gramata.php?lang=fi')).text();
  rec('vieraskirja', 'uusi viesti ei näy ennen hyväksyntää', !after.includes('Uusi testiviesti odottaa moderointia'));
  await api.dispose();
}

/* ---------- Pääohjelma ---------- */
(async () => {
  const t0 = Date.now();
  console.log('manalasdungeon.lv — testit\n');
  const { tmp, site } = makeSiteCopy();
  const mailDir = path.join(tmp, 'mail'); fs.mkdirSync(mailDir);
  const haveDb = setupDb(site);
  phpLint(site);
  const server = startServer(site, mailDir);
  let exitCode = 1;
  try {
    await waitForServer();
    const browser = await chromium.launch();
    const linkSet = new Set();
    const pages = CFG.pages.filter(p => (!ONLY.length || ONLY.includes(p.name)) && (haveDb || !p.db));
    for (const p of pages) {
      for (const lang of (p.langs === false ? ['lv'] : CFG.langs)) {
        for (const [vpName, vp] of Object.entries(CFG.viewports)) await checkPage(browser, p, lang, vpName, vp, linkSet);
      }
      process.stdout.write('.');
    }
    console.log('');
    await browser.close();
    const api = await request.newContext();
    await checkLinks(api, linkSet);
    await checkProtected(api);
    await api.dispose();
    if (!ONLY.length) await checkForms(mailDir, haveDb);

    /* Raportti */
    let lastGroup = '';
    for (const r of results) {
      if (r.group !== lastGroup) { console.log('\n' + r.group.toUpperCase()); lastGroup = r.group; }
      console.log(`  ${r.ok ? '✓' : '✗'} ${r.name}${!r.ok && r.detail ? '\n      → ' + r.detail : ''}`);
    }
    const fails = results.filter(r => !r.ok).length;
    console.log(`\n${results.length - fails}/${results.length} läpi · ${((Date.now() - t0) / 1000).toFixed(0)} s` +
                (UPDATE ? ' · pohjakuvat päivitetty' : ''));
    fs.mkdirSync(OUT_DIR, { recursive: true });
    fs.writeFileSync(path.join(OUT_DIR, 'report.json'), JSON.stringify(results, null, 2));
    exitCode = fails ? 1 : 0;
  } catch (e) {
    console.error('\nTestiajo keskeytyi: ' + e.stack);
  } finally {
    server.kill();
    fs.rmSync(tmp, { recursive: true, force: true });
    process.exit(exitCode);
  }
})();
