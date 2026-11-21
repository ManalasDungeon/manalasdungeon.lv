<?php
require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, kieli, <head>-palat
/**
 * Julkinen projektiesittely (portfolio) — jaettavaksi esim. LinkedInissä.
 * Kolmikielinen: ?lang=en / ?lang=fi, oletus lv.
 * Ei admin-polkuja eikä sisäisiä yksityiskohtia (ne ovat dokumentaatio.html:ssä).
 */

$supported = ['lv', 'en', 'fi'];
$default   = 'lv';
$lang = $_GET['lang'] ?? $default;
if (!in_array($lang, $supported, true)) { $lang = $default; }
$base = 'https://manalasdungeon.lv/';
$flag = ['lv' => '🇱🇻', 'en' => '🇬🇧', 'fi' => '🇫🇮'];
$locale = ['lv' => 'lv_LV', 'en' => 'en_US', 'fi' => 'fi_FI'];

/* Tekniikat (samat kaikilla kielillä) */
$STACK = ['PHP 8.4', 'MySQL / MariaDB', 'PDO', 'JavaScript', 'HTML5', 'CSS3', 'Bootstrap 5',
          'WebAuthn / FIDO2', 'OpenSSL', 'LiteSpeed · .htaccess', 'Google Analytics 4', 'Playwright'];

$C = [
  'lv' => [
    'title'    => 'Manalas Dungeon — projekta apraksts',
    'desc'     => 'Trīsvalodu tīmekļa vietne ar bezparoles administrēšanu (WebAuthn / FIDO2), izstrādāta ar PHP un MySQL bez satura pārvaldības sistēmas.',
    'eyebrow'  => 'Projekta apraksts',
    'sub'      => 'Trīsvalodu tīmekļa vietne ar bezparoles administrēšanu',
    'lead'     => 'Personīga vietne, ceļojumu emuārs, Vidzemes piekrastes ceļvedis un jogas lapa vienā — izstrādāts no nulles ar PHP un MySQL, bez satura pārvaldības sistēmas.',
    'cta_site' => 'Apskatīt vietni',
    'role'     => 'Programmatūras izstrādātājs',
    'stats'    => [
      ['3', 'valodas: latviešu, angļu, somu'],
      ['0', 'paroles administrēšanā'],
      ['PHP 8.4', 'un MySQL, bez ietvara'],
      ['FIDO2', 'passkey pieteikšanās'],
    ],
    's_features' => 'Kas ir vietnē',
    'features' => [
      ['Bezparoles administrēšana',
       'Administrēšanas lapās var ieiet tikai ar reģistrētu ierīci — ar pirksta nospiedumu, seju, ierīces PIN vai drošības atslēgu (WebAuthn / FIDO2). Servera puse ir uzrakstīta PHP bez ārējām bibliotēkām: CBOR dekodēšana, COSE atslēgu pārveide un ES256 / RS256 parakstu pārbaude.',
       ['WebAuthn', 'FIDO2', 'OpenSSL'], true],
      ['Trīsvalodu arhitektūra',
       'Katrai lapai ir latviešu, angļu un somu versija. Valodu nosaka adrese (?lang= vai tīras adreses /en/, /fi/), un meklētājiem tiek norādītas hreflang saites un vietnes karte.',
       ['hreflang', '.htaccess', 'sitemap'], false],
      ['Ziņas un ceļojumu emuārs',
       'Divas publiskas lapas lasa vienu datubāzes tabulu — kategorija nosaka, kur ieraksts parādās. Paša veidotā administrēšana pārvalda ierakstus, attēlu augšupielādi un īskodu attēlu ievietošanai tekstā.',
       ['PDO', 'CRUD', 'lightbox'], false],
      ['Viesu grāmata',
       'Ieraksti tiek publicēti pēc apstiprināšanas. Surogātpastu aptur slēpts lauks (honeypot) un laika ierobežojums vienai IP adresei, bet administrators saņem e-pasta paziņojumu.',
       ['moderācija', 'honeypot', 'rate limit'], false],
      ['Atsauksmju forma',
       'Sākumlapas poga „Komentēt“ atver uznirstošo logu (<dialog>), kura izmērs pielāgojas saturam. Ziņa nonāk tieši administratora e-pastā un netiek publicēta. Aizsardzība: CSRF žetons, slēpts lauks (honeypot), laika slazds, limits vienai IP adresei un visai vietnei, kā arī e-pasta galveņu injekciju novēršana. Darbojas arī bez JavaScript.',
       ['dialog', 'fetch', 'mail()'], false],
      ['Vitrupes pludmales galerija',
       'Ceļojumu vietne ar mainīgu galveno attēlu, mūra (masonry) galeriju un attēlu skatītāju, ko var pārslēgt ar bultiņām, tastatūru vai pārvelkot ar pirkstu.',
       ['Bootstrap 5', 'JSON-LD', 'responsīvs'], false],
      ['Vidzemes ceļvedis',
       'Saites uz ēdināšanas vietām, apskates objektiem, dabas takām un naktsmītnēm, sakārtotas pa novadiem un pilsētām, ar oficiāliem tūrisma avotiem.',
       ['strukturēti dati', 'i18n'], false],
      ['Jogas lapa',
       'Trīsvalodu lapa par jogas vēsturi, filozofiju, vinyasa un Core jogu. Galvenais skats mainās pēc ekrāna: telefonā foto ir visa fona attēls, datorā tas atrodas sānis un izplūst fonā ar CSS maskām. Attēli apstrādāti ar Python (Pillow): divtoņu krāsojums un līniju zīmējumi, pārveidoti par caurspīdīgiem PNG.',
       ['CSS mask', 'background-clip', 'Pillow'], false],
      ['Jaunumu paziņojums',
       'Sākumlapas poga rāda jaunākās ziņas datumu, līdz apmeklētājs to ir izlasījis. Stāvoklis tiek saglabāts pārlūkā (localStorage), bez sīkdatnēm.',
       ['localStorage', 'UX'], false],
      ['Privātums pēc noklusējuma',
       'Google Analytics ielādējas tikai pēc apmeklētāja piekrišanas — atteikties ir tikpat viegli kā piekrist. Trīsvalodu privātuma politikā ir slēdzis, ar kuru izvēli var mainīt jebkurā brīdī. Ziņām ir RSS plūsma, un neatrastām lapām — sava 404 lapa.',
       ['VDAR', 'piekrišana', 'RSS'], false],
    ],
    's_security' => 'Drošība',
    'security_lead' => 'Drošība ir projektēta jau no sākuma, balstoties uz OWASP Top 10 ieteikumiem.',
    'security' => [
      ['Pret pikšķerēšanu noturīga pieteikšanās', 'Passkey ir piesaistīts domēnam, un serverī netiek glabātas paroles.'],
      ['SQL injekciju novēršana', 'Visi datubāzes vaicājumi izmanto PDO sagatavotos vaicājumus (prepared statements).'],
      ['XSS aizsardzība', 'Visa izvade tiek aizsargāta ar htmlspecialchars; saites tekstā tiek veidotas tikai pēc aizsargāšanas.'],
      ['CSRF aizsardzība', 'Katrai administrēšanas darbībai — arī izrakstīšanai — nepieciešams vienreizējs žetons.'],
      ['E-pasta galveņu injekciju novēršana', 'Atsauksmju formā vārds un e-pasts tiek attīrīti no rindu pārnesumiem un kodēti (RFC 2047), tāpēc tajos nevar ievietot papildu galvenes.'],
      ['Tikai HTTPS', 'Visa satiksme tiek pāradresēta uz vienu kanonisko HTTPS adresi, un HSTS liek pārlūkam gadu izmantot tikai HTTPS.'],
      ['Content-Security-Policy', 'Pārlūks drīkst izpildīt tikai vietnes pašas skriptus un Google Analytics; iekšējiem skriptiem ir vienreizējs nonce.'],
      ['Droša attēlu augšupielāde', 'Faila tips tiek pārbaudīts pēc satura, ir izmēra ierobežojums, un augšupielādes mapē PHP izpilde ir aizliegta.'],
    ],
    's_stack'   => 'Tehnoloģijas',
    's_quality' => 'Kvalitāte un testēšana',
    'quality'   => [
      ['Automātiski testi', '38 vienībtesti passkey kriptogrāfijai, pārlūka testi ar virtuālu FIDO2 autentifikatoru un 87 lapu testi (Playwright): trīs valodas, dators un tālrunis, pikseļu salīdzināšana un pieejamība (axe, WCAG 2.1 AA).'],
      ['Responsīvs dizains', 'Pielāgojas tālruņiem, planšetēm un datoriem, ieskaitot iPad fona attēla tālummaiņas kļūdas labojumu.'],
      ['Ātrdarbība', 'Kešatmiņas atjaunināšana pēc faila laika zīmoga, gzip saspiešana un WebP attēli.'],
      ['Rezerves kopijas', 'Datubāze tiek automātiski saglabāta katru nedēļu; glabājas pēdējās astoņas kopijas.'],
      ['Meklētājprogrammām', 'hreflang, Open Graph, strukturēti dati (JSON-LD) un vietnes karte.'],
    ],
    's_live'    => 'Apskati darbībā',
    'live_lead' => 'Vietne ir publiski pieejama — visas lapas trīs valodās.',
    'l_home'    => 'Sākumlapa',
    'l_vitrupe' => 'Vitrupes Pludmale',
    'l_vidzeme' => 'Vidzemes ceļvedis',
    'l_joga'    => 'Jogas lapa',
    'l_arch'    => 'Arhitektūra',
    'contact'   => 'Kontakti',
  ],

  'en' => [
    'title'    => 'Manalas Dungeon — case study',
    'desc'     => 'A trilingual website with passwordless administration (WebAuthn / FIDO2), built with PHP and MySQL without a CMS.',
    'eyebrow'  => 'Case study',
    'sub'      => 'A trilingual website with passwordless administration',
    'lead'     => 'A personal site, travel blog, Vidzeme coast guide and yoga page in one — built from scratch with PHP and MySQL, without a CMS.',
    'cta_site' => 'Visit the site',
    'role'     => 'Software developer',
    'stats'    => [
      ['3', 'languages: Latvian, English, Finnish'],
      ['0', 'passwords in the admin'],
      ['PHP 8.4', 'and MySQL, no framework'],
      ['FIDO2', 'passkey sign-in'],
    ],
    's_features' => 'What’s on the site',
    'features' => [
      ['Passwordless administration',
       'The admin pages open only on a registered device — with a fingerprint, face, device PIN or a security key (WebAuthn / FIDO2). The server side is written in plain PHP without external libraries: CBOR decoding, COSE key conversion and ES256 / RS256 signature verification.',
       ['WebAuthn', 'FIDO2', 'OpenSSL'], true],
      ['Trilingual architecture',
       'Every page exists in Latvian, English and Finnish. The language comes from the URL (?lang= or clean /en/, /fi/ paths), with hreflang links and a sitemap for search engines.',
       ['hreflang', '.htaccess', 'sitemap'], false],
      ['News & travel blog',
       'Two public pages read from one database table — a category decides where an entry appears. A custom admin handles entries, image uploads and a shortcode for placing images in the text.',
       ['PDO', 'CRUD', 'lightbox'], false],
      ['Guestbook',
       'Messages are published after moderation. Spam is stopped by a hidden honeypot field and a per-IP time limit, and the admin receives an email notification.',
       ['moderation', 'honeypot', 'rate limit'], false],
      ['Feedback form',
       'The “Comment” button on the home page opens a pop-up (<dialog>) sized to its content. The message goes straight to the admin’s email and is never published. Protection: a CSRF token, a hidden honeypot field, a time trap, per-IP and site-wide limits, and email header injection prevention. It also works without JavaScript.',
       ['dialog', 'fetch', 'mail()'], false],
      ['Vitrupe beach gallery',
       'A travel site with a rotating hero image, a masonry gallery and an image viewer you can browse with arrows, the keyboard or a swipe.',
       ['Bootstrap 5', 'JSON-LD', 'responsive'], false],
      ['Vidzeme travel guide',
       'Links to places to eat, sights, nature trails and accommodation, organised by municipality and town, using official tourism sources.',
       ['structured data', 'i18n'], false],
      ['Yoga page',
       'A trilingual page on the history and philosophy of yoga, Vinyasa and Core yoga. The hero adapts to the screen: on phones the photo fills the background, on desktop it sits to the side and fades into the page with CSS masks. Images were processed with Python (Pillow): a duotone tint and line drawings turned into transparent PNGs.',
       ['CSS mask', 'background-clip', 'Pillow'], false],
      ['New-post badge',
       'The home page button shows the date of the latest news post until the visitor has read it. The state is kept in the browser (localStorage), without cookies.',
       ['localStorage', 'UX'], false],
      ['Privacy by default',
       'Google Analytics loads only after the visitor agrees — declining is as easy as accepting. The trilingual privacy policy has a switch to change the choice at any time. The news has an RSS feed, and missing pages get their own 404 page.',
       ['GDPR', 'consent', 'RSS'], false],
    ],
    's_security' => 'Security',
    'security_lead' => 'Security was designed in from the start, following the OWASP Top 10 guidance.',
    'security' => [
      ['Phishing-resistant sign-in', 'Passkeys are bound to the domain, and no passwords are stored on the server.'],
      ['SQL injection prevention', 'Every database query uses PDO prepared statements.'],
      ['XSS protection', 'All output is escaped with htmlspecialchars; links in text are created only after escaping.'],
      ['CSRF protection', 'Every admin action — including sign-out — requires a one-time token.'],
      ['Email header injection prevention', 'In the feedback form, the name and email are stripped of line breaks and encoded (RFC 2047), so no extra headers can be injected.'],
      ['HTTPS only', 'All traffic is redirected to a single canonical HTTPS address, and HSTS tells browsers to use only HTTPS for a year.'],
      ['Content-Security-Policy', 'The browser may run only the site’s own scripts and Google Analytics; inline scripts carry a one-time nonce.'],
      ['Safe image uploads', 'The file type is checked from its content, size is limited, and PHP execution is disabled in the upload folder.'],
    ],
    's_stack'   => 'Tech stack',
    's_quality' => 'Quality & testing',
    'quality'   => [
      ['Automated tests', '38 unit tests for the passkey cryptography, browser tests with a virtual FIDO2 authenticator and 87 page tests (Playwright): three languages, desktop and phone, pixel comparison and accessibility (axe, WCAG 2.1 AA).'],
      ['Responsive design', 'Works on phones, tablets and desktops, including a fix for the iPad background-zoom bug.'],
      ['Performance', 'Cache busting by file timestamp, gzip compression and WebP images.'],
      ['Backups', 'The database is backed up automatically every week; the latest eight copies are kept.'],
      ['SEO', 'hreflang, Open Graph, structured data (JSON-LD) and a sitemap.'],
    ],
    's_live'    => 'See it live',
    'live_lead' => 'The site is publicly available — every page in three languages.',
    'l_home'    => 'Home page',
    'l_vitrupe' => 'Vitrupe Beach',
    'l_vidzeme' => 'Vidzeme guide',
    'l_joga'    => 'Yoga page',
    'l_arch'    => 'Architecture',
    'contact'   => 'Contact',
  ],

  'fi' => [
    'title'    => 'Manalas Dungeon — projektiesittely',
    'desc'     => 'Kolmikielinen verkkosivusto ja salasanaton ylläpito (WebAuthn / FIDO2), toteutettu PHP:llä ja MySQL:llä ilman julkaisujärjestelmää.',
    'eyebrow'  => 'Projektiesittely',
    'sub'      => 'Kolmikielinen verkkosivusto ja salasanaton ylläpito',
    'lead'     => 'Henkilökohtainen sivusto, matkablogi, Vidzemen rannikon opas ja joogasivu yhdessä — rakennettu alusta asti PHP:llä ja MySQL:llä, ilman julkaisujärjestelmää.',
    'cta_site' => 'Katso sivusto',
    'role'     => 'Ohjelmistokehittäjä',
    'stats'    => [
      ['3', 'kieltä: latvia, englanti, suomi'],
      ['0', 'salasanaa ylläpidossa'],
      ['PHP 8.4', 'ja MySQL, ei sovelluskehystä'],
      ['FIDO2', 'passkey-kirjautuminen'],
    ],
    's_features' => 'Mitä sivustolla on',
    'features' => [
      ['Salasanaton ylläpito',
       'Ylläpitosivuille pääsee vain rekisteröidyllä laitteella — sormenjäljellä, kasvoilla, laitteen PIN-koodilla tai turva-avaimella (WebAuthn / FIDO2). Palvelinpuoli on kirjoitettu PHP:llä ilman ulkoisia kirjastoja: CBOR-dekoodaus, COSE-avainten muunnos sekä ES256 / RS256 -allekirjoitusten tarkistus.',
       ['WebAuthn', 'FIDO2', 'OpenSSL'], true],
      ['Kolmikielinen arkkitehtuuri',
       'Jokaisesta sivusta on latvian-, englannin- ja suomenkielinen versio. Kieli tulee osoitteesta (?lang= tai siistit /en/- ja /fi/-polut), ja hakukoneille on hreflang-linkit ja sivukartta.',
       ['hreflang', '.htaccess', 'sitemap'], false],
      ['Uutiset ja matkablogi',
       'Kaksi julkista sivua lukee samaa tietokantataulua — kategoria ratkaisee, missä merkintä näkyy. Oma ylläpito hoitaa merkinnät, kuvien latauksen ja lyhenteen kuvien sijoittamiseen tekstiin.',
       ['PDO', 'CRUD', 'lightbox'], false],
      ['Vieraskirja',
       'Viestit julkaistaan hyväksynnän jälkeen. Roskapostin pysäyttävät piilokenttä (honeypot) ja IP-kohtainen aikaraja, ja ylläpitäjä saa sähköposti-ilmoituksen.',
       ['moderointi', 'honeypot', 'rate limit'], false],
      ['Palautelomake',
       'Etusivun Kommentoi-nappi avaa ponnahdusikkunan (<dialog>), jonka koko mukautuu sisältöön. Viesti menee suoraan ylläpitäjän sähköpostiin, eikä sitä julkaista. Suojauksena CSRF-tunniste, piilokenttä (honeypot), aikaraja, IP-kohtainen ja koko sivuston lähetysraja sekä sähköpostin otsakeinjektion esto. Toimii myös ilman JavaScriptiä.',
       ['dialog', 'fetch', 'mail()'], false],
      ['Vitrupen rannan galleria',
       'Matkasivusto, jossa on vaihtuva pääkuva, masonry-galleria ja kuvakatselin, jota selataan nuolilla, näppäimistöllä tai pyyhkäisemällä.',
       ['Bootstrap 5', 'JSON-LD', 'responsiivinen'], false],
      ['Vidzemen matkailuopas',
       'Linkit ruokapaikkoihin, nähtävyyksiin, luontopolkuihin ja majoitukseen lääneittäin ja kaupungeittain, virallisista matkailulähteistä.',
       ['rakenteinen data', 'i18n'], false],
      ['Joogasivu',
       'Kolmikielinen sivu joogan historiasta, filosofiasta, vinyasasta ja Core-joogasta. Yläosa mukautuu näyttöön: puhelimessa kuva täyttää taustan, tietokoneella se on sivussa ja häivytetään taustaan CSS-maskeilla. Kuvat on käsitelty Pythonilla (Pillow): kaksisävyinen värjäys ja viivapiirrokset läpinäkyviksi PNG-kuviksi.',
       ['CSS-maskit', 'background-clip', 'Pillow'], false],
      ['Uutuusmerkki',
       'Etusivun painike näyttää uusimman uutisen päivämäärän, kunnes kävijä on lukenut sen. Tila säilyy selaimessa (localStorage) ilman evästeitä.',
       ['localStorage', 'UX'], false],
      ['Yksityisyys oletuksena',
       'Google Analytics latautuu vasta kävijän suostumuksella — kieltäytyminen on yhtä helppoa kuin hyväksyminen. Kolmikielisessä tietosuojaselosteessa on kytkin, jolla valintaa voi muuttaa milloin tahansa. Uutisilla on RSS-syöte, ja puuttuville sivuille on oma 404-sivu.',
       ['GDPR', 'suostumus', 'RSS'], false],
    ],
    's_security' => 'Tietoturva',
    'security_lead' => 'Tietoturva on suunniteltu alusta asti OWASP Top 10 -suositusten pohjalta.',
    'security' => [
      ['Kalastelulta suojattu kirjautuminen', 'Passkey on sidottu verkkotunnukseen, eikä palvelimella säilytetä salasanoja.'],
      ['SQL-injektioiden esto', 'Kaikki tietokantakyselyt käyttävät PDO:n prepared statementeja.'],
      ['XSS-suojaus', 'Kaikki tuloste suojataan htmlspecialchars-funktiolla, ja tekstin linkit muodostetaan vasta suojauksen jälkeen.'],
      ['CSRF-suojaus', 'Jokainen ylläpitotoiminto — myös uloskirjautuminen — vaatii kertakäyttöisen tunnisteen.'],
      ['Otsakeinjektion esto', 'Palautelomakkeessa nimestä ja sähköpostista poistetaan rivinvaihdot ja ne koodataan (RFC 2047), joten niihin ei voi upottaa ylimääräisiä otsakkeita.'],
      ['Vain HTTPS', 'Kaikki liikenne ohjataan yhteen kanoniseen HTTPS-osoitteeseen, ja HSTS käskee selainta käyttämään vuoden ajan vain HTTPS:ää.'],
      ['Content-Security-Policy', 'Selain saa ajaa vain sivuston omia skriptejä ja Google Analyticsia; sivun sisäisillä skripteillä on kertakäyttöinen nonce.'],
      ['Turvallinen kuvien lataus', 'Tiedostotyyppi tarkistetaan sisällöstä, koko on rajattu, ja PHP:n ajo on estetty latauskansiossa.'],
    ],
    's_stack'   => 'Tekniikat',
    's_quality' => 'Laatu ja testaus',
    'quality'   => [
      ['Automaattiset testit', '38 yksikkötestiä passkey-kryptografialle, selaintestit virtuaalisella FIDO2-autentikaattorilla ja 87 sivutestiä (Playwright): kolme kieltä, tietokone ja puhelin, pikselivertailu ja saavutettavuus (axe, WCAG 2.1 AA).'],
      ['Responsiivinen ulkoasu', 'Toimii puhelimilla, tableteilla ja tietokoneilla, mukana korjaus iPadin taustakuvan zoomausbugiin.'],
      ['Suorituskyky', 'Välimuistin ohitus tiedoston aikaleimalla, gzip-pakkaus ja WebP-kuvat.'],
      ['Varmuuskopiot', 'Tietokanta varmuuskopioidaan automaattisesti viikoittain; kahdeksan uusinta kopiota säilytetään.'],
      ['Hakukoneoptimointi', 'hreflang, Open Graph, rakenteinen data (JSON-LD) ja sivukartta.'],
    ],
    's_live'    => 'Katso toiminnassa',
    'live_lead' => 'Sivusto on julkisesti saatavilla — kaikki sivut kolmella kielellä.',
    'l_home'    => 'Etusivu',
    'l_vitrupe' => 'Vitrupen ranta',
    'l_vidzeme' => 'Vidzeme-opas',
    'l_joga'    => 'Joogasivu',
    'l_arch'    => 'Arkkitehtuuri',
    'contact'   => 'Yhteystiedot',
  ],
];
$T = $C[$lang];

function pf_url(string $lg): string { return page_url('portfolio.php', $lg); }
$q          = $lang === $default ? '' : '?lang=' . $lang;
$homeUrl    = $base . $q;
$vitrupeUrl = $base . 'vitrupe/' . ($lang === $default ? '' : $lang . '/');
$vidzemeUrl = $base . 'vidzeme.php' . $q;
$jogaUrl    = $base . 'joga.php' . $q;
$archUrl    = $base . 'arhitektura.php' . $q;
$ogImage    = $base . 'images/portfolio_linkedin.jpg';   // baseline-JPEG, uusi nimi ohittaa LinkedInin välimuistin

$ld = [
    '@context'    => 'https://schema.org',
    '@type'       => 'WebPage',
    'name'        => $T['title'],
    'description' => $T['desc'],
    'url'         => pf_url($lang),
    'inLanguage'  => $lang,
    'author'      => [
        '@type'    => 'Person',
        'name'     => 'Marko Nakari',
        'jobTitle' => $T['role'],
        'url'      => $base,
        'sameAs'   => ['https://github.com/ManalasDungeon', 'https://www.linkedin.com/in/manalainen'],
    ],
];

/* Pienet ikonit */
$ICON_CHECK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>';
$ICON_KEY   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="7.5" cy="15.5" r="4.5"/><path d="m10.7 12.3 9.8-9.8"/><path d="m15.5 7.5 3 3"/><path d="m18 5 2.5 2.5"/></svg>';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?= head_analytics('') ?>
<title><?= h($T['title']) ?></title>
<meta name="description" content="<?= h($T['desc']) ?>">
<meta name="author" content="Marko Nakari">
<meta name="robots" content="index, follow">
<meta name="theme-color" content="#9E1B32">
<?= head_alternates('pf_url', $lang, '') ?>
<!-- Open Graph (LinkedIn, Facebook) -->
<meta property="og:type" content="article">
<meta property="og:site_name" content="Manalas Dungeon">
<meta name="title" property="og:title" content="<?= h($T['title']) ?>">
<meta property="og:description" content="<?= h($T['desc']) ?>">
<meta property="og:url" content="<?= h(pf_url($lang)) ?>">
<meta name="image" property="og:image" content="<?= h($ogImage) ?>">
<meta property="og:image:secure_url" content="<?= h($ogImage) ?>">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="627">
<meta property="og:image:alt" content="<?= h($T['title']) ?>">
<meta property="og:locale" content="<?= h($locale[$lang]) ?>">
<meta name="publish_date" property="og:publish_date" content="2026-11-05T18:00:00+02:00">
<meta property="article:published_time" content="2026-11-05T18:00:00+02:00">
<meta property="article:modified_time" content="<?= h(date('c', @filemtime(__FILE__) ?: time())) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= h($T['title']) ?>">
<meta name="twitter:description" content="<?= h($T['desc']) ?>">
<meta name="twitter:image" content="<?= h($ogImage) ?>">

<?= head_fonts('playfair-display-latin-wght-normal.woff2', '') ?><link rel="stylesheet" href="<?= h(asset('portfolio.css')) ?>">
<script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
</head>
<body class="pf-body">

<header class="pf-hero">
  <div class="pf-top">
    <a class="pf-brand" href="<?= h($homeUrl) ?>">manalasdungeon.lv</a>
    <nav class="pf-lang" aria-label="Language">
      <?php foreach ($supported as $lg): ?>
      <a href="<?= h(pf_url($lg)) ?>" hreflang="<?= $lg ?>" class="<?= $lg === $lang ? 'active' : '' ?>"><span class="pf-lang__flag"><?= $flag[$lg] ?></span> <?= strtoupper($lg) ?></a>
      <?php endforeach; ?>
    </nav>
  </div>

  <div class="pf-hero__inner">
    <p class="pf-eyebrow"><?= h($T['eyebrow']) ?></p>
    <h1 class="pf-title">Manalas Dungeon</h1>
    <p class="pf-sub"><?= h($T['sub']) ?></p>
    <p class="pf-lead"><?= h($T['lead']) ?></p>
    <div class="pf-cta">
      <a class="pf-btn pf-btn--gold" href="<?= h($homeUrl) ?>"><?= h($T['cta_site']) ?> &rarr;</a>
      <a class="pf-btn pf-btn--ghost" href="<?= h($archUrl) ?>"><?= h($T['l_arch']) ?></a>
      <a class="pf-btn pf-btn--ghost" href="https://github.com/ManalasDungeon/manalasdungeon.lv" target="_blank" rel="noopener">GitHub</a>
    </div>
    <p class="pf-byline">Marko Nakari · <?= h($T['role']) ?></p>
  </div>
</header>

<main class="pf-main">

  <section class="pf-stats" aria-label="<?= h($T['eyebrow']) ?>">
    <?php foreach ($T['stats'] as [$num, $label]): ?>
    <div class="pf-stat">
      <span class="pf-stat__num"><?= h($num) ?></span>
      <span class="pf-stat__label"><?= h($label) ?></span>
    </div>
    <?php endforeach; ?>
  </section>

  <section class="pf-section">
    <h2 class="pf-h2"><?= h($T['s_features']) ?></h2>
    <div class="pf-features">
      <?php foreach ($T['features'] as [$title, $text, $tags, $hl]): ?>
      <article class="pf-card<?= $hl ? ' pf-card--hl' : '' ?>">
        <?php if ($hl): ?><span class="pf-card__icon"><?= $ICON_KEY ?></span><?php endif; ?>
        <h3 class="pf-card__title"><?= h($title) ?></h3>
        <p class="pf-card__text"><?= h($text) ?></p>
        <ul class="pf-tags">
          <?php foreach ($tags as $tg): ?><li><?= h($tg) ?></li><?php endforeach; ?>
        </ul>
      </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="pf-section">
    <h2 class="pf-h2"><?= h($T['s_security']) ?></h2>
    <p class="pf-section__lead"><?= h($T['security_lead']) ?></p>
    <ul class="pf-security">
      <?php foreach ($T['security'] as [$title, $text]): ?>
      <li class="pf-sec">
        <span class="pf-sec__icon"><?= $ICON_CHECK ?></span>
        <div><strong><?= h($title) ?></strong><span><?= h($text) ?></span></div>
      </li>
      <?php endforeach; ?>
    </ul>
  </section>

  <section class="pf-section">
    <h2 class="pf-h2"><?= h($T['s_stack']) ?></h2>
    <ul class="pf-stack">
      <?php foreach ($STACK as $s): ?><li><?= h($s) ?></li><?php endforeach; ?>
    </ul>
  </section>

  <section class="pf-section">
    <h2 class="pf-h2"><?= h($T['s_quality']) ?></h2>
    <div class="pf-quality">
      <?php foreach ($T['quality'] as [$title, $text]): ?>
      <div class="pf-q">
        <h3 class="pf-q__title"><?= h($title) ?></h3>
        <p class="pf-q__text"><?= h($text) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

</main>

<section class="pf-live">
  <div class="pf-live__inner">
    <h2 class="pf-live__title"><?= h($T['s_live']) ?></h2>
    <p class="pf-live__lead"><?= h($T['live_lead']) ?></p>
    <div class="pf-live__links">
      <a class="pf-btn pf-btn--gold" href="<?= h($homeUrl) ?>"><?= h($T['l_home']) ?></a>
      <a class="pf-btn pf-btn--ghost" href="<?= h($vitrupeUrl) ?>"><?= h($T['l_vitrupe']) ?></a>
      <a class="pf-btn pf-btn--ghost" href="<?= h($vidzemeUrl) ?>"><?= h($T['l_vidzeme']) ?></a>
      <a class="pf-btn pf-btn--ghost" href="<?= h($jogaUrl) ?>"><?= h($T['l_joga']) ?></a>
      <a class="pf-btn pf-btn--ghost" href="<?= h($archUrl) ?>"><?= h($T['l_arch']) ?></a>
      <a class="pf-btn pf-btn--ghost" href="https://github.com/ManalasDungeon/manalasdungeon.lv" target="_blank" rel="noopener">GitHub</a>
    </div>
  </div>
</section>

<footer class="pf-footer">
  <p><span class="pf-footer__label"><?= h($T['contact']) ?>:</span> marko.nakari(at)inbox.lv ·
     <a href="https://www.linkedin.com/in/manalainen" target="_blank" rel="noopener">LinkedIn</a> ·
     <a href="https://github.com/ManalasDungeon" target="_blank" rel="noopener">GitHub</a></p>
  <p class="pf-footer__copy">&copy; 2026 <a href="<?= h($homeUrl) ?>">Marko Nakari</a> · <?= h($T['role']) ?></p>
</footer>

</body>
</html>
