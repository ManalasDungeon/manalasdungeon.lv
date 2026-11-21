# Manalas Dungeon — Ziņas & Ceļojums

Tekninen dokumentaatio kahdesta blogityyppisestä sivusta: **Ziņas** (uutiset,
`blogi.php`) ja **Ceļojums** (matkablogi, `celojums.php`). Molemmat jakavat saman
tietokantataulun ja saman hallintanäkymän; ne eroavat toisistaan vain
`kategorija`-sarakkeen arvolla.

Päivitetty: 5.11.2026 (salasanakirjautuminen korvattu passkeyllä)

---

## 1. Yleiskuva

| | Ziņas (uutiset) | Ceļojums (matkablogi) |
|---|---|---|
| Julkinen sivu | `blogi.php` | `celojums.php` |
| URL | manalasdungeon.lv/blogi.php | manalasdungeon.lv/celojums.php |
| Kategoria kannassa | `kategorija = 'zina'` | `kategorija = 'celojums'` |
| Tyylit | `blogi.css` | `celojums.css` |
| Skripti | `blogi.js` (erillinen) | inline `celojums.php`:ssä |
| Taustakuva | `images/blogi_bg.jpg` | `images/celojums_bg.jpg` |

Molemmat ovat **lukusivuja**: ne vain näyttävät kannassa olevat merkinnät.
Sisällön lisääminen, muokkaus ja piilotus tapahtuu yhteisessä
hallintanäkymässä **`blogi_admin.php`**.

Kieliä on kolme: **lv (oletus), en, fi**. Kieli valitaan osoitteen
`?lang=`-parametrilla; ilman sitä käytetään latviaa.

---

## 2. Yhteinen arkkitehtuuri

### 2.1 Tietokanta — `zinas`-taulu

Molemmat sivut lukevat samasta taulusta. Taulu luotiin alun perin uutisille
(`blogi_taulu.sql`):

```sql
CREATE TABLE IF NOT EXISTS zinas (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    virsraksts VARCHAR(160) NOT NULL,   -- otsikko
    teksts     TEXT         NOT NULL,   -- leipäteksti (sis. [kuva:...]-lyhenteet)
    izveidots  DATETIME     NOT NULL,   -- julkaisun/näkymän aikaleima
    publicets  TINYINT(1)   NOT NULL DEFAULT 1,  -- 1 = julkinen, 0 = piilossa
    PRIMARY KEY (id),
    INDEX idx_izveidots (izveidots)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Matkablogia varten lisättiin **kategoria-sarake** (`celojums_taulu.sql`):

```sql
ALTER TABLE zinas
  ADD COLUMN kategorija VARCHAR(20) NOT NULL DEFAULT 'zina' AFTER teksts,
  ADD INDEX idx_kategorija (kategorija);
```

- Vanhat rivit saivat automaattisesti arvon `'zina'`, joten ne pysyivät
  uutissivulla.
- Matkamerkinnät tallennetaan arvolla `'celojums'`.
- **Sama taulu, sama hallinta — kategoria ratkaisee, kummalla sivulla rivi näkyy.**

Tietokanta: `manalasd_Viesugramata` (sama kuin vieraskirjalla). Yhteys avataan
jaetusta **`db_config.php`**-tiedostosta funktiolla `db()`, joka palauttaa
PDO-olion. Kaikki kyselyt käyttävät valmisteltuja lauseita (prepared statements).

### 2.2 Julkisen sivun kysely

Kumpikin sivu hakee omat rivinsä kategorian mukaan, uusin ylinnä:

```php
// blogi.php  (uutiset)
SELECT virsraksts, teksts, izveidots
FROM zinas WHERE publicets = 1 AND kategorija = 'zina'
ORDER BY izveidots DESC, id DESC LIMIT 100

// celojums.php (matkablogi)
SELECT virsraksts, teksts, izveidots
FROM zinas WHERE publicets = 1 AND kategorija = 'celojums'
ORDER BY izveidots DESC, id DESC LIMIT 200
```

Vain `publicets = 1` -rivit näkyvät; piilotetut (0) jäävät pois.

### 2.3 Tekstin renderöinti — `render_teksts()`

Molemmilla sivuilla on sama funktio, joka muuttaa kannan raakatekstin
turvalliseksi HTML:ksi:

1. **`htmlspecialchars(...)`** — suojaa kaiken HTML:n (XSS-suojaus).
2. **`nl2br(...)`** — rivinvaihdot `<br>`-tageiksi.
3. **URL-automaattilinkitys** — paljaat `http(s)://`-osoitteet muuttuvat
   klikattaviksi linkeiksi (`target="_blank" rel="noopener"`).
4. **Kuvalyhenne `[kuva:tiedosto.jpg]`** → `<figure class="…-fig"><img
   src="blogi_kuvat/tiedosto.jpg" loading="lazy"></figure>`. `..`-polut estetään.
5. Siivotaan ylimääräiset rivinvaihdot kuvan ympäriltä.

Luokkanimi eroaa: uutisilla `zina-fig`, matkablogilla `cel-fig` (tärkeä
lightboxin kannalta, ks. kohta 5).

### 2.4 Hallinta — `blogi_admin.php`

Yhteinen, passkey-suojattu hallintanäkymä molemmille.

**Kirjautuminen ja turva**
- Kirjautuminen tapahtuu **passkeyllä (WebAuthn / FIDO2)** — ei salasanaa.
  Tiedoston alussa oleva rivi `require __DIR__ . '/passkey/guard.php';`
  päästää läpi vain passkeyllä kirjautuneen istunnon ja ohjaa muut
  kirjautumissivulle (ks. kohta 2.5).
- **CSRF-token** (`$_SESSION['bcsrf']`) tarkistetaan jokaisessa toiminnossa
  `hash_equals`-vertailulla. Myös uloskirjautumislinkki (`?logout=1&t=…`)
  vaatii tokenin, joten toinen sivusto ei voi kirjata ylläpitäjää ulos.
- **Aikaleima** tarkistetaan (`DateTime::createFromFormat`); virheellinen arvo
  korvataan nykyhetkellä, jottei tallennus kaadu MySQL:n strict-tilassa.
- Otsikon linkki **Ierīces** vie laitteiden hallintaan, **Iziet** kirjaa ulos.

**Toiminnot** (POST + `action`-kenttä):
- `add` — lisää uusi merkintä
- `update` — muokkaa olemassa olevaa
- `delete` — poista
- `toggle` — vaihda julkinen/piilossa (`publicets = 1 - publicets`)
- `upload_img` — lataa kuva
- `delete_img` — poista kuva

**Merkinnän kentät**: otsikko (`virsraksts`), teksti (`teksts`), aikaleima
(`izveidots`), julkisuus (`publicets`) ja **kategoria** (pudotusvalikko:
`zina` tai `celojums`). Kategoria ratkaisee, kummalla sivulla merkintä näkyy.

**Kuvien lataus** (`upload_img`):
- Sallitut tyypit: **JPG, PNG, WEBP, GIF** (tarkistus `getimagesize()` +
  MIME-whitelist, ei pelkkä tiedostopääte).
- Maksimikoko **6 MB**.
- Tiedostonimi siivotaan ja siihen lisätään satunnainen pääte
  (`nimi-ab12cd.jpg`) päällekkäisyyksien estämiseksi.
- Kuvat tallentuvat **`blogi_kuvat/`**-kansioon. Kansioon kirjoitetaan
  automaattisesti `.htaccess`, joka **estää PHP:n ajon** siellä (kovennus:
  ettei ladattua tiedostoa voi ajaa skriptinä).
- Latauksen jälkeen admin saa valmiin lyhenteen: `[kuva:nimi.jpg]`, jonka
  liittää tekstiin haluamaansa kohtaan.
- Kuvalistassa näytetään vain turvallisesti nimetyt tiedostot
  (`A–Z a–z 0–9 _ - .`), joten esim. FTP:llä tuotu erikoisnimi ei voi ajaa
  koodia kopiointinapin kautta (XSS-kovennus).

### 2.5 Passkey-kirjautuminen — `passkey/`

Molemmat admin-sivut (`blogi_admin.php`, `viesu_admin.php`) on suojattu
yhteisellä passkey-moduulilla. Passkey on laitteelle tallennettu
kryptografinen avain, joka avataan sormenjäljellä, kasvoilla, laitteen
PIN-koodilla tai turva-avaimella (Windows Hello, Touch ID, iPhone, Android,
YubiKey).

**Toimintaperiaate**
1. Admin-sivun alussa `passkey/guard.php` tarkistaa istunnon.
   Kirjautumaton ohjataan `passkey/login.php?next=…`-sivulle.
2. Kirjautuessa palvelin lähettää kertakäyttöisen haasteen; laite
   allekirjoittaa sen yksityisellä avaimellaan käyttäjän tunnistuksen
   jälkeen.
3. Palvelin tarkistaa allekirjoituksen tallennetulla julkisella avaimella
   ja palauttaa käyttäjän alkuperäiselle admin-sivulle.

**Turvaominaisuudet**
- Yksityinen avain ei koskaan poistu laitteelta; palvelimella on vain
  julkinen avain → vuotava tietokanta ei paljasta mitään käyttökelpoista.
- Avain on sidottu verkkotunnukseen (`rp_id = manalasdungeon.lv`) ja
  alkuperään → **kalastelusivu ei voi käyttää sitä**.
- Käyttäjän tunnistus laitteella (PIN/biometria) vaaditaan aina.
- Allekirjoituslaskuri paljastaa kopioidun avaimen.
- Istunto vanhenee 30 min toimettomuuden jälkeen, enintään 8 h.
- Session-ID uusitaan kirjautuessa ja uloskirjautuessa.

**Laitteet**
- Hallinta: **`/passkey/manage.php`** — laitelista, uuden laitteen lisäys,
  poisto. Viimeistä laitetta ei voi poistaa (lukitusvarmistus).
- Puhelimen voi lisätä tietokoneelta valitsemalla selaimen ikkunasta
  "Käytä puhelinta tai tablettia" (QR-koodi).
- Ensimmäinen laite rekisteröidään `config.php`:n **asennusavaimella**
  (`setup_token`). Se toimii vain, kun yhtään laitetta ei ole tallennettu.
- **Palautus**, jos kaikki laitteet katoavat: poista rivit taulusta
  `admin_passkeys` phpMyAdminissa → asennusavain toimii taas.

**Tietokanta**: taulu `admin_passkeys` samassa kannassa kuin `zinas`
(luonti: `passkey/schema.sql`). Sarakkeet: julkinen avain (PEM),
avaintunnisteen SHA-256-tiiviste, allekirjoituslaskuri, laitteen nimi,
luonti- ja käyttöaika.

**Uuden admin-sivun suojaaminen** — lisää tiedoston alkuun:
```php
require __DIR__ . '/passkey/guard.php';
require __DIR__ . '/db_config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
```

---

## 3. Ziņas — `blogi.php`

Julkinen uutissivu ("Manas ziņas").

### 3.1 Rakenne
- Paluulinkki etusivulle + kielivalitsin (lv/en/fi, säilyttää kielen).
- Hero: otsikko + alaotsikko (käännökset `$TT`-taulukossa).
- Uutiskortit (`.zina`), uusin ylinnä. Kortit liukuvat esiin vieritettäessä
  (reveal-animaatio).
- Alhaalla kaksi nappia: **← Etusivu** ja **Ceļojums pa Latviju →**
  (linkki matkablogiin, lisätty koska etusivun esittelyssä mainitaan
  "matkamerkinnät"). Napit säilyttävät valitun kielen.
- Footer.

### 3.2 Taustakuva ja iPad-korjaus
Taustakuva on omassa **kiinteässä `body::before`-kerroksessaan** (ei
`background-attachment: fixed`), mikä estää iOS/iPad-bugin, jossa kiinteä
tausta skaalautuu dokumentin korkuiseksi ja sumentuu.

Taustan versiointi (cache-busting) on sidottu kuvan muokkausaikaan:
`index.php`-tyyliin `blogi.php` asettaa `<body style="--blogi-bg:url(images/blogi_bg.jpg?v=…)">`
ja `blogi.css` käyttää `var(--blogi-bg, …)`. Kun kuva vaihdetaan, uusi
versio latautuu automaattisesti kaikille.

### 3.3 `blogi.js` — kolme tehtävää
1. **Uutuusmerkin tila** — tallentaa viimeksi nähdyn ziņan aikaleiman
   selaimen `localStorage`-muistiin avaimella `zina_seen_ts` (luetaan
   `body[data-zina-ts]`-attribuutista). Tämä liittyy etusivun merkkiin, ks. 3.4.
2. **Reveal-animaatio** — `.reveal`-elementit tulevat esiin
   IntersectionObserverilla.
3. **Lightbox** — uutiskuvan (`.zina-fig img`) klikkaus avaa sen täyskuvana.
   Tyylit annetaan JS:ssä ja klikkaus delegoidaan dokumenttiin, joten se toimii
   myös lazy-load-kuville. Sulku: tausta, ×-painike tai Esc.

### 3.4 Uutuusmerkki etusivulla (kävijäkohtainen)
Etusivun (`index.php`) "Ziņas/Uutiset"-linkissä on kultainen päiväysmerkki,
joka näkyy vain, jos kävijä ei ole vielä avannut uusinta ziņaa:

1. `index.php` hakee kannasta uusimman julkaistun `zina`-rivin aikaleiman
   (`UNIX_TIMESTAMP(MAX(izveidots))`) ja laittaa sen linkkiin
   `data-zina-ts`-attribuuttina + näyttää päiväyksen merkkinä.
2. Etusivun skripti vertaa tätä `localStorage`-arvoon `zina_seen_ts`. Jos
   uusin on tuoreempi kuin nähty → merkki näkyy.
3. Kun kävijä klikkaa linkkiä (tai avaa `blogi.php`:n), `zina_seen_ts`
   päivitetään → merkki katoaa, kunnes uusi ziņa lisätään.

Tämä toimii yli www/ei-www-rajan, koska merkintä tallennetaan sekä klikatessa
etusivulla että blogisivulla.

---

## 4. Ceļojums — `celojums.php`

Julkinen matkablogi ("Ceļojums pa Latviju"). Rakenteeltaan lähes identtinen
Ziņas-sivun kanssa, mutta omalla ilmeellä ja kiinteillä matkatiedoilla.

### 4.1 Erot uutissivuun
- **Kiinteät matkatiedot** koodissa: `$DATES = '17.–24.10.2026'` ja
  `$REGION = 'Limbažu novads · Cēsu novads'`. Nämä näkyvät herossa.
- **Novadien vaakunat** (Limbaži, Cēsi) heron alla linkkeinä kuntien sivuille
  — ne korvaavat uutissivun koristenauhan.
- Alhaalla nappi vieraskirjaan.
- Kysely suodattaa `kategorija = 'celojums'`.
- Kuvat käyttävät luokkaa **`cel-fig`** (vrt. `zina-fig`).

### 4.2 Lightbox (inline)
Matkablogin lightbox on **suoraan `celojums.php`:n lopussa inline-skriptinä**,
ja se **tyylittää overlayn itse JS:ssä**. Tämä on tietoinen valinta:
aiemmin lightbox oli erillisessä `celojums.js`:ssä, joka luotti
`celojums.css`:n `.cel-lightbox`-sääntöihin. Koska noita sääntöjä ei ollut
CSS:ssä, kuva ei suurentunut. Inline + JS-tyylit poistaa riippuvuuden sekä
`celojums.js`:stä että `celojums.css`:stä → toimii varmasti.

Toiminta: klikkaa `.cel-fig img` → kuva täyskuvana tummalla taustalla.
Sulku: tausta, kuva, ×-painike tai Esc. Klikkaus on delegoitu dokumenttiin.

> Vanha `celojums.js` on käyttämätön — voi jäädä palvelimelle tai poistaa.

### 4.3 Taustakuva
Sama tekniikka kuin uutissivulla: taustakuva (`images/celojums_bg.jpg`)
omassa `body::before`-kerroksessaan → ei iPad-zoombugia.

---

## 5. Kuvat tekstin seassa

Molemmilla sivuilla sama järjestelmä:
1. Lataa kuva adminissa → se tallentuu `blogi_kuvat/`-kansioon ja saat
   lyhenteen `[kuva:nimi.jpg]`.
2. Liitä lyhenne tekstiin kohtaan, jossa haluat kuvan näkyvän.
3. `render_teksts()` muuttaa sen `<figure>`-kuvaksi julkisella sivulla.
4. Klikkaamalla kuva suurenee (lightbox).

**Kuvien esikäsittelyresepti** (kun kuvat valmistellaan erikseen ennen
latausta): skaalaus max ~1800 px leveäksi, EXIF pois, JPEG laatu 82
progressiivisena, tarvittaessa hienovarainen kirkkaus/kontrasti/lämpö.

---

## 6. SEO ja sosiaalinen media

Molemmilla sivuilla `<head>`:ssä:
- **Kolmikielinen** `hreflang` (lv/en/fi) + `x-default`.
- **Canonical**-osoite valitulla kielellä.
- **Open Graph** + **Twitter Card** (otsikko, kuvaus, OG-kuva =
  taustakuva, locale).
- `robots: index, follow`.

Sivut ovat myös juuren `sitemap.xml`:ssä (lv/en/fi-variantit).

---

## 7. Kävijäseuranta (Google Analytics)

Molemmilla sivuilla on `<head>`:ssä GA4-snippet (mittaustunnus
**`G-0Q1HVYWR72`**), heti `viewport`-metan jälkeen. Sama tunnus kuin muillakin
sivuilla, joten data kertyy yhteen tiliin (erottele sivupolun mukaan).

Huom: osa kävijöistä estää GA:n mainostenestäjällä
(`net::ERR_BLOCKED_BY_CLIENT` konsolissa) — tämä on normaalia eikä riko sivua.

---

## 8. Uuden merkinnän lisääminen (työnkulku)

1. Avaa **`blogi_admin.php`** ja kirjaudu passkeyllä (sormenjälki / PIN).
2. (Jos kuvia:) lataa kuvat → kopioi `[kuva:nimi.jpg]`-lyhenteet.
3. Täytä otsikko ja teksti. Liitä kuvalyhenteet tekstiin.
4. Valitse **kategoria**:
   - `zina` → näkyy **Ziņas**-sivulla (`blogi.php`)
   - `celojums` → näkyy **matkablogissa** (`celojums.php`)
5. Aseta aikaleima ja "julkinen" päälle → tallenna.
6. Merkintä ilmestyy heti oikealle sivulle (uusin ylinnä). Uutisissa myös
   etusivun uutuusmerkki syttyy.

Vaihtoehtoisesti merkinnän voi lisätä suoraan `zinas`-tauluun SQL:llä
(INSERT), kunhan `kategorija` on `zina` tai `celojums`.

---

## 9. Tiedostoluettelo

**Jaettu**
- `db_config.php` — tietokantayhteys (`db()` → PDO)
- `blogi_admin.php` + `blogi_admin.css` — hallinta (molemmat kategoriat)
- `blogi_kuvat/` — ladatut kuvat (+ suojaava `.htaccess`)
- `blogi_taulu.sql`, `celojums_taulu.sql` — taulun luonti ja kategoria-sarake

**Passkey-kirjautuminen** (`passkey/`)
- `guard.php` — portti admin-sivujen alkuun
- `login.php` — kirjautumissivu
- `manage.php` — laitteiden hallinta ja ensimmäisen laitteen rekisteröinti
- `api.php` — JSON-rajapinta (haasteet, tarkistukset, poisto, uloskirjautuminen)
- `lib.php` — WebAuthn-tarkistus (CBOR, COSE→PEM, ES256/RS256), istunto
- `config.php` — asetukset (rp_id, asennusavain, aikakatkaisut)
- `passkey.js`, `passkey.css` — selainpuoli ja teema
- `schema.sql` — taulun `admin_passkeys` luonti
- `.htaccess` — estää `config.php`:n, `lib.php`:n ja `schema.sql`:n suoran haun

**Ziņas**
- `blogi.php` — julkinen sivu
- `blogi.css` — tyylit
- `blogi.js` — seen-ts + reveal + lightbox
- `images/blogi_bg.jpg` — taustakuva

**Ceļojums**
- `celojums.php` — julkinen sivu (lightbox inline sisällä)
- `celojums.css` — tyylit
- `images/celojums_bg.jpg` — taustakuva
- `images/limbazi.png`, `images/cesis.png` — novadien vaakunat
- `celojums.js` — (vanha, käyttämätön)

---

## 10. Avainkohdat tiiviisti

- **Yksi taulu, kaksi sivua** — `kategorija` ('zina' / 'celojums') ratkaisee.
- **Yksi hallinta** (`blogi_admin.php`) molemmille, kategoriavalinnalla.
- **Turva**: passkey-kirjautuminen (WebAuthn/FIDO2, ei salasanaa),
  CSRF-tokenit, prepared statements, HTML-suojaus (`htmlspecialchars`),
  kuvakansion PHP-esto.
- **Kuvat**: `[kuva:tiedosto]`-lyhenne → `<figure>` → klikattava lightbox.
- **Lightbox**: uutisilla `blogi.js`, matkablogilla inline — molemmat
  tyylittävät overlayn JS:ssä (ei CSS-riippuvuutta).
- **Taustat** `body::before`-kerroksessa (iPad-turvallinen) + kuva-mtime
  cache-busting.
- **Kolmikielisyys** `?lang=`-parametrilla, lv oletuksena.
- **Etusivun uutuusmerkki** kytkeytyy uutisiin `localStorage`-aikaleiman
  (`zina_seen_ts`) kautta.
