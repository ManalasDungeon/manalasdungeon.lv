<?php
require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, kieli, <head>-palat
/**
 * Vidzemes apkārtne — matkailukohteiden linkkisivu (kolmikielinen lv/en/fi).
 * Kieli: ?lang=en / ?lang=fi, oletus lv.
 * Ryhmittely: novads → kaupungit → kategoriat (ravintolat, apskate, luonto, majoitus).
 * Linkit osoittavat virallisiin matkailulähteisiin (visitlimbazi.lv,
 * visitlimbazi.lv, visit.cesis.lv, entergauja.com, celotajs.lv jne.).
 */

/* ---- Kieli ---- */
$supported = ['lv', 'en', 'fi'];
$default   = 'lv';
$lang = $_GET['lang'] ?? $default;
if (!in_array($lang, $supported, true)) { $lang = $default; }
$base = 'https://manalasdungeon.lv/';
$homeUrl = $base . ($lang === $default ? '' : '?lang=' . $lang);   // paluu etusivulle samalla kielellä

/* ---- Käännökset (vain UI; kohteiden ja novadsien nimet pysyvät latviaksi) ---- */
$TT = [
  'lv' => [
    'title'   => 'Vidzemes apkārtne — Manalasdungeon.lv',
    'eyebrow' => 'Vidzemes piekraste · Cēsu puse',
    'h1'      => 'Piedzīvo Vidzemi',
    'lead'    => 'Ieteikumi apkārtnei — kur paēst, ko apskatīt, kur pastaigāt un palikt pa nakti Vidzemes piekrastē un Cēsu pusē. Saites ved uz oficiālajām tūrisma lapām un uzņēmumiem.',
    'guides'  => 'Oficiālie tūrisma ceļveži',
    'cat_eat'    => 'Kur paēst',
    'cat_see'    => 'Apskates vietas',
    'cat_nature' => 'Daba un takas',
    'cat_stay'   => 'Naktsmītnes',
    'cat_shop'   => 'Iepirkšanās un vietējie produkti',
    'hub'     => 'Vairāk',
    'back'    => 'Sākums',
    'note'    => 'Ārējās saites atveras jaunā cilnē. Pirms ceļojuma ieteicams pārbaudīt darba laikus tieši uzņēmumu lapās.',
    'role'    => 'Programmatūras izstrādātājs 2026',
    'loc'     => 'lv_LV',
  ],
  'en' => [
    'title'   => 'Around Vidzeme — Manalasdungeon.lv',
    'eyebrow' => 'Vidzeme coast · Cēsis area',
    'h1'      => 'Experience Vidzeme',
    'lead'    => 'Tips for the area — where to eat, what to see, where to walk and stay overnight on the Vidzeme coast and around Cēsis. Links lead to official tourism pages and businesses.',
    'guides'  => 'Official tourism guides',
    'cat_eat'    => 'Where to eat',
    'cat_see'    => 'Sights',
    'cat_nature' => 'Nature & trails',
    'cat_stay'   => 'Accommodation',
    'cat_shop'   => 'Shopping & local products',
    'hub'     => 'More',
    'back'    => 'Home',
    'note'    => 'External links open in a new tab. Before travelling, it is worth checking opening hours on the businesses’ own pages.',
    'role'    => 'Software developer 2026',
    'loc'     => 'en_US',
  ],
  'fi' => [
    'title'   => 'Vidzemen ympäristö — Manalasdungeon.lv',
    'eyebrow' => 'Vidzemen rannikko · Cēsisin seutu',
    'h1'      => 'Koe elämyksiä',
    'lead'    => 'Vinkkejä lähialueelle — missä syödä, mitä nähdä, missä kävellä ja yöpyä Vidzemen rannikolla ja Cēsisin seudulla. Linkit vievät virallisille matkailusivuille ja yrityksiin.',
    'guides'  => 'Viralliset matkailuoppaat',
    'cat_eat'    => 'Missä syödä',
    'cat_see'    => 'Nähtävyydet',
    'cat_nature' => 'Luonto ja polut',
    'cat_stay'   => 'Majoitus',
    'cat_shop'   => 'Ostokset ja paikallistuotteet',
    'hub'     => 'Lisää',
    'back'    => 'Etusivu',
    'note'    => 'Ulkoiset linkit avautuvat uuteen välilehteen. Tarkista aukioloajat yritysten omilta sivuilta ennen matkaa.',
    'role'    => 'Ohjelmistokehittäjä 2026',
    'loc'     => 'fi_FI',
  ],
];
$T = $TT[$lang];

/* ---- Historia: kolme tähteä, Brīvības piemineklis, vaakuna ja aikajana ----
 * Lähteet: Freedom Monument (Wikipedia), lvportals.lv (valsts ģerbonis),
 * Flag of Latvia, Battle of Cēsis 1919, daba.gov.lv (Gaujas NP), celotajs.lv (Cēsu pils). */
$HISTORY = [
  'lv' => [
    'link'    => 'Vidzeme vēsturē',
    'eyebrow' => 'Vēsture un simboli',
    'h2'      => 'Viena no trim zvaigznēm',
    'p1'      => 'Trīs zvaigznes simbolizē Latvijas trīs vēsturiskos novadus — Kurzemi, Vidzemi un Latgali. Zemgale atsevišķi netiek izcelta, jo tolaik to uzskatīja par Kurzemes daļu (Kurzeme-Zemgale), tāpat kā Latvijas valsts ģerbonī.',
    'p2'      => 'Brīvības piemineklī Rīgā „Milda“ tur virs galvas trīs zeltītas zvaigznes — viena no tām ir Vidzeme.',
    'stars'   => [['Kurzeme', 'ar Zemgali'], ['Vidzeme', '„vidus zeme“'], ['Latgale', 'Latgola']],
    'stars_aria' => 'Trīs zvaigznes: Kurzeme, Vidzeme un Latgale',
    'insc_label' => 'Brīvības pieminekļa uzraksts',
    'insc_tr'    => '',
    'cards'   => [
      ['t' => 'Brīvības piemineklis', 'f' => [
        'Atklāts 1935. gada 18. novembrī — Latvijas valsts proklamēšanas gadadienā.',
        'Tēlnieks Kārlis Zāle, arhitekts Ernests Štālbergs; augstums 42 m.',
        'Uzraksta „Tēvzemei un Brīvībai“ autors ir rakstnieks Kārlis Skalbe.',
        'Celts par tautas ziedojumiem; Zāles konkursa mets saucās „Mirdzi kā zvaigzne!“.',
      ]],
      ['t' => 'Valsts ģerbonis', 'f' => [
        'Apstiprināts 1921. gada 15. jūnijā Satversmes sapulcē.',
        'Sarkanais lauva — Kurzeme un Zemgale.',
        'Sudraba grifs — Vidzeme un Latgale.',
        'Uzlecošā saule — jaunas valsts dzimšana; trīs zvaigznes — novadu vienotība.',
      ]],
    ],
    'tl_title' => 'Vidzeme laika gaitā',
    'tl_intro' => 'Vārds „Vidzeme“ nozīmē „vidus zeme“.',
    'tl' => [
      ['līdz 13. gs.', 'Piekrastē un Gaujas lejtecē dzīvo lībieši, Tālavā — latgaļi.'],
      ['13. gs.',      'Cēsu pils kļūst par Livonijas ordeņa mestru rezidenci — vienu no stiprākajiem cietokšņiem Baltijā.'],
      ['ap 1279',      'Atskaņu hronika pie Cēsīm piemin sarkanu karogu ar baltu svītru — sarkanbaltsarkanā karoga leģenda.'],
      ['1629',         'Altmarkas pamiers — Vidzeme nonāk Zviedrijas varā (Zviedru Vidzeme).'],
      ['1721',         'Nīstades miers — Vidzeme kļūst par Krievijas impērijas daļu.'],
      ['1919',         'Cēsu kaujas (19.–23. jūnijs): igauņu un latviešu karaspēks uzvar landesvēru. Uzvaru atceras 22. jūnijā gan Latvijā, gan Igaunijā.'],
      ['1973',         'Dibināts Gaujas nacionālais parks — pirmais un vecākais Latvijā.'],
    ],
  ],
  'en' => [
    'link'    => 'Vidzeme in history',
    'eyebrow' => 'History & symbols',
    'h2'      => 'One of three stars',
    'p1'      => 'The three stars stand for Latvia’s three historical regions — Kurzeme, Vidzeme and Latgale. Zemgale is not shown separately, because at the time it was counted as part of Kurzeme (Kurzeme-Zemgale), just as on the Latvian coat of arms.',
    'p2'      => 'On the Freedom Monument in Riga, “Milda” holds three gilded stars above her head — one of them is Vidzeme.',
    'stars'   => [['Kurzeme', 'with Zemgale'], ['Vidzeme', '“middle land”'], ['Latgale', 'Latgola']],
    'stars_aria' => 'Three stars: Kurzeme, Vidzeme and Latgale',
    'insc_label' => 'Inscription on the Freedom Monument',
    'insc_tr'    => 'For Fatherland and Freedom',
    'cards'   => [
      ['t' => 'Freedom Monument', 'f' => [
        'Unveiled on 18 November 1935 — the anniversary of Latvia’s proclamation.',
        'Sculptor Kārlis Zāle, architect Ernests Štālbergs; 42 m tall.',
        'The inscription “Tēvzemei un Brīvībai” was written by the writer Kārlis Skalbe.',
        'Built with public donations; Zāle’s competition entry was called “Mirdzi kā zvaigzne!” (Shine like a star!).',
      ]],
      ['t' => 'Coat of arms', 'f' => [
        'Approved on 15 June 1921 by the Constitutional Assembly.',
        'Red lion — Kurzeme and Zemgale.',
        'Silver griffin — Vidzeme and Latgale.',
        'Rising sun — the birth of a new state; three stars — the unity of the regions.',
      ]],
    ],
    'tl_title' => 'Vidzeme through time',
    'tl_intro' => 'The name Vidzeme means “middle land”.',
    'tl' => [
      ['before 1200s', 'Livonians live on the coast and along the lower Gauja, Latgalians in Tālava.'],
      ['1200s',        'Cēsis Castle becomes the residence of the masters of the Livonian Order — one of the strongest fortresses in the Baltics.'],
      ['c. 1279',      'Near Cēsis, the Livonian Rhymed Chronicle mentions a red flag with a white stripe — the origin legend of the red-white-red flag.'],
      ['1629',         'Truce of Altmark — Vidzeme comes under Swedish rule (Swedish Livonia).'],
      ['1721',         'Treaty of Nystad — Vidzeme becomes part of the Russian Empire.'],
      ['1919',         'Battle of Cēsis (19–23 June): Estonian and Latvian forces defeat the Landeswehr. The victory is remembered on 22 June in both Latvia and Estonia.'],
      ['1973',         'Gauja National Park is founded — the first and oldest in Latvia.'],
    ],
  ],
  'fi' => [
    'link'    => 'Vidzeme historiassa',
    'eyebrow' => 'Historia ja symbolit',
    'h2'      => 'Yksi kolmesta tähdestä',
    'p1'      => 'Kolme tähteä kuvaavat Latvian kolmea historiallista aluetta: Kurzemea, Vidzemeä ja Latgalea. Zemgalea ei ole erikseen, koska se laskettiin tuolloin osaksi Kurzemea (Kurzeme-Zemgale), samaan tapaan kuin Latvian vaakunassa.',
    'p2'      => 'Riian Vapaudenmonumentissa ”Milda” kannattelee päänsä yllä kolmea kullattua tähteä — yksi niistä on Vidzeme.',
    'stars'   => [['Kurzeme', 'ja Zemgale'], ['Vidzeme', '”keskinen maa”'], ['Latgale', 'Latgola']],
    'stars_aria' => 'Kolme tähteä: Kurzeme, Vidzeme ja Latgale',
    'insc_label' => 'Vapaudenmonumentin kirjoitus',
    'insc_tr'    => 'Isänmaalle ja vapaudelle',
    'cards'   => [
      ['t' => 'Vapaudenmonumentti', 'f' => [
        'Paljastettiin 18. marraskuuta 1935 — Latvian itsenäisyysjulistuksen vuosipäivänä.',
        'Kuvanveistäjä Kārlis Zāle, arkkitehti Ernests Štālbergs; korkeus 42 m.',
        'Kirjoituksen ”Tēvzemei un Brīvībai” on laatinut kirjailija Kārlis Skalbe.',
        'Rakennettiin kansalaisten lahjoituksin; Zālen kilpailuehdotuksen nimi oli ”Mirdzi kā zvaigzne!” (Loista kuin tähti!).',
      ]],
      ['t' => 'Valtion vaakuna', 'f' => [
        'Hyväksyttiin 15. kesäkuuta 1921 perustuslakia säätävässä kokouksessa.',
        'Punainen leijona — Kurzeme ja Zemgale.',
        'Hopeinen aarnikotka — Vidzeme ja Latgale.',
        'Nouseva aurinko — uuden valtion syntymä; kolme tähteä — alueiden yhtenäisyys.',
      ]],
    ],
    'tl_title' => 'Vidzeme aikojen saatossa',
    'tl_intro' => 'Nimi Vidzeme tarkoittaa ”keskistä maata” (latv. vidus = keski-, keskellä; zeme = maa).',
    'tl' => [
      ['ennen 1200-lukua', 'Rannikolla ja Gaujan alajuoksulla asuvat liiviläiset, Tālavassa latgalit.'],
      ['1200-luku',        'Cēsisin linnasta tulee Liivinmaan ritarikunnan maamestarien asuinpaikka — yksi Baltian vahvimmista linnoituksista.'],
      ['n. 1279',          'Liivinmaan riimikronikka mainitsee Cēsisin lähellä punaisen lipun, jossa on valkoinen raita — puna-valko-punaisen lipun syntytarina.'],
      ['1629',             'Altmarkin aselepo — Vidzeme siirtyy Ruotsin vallan alle (Ruotsin Liivinmaa).'],
      ['1721',             'Uudenkaupungin rauha — Vidzemestä tulee osa Venäjän keisarikuntaa.'],
      ['1919',             'Cēsisin taistelu (19.–23. kesäkuuta): virolaiset ja latvialaiset joukot voittavat Landeswehrin. Voittoa muistetaan 22. kesäkuuta sekä Latviassa että Virossa.'],
      ['1973',             'Gaujan kansallispuisto perustetaan — Latvian ensimmäinen ja vanhin.'],
    ],
  ],
];
$H = $HISTORY[$lang];

/* ---- Viralliset oppaat (chip-rivi) ---- */
$GUIDES = [
  ['t' => 'Visit Salacgrīva', 'u' => 'https://www.visitlimbazi.lv/lv/salacgriva'],
  ['t' => 'Visit Limbaži',    'u' => 'https://www.visitlimbazi.lv/'],
  ['t' => 'Visit Cēsis',      'u' => 'https://visit.cesis.lv/'],
  ['t' => 'Enter Gauja',      'u' => 'https://www.entergauja.com/en'],
  ['t' => 'Lauku ceļotājs',   'u' => 'https://www.celotajs.lv/'],
  ['t' => 'Latvia Travel: Vidzeme', 'u' => 'https://www.latvia.travel/en/vidzeme-shoreline'],
];

/* ---- Kohteet: novads → kaupungit → kategoriat. Nimet latviaksi. ---- */
$AREAS = [
  [
    'novads' => 'Limbažu novads',
    'cities' => [
      [
        'name' => 'Salacgrīva',
        'hub'  => 'https://www.visitlimbazi.lv/lv/salacgriva',
        'cats' => [
          'eat' => [
            ['t' => 'Restorāns «Pļavas»',   'u' => 'https://www.celotajs.lv/e/restoransplavas'],
            ['t' => 'Pasēdnīca',            'u' => 'https://visitlimbazi.lv/en/catering?1307700454'],
            ['t' => 'Pie Lasa kundzes',     'u' => 'https://www.celotajs.lv/en/e/pie_lasa_kundzes'],
            ['t' => 'Fon Meks',             'u' => 'https://www.celotajs.lv/en/e/fon_meks'],
            ['t' => 'Il Capitano',          'u' => 'https://www.celotajs.lv/en/e/il_capitano'],
          ],
          'see' => [
            ['t' => 'Salacgrīvas bāka',     'u' => 'https://www.visitlimbazi.lv/lv/salacgriva'],
          ],
          'nature' => [
            ['t' => 'Randu pļavas',                     'u' => 'https://www.celotajs.lv/en/e/randu_plavu?lang=en'],
            ['t' => 'Ziemeļvidzemes biosfēras rezervāts', 'u' => 'https://www.celotajs.lv/e/zvbiosferasrezervats'],
          ],
          'stay' => [
            ['t' => 'Naktsmītnes — Visit Limbaži', 'u' => 'https://www.visitlimbazi.lv/en/accommodation'],
          ],
        ],
      ],
      [
        'name' => 'Limbaži',
        'hub'  => 'https://www.visitlimbazi.lv/',
        'cats' => [
          'eat'  => [
            ['t' => 'Kafejnīca (Rīgas iela 23)',         'u' => 'https://maps.app.goo.gl/XrRfbkU5bu9Q7cSH6'],
            ['t' => 'Bistro un ēdnīcas — Visit Limbaži', 'u' => 'https://visitlimbazi.lv/en/canteens-and-bistros'],
            ['t' => 'Kafejnīcas — Visit Limbaži',        'u' => 'https://www.visitlimbazi.lv/en/cafes'],
          ],
          'stay' => [ ['t' => 'Naktsmītnes — Visit Limbaži', 'u' => 'https://www.visitlimbazi.lv/en/accommodation'] ],
        ],
      ],
      [
        'name' => 'Tūja un piekraste',
        'hub'  => 'https://www.celotajs.lv/e/vidzemesjurmala',
        'cats' => [
          'nature' => [ ['t' => 'Vidzemes jūrmala', 'u' => 'https://www.celotajs.lv/e/vidzemesjurmala'] ],
          'stay'   => [ ['t' => 'Naktsmītnes — Visit Limbaži', 'u' => 'https://www.visitlimbazi.lv/en/accommodation'] ],
        ],
      ],
    ],
  ],
  [
    'novads' => 'Cēsu novads',
    'cities' => [
      [
        'name' => 'Cēsis',
        'hub'  => 'https://visit.cesis.lv/',
        'cats' => [
          'eat' => [
            ['t' => 'H.E. Vanadziņš — Ziemeļu restorāns', 'u' => 'https://www.entergauja.com/en/eat-drink/restaurants/h-e-vanadzins-ziemelu-restorans'],
            ['t' => 'Vecpilsētas restorāns',             'u' => 'https://turisms.cesis.lv/en/where-to-eat/restaurants/vecpilsetas-restorans'],
            ['t' => 'Bistro «CATA»',                     'u' => 'https://www.bistrocata.com/'],
            ['t' => 'Cafe 2 locals',                     'u' => 'https://www.celotajs.lv/e/cafe_2_locals'],
          ],
          'see' => [
            ['t' => 'Cēsu viduslaiku pils',  'u' => 'https://visit.cesis.lv/'],
          ],
          'nature' => [
            ['t' => 'Cīrulīšu dabas taka',   'u' => 'https://visit.cesis.lv/en/?p=19026'],
          ],
          'stay' => [
            ['t' => 'Naktsmītnes — Enter Gauja', 'u' => 'https://www.entergauja.com/en'],
          ],
        ],
      ],
      [
        'name' => 'Līgatne',
        'hub'  => 'https://visit.cesis.lv/',
        'cats' => [
          'see' => [
            ['t' => 'Padomju slepenais bunkurs', 'u' => 'https://bunkurs.lv/'],
          ],
          'nature' => [
            ['t' => 'Līgatnes dabas takas', 'u' => 'https://www.celotajs.lv/en/e/ligatnes_dabas_takas'],
          ],
        ],
      ],
      [
        'name' => 'Straupe',
        'hub'  => 'https://straupestirdzins.lv/',
        'cats' => [
          'shop' => [
            ['t' => 'Slow Food Straupes tirdziņš', 'u' => 'https://straupestirdzins.lv/'],
          ],
        ],
      ],
    ],
  ],
  [
    'novads' => 'Siguldas novads',
    'cities' => [
      [
        'name' => 'Sigulda',
        'hub'  => 'https://www.latvia.travel/lv/pilseta/sigulda',
        'cats' => [
          'see' => [
            ['t' => 'Turaidas muzejrezervāts', 'u' => 'https://www.turaida-muzejs.lv/'],
          ],
          'nature' => [
            ['t' => 'Gaujas nacionālais parks — Enter Gauja', 'u' => 'https://www.entergauja.com/en'],
          ],
        ],
      ],
    ],
  ],
  [
    'novads' => 'Valmieras novads',
    'cities' => [
      [
        'name' => 'Valmiera',
        'hub'  => 'https://visit.valmiera.lv/',
        'cats' => [
          'see' => [
            ['t' => 'Valmiermuiža', 'u' => 'https://www.valmiermuiza.lv/'],
          ],
          'nature' => [
            ['t' => 'Enter Gauja — Valmieras puse', 'u' => 'https://www.entergauja.com/en'],
          ],
        ],
      ],
    ],
  ],
];

/* ---- Yleinen "iepirkšanās" (ei kaupunkikohtaista; viralliset lähteet) ---- */
$SHOP = [
  ['t' => 'Latvijas tirgus kultūra — Latvia Travel', 'u' => 'https://www.latvia.travel/en/latvian-market-culture'],
  ['t' => 'Vietējie uzņēmumi — Lauku ceļotājs',      'u' => 'https://www.celotajs.lv/'],
];

/* Kategorioiden tulostusjärjestys ja otsikot */
$CAT_ORDER  = ['eat', 'see', 'nature', 'stay', 'shop'];
$CAT_LABELS = ['eat' => $T['cat_eat'], 'see' => $T['cat_see'], 'nature' => $T['cat_nature'], 'stay' => $T['cat_stay'], 'shop' => $T['cat_shop']];

/* ---- Apufunktiot ---- */
function vz_url(string $lg): string { return page_url('vidzeme', $lg); }
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?= head_analytics('    ') ?>
    <title><?= h($T['title']) ?></title>
    <meta name="description" content="<?= h($T['lead']) ?>">
    <meta name="author" content="Marko Nakari">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#9E1B32">
    <?= head_alternates('vz_url', $lang, '    ') ?>    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Manalas Dungeon">
    <meta property="og:title" content="<?= h($T['title']) ?>">
    <meta property="og:description" content="<?= h($T['lead']) ?>">
    <meta property="og:url" content="<?= h(vz_url($lang)) ?>">
    <meta property="og:locale" content="<?= h($T['loc']) ?>">

    <link rel="stylesheet" href="<?= h(asset('manala.css')) ?>">
    <?= head_fonts('playfair-display-latin-wght-normal.woff2', '    ') ?>    <link rel="stylesheet" href="<?= h(asset('vidzeme.css')) ?>">
</head>
<body class="vz-body">

    <!-- Paluu ja kielivalitsin — sama tyyli kuin Ziņas- ja Ceļojums-sivuilla -->
    <a class="vz-back" href="<?= h($homeUrl) ?>">&larr; <?= h($T['back']) ?></a>
    <div class="vz-lang">
        <?php foreach ($supported as $lg): ?>
        <a href="<?= h(vz_url($lg)) ?>" hreflang="<?= $lg ?>" class="<?= $lg === $lang ? 'active' : '' ?>"><?= strtoupper($lg) ?></a>
        <?php endforeach; ?>
    </div>

    <header class="vz-hero">
        <img class="vz-hero__img" src="<?= h(asset('images/vidzeme_banner.webp')) ?>"
             srcset="<?= h(asset('images/vidzeme_banner_800.webp')) ?> 800w, <?= h(asset('images/vidzeme_banner_1200.webp')) ?> 1200w, <?= h(asset('images/vidzeme_banner.webp')) ?> 1855w"
             sizes="100vw" alt="Latvija — Vidzeme, Cēsu pils" width="1855" height="576">
        <div class="vz-hero__inner">
            <h1 class="vz-hero__title"><?= h($T['h1']) ?></h1>
            <p class="vz-hero__sub"><?= h($T['eyebrow']) ?></p>
        </div>
    </header>

    <section class="vz-intro">
        <p class="vz-intro__text"><?= h($T['lead']) ?></p>
        <a class="vz-intro__hist" href="#vesture"><span aria-hidden="true">★</span> <?= h($H['link']) ?> <span aria-hidden="true">↓</span></a>
    </section>

    <!-- Viralliset oppaat -->
    <section class="vz-guides">
        <p class="vz-guides__head"><?= h($T['guides']) ?></p>
        <div class="vz-chips">
            <?php foreach ($GUIDES as $g): ?>
            <a class="vz-chip" href="<?= h($g['u']) ?>" target="_blank" rel="noopener"><?= h($g['t']) ?></a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Kohteet novadseittain -->
    <main class="vz-main">
        <?php foreach ($AREAS as $area): ?>
        <section class="vz-novads">
            <h2 class="vz-novads__title"><?= h($area['novads']) ?></h2>
            <div class="vz-cities">
                <?php foreach ($area['cities'] as $city): ?>
                <article class="vz-city">
                    <div class="vz-city__head">
                        <h3 class="vz-city__name"><?= h($city['name']) ?></h3>
                        <?php if (!empty($city['hub'])): ?>
                        <a class="vz-city__hub" href="<?= h($city['hub']) ?>" target="_blank" rel="noopener"><?= h($T['hub']) ?> &rarr;</a>
                        <?php endif; ?>
                    </div>
                    <?php foreach ($CAT_ORDER as $cat): ?>
                        <?php if (!empty($city['cats'][$cat])): ?>
                        <div class="vz-cat">
                            <p class="vz-cat__label"><?= h($CAT_LABELS[$cat]) ?></p>
                            <ul class="vz-links">
                                <?php foreach ($city['cats'][$cat] as $item): ?>
                                <li><a href="<?= h($item['u']) ?>" target="_blank" rel="noopener"><?= h($item['t']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endforeach; ?>

        <!-- Iepirkšanās (yleinen) -->
        <section class="vz-novads">
            <h2 class="vz-novads__title"><?= h($T['cat_shop']) ?></h2>
            <div class="vz-cities">
                <article class="vz-city">
                    <div class="vz-cat">
                        <ul class="vz-links">
                            <?php foreach ($SHOP as $item): ?>
                            <li><a href="<?= h($item['u']) ?>" target="_blank" rel="noopener"><?= h($item['t']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </article>
            </div>
        </section>
    </main>

    <!-- Historia: kolme tähteä (Brīvības piemineklis, vaakuna) + aikajana -->
    <?php $starPath = 'M12 1.6l2.95 6.55 7.15.72-5.38 4.8 1.55 7.03L12 17.1l-6.27 3.6 1.55-7.03L1.9 8.87l7.15-.72z'; ?>
    <section class="vz-history" id="vesture" aria-labelledby="vesture-title">
        <div class="vz-history__inner">
            <p class="vz-history__eyebrow"><?= h($H['eyebrow']) ?></p>
            <h2 class="vz-novads__title" id="vesture-title"><?= h($H['h2']) ?></h2>

            <div class="vz-stars-row">
                <div class="vz-stars" role="img" aria-label="<?= h($H['stars_aria']) ?>">
                    <?php foreach ($H['stars'] as $i => $s): ?>
                    <div class="vz-star<?= $i === 1 ? ' vz-star--on' : '' ?>">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?= $starPath ?>"/></svg>
                        <span class="vz-star__name"><?= h($s[0]) ?></span>
                        <span class="vz-star__note"><?= h($s[1]) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="vz-history__text">
                    <p><?= h($H['p1']) ?></p>
                    <p class="vz-history__milda"><?= h($H['p2']) ?></p>
                </div>
            </div>

            <figure class="vz-insc">
                <figcaption class="vz-insc__label"><?= h($H['insc_label']) ?></figcaption>
                <blockquote class="vz-insc__plate" lang="lv">
                    <p>TĒVZEMEI UN BRĪVĪBAI</p>
                </blockquote>
                <?php if ($H['insc_tr'] !== ''): ?>
                <p class="vz-insc__tr"><?= $lang === 'fi' ? '”' : '“' ?><?= h($H['insc_tr']) ?>”</p>
                <?php endif; ?>
                <p class="vz-insc__by">— Kārlis Skalbe</p>
            </figure>

            <div class="vz-hist-cards">
                <?php foreach ($H['cards'] as $c): ?>
                <article class="vz-hist-card">
                    <h3 class="vz-hist-card__title"><?= h($c['t']) ?></h3>
                    <ul class="vz-hist-card__list">
                        <?php foreach ($c['f'] as $f): ?>
                        <li><?= h($f) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
                <?php endforeach; ?>
            </div>

            <h3 class="vz-tl__title"><?= h($H['tl_title']) ?></h3>
            <p class="vz-tl__intro"><?= h($H['tl_intro']) ?></p>
            <ol class="vz-tl">
                <?php foreach ($H['tl'] as $row): ?>
                <li class="vz-tl__item">
                    <span class="vz-tl__year"><?= h($row[0]) ?></span>
                    <p class="vz-tl__text"><?= h($row[1]) ?></p>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <footer class="vz-footer">
        <p class="vz-footer__note"><?= h($T['note']) ?></p>
        <?php $gb = ['lv' => 'Viesu grāmata', 'en' => 'Guestbook', 'fi' => 'Vieraskirja']; ?>
        <a class="vz-footer-link" href="viesu_gramata<?= $lang === $default ? '' : '?lang=' . $lang ?>">
            <?= h($gb[$lang]) ?> <span class="vz-arrow" aria-hidden="true">&rarr;</span>
        </a>
        <p class="vz-footer__copy">&copy; <a href="https://manalasdungeon.lv">Marko Nakari</a> &middot; <?= h($T['role']) ?></p>
    </footer>

</body>
</html>
