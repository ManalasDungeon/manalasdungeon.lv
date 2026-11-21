# Testit — manalasdungeon.lv

Automaattiset testit, jotka ajetaan **sivuston kopiota vastaan** väliaikaiskansiossa. Oikeisiin tiedostoihin
ei kosketa, eikä oikeaa tietokantaa käytetä.

## Mitä testataan

| Ryhmä | Tarkistukset |
|---|---|
| PHP | `php -l` jokaiselle PHP-tiedostolle |
| Sivut | Jokainen sivu × 3 kieltä × tietokone (1366 px) ja puhelin (390 px): HTTP 200, ei PHP-varoituksia, ei JS-virheitä, ei epäonnistuneita pyyntöjä, `<html lang>`, yksi `<h1>`, canonical ja hreflang (lv, en, fi, x-default), ei vaakasuuntaista ylivuotoa, kuvat latautuvat ja niillä on `alt`, ei CSP-rikkeitä |
| Sisältö | Julkaistut merkinnät näkyvät, luonnokset ja moderoimattomat viestit eivät; XSS-yritykset näkyvät suojattuna tekstinä |
| Linkit | Kaikki sivujen sisäiset linkit vastaavat alle 400 |
| Ylläpito | Ylläpitosivut ohjaavat passkey-kirjautumiseen |
| Palaute | Väärä CSRF, ansakenttä, virheelliset kentät, kelvollinen viesti (posti tallennetaan tiedostoon), otsakeinjektio, lähetysraja |
| Vieraskirja | CSRF, viesti tallentuu odottamaan, ei näy ennen hyväksyntää |
| Kuvavertailu | Sivun yläosa (3200 px) verrataan pohjakuvaan; yli 0,3 %:n ero → virhe ja erokuva `output/diff/`-kansioon |

## Asennus (kerran)

```bash
cd tests
npm install
npx playwright install chromium
# Testitietokanta (MySQL / MariaDB):
mysql -u root -e "CREATE DATABASE manalas_test CHARACTER SET utf8mb4;
  CREATE USER 'manalas_test'@'localhost' IDENTIFIED BY 'manalas_test';
  GRANT ALL ON manalas_test.* TO 'manalas_test'@'localhost';"
```

Ilman tietokantaa testit toimivat silti: tietokantaa tarvitsevat sivut ohitetaan.

## Ajo

```bash
npm test                 # kaikki testit
npm run test:quick       # ilman kuvavertailua
npm run test:update      # tarkoituksellisen ulkoasumuutoksen jälkeen: uudet pohjakuvat
node run.js --only=uutiset,linkit
```

Ensimmäinen ajo luo pohjakuvat. Ne ovat konekohtaisia (fontit ja renderöinti vaihtelevat), joten ne eivät
kuulu versionhallintaan.

## Ympäristömuuttujat

| Muuttuja | Tarkoitus |
|---|---|
| `TEST_DB_HOST`, `TEST_DB_NAME`, `TEST_DB_USER`, `TEST_DB_PASS` | Testitietokanta (oletus `manalas_test`) |
| `TEST_BASELINE` | Pohjakuvien kansio (oletus `tests/baseline`) |
| `TEST_OVERLAY` | Kansio, jonka tiedostot kopioidaan sivuston kopion päälle (esim. palvelimella olevat kuvat, joita ei ole repossa) |

## Uusi sivu

Lisää rivi tiedostoon `pages.js`, esimerkiksi:

```js
{ name: 'uusi-sivu', path: '/uusi.php' },
```

## Rakenne

```
tests/
├── run.js               testiajo
├── pages.js             testattavat sivut ja asetukset
├── fixtures/
│   ├── schema.sql       testitietokannan taulut
│   ├── seed.sql         kiinteä testidata (myös XSS-yritykset ja luonnokset)
│   ├── db_config.test.php
│   ├── passkey_config.test.php  passkeyn testiasetukset (oikea config.php ei ole mukana)
│   ├── overlay/         korvikkeet vain palvelimella oleville tiedostoille (GitHub Actions)
│   ├── router.php       jäljittelee .htaccess-sääntöjä PHP:n kehityspalvelimessa
│   └── sendmail.sh      tallentaa postin tiedostoon
├── baseline/            pohjakuvat (ei versionhallinnassa)
└── output/              raportti ja erokuvat (ei versionhallinnassa)
```

## GitHub Actions

`.github/workflows/testit.yml` ajaa testit automaattisesti jokaisen päivityksen (push) ja pull requestin yhteydessä:
oma MariaDB-palvelu, PHP 8.4, Node 24 ja Chromium. Kuvavertailu jätetään siellä pois (`--no-visual`), koska fontit
piirtyvät eri koneilla hieman eri tavoin; kuvavertailu ajetaan paikallisesti omilla pohjakuvilla.
Jos testi epäonnistuu, raportti tallentuu ajon liitteeksi (Artifacts → testitulokset).

Palvelimella on tiedostoja, joita ei ole versionhallinnassa (esim. `main.js`, `app.js`, `date_time.js` ja muutama
kuva). `fixtures/overlay/` sisältää niille pienet korvikkeet, ja `TEST_OVERLAY` kopioi ne testikopion päälle.

## Saavutettavuus

Jokainen sivu tarkistetaan axe-corella (WCAG 2.1 A ja AA) oletuskielellä tietokoneen kokoisena. Vakavat ja
kriittiset löydökset kaatavat testin. Ohitus tarvittaessa: `node run.js --no-a11y`.
