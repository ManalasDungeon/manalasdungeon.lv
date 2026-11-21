<?php
/**
 * Arkkitehtuuri — julkinen versio sivuston arkkitehtuuridokumentaatiosta.
 * Kolmikielinen: oletus lv, ?lang=en / ?lang=fi.
 * HUOM: julkinen sivu → ei ylläpidon tiedostopolkuja, tarkkoja raja-arvoja,
 * aikakatkaisuja, rajatiedoston sijaintia eikä palautusmenettelyä.
 * Kaaviot ovat inline-SVG:tä (tekstit kielitiedoista), animaatiot: arhitektura.css + .js
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, kieli, <head>-palat

$supported = ['lv', 'en', 'fi'];
$default   = 'lv';
$lang = $_GET['lang'] ?? $default;
if (!is_string($lang) || !in_array($lang, $supported, true)) { $lang = $default; }
$base   = 'https://manalasdungeon.lv/';
$flag   = ['lv' => '🇱🇻', 'en' => '🇬🇧', 'fi' => '🇫🇮'];
$locale = ['lv' => 'lv_LV', 'en' => 'en_US', 'fi' => 'fi_FI'];

function ar_url(string $lg): string { return page_url('arhitektura.php', $lg); }
$q            = $lang === $default ? '' : '?lang=' . $lang;
$homeUrl      = $base . $q;
$portfolioUrl = $base . 'portfolio.php' . $q;
$ogImage      = $base . 'images/portfolio_linkedin.jpg';

/* ===================================================================
   Tekstit
   =================================================================== */
$TT = [
'lv' => [
  'title'   => 'Arhitektūra — manalasdungeon.lv',
  'desc'    => 'Kā uzbūvēta trīsvalodu vietne ar bezparoles administrēšanu: serveris, pieprasījumi, dati, passkey pieteikšanās un drošība.',
  'eyebrow' => 'Arhitektūras dokumentācija',
  'h1'      => 'Arhitektūra',
  'sub'     => 'Kā manalasdungeon.lv darbojas no iekšpuses',
  'lead'    => 'Trīsvalodu vietne, kas uzbūvēta ar tīru PHP 8.4, MySQL un JavaScript koplietošanas hostingā, bez satura pārvaldības sistēmas un bez ietvara. Administrēšanā nav paroļu — tikai passkey.',
  'cta_pf'  => 'Projekta apraksts',
  'role'    => 'Programmatūras izstrādātājs',
  'toc'     => 'Saturs',
  'stats'   => [['3', 'valodas katrā lapā'], ['0', 'paroles administrēšanā'], ['3', 'datubāzes tabulas'], ['38', 'vienībtesti kriptogrāfijai']],
  'layers'  => ['Pārlūks', 'LiteSpeed · .htaccess', 'PHP 8.4', 'MySQL'],
  'nav'     => ['principles' => 'Principi', 'context' => 'Sistēmas konteksts', 'request' => 'Pieprasījuma ceļš', 'components' => 'Komponenti', 'i18n' => 'Valodas', 'data' => 'Dati', 'auth' => 'Autentifikācija', 'security' => 'Drošība', 'flows' => 'Formas', 'front' => 'Saskarne un ātrums', 'quality' => 'Kvalitāte', 'decisions' => 'Lēmumi'],
  'principles_lead' => 'Seši noteikumi, kas nosaka katru tehnisko izvēli.',
  'principles' => [
    ['layers', 'Vienkārša tehnoloģija', 'Katra lapa ir viens PHP fails ar savu CSS. Izvietošana nozīmē vienkārši augšupielādēt failus.'],
    ['globe',  'Trīs valodas visur', 'Latviešu pēc noklusējuma, kā arī angļu un somu valoda katrā publiskajā lapā ar pareizām hreflang norādēm.'],
    ['shield', 'Drošība no sākuma', 'Administrēšanā nav paroļu. Katrai formai ir CSRF, surogātpasta un ātruma aizsardzība, visa izvade tiek aizsargāta.'],
    ['data',   'Saturs kā dati', 'Saites, laika līnijas un tulkojumi ir PHP masīvi. Jauna satura pievienošana parasti ir viena rinda.'],
    ['code',   'Darbojas bez JavaScript', 'JavaScript pievieno uznirstošo logu, attēlu skatītāju un jaunumu zīmi, bet lapas strādā arī bez tā.'],
    ['coin',   'Lēta uzturēšana', 'Viens koplietošanas hostinga konts: bez kompilēšanas, bez Node serverī, bez maksas pakalpojumiem.'],
  ],
  'context_lead' => 'Visa vietne darbojas vienā cPanel kontā. Ārpus tā ir tikai Google Analytics, ko ielādē apmeklētāja pārlūks, un e-pasts, ko sūta serveris. Fonti nāk no tā paša servera.',
  'context_cap'  => 'Sistēmas konteksts · klienti, serveris, ārējie pakalpojumi',
  'request_lead' => 'Katrs pieprasījums iziet cauri .htaccess noteikumiem no augšas uz leju, pirms sāk darboties PHP. Secība ir svarīga.',
  'request' => [
    ['Viena adrese', 'HTTP un www tiek pāradresēti ar 301 uz vienu kanonisko HTTPS adresi.'],
    ['Tīri URL', 'Piemēram, /linkit iekšēji atver linkit.php; vecā adrese saņem 301 pāradresāciju, un nosacījums novērš cilpu.'],
    ['Apstrāde', 'PHP apstrādā lapas; noklusējuma lapa ir index.php.'],
    ['Kešatmiņa', 'CSS, JS un fonti — gads, attēli — nedēļa, HTML — vienmēr svaigs.'],
    ['Saspiešana', 'gzip visiem teksta formātiem: HTML, CSS, JS, JSON, SVG un XML.'],
    ['Hotlink aizsardzība', 'Citu vietņu lapām attēli netiek rādīti; sociālo tīklu priekšskata roboti un kopīgošanas attēli ir atļauti.'],
  ],
  'boot_title' => 'Lapas palaišana',
  'boot' => ['Kopīgs bootstrap.php un drošības galvenes', 'Valoda no ?lang=, pārbaudīta pret atļauto sarakstu', 'Teksti no valodas masīva', 'Adreses un asset() ar faila laiku', 'Datubāze tikai tad, ja to vajag', 'Izvade caur htmlspecialchars()'],
  'components_lead' => 'Vietne ir neatkarīgu lapu kopums ar kopīgu izskatu, nevis viena lietotne. Katru lapu var mainīt, neaizskarot citas.',
  'components' => [
    ['Sākumlapa', 'Saites, valodas, jaunumu zīme un poga „Komentēt”', 'PHP + JS'],
    ['Ziņas un ceļojumi', 'Divi emuāri no vienas datubāzes tabulas; ziņām RSS plūsma', 'MySQL'],
    ['Viesu grāmata', 'Publiski sveicieni pēc moderācijas', 'MySQL'],
    ['Atsauksmes', 'Privāta ziņa tieši uz e-pastu', 'e-pasts'],
    ['Vidzemes ceļvedis', 'Vietas pa novadiem un vēstures sadaļa', 'PHP masīvi'],
    ['Jogas lapa', 'Vēsture, filozofija, vinyasa un Core joga', 'PHP masīvi'],
    ['Portfolio un saites', 'Projekta apraksts, saišu lapa ar QR kodu un privātuma politika', 'PHP masīvi'],
    ['Administrēšana', 'Ierakstu redaktors un moderācija aiz passkey', 'passkey'],
    ['Apakšvietnes', 'Vitrupes pludmale, putnu spēle, bibliotēka', 'atsevišķi'],
  ],
  'i18n_lead' => 'Valoda tiek izvēlēta no adreses, nevis saglabāta sīkdatnē, tāpēc katra valodas versija ir sava adrese, ko var indeksēt un kopīgot.',
  'i18n' => [
    ['Izvēle', '?lang=en vai ?lang=fi; viss pārējais nozīmē latviešu valodu.'],
    ['Teksti', 'Katra lapa glabā tekstus masīvā pa valodām; formām ir kopīgs tulkojumu fails.'],
    ['Meklētāji', 'canonical, hreflang visām trim valodām un x-default, Open Graph katrai valodai, vietnes karte ar visām versijām.'],
    ['Tipogrāfija', 'Latviešu diakritiskās zīmes prasa latin-ext fontus; tie nāk no sava servera un ielādējas tikai tad, ja vajag. Pēdiņas atbilst katrai valodai.'],
  ],
  'data_lead' => 'Datubāze ir apzināti maza: trīs InnoDB tabulas (utf8mb4). Viss pārējais ir PHP masīvi vai faili.',
  'tables' => [
    ['Ziņas', 'Ziņas un ceļojumu ieraksti vienā tabulā; kategorija nosaka, kur ieraksts parādās. Attēli tekstā ar īskodu, kas tiek apstrādāts pēc aizsargāšanas.'],
    ['Viesu grāmata', 'Jauns ieraksts gaida apstiprinājumu. No IP adreses tiek glabāta tikai jaucējvērtība.'],
    ['Passkey atslēgas', 'Tikai publiskās atslēgas un parakstu skaitītājs; meklēšana pēc atslēgas ID jaucējvērtības.'],
  ],
  'outside_title' => 'Ārpus datubāzes',
  'outside' => ['Ierakstu attēli mapē, kurā PHP izpilde ir aizliegta', 'Atsauksmju limitu dati ārpus tīmekļa saknes', 'Pārlūka localStorage jaunumu zīmei — bez sīkdatnēm'],
  'auth_lead' => 'Administrators piesakās ar passkey (WebAuthn / FIDO2) un nekad neievada paroli. Servera puse ir tīrs PHP bez ārējām bibliotēkām: CBOR dekodēšana, COSE → PEM un ES256 / RS256 parakstu pārbaude ar OpenSSL.',
  'auth_cap'  => 'Passkey pieteikšanās · 9 soļi',
  'replay'    => 'Atskaņot vēlreiz',
  'auth_points' => ['Izaicinājums ir derīgs īsu brīdi un tiek dzēsts pēc pirmās lietošanas', 'Pieteikšanās un izrakstīšanās laikā tiek nomainīts sesijas ID', 'Sesijai ir neaktivitātes un kopējais laika limits', 'Katrai administrēšanas darbībai ir CSRF žetons'],
  'security_lead' => 'Drošība seko OWASP Top 10 ieteikumiem: katram draudam ir sava aizsardzība.',
  'threat' => 'Drauds',
  'security' => [
    ['Nozagta vai izkrāpta parole', 'Paroļu nav. Passkey ir piesaistīts domēnam, nepieciešama lietotāja verifikācija, tiek pārbaudīts parakstu skaitītājs.'],
    ['Sesijas pārņemšana', 'Jauns sesijas ID pieteikšanās brīdī, laika limiti, Secure, HttpOnly un SameSite sīkdatnes.'],
    ['SQL injekcijas', 'PDO sagatavotie vaicājumi katrā vaicājumā.'],
    ['XSS', 'htmlspecialchars() visā izvadē; saites un attēlu īskodi tiek veidoti tikai pēc aizsargāšanas.'],
    ['CSRF', 'Vienreizējs žetons katrā administrēšanas darbībā un abās publiskajās formās.'],
    ['Surogātpasts un plūdi', 'Slēpts lamatu lauks, laika slazds, limiti katrai IP adresei (jaucējvērtība) un visai vietnei.'],
    ['E-pasta galveņu injekcijas', 'Rindu pārnesumi tiek izņemti, galvenes kodētas pēc RFC 2047, e-pasts pārbaudīts.'],
    ['Ļaunprātīgi faili', 'Faila tips pēc satura, izmēra limits, augšupielādes mapē PHP nedarbojas.'],
    ['Svešs skripts lapā', 'Content-Security-Policy: atļauti tikai savi skripti un Google Analytics; iekšējiem skriptiem vienreizējs nonce, pārkāpumi tiek reģistrēti.'],
    ['Iegulšana svešā lapā', 'X-Frame-Options, kā arī nosniff, Referrer-Policy un Permissions-Policy galvenes.'],
    ['Pārtverts savienojums', 'HTTPS pāradresācija un HSTS: pārlūks gadu izmanto tikai HTTPS, arī pirmajā pieprasījumā.'],
    ['Apmeklētāju izsekošana', 'Google Analytics ielādējas tikai pēc piekrišanas; IP adreses glabājas tikai kā jaucējkodi.'],
  ],
  'secrets' => 'Noslēpumi (datubāzes piekļuve, passkey iestatījumi) ir tikai serverī. Repozitorijā ir tikai paraugfaili, un .gitignore bloķē īstos.',
  'flows_lead' => 'Apmeklētājs var rakstīt vietnei divos veidos: publiskajā viesu grāmatā, kuru moderē, un privātajā atsauksmju formā, kuras ziņas nonāk tikai administratora e-pastā.',
  'flows_cap'  => 'Publiskās formas · viesu grāmata un atsauksmes',
  'flows_notes' => [
    ['Viesu grāmata', 'Ieraksts saglabājas ar statusu „gaida”, administrators saņem paziņojumu un apstiprina to aiz passkey.'],
    ['Atsauksmes', 'CSRF žetons tiek ielādēts tikai tad, kad logs atveras, tāpēc sākumlapa paliek bez sīkdatnēm. Datubāzē netiek rakstīts nekas.'],
  ],
  'front_cols' => [
    ['Saskarne', ['Katrai lapai savs stilu fails ar CSS mainīgajiem', 'Playfair Display, Nunito Sans, EB Garamond un Great Vibes no sava servera (woff2)', 'Dabiskais <dialog> uznirstošajam logam', 'Mobilais vispirms; ievēro prefers-reduced-motion']],
    ['Ātrums', ['Kešatmiņa uz gadu, jo katrai saitei ir ?v=<faila laiks>', 'HTML vienmēr svaigs (no-cache)', 'gzip un WebP attēli', 'Galvenajā vietnē nav JS bibliotēku']],
    ['Meklētāji', ['canonical, hreflang un x-default', 'Open Graph un Twitter kartītes', 'schema.org JSON-LD', 'Vietnes karte ar visām valodām']],
  ],
  'swatches' => 'Krāsas',
  'quality' => [
    ['Vienībtesti', '38 testi passkey kriptogrāfijai: CBOR, COSE → PEM, ES256 un RS256.'],
    ['Pārlūka testi', 'Playwright ar Chromium virtuālo WebAuthn autentifikatoru.'],
    ['Formu testi', 'Nepareizs žetons, lamatu lauks, laika slazds, galveņu injekcijas un limiti.'],
    ['Lapu testi', '87 automātiski testi: 14 lapas trīs valodās, datorā un tālrunī — kļūdas, saites, formas, CSP, pikseļu salīdzināšana un pieejamība (axe, WCAG 2.1 AA).'],
    ['Servera iestatījumi', '.htaccess noteikumi pārbaudīti lokālā Apache serverī.'],
    ['Attēli', 'Python un Pillow: divtoņu krāsas, līniju zīmējumi, WebP, OG attēli un QR kods.'],
  ],
  'decisions_lead' => 'Lielākā daļa lēmumu apmaina ērtības pret mazāk atkarību un mazāku uzbrukuma virsmu.',
  'why' => 'Kāpēc', 'tradeoff' => 'Kompromiss',
  'decisions' => [
    ['Tīrs PHP bez CMS un ietvara', 'Jāatjaunina tikai PHP; pilna kontrole pār izvadi un drošību.', 'Nav ORM; kopīgais kods jāuztur savā includes/ slānī.'],
    ['Tikai passkey, sava WebAuthn realizācija', 'Nav paroles, ko izkrāpt vai nopludināt; nav trešo pušu bibliotēku.', 'Sava kriptogrāfija jāuztur — tāpēc 38 testi.'],
    ['Viena tabula ziņām un ceļojumiem', 'Viens redaktors un vieni lauki; pietiek ar kategoriju.', 'Kategorijai specifiski lauki prasītu shēmas maiņu.'],
    ['Valoda adresē, nevis sīkdatnē', 'Katru versiju var indeksēt un kopīgot; strādā ar kešatmiņu.', 'Garākas ?lang= adreses.'],
    ['Atsauksmes e-pastā, nevis datubāzē', 'Privāts pēc būtības; nav ko moderēt vai glabāt.', 'Atkarīgs no hostinga e-pasta piegādes.'],
    ['Saturs PHP masīvos', 'Tulkojumi blakus marķējumam; viegli pārskatīt Git.', 'Satura labošana ir koda labošana.'],
    ['Analytics tikai ar piekrišanu', 'Atbilst VDAR; Google nesaņem datus no tiem, kas atsakās.', 'Statistikā redzama tikai daļa apmeklētāju.'],
    ['Fonti no sava servera', 'Apmeklētāja IP nenonāk pie Google; viens ārējs domēns mazāk; vienkāršāka CSP.', 'Fontu atjauninājumi jāveic pašam.'],
    ['CSP vispirms tikai ziņošanas režīmā', 'Žurnāls atklāja trūkstošu Google Analytics adresi, pirms kaut kas salūza.', 'Pirmajās dienās politika neko nebloķēja.'],
  ],
  'done_title' => 'Nesen paveikts',
  'done' => ['Kopīgs includes/ slānis atkārtotam kodam', 'Automātiski testi lapu attēlošanai', 'Fonti no sava servera, arī Vitrupes vietnē', 'Content-Security-Policy galvene bloķēšanas režīmā', 'Sīkdatņu piekrišana un privātuma politika', 'HSTS, sava 404 lapa un ziņu RSS plūsma', 'Iknedēļas datubāzes rezerves kopija', 'WebP attēli un pieejamības pārbaude testos'],
  'next_title' => 'Tālāk',
  'next' => [],
  'end_title' => 'Gribi redzēt, kā tas izskatās?',
  'end_lead'  => 'Vietne ir publiska — visas lapas trīs valodās.',
  'l_home'    => 'Uz vietni',
  'contact'   => 'Kontakti',
  'scroll'    => 'ritiniet sāniski',
],
'en' => [
  'title'   => 'Architecture — manalasdungeon.lv',
  'desc'    => 'How a trilingual website with passwordless admin is built: hosting, request flow, data, passkey sign-in and security.',
  'eyebrow' => 'Architecture documentation',
  'h1'      => 'Architecture',
  'sub'     => 'How manalasdungeon.lv works on the inside',
  'lead'    => 'A trilingual website built with plain PHP 8.4, MySQL and JavaScript on shared hosting, with no CMS and no framework. The admin has no passwords — passkeys only.',
  'cta_pf'  => 'Case study',
  'role'    => 'Software developer',
  'toc'     => 'Contents',
  'stats'   => [['3', 'languages on every page'], ['0', 'passwords in the admin'], ['3', 'database tables'], ['38', 'unit tests for the crypto']],
  'layers'  => ['Browser', 'LiteSpeed · .htaccess', 'PHP 8.4', 'MySQL'],
  'nav'     => ['principles' => 'Principles', 'context' => 'System context', 'request' => 'Request lifecycle', 'components' => 'Components', 'i18n' => 'Languages', 'data' => 'Data', 'auth' => 'Authentication', 'security' => 'Security', 'flows' => 'Forms', 'front' => 'Front end & speed', 'quality' => 'Quality', 'decisions' => 'Decisions'],
  'principles_lead' => 'Six rules behind every technical choice.',
  'principles' => [
    ['layers', 'Plain stack', 'Each page is one PHP file with its own CSS. Deploying means uploading files.'],
    ['globe',  'Three languages everywhere', 'Latvian by default, plus English and Finnish on every public page, with correct hreflang tags.'],
    ['shield', 'Security by design', 'No passwords in the admin. Every form has CSRF, spam and rate controls, and all output is escaped.'],
    ['data',   'Content as data', 'Links, timelines and translations are PHP arrays. Adding content is usually one line.'],
    ['code',   'Works without JavaScript', 'JavaScript adds the pop-up, the lightbox and the news badge, but pages work without it.'],
    ['coin',   'Cheap to run', 'One shared hosting account: no build step, no Node on the server, no paid services.'],
  ],
  'context_lead' => 'The whole site runs in one cPanel account. Only Google Analytics, loaded by the visitor’s browser, and email sent by the server leave it. Fonts come from the same server.',
  'context_cap'  => 'System context · clients, hosting, outside services',
  'request_lead' => 'Every request passes the .htaccess rules top to bottom before PHP runs. The order matters.',
  'request' => [
    ['One address', 'HTTP and www get a 301 to one canonical HTTPS address.'],
    ['Clean URLs', '/linkit, for example, opens linkit.php internally; the old address gets a 301, and a condition prevents a loop.'],
    ['Handlers', 'PHP handles the pages; the default page is index.php.'],
    ['Browser cache', 'CSS, JS and fonts: a year. Images: a week. HTML: always fresh.'],
    ['Compression', 'gzip for every text format: HTML, CSS, JS, JSON, SVG and XML.'],
    ['Hotlink guard', 'Images are not served to other sites’ pages; social preview bots and share images are allowed.'],
  ],
  'boot_title' => 'Page bootstrap',
  'boot' => ['Shared bootstrap.php and security headers', 'Language from ?lang=, checked against a whitelist', 'Texts from the language array', 'URLs and asset() with file time', 'Database only if the page needs it', 'Output through htmlspecialchars()'],
  'components_lead' => 'The site is a set of independent pages that share a look, not one application. Each page can change without touching the others.',
  'components' => [
    ['Home', 'Links, languages, news badge and a “Comment” button', 'PHP + JS'],
    ['News and travel', 'Two blogs from one database table; RSS feed for the news', 'MySQL'],
    ['Guestbook', 'Public greetings after moderation', 'MySQL'],
    ['Feedback', 'Private message straight to email', 'email'],
    ['Vidzeme guide', 'Places by municipality and a history section', 'PHP arrays'],
    ['Yoga page', 'History, philosophy, Vinyasa and Core yoga', 'PHP arrays'],
    ['Portfolio and links', 'Case study, a link page with a QR code and the privacy policy', 'PHP arrays'],
    ['Admin', 'Post editor and moderation behind passkeys', 'passkey'],
    ['Sub-sites', 'Vitrupe beach, bird game, library', 'separate'],
  ],
  'i18n_lead' => 'The language comes from the URL, not a cookie, so every language version is its own address that can be indexed and shared.',
  'i18n' => [
    ['Choice', '?lang=en or ?lang=fi; anything else means Latvian.'],
    ['Texts', 'Each page keeps its strings in a per-language array; the forms share one translation file.'],
    ['Search engines', 'canonical, hreflang for all three languages plus x-default, Open Graph per language, a sitemap with every version.'],
    ['Typography', 'Latvian diacritics need latin-ext fonts; they are self-hosted and load only when needed. Quotation marks follow each language.'],
  ],
  'data_lead' => 'The database is small on purpose: three InnoDB tables (utf8mb4). Everything else is PHP arrays or files.',
  'tables' => [
    ['Posts', 'News and travel posts in one table; a category decides where a post appears. Images go in the text as a shortcode, rendered after escaping.'],
    ['Guestbook', 'New entries wait for approval. Only a hash of the IP address is stored.'],
    ['Passkeys', 'Public keys and the signature counter only; looked up by a hash of the credential ID.'],
  ],
  'outside_title' => 'Outside the database',
  'outside' => ['Post images in a folder where PHP cannot run', 'Feedback rate-limit data outside the web root', 'Browser localStorage for the news badge — no cookies'],
  'auth_lead' => 'The admin signs in with a passkey (WebAuthn / FIDO2) and never types a password. The server side is plain PHP with no external libraries: CBOR decoding, COSE → PEM and ES256 / RS256 signature checks with OpenSSL.',
  'auth_cap'  => 'Passkey sign-in · 9 steps',
  'replay'    => 'Play again',
  'auth_points' => ['The challenge is short-lived and deleted on first use', 'The session ID changes on sign-in and sign-out', 'Sessions have an idle and a total time limit', 'Every admin action carries a CSRF token'],
  'security_lead' => 'Security follows the OWASP Top 10, with one control per threat.',
  'threat' => 'Threat',
  'security' => [
    ['Stolen or phished password', 'No passwords. Passkeys are bound to the domain, user verification is required, the signature counter is checked.'],
    ['Session hijacking', 'New session ID on sign-in, time limits, Secure, HttpOnly and SameSite cookies.'],
    ['SQL injection', 'PDO prepared statements on every query.'],
    ['XSS', 'htmlspecialchars() on all output; links and image shortcodes are built only after escaping.'],
    ['CSRF', 'A one-time token on every admin action and on both public forms.'],
    ['Spam and floods', 'Hidden honeypot field, time trap, per-IP limits (hashed) and a site-wide cap.'],
    ['Email header injection', 'Line breaks stripped, headers RFC 2047-encoded, email address validated.'],
    ['Malicious uploads', 'File type from content, size limit, no PHP in the upload folder.'],
    ['Injected scripts', 'Content-Security-Policy: only own scripts and Google Analytics; inline scripts carry a one-time nonce, violations are logged.'],
    ['Framing by other sites', 'X-Frame-Options, plus nosniff, Referrer-Policy and Permissions-Policy headers.'],
    ['Intercepted connection', 'HTTPS redirect and HSTS: browsers use only HTTPS for a year, even on the first request.'],
    ['Visitor tracking', 'Google Analytics loads only after consent; IP addresses are stored only as hashes.'],
  ],
  'secrets' => 'Secrets (database access, passkey settings) live only on the server. The repository holds templates only, and .gitignore blocks the real files.',
  'flows_lead' => 'Visitors can write to the site in two ways: a public guestbook that is moderated, and a private feedback form whose messages go only to the admin’s email.',
  'flows_cap'  => 'Public forms · guestbook and feedback',
  'flows_notes' => [
    ['Guestbook', 'The entry is saved as pending, the admin gets an alert and approves it behind a passkey.'],
    ['Feedback', 'The CSRF token is fetched only when the pop-up opens, so the home page stays cookie-free. Nothing is written to the database.'],
  ],
  'front_cols' => [
    ['Front end', ['One stylesheet per page, built on CSS custom properties', 'Playfair Display, Nunito Sans, EB Garamond and Great Vibes, self-hosted (woff2)', 'Native <dialog> for the pop-up', 'Mobile first; respects prefers-reduced-motion']],
    ['Speed', ['One-year cache because every link carries ?v=<file time>', 'HTML always fresh (no-cache)', 'gzip and WebP images', 'No JS libraries on the main site']],
    ['Search', ['canonical, hreflang and x-default', 'Open Graph and Twitter cards', 'schema.org JSON-LD', 'Sitemap with every language']],
  ],
  'swatches' => 'Colours',
  'quality' => [
    ['Unit tests', '38 tests for the passkey crypto: CBOR, COSE → PEM, ES256 and RS256.'],
    ['Browser tests', 'Playwright with Chromium’s virtual WebAuthn authenticator.'],
    ['Form tests', 'Wrong token, honeypot, time trap, header injection and limits.'],
    ['Page tests', '87 automated tests: 14 pages in three languages, desktop and phone — errors, links, forms, CSP, pixel comparison and accessibility (axe, WCAG 2.1 AA).'],
    ['Server config', '.htaccess rules tested on a local Apache.'],
    ['Images', 'Python and Pillow: duotones, line art, WebP, OG images and a QR code.'],
  ],
  'decisions_lead' => 'Most decisions trade convenience for fewer dependencies and a smaller attack surface.',
  'why' => 'Why', 'tradeoff' => 'Trade-off',
  'decisions' => [
    ['Plain PHP, no CMS or framework', 'Only PHP to patch; full control of output and security.', 'No ORM; shared code lives in an includes/ layer to maintain.'],
    ['Passkeys only, own WebAuthn code', 'No password to phish or leak; no third-party library.', 'Own crypto to maintain — hence 38 tests.'],
    ['One table for news and travel', 'One editor, one set of fields; a category is enough.', 'Category-specific fields would need a schema change.'],
    ['Language in the URL, not a cookie', 'Every version can be indexed and shared; works with caching.', 'Longer ?lang= URLs.'],
    ['Feedback by email, not the database', 'Private by design; nothing to moderate or store.', 'Depends on the host’s mail delivery.'],
    ['Content in PHP arrays', 'Translations sit next to the markup; easy to review in Git.', 'Editing content means editing code.'],
    ['Analytics only with consent', 'GDPR-compliant; Google gets no data from visitors who decline.', 'The statistics show only part of the visitors.'],
    ['Self-hosted fonts', 'Visitor IPs don’t reach Google; one external domain less; a simpler CSP.', 'Font updates are up to me.'],
    ['CSP in report-only mode first', 'The log caught a missing Google Analytics address before anything broke.', 'For the first days the policy blocked nothing.'],
  ],
  'done_title' => 'Recently done',
  'done' => ['A shared includes/ layer for repeated code', 'Automated tests for page rendering', 'Self-hosted fonts, the Vitrupe site included', 'An enforced Content-Security-Policy header', 'Cookie consent and a privacy policy', 'HSTS, a custom 404 page and an RSS feed for the news', 'Weekly database backups', 'WebP images and accessibility checks in the tests'],
  'next_title' => 'Next steps',
  'next' => [],
  'end_title' => 'Want to see it in action?',
  'end_lead'  => 'The site is public — every page in three languages.',
  'l_home'    => 'Visit the site',
  'contact'   => 'Contact',
  'scroll'    => 'scroll sideways',
],
'fi' => [
  'title'   => 'Arkkitehtuuri — manalasdungeon.lv',
  'desc'    => 'Miten kolmikielinen, salasanattomasti ylläpidetty sivusto on rakennettu: palvelin, pyynnöt, data, passkey-kirjautuminen ja tietoturva.',
  'eyebrow' => 'Arkkitehtuuridokumentaatio',
  'h1'      => 'Arkkitehtuuri',
  'sub'     => 'Miten manalasdungeon.lv toimii sisältä',
  'lead'    => 'Kolmikielinen sivusto, joka on rakennettu pelkällä PHP 8.4:llä, MySQL:llä ja JavaScriptillä jaetulle palvelimelle ilman julkaisujärjestelmää tai sovelluskehystä. Ylläpidossa ei ole salasanoja — vain passkey.',
  'cta_pf'  => 'Projektiesittely',
  'role'    => 'Ohjelmistokehittäjä',
  'toc'     => 'Sisältö',
  'stats'   => [['3', 'kieltä joka sivulla'], ['0', 'salasanaa ylläpidossa'], ['3', 'tietokantataulua'], ['38', 'yksikkötestiä kryptolle']],
  'layers'  => ['Selain', 'LiteSpeed · .htaccess', 'PHP 8.4', 'MySQL'],
  'nav'     => ['principles' => 'Periaatteet', 'context' => 'Järjestelmän konteksti', 'request' => 'Pyynnön kulku', 'components' => 'Osat', 'i18n' => 'Kielet', 'data' => 'Data', 'auth' => 'Tunnistautuminen', 'security' => 'Tietoturva', 'flows' => 'Lomakkeet', 'front' => 'Käyttöliittymä ja nopeus', 'quality' => 'Laatu', 'decisions' => 'Päätökset'],
  'principles_lead' => 'Kuusi sääntöä jokaisen teknisen valinnan takana.',
  'principles' => [
    ['layers', 'Yksinkertainen tekniikka', 'Jokainen sivu on yksi PHP-tiedosto omalla CSS:llään. Käyttöönotto on pelkkä tiedostojen lataus.'],
    ['globe',  'Kolme kieltä kaikkialla', 'Latvia oletuksena sekä englanti ja suomi jokaisella julkisella sivulla, oikeilla hreflang-merkinnöillä.'],
    ['shield', 'Tietoturva alusta asti', 'Ylläpidossa ei ole salasanoja. Jokaisessa lomakkeessa on CSRF-, roskaposti- ja lähetysrajasuojaus, ja kaikki tulostus suojataan.'],
    ['data',   'Sisältö datana', 'Linkit, aikajanat ja käännökset ovat PHP-taulukoita. Uusi sisältö on yleensä yksi rivi.'],
    ['code',   'Toimii ilman JavaScriptiä', 'JavaScript tuo ponnahdusikkunan, kuvakatselimen ja uutismerkin, mutta sivut toimivat ilman sitäkin.'],
    ['coin',   'Edullinen ylläpitää', 'Yksi jaettu palvelintili: ei käännösvaihetta, ei Nodea palvelimella, ei maksullisia palveluita.'],
  ],
  'context_lead' => 'Koko sivusto toimii yhdellä cPanel-tilillä. Ulos lähtevät vain kävijän selaimen lataama Google Analytics ja palvelimen lähettämä sähköposti. Fontit tulevat samalta palvelimelta.',
  'context_cap'  => 'Järjestelmän konteksti · asiakkaat, palvelin, ulkoiset palvelut',
  'request_lead' => 'Jokainen pyyntö kulkee .htaccess-sääntöjen läpi ylhäältä alas ennen kuin PHP käynnistyy. Järjestyksellä on väliä.',
  'request' => [
    ['Yksi osoite', 'HTTP ja www ohjataan 301-ohjauksella yhteen kanoniseen HTTPS-osoitteeseen.'],
    ['Siistit osoitteet', 'Esimerkiksi /linkit avaa sisäisesti linkit.php:n; vanha osoite saa 301-ohjauksen, ja ehto estää silmukan.'],
    ['Käsittely', 'PHP käsittelee sivut; oletussivu on index.php.'],
    ['Välimuisti', 'CSS, JS ja fontit vuoden, kuvat viikon, HTML aina tuoreena.'],
    ['Pakkaus', 'gzip kaikille tekstimuodoille: HTML, CSS, JS, JSON, SVG ja XML.'],
    ['Upotuksen esto', 'Kuvia ei näytetä muiden sivustojen sivuilla; somen esikatselurobotit ja jakokuvat ovat sallittuja.'],
  ],
  'boot_title' => 'Sivun käynnistys',
  'boot' => ['Yhteinen bootstrap.php ja turvaotsakkeet', 'Kieli ?lang=-parametrista, tarkistus sallittujen listaa vasten', 'Tekstit kielitaulukosta', 'Osoitteet ja asset() muokkausajalla', 'Tietokanta vain tarvittaessa', 'Tulostus htmlspecialchars()-funktion läpi'],
  'components_lead' => 'Sivusto on joukko itsenäisiä sivuja, joilla on yhteinen ilme — ei yksi sovellus. Jokaista sivua voi muuttaa koskematta muihin.',
  'components' => [
    ['Etusivu', 'Linkit, kielet, uutismerkki ja Kommentoi-painike', 'PHP + JS'],
    ['Uutiset ja matkat', 'Kaksi blogia yhdestä tietokantataulusta; uutisille RSS-syöte', 'MySQL'],
    ['Vieraskirja', 'Julkiset terveiset moderoinnin jälkeen', 'MySQL'],
    ['Palaute', 'Yksityinen viesti suoraan sähköpostiin', 'sähköposti'],
    ['Vidzemen opas', 'Paikat lääneittäin ja historiaosio', 'PHP-taulukot'],
    ['Joogasivu', 'Historia, filosofia, Vinyasa ja Core-jooga', 'PHP-taulukot'],
    ['Portfolio ja linkit', 'Projektiesittely ja linkkisivu QR-koodilla sekä tietosuojaseloste', 'PHP-taulukot'],
    ['Ylläpito', 'Merkintöjen muokkain ja moderointi passkeyn takana', 'passkey'],
    ['Alisivustot', 'Vitrupen ranta, lintupeli, kirjasto', 'erilliset'],
  ],
  'i18n_lead' => 'Kieli tulee osoitteesta eikä evästeestä, joten jokainen kieliversio on oma osoitteensa, jonka voi indeksoida ja jakaa.',
  'i18n' => [
    ['Valinta', '?lang=en tai ?lang=fi; kaikki muu tarkoittaa latviaa.'],
    ['Tekstit', 'Jokainen sivu pitää tekstinsä kielikohtaisessa taulukossa; lomakkeilla on yhteinen käännöstiedosto.'],
    ['Hakukoneet', 'canonical, hreflang kaikille kolmelle kielelle ja x-default, Open Graph kielittäin, sivukartta kaikista versioista.'],
    ['Typografia', 'Latvian diakriitit vaativat latin-ext-fontit; ne tulevat omalta palvelimelta ja latautuvat vain tarvittaessa. Lainausmerkit noudattavat kunkin kielen tapaa.'],
  ],
  'data_lead' => 'Tietokanta on tarkoituksella pieni: kolme InnoDB-taulua (utf8mb4). Kaikki muu on PHP-taulukoita tai tiedostoja.',
  'tables' => [
    ['Merkinnät', 'Uutiset ja matkamerkinnät samassa taulussa; kategoria ratkaisee, missä merkintä näkyy. Kuvat lisätään tekstiin lyhenteellä, joka käsitellään suojauksen jälkeen.'],
    ['Vieraskirja', 'Uusi merkintä odottaa hyväksyntää. IP-osoitteesta tallennetaan vain tiiviste.'],
    ['Passkey-avaimet', 'Vain julkiset avaimet ja allekirjoituslaskuri; haku avaintunnisteen tiivisteellä.'],
  ],
  'outside_title' => 'Tietokannan ulkopuolella',
  'outside' => ['Merkintöjen kuvat kansiossa, jossa PHP ei toimi', 'Palautteen lähetysrajat web-juuren ulkopuolella', 'Selaimen localStorage uutismerkille — ei evästeitä'],
  'auth_lead' => 'Ylläpitäjä kirjautuu passkeyllä (WebAuthn / FIDO2) eikä kirjoita koskaan salasanaa. Palvelinpuoli on pelkkää PHP:tä ilman ulkoisia kirjastoja: CBOR-dekoodaus, COSE → PEM sekä ES256- ja RS256-allekirjoitusten tarkistus OpenSSL:llä.',
  'auth_cap'  => 'Passkey-kirjautuminen · 9 vaihetta',
  'replay'    => 'Toista uudelleen',
  'auth_points' => ['Haaste on lyhytikäinen ja poistetaan ensimmäisellä käyttökerralla', 'Istuntotunnus vaihdetaan sisään- ja uloskirjautuessa', 'Istunnolla on käyttämättömyys- ja kokonaisaikaraja', 'Jokaisessa ylläpitotoiminnossa on CSRF-tunniste'],
  'security_lead' => 'Tietoturva noudattaa OWASP Top 10 -suosituksia: jokaista uhkaa vastaan on oma suojauksensa.',
  'threat' => 'Uhka',
  'security' => [
    ['Varastettu tai kalasteltu salasana', 'Ei salasanoja. Passkey on sidottu verkkotunnukseen, käyttäjän vahvistus vaaditaan ja allekirjoituslaskuri tarkistetaan.'],
    ['Istunnon kaappaus', 'Uusi istuntotunnus kirjautuessa, aikarajat sekä Secure-, HttpOnly- ja SameSite-evästeet.'],
    ['SQL-injektio', 'PDO:n valmistellut kyselyt jokaisessa kyselyssä.'],
    ['XSS', 'htmlspecialchars() kaikessa tulostuksessa; linkit ja kuvalyhenteet muodostetaan vasta suojauksen jälkeen.'],
    ['CSRF', 'Kertakäyttöinen tunniste jokaisessa ylläpitotoiminnossa ja molemmissa julkisissa lomakkeissa.'],
    ['Roskaposti ja tulvat', 'Piilotettu ansakenttä, aikaraja, IP-kohtaiset rajat (tiivisteenä) ja koko sivuston katto.'],
    ['Sähköpostin otsakeinjektio', 'Rivinvaihdot poistetaan, otsakkeet koodataan RFC 2047 -muotoon ja osoite tarkistetaan.'],
    ['Haitalliset tiedostot', 'Tyyppi tarkistetaan sisällöstä, kokoraja, latauskansiossa PHP ei toimi.'],
    ['Vieras skripti sivulla', 'Content-Security-Policy: sallittu vain omat skriptit ja Google Analytics; sivun sisäisillä skripteillä kertakäyttöinen nonce, rikkeet kirjataan.'],
    ['Upotus vieraalle sivulle', 'X-Frame-Options sekä nosniff-, Referrer-Policy- ja Permissions-Policy-otsakkeet.'],
    ['Kaapattu yhteys', 'HTTPS-ohjaus ja HSTS: selain käyttää vuoden ajan vain HTTPS:ää, myös ensimmäisellä pyynnöllä.'],
    ['Kävijöiden seuranta', 'Google Analytics latautuu vasta suostumuksella; IP-osoitteet tallennetaan vain tiivisteinä.'],
  ],
  'secrets' => 'Salaisuudet (tietokantayhteys, passkey-asetukset) ovat vain palvelimella. Repossa on pelkät mallitiedostot, ja .gitignore estää oikeat.',
  'flows_lead' => 'Kävijä voi kirjoittaa sivustolle kahdella tavalla: julkiseen vieraskirjaan, joka moderoidaan, ja yksityiseen palautelomakkeeseen, jonka viestit menevät vain ylläpitäjän sähköpostiin.',
  'flows_cap'  => 'Julkiset lomakkeet · vieraskirja ja palaute',
  'flows_notes' => [
    ['Vieraskirja', 'Merkintä tallentuu odottavaksi, ylläpitäjä saa ilmoituksen ja hyväksyy sen passkeyn takana.'],
    ['Palaute', 'CSRF-tunniste haetaan vasta, kun ikkuna avataan, joten etusivu pysyy evästeettömänä. Tietokantaan ei kirjoiteta mitään.'],
  ],
  'front_cols' => [
    ['Käyttöliittymä', ['Oma tyylitiedosto joka sivulle, CSS-muuttujat', 'Playfair Display, Nunito Sans, EB Garamond ja Great Vibes omalta palvelimelta (woff2)', 'Selaimen oma <dialog> ponnahdusikkunana', 'Ensin puhelimelle; noudattaa prefers-reduced-motion-asetusta']],
    ['Nopeus', ['Vuoden välimuisti, koska linkeissä on ?v=<muokkausaika>', 'HTML aina tuoreena (no-cache)', 'gzip ja WebP-kuvat', 'Pääsivustolla ei JS-kirjastoja']],
    ['Hakukoneet', ['canonical, hreflang ja x-default', 'Open Graph- ja Twitter-kortit', 'schema.org JSON-LD', 'Sivukartta kaikilla kielillä']],
  ],
  'swatches' => 'Värit',
  'quality' => [
    ['Yksikkötestit', '38 testiä passkey-kryptolle: CBOR, COSE → PEM, ES256 ja RS256.'],
    ['Selaintestit', 'Playwright ja Chromiumin virtuaalinen WebAuthn-todentaja.'],
    ['Lomaketestit', 'Väärä tunniste, ansakenttä, aikaraja, otsakeinjektio ja rajat.'],
    ['Sivutestit', '87 automaattista testiä: 14 sivua kolmella kielellä, tietokone ja puhelin — virheet, linkit, lomakkeet, CSP, pikselivertailu ja saavutettavuus (axe, WCAG 2.1 AA).'],
    ['Palvelinasetukset', '.htaccess-säännöt testattu paikallisella Apachella.'],
    ['Kuvat', 'Python ja Pillow: kaksisävyvärit, viivapiirrokset, WebP, OG-kuvat ja QR-koodi.'],
  ],
  'decisions_lead' => 'Useimmissa ratkaisuissa on vaihdettu mukavuutta pienempään määrään riippuvuuksia ja pienempään hyökkäyspintaan.',
  'why' => 'Miksi', 'tradeoff' => 'Kompromissi',
  'decisions' => [
    ['Pelkkä PHP, ei julkaisujärjestelmää tai kehystä', 'Päivitettävänä vain PHP; täysi hallinta tulostukseen ja tietoturvaan.', 'Ei ORM:ää; yhteinen koodi ylläpidetään omassa includes/-kerroksessa.'],
    ['Vain passkey, oma WebAuthn-toteutus', 'Ei kalasteltavaa tai vuotavaa salasanaa; ei kolmannen osapuolen kirjastoa.', 'Oma krypto ylläpidettävänä — siksi 38 testiä.'],
    ['Yksi taulu uutisille ja matkoille', 'Yksi muokkain ja samat kentät; kategoria riittää.', 'Kategoriakohtaiset kentät vaatisivat taulumuutoksen.'],
    ['Kieli osoitteessa, ei evästeessä', 'Jokaisen version voi indeksoida ja jakaa; toimii välimuistin kanssa.', 'Pidemmät ?lang=-osoitteet.'],
    ['Palaute sähköpostiin, ei tietokantaan', 'Yksityinen jo rakenteeltaan; ei moderoitavaa eikä tallennettavaa.', 'Riippuu palvelimen postinvälityksestä.'],
    ['Sisältö PHP-taulukoissa', 'Käännökset merkinnän vieressä; helppo tarkistaa Gitissä.', 'Sisällön muokkaus on koodin muokkausta.'],
    ['Analytics vain suostumuksella', 'Tietosuoja-asetuksen mukainen; Google ei saa tietoja kieltäytyneiltä.', 'Tilastoissa näkyy vain osa kävijöistä.'],
    ['Fontit omalta palvelimelta', 'Kävijän IP ei päädy Googlelle; yksi ulkoinen osoite vähemmän; yksinkertaisempi CSP.', 'Fonttien päivitykset tehdään itse.'],
    ['CSP ensin raportointitilassa', 'Loki paljasti puuttuvan Google Analytics -osoitteen ennen kuin mikään hajosi.', 'Ensimmäisinä päivinä sääntö ei estänyt mitään.'],
  ],
  'done_title' => 'Juuri valmistunut',
  'done' => ['Yhteinen includes/-kerros toistuvalle koodille', 'Automaattiset testit sivujen renderöinnille', 'Fontit omalta palvelimelta, myös Vitrupen sivulle', 'Content-Security-Policy-otsake estävässä tilassa', 'Evästesuostumus ja tietosuojaseloste', 'HSTS, oma 404-sivu ja uutisten RSS-syöte', 'Viikoittainen tietokannan varmuuskopio', 'WebP-kuvat ja saavutettavuustarkistus testeissä'],
  'next_title' => 'Seuraavaksi',
  'next' => [],
  'end_title' => 'Haluatko nähdä sen toiminnassa?',
  'end_lead'  => 'Sivusto on julkinen — kaikki sivut kolmella kielellä.',
  'l_home'    => 'Siirry sivustolle',
  'contact'   => 'Yhteystiedot',
  'scroll'    => 'vieritä sivusuunnassa',
],
];
$T = $TT[$lang];

/* ===================================================================
   Kaavioiden tekstit (ennalta mitoitettu laatikoihin)
   =================================================================== */
$DG = [
'lv' => [
  'c' => ['visitor' => ['Apmeklētāja pārlūks', 'lapas lv, en, fi'], 'admin' => ['Administrators', 'ierīce ar passkey'], 'bots' => ['Priekšskata roboti', 'LinkedIn, Facebook'],
          'host' => 'nano.lv koplietošanas hostings (cPanel)', 'ls' => ['LiteSpeed + .htaccess', 'HTTPS, tīri URL, kešatmiņa,', 'gzip, aizsardzība pret hotlink'],
          'php' => ['PHP 8.4 lapas', 'publiskās lapas un atsauksmes', 'administrēšana aiz passkey'], 'db' => ['MySQL', 'ziņas un viesu', 'grāmata, atslēgas'],
          'files' => ['Faili diskā', 'attēli, fonti,', 'limitu dati'], 'ga' => ['GA4', 'klikšķu notikumi'],
          'mail' => ['E-pasts', 'paziņojumi un', 'atsauksmes'], 'lbl' => 'analītika ielādējas pārlūkā'],
  's' => ['dev' => ['Administratora ierīce', 'pārlūks + autentifikators'], 'srv' => ['Serveris', 'passkey modulis (PHP)'], 'db' => ['MySQL', 'publiskās atslēgas'],
          'm' => ['1  pieprasa pieteikšanās izaicinājumu', '2  nejaušs izaicinājums ar termiņu', '4  paraksts + autentifikatora dati', '5  meklē atslēgu pēc ID jaucējvērtības', '6  publiskā atslēga + skaitītājs', '8  saglabā skaitītāju un laiku', '9  jauna sesija; administrēšana atveras'],
          'n3' => ['3  pirksta nospiedums, seja', 'vai PIN; privātā atslēga', 'paraksta izaicinājumu'],
          'n7' => ['7  pārbauda vai noraida:', 'izaicinājums, izcelsme, rp_id,', 'UV karogs, paraksts (OpenSSL),', 'skaitītājs ir pieaudzis']],
  'f' => ['rowA' => 'PUBLISKI', 'rowB' => 'PRIVĀTI',
          'gb' => ['Viesu grāmata', 'publisks sveiciens', 'tiek moderēta'], 'fb' => ['Atsauksmju logs', 'privāta ziņa', 'uznirstošais logs'],
          'ck' => ['Vienādas pārbaudes', 'CSRF žetons', 'slēpts lamatu lauks', 'limits katrai IP', 'garums un saites', 'droša izvade'],
          'fail' => ['kļūda: paziņojums', 'bots: viltus paldies'],
          'pd' => ['Saglabā, gaida', 'apstiprinājumu', '+ e-pasta paziņojums'], 'ap' => ['Admins apstiprina', 'pēc passkey,', 'tad redzams visiem'],
          'ml' => ['Sūta ar mail()', 'vārds sūtītāja laukā,', 'drošas galvenes'], 'ib' => ['Admina e-pasts', 'atbilde sūtītājam', 'nekas netiek glabāts']],
],
'en' => [
  'c' => ['visitor' => ['Visitor’s browser', 'pages in lv, en, fi'], 'admin' => ['Admin’s device', 'passkey sign-in'], 'bots' => ['Preview bots', 'LinkedIn, Facebook'],
          'host' => 'nano.lv shared hosting (cPanel)', 'ls' => ['LiteSpeed + .htaccess', 'HTTPS, clean URLs, cache,', 'gzip, hotlink guard'],
          'php' => ['PHP 8.4 pages', 'public pages and feedback', 'admin behind passkeys'], 'db' => ['MySQL', 'posts, guestbook,', 'passkey keys'],
          'files' => ['Files on disk', 'images, fonts,', 'rate-limit data'], 'ga' => ['GA4', 'link-click events'],
          'mail' => ['Admin mailbox', 'alerts and', 'feedback'], 'lbl' => 'analytics loads in the browser'],
  's' => ['dev' => ['Admin’s device', 'browser + authenticator'], 'srv' => ['Server', 'passkey module (PHP)'], 'db' => ['MySQL', 'public keys'],
          'm' => ['1  ask for a sign-in challenge', '2  random challenge, short-lived', '4  signature + authenticator data', '5  look up key by credential hash', '6  public key (PEM) + counter', '8  save counter + last used', '9  new session ID; admin opens'],
          'n3' => ['3  fingerprint, face or PIN;', 'the private key signs', 'the challenge'],
          'n7' => ['7  verify, or reject:', 'challenge, origin and rp_id,', 'user-verified flag, signature', '(OpenSSL), counter went up']],
  'f' => ['rowA' => 'PUBLIC', 'rowB' => 'PRIVATE',
          'gb' => ['Guestbook form', 'public greeting', 'moderated'], 'fb' => ['Comment pop-up', 'private message', 'opens in a dialog'],
          'ck' => ['Same checks', 'CSRF token', 'hidden honeypot field', 'per-IP limit, hashed IP', 'length + link checks', 'escaping on output'],
          'fail' => ['fails: error shown', 'bots: fake thank-you'],
          'pd' => ['Saved as pending', 'awaiting approval', '+ email alert'], 'ap' => ['Admin approves', 'after passkey sign-in,', 'then shown publicly'],
          'ml' => ['Sent by mail()', 'name in From,', 'safe encoded headers'], 'ib' => ['Admin’s inbox', 'reply goes to sender', 'nothing is stored']],
],
'fi' => [
  'c' => ['visitor' => ['Kävijän selain', 'sivut lv, en, fi'], 'admin' => ['Ylläpitäjän laite', 'kirjaudu passkeyllä'], 'bots' => ['Esikatselurobotit', 'LinkedIn, Facebook'],
          'host' => 'nano.lv jaettu palvelin (cPanel)', 'ls' => ['LiteSpeed + .htaccess', 'HTTPS, siistit osoitteet,', 'välimuisti, gzip, upotuksen esto'],
          'php' => ['PHP 8.4 -sivut', 'julkiset sivut ja palaute', 'ylläpito passkeyn takana'], 'db' => ['MySQL', 'blogit, vieraskirja,', 'passkey-avaimet'],
          'files' => ['Tiedostot', 'kuvat, fontit,', 'lähetysrajat'], 'ga' => ['GA4', 'linkkiklikkaukset'],
          'mail' => ['Ylläpidon posti', 'ilmoitukset ja', 'palauteviestit'], 'lbl' => 'analytiikka latautuu selaimessa'],
  's' => ['dev' => ['Ylläpitäjän laite', 'selain + todentaja'], 'srv' => ['Palvelin', 'passkey-moduuli (PHP)'], 'db' => ['MySQL', 'julkiset avaimet'],
          'm' => ['1  pyydä kirjautumishaaste', '2  satunnainen haaste, lyhytikäinen', '4  allekirjoitus + todentajan data', '5  hae avain tunnisteen tiivisteellä', '6  julkinen avain (PEM) + laskuri', '8  tallenna laskuri + käyttöaika', '9  uusi istunto; ylläpito aukeaa'],
          'n3' => ['3  sormenjälki, kasvot', 'tai PIN, ja yksityinen', 'avain allekirjoittaa'],
          'n7' => ['7  tarkista tai hylkää:', 'haaste, alkuperä ja rp_id,', 'vahvistuslippu, allekirjoitus', '(OpenSSL), laskuri kasvoi']],
  'f' => ['rowA' => 'JULKINEN', 'rowB' => 'YKSITYINEN',
          'gb' => ['Vieraskirja', 'julkinen tervehdys', 'moderoitu'], 'fb' => ['Palauteikkuna', 'yksityinen viesti', 'avautuu ikkunaan'],
          'ck' => ['Samat tarkistukset', 'CSRF-tunniste', 'piilotettu ansakenttä', 'IP-raja, IP tiivisteenä', 'pituus ja linkit', 'suojattu tulostus'],
          'fail' => ['hylätty: virhe näkyviin', 'botti: valekiitos'],
          'pd' => ['Tallennus odottaa', 'hyväksyntää', '+ ilmoitus postiin'], 'ap' => ['Ylläpito hyväksyy', 'passkey-kirjautuminen,', 'sitten julkinen'],
          'ml' => ['Lähetys mail()', 'nimi From-kentässä,', 'turvalliset otsakkeet'], 'ib' => ['Ylläpidon posti', 'vastaus lähettäjälle', 'mitään ei tallenneta']],
],
];
$D = $DG[$lang];

/* SVG-apuri: laatikko nimen ja rivien kanssa (tekstit suojataan) */
function node(int $x, int $y, int $w, int $hgt, array $lines, int $i, string $cls = 'd-box'): string {
    $o = '<g class="d-node" style="--i:' . $i . '"><rect class="' . $cls . '" x="' . $x . '" y="' . $y . '" width="' . $w . '" height="' . $hgt . '" rx="10"/>';
    $o .= '<text class="d-name" x="' . ($x + 12) . '" y="' . ($y + 24) . '">' . h($lines[0]) . '</text>';
    foreach (array_slice($lines, 1) as $k => $ln) {
        $o .= '<text class="d-line" x="' . ($x + 12) . '" y="' . ($y + 42 + 16 * $k) . '">' . h($ln) . '</text>';
    }
    return $o . '</g>';
}
$ARROW = '<defs><marker id="%s" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" class="d-arrowhead"/></marker></defs>';

/* Ikonit periaatteille */
$ICONS = [
  'layers' => '<path d="M12 3 3 8l9 5 9-5-9-5z"/><path d="m3 13 9 5 9-5"/><path d="m3 17.5 9 5 9-5" opacity=".5"/>',
  'globe'  => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
  'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
  'data'   => '<ellipse cx="12" cy="5.5" rx="8" ry="3"/><path d="M4 5.5v13c0 1.7 3.6 3 8 3s8-1.3 8-3v-13"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
  'code'   => '<path d="m8 7-5 5 5 5M16 7l5 5-5 5M14 4l-4 16"/>',
  'coin'   => '<circle cx="12" cy="12" r="9"/><path d="M15 9.5c-.6-1-1.7-1.5-3-1.5-1.7 0-3 .9-3 2s1.3 1.8 3 2 3 .9 3 2-1.3 2-3 2c-1.3 0-2.4-.5-3-1.5M12 6v2M12 16v2"/>',
];
$ICON_SHIELD = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $ICONS['shield'] . '</svg>';

$ld = [
  '@context' => 'https://schema.org', '@type' => 'TechArticle',
  'headline' => $T['title'], 'description' => $T['desc'], 'url' => ar_url($lang), 'inLanguage' => $lang,
  'author' => ['@type' => 'Person', 'name' => 'Marko Nakari', 'jobTitle' => $T['role'], 'url' => $base,
               'sameAs' => ['https://github.com/ManalasDungeon', 'https://www.linkedin.com/in/manalainen']],
  'about' => ['PHP', 'MySQL', 'WebAuthn', 'Web architecture'],
];
$navIds = array_keys($T['nav']);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?= head_analytics('') ?><script nonce="<?= csp_nonce() ?>">document.documentElement.classList.add('js');</script>
<title><?= h($T['title']) ?></title>
<meta name="description" content="<?= h($T['desc']) ?>">
<meta name="author" content="Marko Nakari">
<meta name="theme-color" content="#6E1223">
<?= head_alternates('ar_url', $lang, '') ?><meta property="og:type" content="article">
<meta property="og:site_name" content="Manalas Dungeon">
<meta name="title" property="og:title" content="<?= h($T['title']) ?>">
<meta property="og:description" content="<?= h($T['desc']) ?>">
<meta property="og:url" content="<?= h(ar_url($lang)) ?>">
<meta name="image" property="og:image" content="<?= h($ogImage) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="627">
<meta property="og:locale" content="<?= h($locale[$lang]) ?>">
<meta name="twitter:card" content="summary_large_image">
<?= head_fonts('playfair-display-latin-wght-normal.woff2', '') ?><link rel="stylesheet" href="<?= h(asset('portfolio.css')) ?>">
<link rel="stylesheet" href="<?= h(asset('arhitektura.css')) ?>">
<script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
</head>
<body class="pf-body ar-body">
<div class="ar-progress" aria-hidden="true"><span></span></div>

<header class="pf-hero ar-hero">
  <div class="ar-grid" aria-hidden="true"></div>
  <div class="pf-top">
    <a class="pf-brand" href="<?= h($homeUrl) ?>">manalasdungeon.lv</a>
    <nav class="pf-lang" aria-label="Language">
      <?php foreach ($supported as $lg): ?>
      <a href="<?= h(ar_url($lg)) ?>" hreflang="<?= $lg ?>" class="<?= $lg === $lang ? 'active' : '' ?>"><span class="pf-lang__flag"><?= $flag[$lg] ?></span> <?= strtoupper($lg) ?></a>
      <?php endforeach; ?>
    </nav>
  </div>

  <div class="ar-hero__inner">
    <div class="ar-hero__text">
      <p class="pf-eyebrow"><?= h($T['eyebrow']) ?></p>
      <h1 class="pf-title ar-title"><?= h($T['h1']) ?></h1>
      <p class="pf-sub"><?= h($T['sub']) ?></p>
      <p class="pf-lead"><?= h($T['lead']) ?></p>
      <div class="pf-cta">
        <a class="pf-btn pf-btn--gold" href="#principles"><?= h($T['nav']['principles']) ?> &darr;</a>
        <a class="pf-btn pf-btn--ghost" href="<?= h($portfolioUrl) ?>"><?= h($T['cta_pf']) ?></a>
      </div>
      <p class="pf-byline">Marko Nakari · <?= h($T['role']) ?></p>
    </div>

    <!-- Isometrinen kerrospino: pyyntö kulkee selaimesta tietokantaan -->
    <svg class="ar-stack" viewBox="0 0 500 400" role="img" aria-label="<?= h(implode(' → ', $T['layers'])) ?>">
      <?php foreach ($T['layers'] as $i => $label):
        $cx = 160; $cy = 70 + $i * 84; $acc = $i === 2;
        $top   = ($cx) . ',' . ($cy - 34) . ' ' . ($cx + 120) . ',' . $cy . ' ' . $cx . ',' . ($cy + 34) . ' ' . ($cx - 120) . ',' . $cy;
        $left  = ($cx - 120) . ',' . $cy . ' ' . $cx . ',' . ($cy + 34) . ' ' . $cx . ',' . ($cy + 50) . ' ' . ($cx - 120) . ',' . ($cy + 16);
        $right = $cx . ',' . ($cy + 34) . ' ' . ($cx + 120) . ',' . $cy . ' ' . ($cx + 120) . ',' . ($cy + 16) . ' ' . $cx . ',' . ($cy + 50);
      ?>
      <g class="ar-layer<?= $acc ? ' ar-layer--acc' : '' ?>" style="--i:<?= $i ?>">
        <polygon class="ar-layer__left" points="<?= $left ?>"/>
        <polygon class="ar-layer__right" points="<?= $right ?>"/>
        <polygon class="ar-layer__top" points="<?= $top ?>"/>
        <line class="ar-layer__lead" x1="<?= $cx + 120 ?>" y1="<?= $cy ?>" x2="<?= $cx + 150 ?>" y2="<?= $cy ?>"/>
        <text class="ar-layer__label" x="<?= $cx + 158 ?>" y="<?= $cy + 5 ?>"><?= h($label) ?></text>
      </g>
      <?php endforeach; ?>
      <circle class="ar-packet" r="6" cx="160" cy="70">
        <animate attributeName="cy" values="40;326;40" dur="5.5s" repeatCount="indefinite" keyTimes="0;0.85;1" calcMode="spline" keySplines=".45 0 .55 1;0 0 1 1"/>
        <animate attributeName="opacity" values="0;1;1;0;0" keyTimes="0;0.08;0.78;0.85;1" dur="5.5s" repeatCount="indefinite"/>
      </circle>
    </svg>
  </div>
</header>

<div class="ar-stats-wrap">
  <section class="pf-stats ar-stats" aria-label="<?= h($T['eyebrow']) ?>">
    <?php foreach ($T['stats'] as [$num, $label]): ?>
    <div class="pf-stat" data-reveal>
      <span class="pf-stat__num" data-count="<?= h($num) ?>"><?= h($num) ?></span>
      <span class="pf-stat__label"><?= h($label) ?></span>
    </div>
    <?php endforeach; ?>
  </section>
</div>

<div class="ar-layout">
  <nav class="ar-toc" aria-label="<?= h($T['toc']) ?>">
    <p class="ar-toc__title"><?= h($T['toc']) ?></p>
    <ol>
      <?php foreach ($T['nav'] as $id => $label): ?>
      <li><a href="#<?= $id ?>" data-toc="<?= $id ?>"><?= h($label) ?></a></li>
      <?php endforeach; ?>
    </ol>
  </nav>

  <main class="ar-main">

    <!-- 1 Periaatteet -->
    <section class="ar-sec" id="principles">
      <h2 class="pf-h2"><?= h($T['nav']['principles']) ?></h2>
      <p class="pf-section__lead"><?= h($T['principles_lead']) ?></p>
      <div class="ar-principles">
        <?php foreach ($T['principles'] as $i => [$ic, $title, $text]): ?>
        <article class="ar-pcard" data-reveal style="--i:<?= $i ?>">
          <span class="ar-pcard__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $ICONS[$ic] ?></svg></span>
          <h3><?= h($title) ?></h3>
          <p><?= h($text) ?></p>
        </article>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- 2 Järjestelmän konteksti -->
    <section class="ar-sec" id="context">
      <h2 class="pf-h2"><?= h($T['nav']['context']) ?></h2>
      <p class="pf-section__lead"><?= h($T['context_lead']) ?></p>
      <?php $c = $D['c']; ?>
      <figure class="ar-fig" data-reveal>
        <div class="ar-fig__scroll">
        <svg class="ar-diagram" viewBox="0 0 760 412" role="img" aria-label="<?= h($T['context_cap']) ?>">
          <?= sprintf($ARROW, 'ctx-arrow') ?>
          <g class="d-node" style="--i:0"><rect class="d-host" x="228" y="80" width="336" height="320" rx="12"/><text class="d-name d-host__name" x="244" y="104"><?= h($c['host']) ?></text></g>
          <g class="d-edges">
            <path class="d-edge" d="M192 124H210M192 204H210M192 284H210M210 124V284"/>
            <path class="d-edge d-flow" d="M210 156H244" marker-end="url(#ctx-arrow)"/>
            <path class="d-edge d-flow" d="M396 192V216" marker-end="url(#ctx-arrow)"/>
            <path class="d-edge d-flow" d="M316 288V312" marker-end="url(#ctx-arrow)"/>
            <path class="d-edge d-flow" d="M476 288V312" marker-end="url(#ctx-arrow)"/>
            <path class="d-edge d-flow" d="M108 96V60H582V124H600" marker-end="url(#ctx-arrow)"/>
            <path class="d-edge d-flow" d="M548 252H582V340H600" marker-end="url(#ctx-arrow)"/>
          </g>
          <?= node(24, 96, 168, 56, $c['visitor'], 1) ?>
          <?= node(24, 176, 168, 56, $c['admin'], 2) ?>
          <?= node(24, 256, 168, 56, $c['bots'], 3) ?>
          <?= node(244, 120, 304, 72, $c['ls'], 4) ?>
          <?= node(244, 216, 304, 72, $c['php'], 5, 'd-acc') ?>
          <?= node(244, 312, 144, 72, $c['db'], 6) ?>
          <?= node(404, 312, 144, 72, $c['files'], 7) ?>
          <?= node(600, 96, 136, 56, $c['ga'], 8) ?>
          <?= node(600, 304, 136, 72, $c['mail'], 10) ?>
          <text class="d-label" x="345" y="52" text-anchor="middle"><?= h($c['lbl']) ?></text>
          <text class="d-label" x="588" y="294">mail()</text>
          <text class="d-label" x="322" y="304">PDO</text>
        </svg>
        </div>
        <figcaption><span><?= h($T['context_cap']) ?><span class="ar-scrollhint"> · <?= h($T['scroll']) ?></span></span></figcaption>
      </figure>
    </section>

    <!-- 3 Pyynnön kulku -->
    <section class="ar-sec" id="request">
      <h2 class="pf-h2"><?= h($T['nav']['request']) ?></h2>
      <p class="pf-section__lead"><?= h($T['request_lead']) ?></p>
      <div class="ar-request">
        <ol class="ar-pipe" data-reveal>
          <?php foreach ($T['request'] as $i => [$title, $text]): ?>
          <li style="--i:<?= $i ?>"><span class="ar-pipe__num"><?= $i + 1 ?></span><div><h3><?= h($title) ?></h3><p><?= h($text) ?></p></div></li>
          <?php endforeach; ?>
        </ol>
        <aside class="ar-boot" data-reveal>
          <h3><?= h($T['boot_title']) ?></h3>
          <ol class="ar-code">
            <?php foreach ($T['boot'] as $i => $line): ?>
            <li style="--i:<?= $i ?>"><?= h($line) ?></li>
            <?php endforeach; ?>
          </ol>
        </aside>
      </div>
    </section>

    <!-- 4 Osat -->
    <section class="ar-sec" id="components">
      <h2 class="pf-h2"><?= h($T['nav']['components']) ?></h2>
      <p class="pf-section__lead"><?= h($T['components_lead']) ?></p>
      <div class="ar-comps">
        <?php foreach ($T['components'] as $i => [$name, $what, $chip]): ?>
        <article class="ar-comp" data-reveal style="--i:<?= $i % 3 ?>">
          <h3><?= h($name) ?></h3>
          <p><?= h($what) ?></p>
          <span class="ar-chip<?= $chip === 'passkey' ? ' ar-chip--key' : '' ?>"><?= h($chip) ?></span>
        </article>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- 5 Kielet -->
    <section class="ar-sec" id="i18n">
      <h2 class="pf-h2"><?= h($T['nav']['i18n']) ?></h2>
      <p class="pf-section__lead"><?= h($T['i18n_lead']) ?></p>
      <div class="ar-i18n">
        <div class="ar-urls" data-reveal aria-hidden="true">
          <?php foreach (['lv' => '', 'en' => '?lang=en', 'fi' => '?lang=fi'] as $lg => $qs): ?>
          <div class="ar-url<?= $lg === $lang ? ' is-current' : '' ?>"><span class="ar-url__flag"><?= $flag[$lg] ?></span><code>/arhitektura.php<b><?= h($qs) ?></b></code></div>
          <?php endforeach; ?>
        </div>
        <dl class="ar-dl">
          <?php foreach ($T['i18n'] as $i => [$k, $v]): ?>
          <div data-reveal style="--i:<?= $i ?>"><dt><?= h($k) ?></dt><dd><?= h($v) ?></dd></div>
          <?php endforeach; ?>
        </dl>
      </div>
    </section>

    <!-- 6 Data -->
    <section class="ar-sec" id="data">
      <h2 class="pf-h2"><?= h($T['nav']['data']) ?></h2>
      <p class="pf-section__lead"><?= h($T['data_lead']) ?></p>
      <div class="ar-tables">
        <?php foreach ($T['tables'] as $i => [$name, $text]): ?>
        <article class="ar-table" data-reveal style="--i:<?= $i ?>">
          <svg class="ar-table__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><?= $ICONS['data'] ?></svg>
          <h3><?= h($name) ?></h3>
          <p><?= h($text) ?></p>
        </article>
        <?php endforeach; ?>
      </div>
      <h3 class="ar-h3"><?= h($T['outside_title']) ?></h3>
      <ul class="ar-ticks">
        <?php foreach ($T['outside'] as $o): ?><li data-reveal><?= h($o) ?></li><?php endforeach; ?>
      </ul>
    </section>

    <!-- 7 Tunnistautuminen -->
    <section class="ar-sec" id="auth">
      <h2 class="pf-h2"><?= h($T['nav']['auth']) ?></h2>
      <p class="pf-section__lead"><?= h($T['auth_lead']) ?></p>
      <?php $s = $D['s']; $dev = 120; $srv = 380; $dbx = 630;
        $msgs = [[$dev, $srv, 112, $s['m'][0]], [$srv, $dev, 152, $s['m'][1]], [$dev, $srv, 264, $s['m'][2]], [$srv, $dbx, 304, $s['m'][3]], [$dbx, $srv, 344, $s['m'][4]], [$srv, $dbx, 480, $s['m'][5]], [$srv, $dev, 520, $s['m'][6]]];
        $order = [0, 1, 3, 4, 5, 7, 8]; /* animaatiojärjestys: muistiinpanot 3 ja 7 väliin */ ?>
      <figure class="ar-fig ar-seq" data-reveal>
        <div class="ar-fig__scroll">
        <svg class="ar-diagram" viewBox="0 0 760 556" role="img" aria-label="<?= h($T['auth_cap']) ?>">
          <?= sprintf($ARROW, 'seq-arrow') ?>
          <g class="d-lifelines">
            <line x1="<?= $dev ?>" x2="<?= $dev ?>" y1="72" y2="168"/><line x1="<?= $dev ?>" x2="<?= $dev ?>" y1="232" y2="540"/>
            <line x1="<?= $srv ?>" x2="<?= $srv ?>" y1="72" y2="364"/><line x1="<?= $srv ?>" x2="<?= $srv ?>" y1="444" y2="540"/>
            <line x1="<?= $dbx ?>" x2="<?= $dbx ?>" y1="72" y2="540"/>
          </g>
          <?php foreach ([[$dev, $s['dev']], [$srv, $s['srv']], [$dbx, $s['db']]] as [$x, $hd]): ?>
          <g><rect class="d-box" x="<?= $x - 100 ?>" y="16" width="200" height="56" rx="10"/>
            <text class="d-name" x="<?= $x ?>" y="40" text-anchor="middle"><?= h($hd[0]) ?></text>
            <text class="d-line" x="<?= $x ?>" y="58" text-anchor="middle"><?= h($hd[1]) ?></text></g>
          <?php endforeach; ?>
          <?php foreach ($msgs as $k => [$x1, $x2, $y, $label]): ?>
          <g class="d-msg" style="--s:<?= $order[$k] ?>">
            <path class="d-edge d-draw" d="M<?= $x1 ?> <?= $y ?>H<?= $x2 > $x1 ? $x2 - 2 : $x2 + 2 ?>" pathLength="1" marker-end="url(#seq-arrow)"/>
            <text class="d-msgtext" x="<?= ($x1 + $x2) / 2 ?>" y="<?= $y - 9 ?>" text-anchor="middle"><?= h($label) ?></text>
          </g>
          <?php endforeach; ?>
          <g class="d-msg" style="--s:2"><rect class="d-box" x="20" y="168" width="200" height="64" rx="10"/>
            <?php foreach ($s['n3'] as $k => $ln): ?><text class="d-note" x="32" y="<?= 188 + 16 * $k ?>"><?= h($ln) ?></text><?php endforeach; ?></g>
          <g class="d-msg" style="--s:6"><rect class="d-acc" x="266" y="364" width="228" height="80" rx="10"/>
            <?php foreach ($s['n7'] as $k => $ln): ?><text class="d-note<?= $k === 0 ? ' d-note--b' : '' ?>" x="278" y="<?= 384 + 16 * $k ?>"><?= h($ln) ?></text><?php endforeach; ?></g>
        </svg>
        </div>
        <figcaption><span><?= h($T['auth_cap']) ?><span class="ar-scrollhint"> · <?= h($T['scroll']) ?></span></span> <button type="button" class="ar-replay" data-replay><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg><?= h($T['replay']) ?></button></figcaption>
      </figure>
      <ul class="ar-ticks ar-ticks--2">
        <?php foreach ($T['auth_points'] as $p): ?><li data-reveal><?= h($p) ?></li><?php endforeach; ?>
      </ul>
    </section>

    <!-- 8 Tietoturva -->
    <section class="ar-sec" id="security">
      <h2 class="pf-h2"><?= h($T['nav']['security']) ?></h2>
      <p class="pf-section__lead"><?= h($T['security_lead']) ?></p>
      <div class="ar-sec-grid">
        <?php foreach ($T['security'] as $i => [$threat, $control]): ?>
        <article class="ar-threat" data-reveal style="--i:<?= $i % 2 ?>">
          <span class="ar-threat__icon"><?= $ICON_SHIELD ?></span>
          <div>
            <p class="ar-threat__k"><?= h($T['threat']) ?></p>
            <h3><?= h($threat) ?></h3>
            <p><?= h($control) ?></p>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="ar-note" data-reveal><?= h($T['secrets']) ?></p>
    </section>

    <!-- 9 Lomakkeet -->
    <section class="ar-sec" id="flows">
      <h2 class="pf-h2"><?= h($T['nav']['flows']) ?></h2>
      <p class="pf-section__lead"><?= h($T['flows_lead']) ?></p>
      <?php $f = $D['f']; ?>
      <figure class="ar-fig" data-reveal>
        <div class="ar-fig__scroll">
        <svg class="ar-diagram" viewBox="0 0 760 368" role="img" aria-label="<?= h($T['flows_cap']) ?>">
          <?= sprintf($ARROW, 'flow-arrow') ?>
          <text class="d-row" x="24" y="40"><?= h($f['rowA']) ?></text>
          <text class="d-row" x="24" y="182"><?= h($f['rowB']) ?></text>
          <g class="d-edges">
            <path class="d-edge d-flow" d="M160 90H184" marker-end="url(#flow-arrow)"/>
            <path class="d-edge d-flow" d="M160 230H184" marker-end="url(#flow-arrow)"/>
            <path class="d-edge d-flow" d="M352 90H384" marker-end="url(#flow-arrow)"/>
            <path class="d-edge d-flow" d="M352 230H384" marker-end="url(#flow-arrow)"/>
            <path class="d-edge d-flow" d="M544 90H576" marker-end="url(#flow-arrow)"/>
            <path class="d-edge d-flow" d="M544 230H576" marker-end="url(#flow-arrow)"/>
            <path class="d-edge d-flow d-flow--bad" d="M268 266V290" marker-end="url(#flow-arrow)"/>
          </g>
          <?= node(24, 54, 136, 72, $f['gb'], 0) ?>
          <?= node(24, 194, 136, 72, $f['fb'], 1) ?>
          <g class="d-node" style="--i:2"><rect class="d-acc" x="184" y="54" width="168" height="212" rx="10"/>
            <text class="d-name" x="196" y="80"><?= h($f['ck'][0]) ?></text>
            <?php foreach (array_slice($f['ck'], 1) as $k => $ln): ?><text class="d-note" x="196" y="<?= 108 + 24 * $k ?>"><?= h($ln) ?></text><?php endforeach; ?></g>
          <g class="d-node" style="--i:3"><rect class="d-box d-box--muted" x="184" y="290" width="168" height="52" rx="10"/>
            <text class="d-line" x="196" y="312"><?= h($f['fail'][0]) ?></text><text class="d-line" x="196" y="328"><?= h($f['fail'][1]) ?></text></g>
          <?= node(384, 54, 160, 72, $f['pd'], 4) ?>
          <?= node(576, 54, 160, 72, $f['ap'], 5) ?>
          <?= node(384, 194, 160, 72, $f['ml'], 6) ?>
          <?= node(576, 194, 160, 72, $f['ib'], 7) ?>
        </svg>
        </div>
        <figcaption><span><?= h($T['flows_cap']) ?><span class="ar-scrollhint"> · <?= h($T['scroll']) ?></span></span></figcaption>
      </figure>
      <div class="ar-two">
        <?php foreach ($T['flows_notes'] as [$k, $v]): ?>
        <div class="ar-q" data-reveal><h3><?= h($k) ?></h3><p><?= h($v) ?></p></div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- 10 Käyttöliittymä, nopeus, hakukoneet -->
    <section class="ar-sec" id="front">
      <h2 class="pf-h2"><?= h($T['nav']['front']) ?></h2>
      <div class="ar-cols">
        <?php foreach ($T['front_cols'] as $i => [$title, $items]): ?>
        <div class="ar-col" data-reveal style="--i:<?= $i ?>">
          <h3><?= h($title) ?></h3>
          <ul><?php foreach ($items as $it): ?><li><?= h($it) ?></li><?php endforeach; ?></ul>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="ar-swatches" data-reveal>
        <span class="ar-swatches__title"><?= h($T['swatches']) ?></span>
        <?php foreach (['#9E1B32' => '--lv-red', '#6E1223' => '--lv-red-dark', '#C08A2E' => '--lv-amber', '#E4C77A' => '--lv-amber-light', '#FBF7F0' => '--lv-cream'] as $hex => $var): ?>
        <span class="ar-sw" style="--c:<?= $hex ?>" title="<?= $var ?>"><i></i><code><?= $hex ?></code></span>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- 11 Laatu -->
    <section class="ar-sec" id="quality">
      <h2 class="pf-h2"><?= h($T['nav']['quality']) ?></h2>
      <div class="ar-quality">
        <?php foreach ($T['quality'] as $i => [$title, $text]): ?>
        <div class="pf-q" data-reveal style="--i:<?= $i % 3 ?>"><h3 class="pf-q__title"><?= h($title) ?></h3><p class="pf-q__text"><?= h($text) ?></p></div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- 12 Päätökset -->
    <section class="ar-sec" id="decisions">
      <h2 class="pf-h2"><?= h($T['nav']['decisions']) ?></h2>
      <p class="pf-section__lead"><?= h($T['decisions_lead']) ?></p>
      <div class="ar-decs">
        <?php foreach ($T['decisions'] as $i => [$dec, $why, $trade]): ?>
        <details class="ar-dec" data-reveal<?= $i === 0 ? ' open' : '' ?>>
          <summary><span class="ar-dec__n"><?= sprintf('%02d', $i + 1) ?></span><?= h($dec) ?></summary>
          <div class="ar-dec__body">
            <div><p class="ar-dec__k ar-dec__k--why"><?= h($T['why']) ?></p><p><?= h($why) ?></p></div>
            <div><p class="ar-dec__k"><?= h($T['tradeoff']) ?></p><p><?= h($trade) ?></p></div>
          </div>
        </details>
        <?php endforeach; ?>
      </div>
      <h3 class="ar-h3"><?= h($T['done_title']) ?></h3>
      <ul class="ar-ticks ar-ticks--done">
        <?php foreach ($T['done'] as $n): ?><li data-reveal><?= h($n) ?></li><?php endforeach; ?>
      </ul>
<?php if ($T['next']): ?>
      <h3 class="ar-h3"><?= h($T['next_title']) ?></h3>
      <ul class="ar-ticks ar-ticks--next">
        <?php foreach ($T['next'] as $n): ?><li data-reveal><?= h($n) ?></li><?php endforeach; ?>
      </ul>
<?php endif; ?>
    </section>

  </main>
</div>

<section class="pf-live">
  <div class="pf-live__inner">
    <h2 class="pf-live__title"><?= h($T['end_title']) ?></h2>
    <p class="pf-live__lead"><?= h($T['end_lead']) ?></p>
    <div class="pf-live__links">
      <a class="pf-btn pf-btn--gold" href="<?= h($homeUrl) ?>"><?= h($T['l_home']) ?> &rarr;</a>
      <a class="pf-btn pf-btn--ghost" href="<?= h($portfolioUrl) ?>"><?= h($T['cta_pf']) ?></a>
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

<script src="<?= h(asset('arhitektura.js')) ?>" defer></script>
</body>
</html>
