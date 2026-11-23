<?php
require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, kieli, <head>-palat
/**
 * Tietosuojaseloste / privātuma politika / privacy policy — kolmikielinen.
 * Evästevalintapaneeli: kytkin [data-consent-toggle] ja tila [data-consent-status] (analytics.js).
 */

$lang = site_lang();
$flag = ['lv' => '🇱🇻', 'en' => '🇬🇧', 'fi' => '🇫🇮'];
function pr_url(string $lg): string { return page_url('privatums', $lg); }

const PR_UPDATED = '2026-11-20';
$CONTACT = 'marko.nakari(at)inbox.lv';

$TT = [
  'lv' => [
    'title'   => 'Privātuma politika — Manalas Dungeon',
    'desc'    => 'Kādus datus vietne manalasdungeon.lv apstrādā, kāpēc un kā mainīt sīkdatņu izvēli.',
    'eyebrow' => 'manalasdungeon.lv',
    'h1'      => 'Privātuma politika',
    'lead'    => 'Šī ir personīga vietne. Es apkopoju pēc iespējas mazāk datu un nekad tos nepārdodu.',
    'updated' => 'Atjaunināts',
    'back'    => '← Uz sākumlapu',
    'panel_h' => 'Jūsu sīkdatņu izvēle',
    'nec'     => 'Nepieciešamās sīkdatnes', 'nec_d' => 'Vajadzīgas veidlapu drošībai. Vienmēr ieslēgtas.', 'nec_s' => 'Vienmēr',
    'ana'     => 'Analītikas sīkdatnes (Google Analytics)', 'ana_d' => 'Palīdz man redzēt, kuras lapas tiek apmeklētas.',
    'st_yes'  => 'Atļautas', 'st_no' => 'Atteiktas', 'st_none' => 'Izvēle vēl nav izdarīta', 'nojs' => 'Lai mainītu izvēli, ieslēdziet JavaScript.',
    'sections' => [
      ['Pārzinis', [
        'Personas datu pārzinis ir Marko Nakari, Heinola, Somija. Saziņa: ' . $CONTACT . '.',
      ]],
      ['Apmeklējumu statistika (Google Analytics)', [
        'Ja jūs piekrītat, vietne izmanto Google Analytics 4 (Google Ireland Limited), lai redzētu, cik apmeklētāju ir vietnē, kuras lapas tiek skatītas un no kurienes apmeklētāji atnāk. Google saņem jūsu IP adresi, pārlūka un ierīces informāciju un aptuveno atrašanās vietu, un saglabā pseidonīmu identifikatoru sīkdatnēs. Google Analytics glabā datus par atsevišķiem apmeklējumiem 2 mēnešus, pēc tam tie tiek dzēsti; paliek tikai apkopota statistika.',
        'Bez jūsu piekrišanas Google skripts netiek ielādēts un Google netiek nosūtīti nekādi dati. Juridiskais pamats ir jūsu piekrišana (VDAR 6. panta 1. punkta a) apakšpunkts). Piekrišanu var atsaukt jebkurā laikā ar pogu šajā lapā. Google var pārsūtīt datus uz ASV saskaņā ar ES un ASV datu privātuma regulējumu.',
      ]],
      ['Atsauksmju veidlapa', [
        'Ja nosūtāt atsauksmi, jūsu vārds, ziņa un (ja to norādāt) e-pasta adrese tiek nosūtīti uz manu e-pastu. Tie netiek publicēti un netiek glabāti datubāzē. E-pastu glabāju tikai tik ilgi, cik nepieciešams, lai atbildētu.',
        'Lai aizsargātu veidlapu no surogātpasta, uz 24 stundām tiek saglabāts jūsu IP adreses jaucējkods (hash), no kura IP adresi nevar atjaunot.',
      ]],
      ['Viesu grāmata', [
        'Viesu grāmatā tiek saglabāts jūsu vārds, ziņa un (ja to norādāt) saite. Ieraksts tiek publicēts tikai pēc manas apstiprināšanas. Surogātpasta aizsardzībai kopā ar ierakstu tiek saglabāts IP adreses jaucējkods.',
        'Ja vēlaties, lai jūsu ieraksts tiktu labots vai dzēsts, rakstiet man.',
      ]],
      ['Servera žurnāli un drošība', [
        'Hostinga pakalpojumu sniedzējs nano.lv savos servera žurnālos saglabā tehniskus datus (IP adresi, laiku, lapu, pārlūku), lai vietne darbotos un būtu droša. Ja pārlūks bloķē kādu vietnes skriptu, tas nosūta tehnisku drošības ziņojumu (lapa un bloķētā adrese). Juridiskais pamats ir leģitīmās intereses — vietnes drošība un darbība.',
      ]],
    ],
    'cookies_h'  => 'Sīkdatnes un pārlūka krātuve',
    'cookies_th' => ['Nosaukums', 'Mērķis', 'Glabāšanas laiks'],
    'cookies' => [
      ['md_consent', 'Jūsu sīkdatņu izvēle (pārlūka krātuvē, nevis sīkdatne)', '12 mēneši'],
      ['_ga, _ga_*', 'Google Analytics — tikai ar jūsu piekrišanu', 'līdz 2 gadiem'],
      ['PHPSESSID', 'Veidlapu aizsardzība (atsauksme, viesu grāmata), nepieciešama', 'līdz pārlūka aizvēršanai'],
      ['lv18_seen', 'Neatkarības dienas apsveikums parādīts vienreiz', '1 diena'],
    ],
    'rights_h' => 'Jūsu tiesības',
    'rights'   => 'Jums ir tiesības piekļūt saviem datiem, tos labot vai dzēst, ierobežot to apstrādi, iebilst pret to un atsaukt piekrišanu. Rakstiet man uz ' . $CONTACT . '. Jums ir arī tiesības iesniegt sūdzību uzraudzības iestādei: Somijā Tietosuojavaltuutetun toimisto (tietosuoja.fi), Latvijā Datu valsts inspekcija (dvi.gov.lv).',
  ],
  'en' => [
    'title'   => 'Privacy policy — Manalas Dungeon',
    'desc'    => 'What data manalasdungeon.lv processes, why, and how to change your cookie choice.',
    'eyebrow' => 'manalasdungeon.lv',
    'h1'      => 'Privacy policy',
    'lead'    => 'This is a personal website. I collect as little data as possible and never sell it.',
    'updated' => 'Updated',
    'back'    => '← Back to the home page',
    'panel_h' => 'Your cookie choice',
    'nec'     => 'Necessary cookies', 'nec_d' => 'Needed to protect the forms. Always on.', 'nec_s' => 'Always',
    'ana'     => 'Analytics cookies (Google Analytics)', 'ana_d' => 'Help me see which pages are visited.',
    'st_yes'  => 'Allowed', 'st_no' => 'Declined', 'st_none' => 'No choice made yet', 'nojs' => 'Turn on JavaScript to change your choice.',
    'sections' => [
      ['Controller', [
        'The data controller is Marko Nakari, Heinola, Finland. Contact: ' . $CONTACT . '.',
      ]],
      ['Visitor statistics (Google Analytics)', [
        'If you accept, the site uses Google Analytics 4 (Google Ireland Limited) to see how many people visit, which pages they view and where they come from. Google receives your IP address, browser and device details and approximate location, and stores a pseudonymous identifier in cookies. Google Analytics keeps data about individual visits for 2 months, after which it is deleted; only aggregated statistics remain.',
        'Without your consent the Google script is not loaded and no data is sent to Google. The legal basis is your consent (GDPR Article 6(1)(a)). You can withdraw it at any time with the button on this page. Google may transfer data to the United States under the EU–US Data Privacy Framework.',
      ]],
      ['Feedback form', [
        'If you send feedback, your name, message and (if you give it) email address are sent to my email. They are not published or stored in a database. I keep the email only as long as needed to reply.',
        'To protect the form from spam, a hash of your IP address is kept for 24 hours; the IP address cannot be recovered from it.',
      ]],
      ['Guestbook', [
        'The guestbook stores your name, message and (if you give it) a link. An entry is published only after I approve it. A hash of your IP address is stored with the entry for spam protection.',
        'If you would like your entry corrected or deleted, write to me.',
      ]],
      ['Server logs and security', [
        'The hosting provider nano.lv keeps technical data (IP address, time, page, browser) in its server logs to run and secure the site. If a browser blocks one of the site’s scripts, it sends a technical security report (the page and the blocked address). The legal basis is legitimate interest in the site’s security and operation.',
      ]],
    ],
    'cookies_h'  => 'Cookies and browser storage',
    'cookies_th' => ['Name', 'Purpose', 'Kept for'],
    'cookies' => [
      ['md_consent', 'Your cookie choice (browser storage, not a cookie)', '12 months'],
      ['_ga, _ga_*', 'Google Analytics — only with your consent', 'up to 2 years'],
      ['PHPSESSID', 'Form protection (feedback, guestbook), necessary', 'until the browser closes'],
      ['lv18_seen', 'Independence Day greeting shown once', '1 day'],
    ],
    'rights_h' => 'Your rights',
    'rights'   => 'You have the right to access, correct or delete your data, to restrict or object to its processing, and to withdraw your consent. Write to me at ' . $CONTACT . '. You may also lodge a complaint with a supervisory authority: in Finland the Data Protection Ombudsman (tietosuoja.fi), in Latvia the Data State Inspectorate (dvi.gov.lv).',
  ],
  'fi' => [
    'title'   => 'Tietosuojaseloste — Manalas Dungeon',
    'desc'    => 'Mitä tietoja manalasdungeon.lv käsittelee, miksi ja miten evästevalinnan voi muuttaa.',
    'eyebrow' => 'manalasdungeon.lv',
    'h1'      => 'Tietosuojaseloste',
    'lead'    => 'Tämä on henkilökohtainen sivusto. Kerään mahdollisimman vähän tietoja enkä koskaan myy niitä.',
    'updated' => 'Päivitetty',
    'back'    => '← Etusivulle',
    'panel_h' => 'Evästevalintasi',
    'nec'     => 'Välttämättömät evästeet', 'nec_d' => 'Tarvitaan lomakkeiden suojaukseen. Aina käytössä.', 'nec_s' => 'Aina',
    'ana'     => 'Analytiikkaevästeet (Google Analytics)', 'ana_d' => 'Auttavat minua näkemään, mitä sivuja luetaan.',
    'st_yes'  => 'Sallittu', 'st_no' => 'Estetty', 'st_none' => 'Valintaa ei ole vielä tehty', 'nojs' => 'Ota JavaScript käyttöön muuttaaksesi valintaa.',
    'sections' => [
      ['Rekisterinpitäjä', [
        'Rekisterinpitäjä on Marko Nakari, Heinola. Yhteys: ' . $CONTACT . '.',
      ]],
      ['Kävijätilastot (Google Analytics)', [
        'Jos hyväksyt, sivusto käyttää Google Analytics 4:ää (Google Ireland Limited) nähdäkseen, kuinka moni käy sivustolla, mitä sivuja katsotaan ja mistä kävijät tulevat. Google saa IP-osoitteesi, selaimen ja laitteen tiedot sekä likimääräisen sijainnin ja tallentaa evästeisiin salanimisen tunnisteen. Google Analytics säilyttää yksittäisten käyntien tiedot 2 kuukautta, minkä jälkeen ne poistetaan; jäljelle jää vain koostettu tilasto.',
        'Ilman suostumustasi Googlen skriptiä ei ladata eikä Googlelle lähetetä mitään. Oikeusperuste on suostumuksesi (tietosuoja-asetuksen 6 artiklan 1 kohdan a alakohta). Voit perua sen milloin tahansa tämän sivun painikkeella. Google voi siirtää tietoja Yhdysvaltoihin EU:n ja Yhdysvaltojen tietosuojakehyksen perusteella.',
      ]],
      ['Palautelomake', [
        'Jos lähetät palautetta, nimesi, viestisi ja (jos annat sen) sähköpostiosoitteesi lähetetään sähköpostiini. Niitä ei julkaista eikä tallenneta tietokantaan. Säilytän viestin vain niin kauan kuin vastaaminen vaatii.',
        'Roskapostin estämiseksi IP-osoitteesi tiiviste säilytetään 24 tuntia; IP-osoitetta ei voi palauttaa siitä.',
      ]],
      ['Vieraskirja', [
        'Vieraskirjaan tallennetaan nimesi, viestisi ja (jos annat sen) linkki. Merkintä julkaistaan vasta, kun olen hyväksynyt sen. Roskapostin estämiseksi merkinnän yhteyteen tallennetaan IP-osoitteen tiiviste.',
        'Jos haluat merkintäsi korjattavan tai poistettavan, kirjoita minulle.',
      ]],
      ['Palvelinlokit ja tietoturva', [
        'Palveluntarjoaja nano.lv tallentaa palvelinlokeihin teknisiä tietoja (IP-osoite, aika, sivu, selain) sivuston toimintaa ja tietoturvaa varten. Jos selain estää jonkin sivuston skriptin, se lähettää teknisen tietoturvailmoituksen (sivu ja estetty osoite). Oikeusperuste on oikeutettu etu eli sivuston tietoturva ja toiminta.',
      ]],
    ],
    'cookies_h'  => 'Evästeet ja selaimen tallennustila',
    'cookies_th' => ['Nimi', 'Tarkoitus', 'Säilytysaika'],
    'cookies' => [
      ['md_consent', 'Evästevalintasi (selaimen tallennustila, ei eväste)', '12 kuukautta'],
      ['_ga, _ga_*', 'Google Analytics — vain suostumuksellasi', 'enintään 2 vuotta'],
      ['PHPSESSID', 'Lomakkeiden suojaus (palaute, vieraskirja), välttämätön', 'kunnes selain suljetaan'],
      ['lv18_seen', 'Itsenäisyyspäivän tervehdys näytetään kerran', '1 päivä'],
    ],
    'rights_h' => 'Oikeutesi',
    'rights'   => 'Sinulla on oikeus saada pääsy tietoihisi, korjata tai poistaa ne, rajoittaa niiden käsittelyä, vastustaa sitä ja perua suostumuksesi. Kirjoita osoitteeseen ' . $CONTACT . '. Voit myös tehdä valituksen valvontaviranomaiselle: Suomessa tietosuojavaltuutetun toimisto (tietosuoja.fi), Latviassa Datu valsts inspekcija (dvi.gov.lv).',
  ],
];
$T = $TT[$lang];
$updated = (new DateTimeImmutable(PR_UPDATED))->format('d.m.Y');
$home = page_url('', $lang);
?>
<!DOCTYPE html>
<html lang="<?= h($lang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?= head_analytics('') ?>
<title><?= h($T['title']) ?></title>
<meta name="description" content="<?= h($T['desc']) ?>">
<meta name="robots" content="index, follow">
<meta name="theme-color" content="#9E1B32">
<?= head_alternates('pr_url', $lang, '') ?>
<?= head_fonts('playfair-display-latin-wght-normal.woff2', '') ?>
<link rel="stylesheet" href="/<?= h(asset('privatums.css')) ?>">
</head>
<body>
  <div class="pr-band" aria-hidden="true"></div>

  <nav class="pr-top" aria-label="<?= h($T['h1']) ?>">
    <a class="pr-back" href="<?= h($home) ?>"><?= h($T['back']) ?></a>
    <span class="pr-lang">
      <?php foreach (SITE_LANGS as $lg): ?>
      <a href="<?= h(pr_url($lg)) ?>" hreflang="<?= $lg ?>" class="<?= $lg === $lang ? 'active' : '' ?>"><?= $flag[$lg] ?> <?= strtoupper($lg) ?></a>
      <?php endforeach; ?>
    </span>
  </nav>

  <main class="pr-main">
    <p class="pr-eyebrow"><?= h($T['eyebrow']) ?></p>
    <h1><?= h($T['h1']) ?></h1>
    <p class="pr-lead"><?= h($T['lead']) ?></p>
    <p class="pr-meta"><?= h($T['updated']) ?> <time datetime="<?= PR_UPDATED ?>"><?= h($updated) ?></time></p>

    <section class="pr-panel" id="evasteet" aria-labelledby="pr-panel-h">
      <h2 id="pr-panel-h"><?= h($T['panel_h']) ?></h2>
      <div class="pr-row">
        <div class="pr-row__text">
          <strong><?= h($T['nec']) ?></strong>
          <span><?= h($T['nec_d']) ?></span>
        </div>
        <span class="pr-switch pr-switch--locked">
          <input type="checkbox" checked disabled aria-label="<?= h($T['nec']) ?>">
          <span class="pr-switch__track" aria-hidden="true"></span>
        </span>
      </div>
      <div class="pr-row">
        <div class="pr-row__text">
          <strong id="pr-ana-l"><?= h($T['ana']) ?></strong>
          <span><?= h($T['ana_d']) ?></span>
          <span class="pr-status" data-consent-status
                data-yes="<?= h($T['st_yes']) ?>" data-no="<?= h($T['st_no']) ?>" data-none="<?= h($T['st_none']) ?>"><?= h($T['nojs']) ?></span>
        </div>
        <label class="pr-switch">
          <input type="checkbox" role="switch" data-consent-toggle aria-labelledby="pr-ana-l" disabled>
          <span class="pr-switch__track" aria-hidden="true"></span>
        </label>
      </div>
    </section>

    <?php foreach ($T['sections'] as [$head, $paras]): ?>
    <section class="pr-sec">
      <h2><?= h($head) ?></h2>
      <?php foreach ($paras as $p): ?><p><?= h($p) ?></p><?php endforeach; ?>
    </section>
    <?php endforeach; ?>

    <section class="pr-sec">
      <h2><?= h($T['cookies_h']) ?></h2>
      <div class="pr-table-wrap">
        <table class="pr-table">
          <thead><tr><?php foreach ($T['cookies_th'] as $th): ?><th scope="col"><?= h($th) ?></th><?php endforeach; ?></tr></thead>
          <tbody>
          <?php foreach ($T['cookies'] as [$n, $why, $keep]): ?>
            <tr><td><code><?= h($n) ?></code></td><td><?= h($why) ?></td><td><?= h($keep) ?></td></tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="pr-sec">
      <h2><?= h($T['rights_h']) ?></h2>
      <p><?= h($T['rights']) ?></p>
    </section>

  </main>
</body>
</html>
