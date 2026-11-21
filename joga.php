<?php
require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, kieli, <head>-palat
/**
 * Joga / Yoga / Jooga — joogan historia, filosofia ja vinyasa (kolmikielinen lv/en/fi).
 * Kieli: ?lang=en / ?lang=fi, oletus lv.
 * Linkitetty etusivulta (vasemman yläkulman nappi).
 * Studio: Asana-asema, Torikatu 5, 18100 Heinola — https://www.asana-asema.fi/
 */

/* ---- Kieli ---- */
$supported = ['lv', 'en', 'fi'];
$default   = 'lv';
$lang = $_GET['lang'] ?? $default;
if (!in_array($lang, $supported, true)) { $lang = $default; }
$base    = 'https://manalasdungeon.lv/';
$homeUrl = $base . ($lang === $default ? '' : '?lang=' . $lang);   // paluu etusivulle samalla kielellä

function jg_url(string $lg): string { return page_url('joga.php', $lg); }

$STUDIO = [
    'site'   => 'https://www.asana-asema.fi/',
    'classes'=> 'https://www.asana-asema.fi/studiotunnit',
    'addr'   => 'Torikatu 5, 18100 Heinola',
];

/* ---- Käännökset ---- */
$TT = [
  'lv' => [
    'loc'     => 'lv_LV',
    'title'   => 'Joga — vēsture, filozofija un vinyasa | Manalas Dungeon',
    'desc'    => 'Jogas vēsture un filozofija — un galvenokārt vinyasa, dinamiskā joga, ko praktizēju Heinolas jogas studijā Asana-asema.',
    'back'    => 'Sākums',
    'eyebrow' => 'Elpa · kustība · klātbūtne',
    'h1'      => 'Joga',
    'lead'    => 'Jogas vēsture un filozofija — un galvenokārt vinyasa, dinamiskā joga, ko praktizēju Heinolas jogas studijā Asana-asema.',
    'nav'     => ['history' => 'Vēsture', 'philosophy' => 'Filozofija', 'vinyasa' => 'Vinyasa', 'core' => 'Core', 'studio' => 'Asana-asema'],
    'photo_alt' => 'Marko gaisa jogas nodarbībā studijā Asana-asema, Heinolā',
    'credit'  => 'Uzņemts studijā Asana-asema, Heinolā',

    'h_history' => 'Vēsture',
    'history_intro' => 'Jogas saknes meklējamas senajā Indijā. Gadu tūkstošu laikā no meditatīva prāta disciplīnas tā izauga par praksi, ko šodien pazīst visā pasaulē.',
    'timeline' => [
      ['Senā Indija', 'No Vēdām līdz Upanišadām', 'Vārds „joga” cēlies no sanskrita saknes yuj — „savienot, iejūgt”. Upanišadās, piemēram, Kathas upanišadā, joga jau aprakstīta kā maņu un prāta savaldīšanas ceļš.'],
      ['Ap mūsu ēras sākumu', 'Bhagavadgīta', 'Bhagavadgīta iepazīstina ar jogas ceļiem: karma joga (nesavtīga darbība), bhakti joga (nodošanās) un džņana joga (zināšanas).'],
      ['Pirmie gadsimti m. ē.', 'Patandžali Jogas sūtras', 'Patandžali Jogas sūtras apkopo jogas filozofiju 196 īsos izteikumos. Pētnieku vērtējumi atšķiras, bet daudzi tekstu datē ap 400. gadu.'],
      ['15. gadsimts', 'Hatha joga', 'Hatha joga liek ķermeni centrā. Svātmārāmas „Hatha Yoga Pradīpikā” apraksta āsanas, elpošanas vingrinājumus (prānājāma) un attīrīšanas tehnikas.'],
      ['1893', 'Joga sasniedz Rietumus', 'Svami Vivekananda uzstājas Pasaules reliģiju parlamentā Čikāgā un iepazīstina Rietumu auditoriju ar jogas filozofiju.'],
      ['1930. gadi', 'Krišnamačarja Maisūrā', 'T. Krišnamačarja māca Maisūras pilī. Viņa plūstošais, ar elpu saistītais veids, kā savienot āsanas, ir daudzu mūsdienu stilu — arī vinyasa — pirmsākums. Viņa skolēni bija B. K. S. Ajengars, K. Patabhi Džoiss un Indra Devi.'],
      ['1948', 'Aštanga', 'K. Patabhi Džoiss izveido savu Aštangas sistēmu, kurā saules sveiciena kustības savieno noteiktu āsanu secību.'],
      ['2014–2016', 'Pasaules mantojums', 'ANO pasludina 21. jūniju par Starptautisko jogas dienu (atzīmē kopš 2015. gada), un 2016. gadā UNESCO iekļauj jogu nemateriālā kultūras mantojuma sarakstā.'],
    ],

    'h_philosophy' => 'Filozofija',
    'philosophy_intro' => 'Sākotnēji joga nebija vingrošana, bet garīgs ceļš — veids, kā nomierināt prātu un redzēt skaidri. Āsanas, ko mūsdienās praktizējam zālē, ir tikai viena daļa no plašāka veseluma.',
    'quote_tr'  => 'Joga ir prāta svārstību norimšana.',
    'quote_src' => 'Jogas sūtras 1.2',
    'h_limbs'   => 'Patandžali astoņi posmi (aštānga)',
    'limbs' => [
      ['Jama', 'Attiecības ar citiem: nevardarbība, patiesīgums'],
      ['Nijama', 'Attiecības ar sevi: tīrība, apmierinātība, pašizziņa'],
      ['Āsana', 'Pozīcija — stabila un ērta'],
      ['Prānājāma', 'Elpas vadīšana'],
      ['Pratjāhāra', 'Maņu vēršana uz iekšu'],
      ['Dhārana', 'Koncentrēšanās'],
      ['Dhjāna', 'Meditācija'],
      ['Samādhi', 'Iegrimšana, vienotība'],
    ],

    'h_vinyasa' => 'Vinyasa — mana joga',
    'vinyasa_p' => [
      'Tagad galvenokārt praktizēju dinamisko jogu — vinyasa jogu. Sanskrita vārds vinyāsa nozīmē „novietot īpašā veidā”: katra pozīcija apzināti seko iepriekšējai, un elpa vada kustību. Ieelpa atver un paceļ, izelpa noliec un nolaiž.',
      'Dinamiskajā jogā pozās ilgi neuzkavējas — tās savieno vienā plūstošā kustībā. Prakse sasilda ķermeni, veido spēku, kustīgumu un līdzsvaru, un, kad uzmanība paliek pie elpas, prāts nomierinās pat kustībā.',
      'Vinyasa cēlusies no Krišnamačarjas un Patabhi Džoisa Aštangas, taču atšķirībā no Aštangas noteiktās secības vinyasa flow nodarbībā kārtība mainās. Tāpēc katra nodarbība ir mazliet citāda.',
    ],
    'elements' => [
      ['Surja namaskāra', 'Saules sveiciens — nodarbības mugurkauls'],
      ['Udžaji elpa', 'Dzirdama, nomierinoša elpa caur degunu'],
      ['Drišti', 'Skatiena punkts, kas palīdz līdzsvaram'],
      ['Flow', 'Kustības saplūst vienā plūsmā'],
    ],
    'aerial_note' => 'Esmu izmēģinājis arī gaisa jogu — lapas augšdaļā redzamais foto uzņemts studijas Asana-asema gaisa jogas nodarbībā. Tomēr mans stils ir dinamiskā joga uz paklājiņa.',
    'h_surya'     => 'Saules sveiciens A — elpa ar katru kustību',
    'surya_intro' => 'Saules sveiciens ir vinyasa pamats: katrai kustībai ir sava ieelpa vai izelpa.',
    'b_in' => 'ieelpa', 'b_out' => 'izelpa',
    'surya' => [
      ['in', 'Ūrdhva hastāsana', 'Rokas augšup'],
      ['out', 'Uttānāsana', 'Saliekums uz priekšu'],
      ['in', 'Ardha uttānāsana', 'Pusceļā augšup, mugura taisna'],
      ['out', 'Chaturaṅga daṇḍāsana', 'Kāpiens atpakaļ un nolaišanās'],
      ['in', 'Ūrdhva mukha śvānāsana', 'Suns ar purnu uz augšu'],
      ['out', 'Adho mukha śvānāsana', 'Suns ar purnu uz leju — piecas elpas'],
      ['in', 'Ardha uttānāsana', 'Kāpiens uz priekšu, pusceļā augšup'],
      ['out', 'Uttānāsana', 'Saliekums uz priekšu'],
      ['in', 'Ūrdhva hastāsana', 'Piecelšanās, rokas augšup'],
      ['out', 'Samasthiti', 'Atpakaļ stāvus'],
    ],
    'h_styles' => 'Vinyasa citu stilu vidū',
    'styles' => [
      ['Hatha', 'Mierīgāks temps, pozās uzkavējas ilgāk.', false],
      ['Aštanga', 'Katru reizi viena un tā pati noteikta secība un stingrs elpas ritms.', false],
      ['Vinyasa', 'Plūstoša, saistīta ar elpu un mainīga — mans stils.', true],
      ['Power', 'Uz spēku vērsta dinamiskā joga, kas smeļas iedvesmu Aštangā.', false],
      ['Jin', 'Pasīva un lēna, pozas notur 3–5 minūtes.', false],
    ],
    'h_arc' => 'Nodarbības loks',
    'arc'   => ['Iezemēšanās', 'Iesildīšanās', 'Dinamiskā plūsma', 'Jin pozas', 'Atslābināšanās (šavāsana)'],

    'h_core' => 'Core joga — iekšējā uguns',
    'core_p' => [
      'Core joga stiprina ķermeņa centra dziļos atbalsta muskuļus — vēderu, muguru un iegurņa pamatni — ar jogas āsanām, bez neviena tradicionāla vēdera preses vingrinājuma. Spēcīgs centrs balsta katru pāreju — no plankas uz čaturangu, lēcienos un līdzsvara pozās.',
      'Studijas Asana-asema Core jogas nodarbībās sola sīvu dedzinošu sajūtu visā ķermeņa centrā — un tieši tur jogas filozofija nonāk uz paklājiņa. Jogas tradīcijā šai sajūtai ir vārds: tapas.',
    ],
    'niyama' => [
      ['icon' => 'flame', 'name' => 'Tapas', 'tag' => 'Iekšējā uguns', 'root' => 'No sanskrita saknes tap — karsēt, dedzināt.', 'text' => 'Tapas ir viena no nijamām: apņēmīga, kvēlojoša piepūle, kas pārveido praktizētāju. Kā uguns attīra metālu, tā karsta un pacietīga prakse izdedzina to, kas vairs nekalpo.', 'skt' => 'kāya-indriya-siddhir aśuddhi-kṣayāt tapasaḥ', 'tr' => 'Ar tapas palīdzību izzūd nešķīstības, un ķermenis un maņas sasniedz pilnību.', 'src' => 'Jogas sūtras 2.43', 'mat' => 'Uz paklājiņa: paliec plankā vai laivas pozā vēl vienu elpu, kad centrs deg — mierīgi, ar vienmērīgu elpu. Dedzināšana ir pārmaiņas, nevis sods.'],
      ['icon' => 'eye', 'name' => 'Svādhyāya', 'tag' => 'Pašizziņa', 'root' => 'sva — sevis, adhjāja — pētīšana, lasīšana.', 'text' => 'Svādhjāja ir vēl viena nijama: godīga sevis vērošana. Sākotnēji tā nozīmēja arī svēto tekstu studēšanu — izpratni, kas atspoguļo paša pieredzi.', 'skt' => 'svādhyāyād iṣṭa-devatā-samprayogaḥ', 'tr' => 'Ar pašizziņu rodas saikne ar izvēlēto dievību.', 'src' => 'Jogas sūtras 2.44', 'mat' => 'Uz paklājiņa: vēro, kas notiek šodien. Vai vēders trīc, vai aizturi elpu, vai ego spiež uz priekšu? Pašvērtējums pasaka, kad pielikt uguni un kad atlaist.'],
    ],
    'h_kriya' => 'Krija joga — darbības joga',
    'kriya_intro' => 'Patandžali savieno tapas un svādhjāju ar trešo daļu. Kopā tās veido krija jogu — prakses jogu.',
    'kriya' => [['Tapas', 'piepūle'], ['Svādhyāya', 'apzinātība'], ['Īśvara-praṇidhāna', 'nodošanās, atlaišana']],
    'kriya_sum' => 'Krija joga', 'kriya_skt' => 'tapaḥ-svādhyāyeśvara-praṇidhānāni kriyā-yogaḥ', 'kriya_src' => 'Jogas sūtras 2.1',
    'h_fire' => 'Uguns idejas',
    'fire_intro' => 'Tapas un svādhjāja nav vienīgās uguns idejas. Arī šie jogas jēdzieni pieder tai pašai ugunīgajai praksei.',
    'fire' => [
      ['Agni', 'Pārmaiņu uguns', 'Vēdiskajā domāšanā uguns nogādā upuri dieviem un to pārveido. Ajūrvēdā džatharāgni ir gremošanas uguns — līdzība tam, kā mēs „sagremojam” arī pieredzi.'],
      ['Maṇipūra', 'Nabas čakra', 'Trešā čakra, „dārgakmeņu pilsēta”, atrodas nabas apvidū, un tās elements ir uguns. Tradīcijā to saista ar gribu un iekšējo spēku — tieši uz to vērsta Core joga.'],
      ['Uḍḍīyāna bandha', 'Augšup lidojošā slēdzene', '„Hatha Yoga Pradīpikā” aprakstītā vēdera ievilkšana. Core jogas nodarbībās tās vieglā forma — nabas ievilkšana mugurkaula virzienā — atbalsta pozas un pārejas.'],
      ['Kapālabhāti', 'Attīrošā elpa', 'Attīrīšanas tehnika, kurā izelpu veic asi ar vēderu. To dēvē par ugunīgo elpu, un to praktizē skolotāja vadībā.'],
      ['Sthira sukham āsanam', 'Stabila un ērta', 'Jogas sūtras 2.46: pozai jābūt stabilai un ērtai. Ugunij vajag līdzsvaru — spēku bez sasprindzinājuma.'],
      ['Abhyāsa & vairāgya', 'Prakse un atlaišana', 'Jogas sūtras 1.12: prāts nomierinās ar neatlaidīgu praksi un nepieķeršanos. Uguns deg vienmērīgi, ja to baro katru dienu un netvarstās pēc rezultātiem.'],
      ['Ahiṃsā', 'Nevardarbība', 'Pirmā jama nosaka tapas robežu: uguns drīkst sildīt, bet ne apdedzināt. Ieklausies ķermenī — īpaši plaukstu locītavās un muguras lejasdaļā.'],
    ],
    'h_poses' => 'Ugunīgas āsanas',
    'poses' => [['Phalakāsana', 'Planka'], ['Chaturaṅga daṇḍāsana', 'Zemā planka'], ['Vasiṣṭhāsana', 'Sānu planka'], ['Nāvāsana', 'Laiva'], ['Utkaṭāsana', '„Spēcīgā poza” jeb krēsls']],
    'flame' => 'Tapas aizdedz liesmu, svādhjāja ielūkojas tajā.',

    'h_studio' => 'Asana-asema, Heinola',
    'studio_p' => 'Dinamisko jogu praktizēju Heinolas jogas studijā Asana-asema, kas atrodas Torikatu ielā. Studijas mācību pamatā ir vinyasa stils: kustība plūst elpas ritmā, bieži mūzikas pavadībā, un tēmas mainās — no līdzsvara līdz pagriezieniem un ķermeņa centram. Studijā ir arī joga iesācējiem, Core joga, jin un power joga, kā arī gaisa joga.',
    'welcome' => 'Nāc izmēģināt bez bažām — iepriekšēja pieredze nav vajadzīga, un iesācējiem ir atsevišķas nodarbības.',
    'addr_label' => 'Adrese',
    'btn_site'    => 'Studijas mājaslapa',
    'btn_classes' => 'Nodarbību apraksti',

    'note' => 'Vēstures datējumi ir aptuveni un balstīti uz pētnieku vērtējumiem.',
    'home_btn' => 'Atpakaļ uz sākumu',
  ],

  'en' => [
    'loc'     => 'en_US',
    'title'   => 'Yoga — history, philosophy and Vinyasa | Manalas Dungeon',
    'desc'    => 'The history and philosophy of yoga — and above all Vinyasa, the dynamic yoga I practice at the Asana-asema studio in Heinola.',
    'back'    => 'Home',
    'eyebrow' => 'Breath · movement · presence',
    'h1'      => 'Yoga',
    'lead'    => 'The history and philosophy of yoga — and above all Vinyasa, the dynamic yoga I practice at the Asana-asema studio in Heinola.',
    'nav'     => ['history' => 'History', 'philosophy' => 'Philosophy', 'vinyasa' => 'Vinyasa', 'core' => 'Core', 'studio' => 'Asana-asema'],
    'photo_alt' => 'Marko in an aerial yoga class at Asana-asema, Heinola',
    'credit'  => 'Taken at Asana-asema, Heinola',

    'h_history' => 'History',
    'history_intro' => 'Yoga has its roots in ancient India. Over thousands of years it grew from a meditative discipline of the mind into a practice known all over the world.',
    'timeline' => [
      ['Ancient India', 'From the Vedas to the Upanishads', 'The word yoga comes from the Sanskrit root yuj, “to join, to yoke”. In the Upanishads, such as the Katha Upanishad, yoga is already described as a path to mastering the senses and the mind.'],
      ['Around the turn of the era', 'Bhagavad Gita', 'The Bhagavad Gita presents paths of yoga: karma yoga (selfless action), bhakti yoga (devotion) and jnana yoga (knowledge).'],
      ['First centuries CE', 'Patanjali’s Yoga Sutras', 'Patanjali’s Yoga Sutras distill the philosophy of yoga into 196 short aphorisms. Scholarly estimates vary, but many date the text to around 400 CE.'],
      ['15th century', 'Hatha yoga', 'Hatha yoga puts the body at the center. Svatmarama’s Hatha Yoga Pradipika describes asanas, breathing practices (pranayama) and cleansing techniques.'],
      ['1893', 'Yoga reaches the West', 'Swami Vivekananda speaks at the Parliament of the World’s Religions in Chicago, introducing yoga philosophy to Western audiences.'],
      ['1930s', 'Krishnamacharya in Mysore', 'T. Krishnamacharya teaches at the Mysore Palace. His flowing, breath-linked way of joining asanas is the source of many modern styles — Vinyasa included. His students included B. K. S. Iyengar, K. Pattabhi Jois and Indra Devi.'],
      ['1948', 'Ashtanga', 'K. Pattabhi Jois creates his Ashtanga system, in which the movements of the sun salutation link a fixed sequence of asanas.'],
      ['2014–2016', 'World heritage', 'The UN declares June 21 the International Day of Yoga (celebrated since 2015), and in 2016 UNESCO adds yoga to its list of Intangible Cultural Heritage.'],
    ],

    'h_philosophy' => 'Philosophy',
    'philosophy_intro' => 'Yoga was not originally exercise but a spiritual path — a way to quiet the mind and see clearly. The asanas practiced in studios today are only one part of a wider whole.',
    'quote_tr'  => 'Yoga is the stilling of the fluctuations of the mind.',
    'quote_src' => 'Yoga Sutras 1.2',
    'h_limbs'   => 'Patanjali’s eight limbs (ashtanga)',
    'limbs' => [
      ['Yama', 'How we treat others: non-violence, truthfulness'],
      ['Niyama', 'How we treat ourselves: purity, contentment, self-study'],
      ['Asana', 'Posture — steady and comfortable'],
      ['Pranayama', 'Guiding the breath'],
      ['Pratyahara', 'Turning the senses inward'],
      ['Dharana', 'Concentration'],
      ['Dhyana', 'Meditation'],
      ['Samadhi', 'Absorption, oneness'],
    ],

    'h_vinyasa' => 'Vinyasa — my yoga',
    'vinyasa_p' => [
      'These days I mainly practice dynamic yoga — Vinyasa. The Sanskrit word vinyāsa means “to place in a special way”: each pose follows the last with intention, and the breath carries the movement. The inhale opens and lifts; the exhale folds and lowers.',
      'In dynamic yoga you don’t linger in the poses; they are linked into one smooth flow. The practice warms the body and builds strength, mobility and balance — and when your attention stays on the breath, the mind settles even in motion.',
      'Vinyasa grew out of the Ashtanga of Krishnamacharya and Pattabhi Jois, but unlike Ashtanga’s fixed sequence, the order changes from one Vinyasa flow class to the next — so every class is a little different.',
    ],
    'elements' => [
      ['Surya namaskar', 'The sun salutation — backbone of the class'],
      ['Ujjayi breath', 'An audible, calming breath through the nose'],
      ['Drishti', 'A gaze point that steadies balance'],
      ['Flow', 'Movements merge into one stream'],
    ],
    'aerial_note' => 'I have tried aerial yoga too — the photo at the top of this page was taken in an aerial yoga class at Asana-asema. But my own practice is dynamic yoga on the mat.',
    'h_surya'     => 'Sun salutation A — one breath per movement',
    'surya_intro' => 'The sun salutation is the foundation of Vinyasa: every movement has its own inhale or exhale.',
    'b_in' => 'inhale', 'b_out' => 'exhale',
    'surya' => [
      ['in', 'Ūrdhva hastāsana', 'Arms up'],
      ['out', 'Uttānāsana', 'Forward fold'],
      ['in', 'Ardha uttānāsana', 'Half lift, long spine'],
      ['out', 'Chaturaṅga daṇḍāsana', 'Step back and lower'],
      ['in', 'Ūrdhva mukha śvānāsana', 'Upward-facing dog'],
      ['out', 'Adho mukha śvānāsana', 'Downward-facing dog — five breaths'],
      ['in', 'Ardha uttānāsana', 'Step forward, half lift'],
      ['out', 'Uttānāsana', 'Forward fold'],
      ['in', 'Ūrdhva hastāsana', 'Rise, arms up'],
      ['out', 'Samasthiti', 'Back to standing'],
    ],
    'h_styles' => 'Vinyasa among other styles',
    'styles' => [
      ['Hatha', 'A calmer pace, with poses held longer.', false],
      ['Ashtanga', 'The same fixed series every time, with a strict breath count.', false],
      ['Vinyasa', 'Flowing, breath-linked and ever-changing — my style.', true],
      ['Power', 'Strength-focused dynamic yoga that draws on Ashtanga.', false],
      ['Yin', 'Passive and slow, with poses held for 3–5 minutes.', false],
    ],
    'h_arc' => 'The arc of a class',
    'arc'   => ['Grounding', 'Warm-up', 'Dynamic flow', 'Yin poses', 'Relaxation (savasana)'],

    'h_core' => 'Core yoga — the inner fire',
    'core_p' => [
      'Core yoga strengthens the deep supporting muscles of the body’s center — abdomen, back and pelvic floor — through yoga asanas, without a single traditional crunch. A strong center carries every transition — from plank to chaturanga, through jumps and balancing poses.',
      'Asana-asema’s Core yoga classes promise a sharp burn through the whole core — and that is exactly where yoga philosophy steps onto the mat. In the yoga tradition, that burn has a name: tapas.',
    ],
    'niyama' => [
      ['icon' => 'flame', 'name' => 'Tapas', 'tag' => 'Inner fire', 'root' => 'From the Sanskrit root tap — to heat, to burn.', 'text' => 'Tapas is one of the niyamas: committed, glowing effort that transforms the practitioner. As fire refines metal, hot and patient practice burns away what no longer serves.', 'skt' => 'kāya-indriya-siddhir aśuddhi-kṣayāt tapasaḥ', 'tr' => 'Through tapas, impurities are destroyed and the body and senses reach perfection.', 'src' => 'Yoga Sutras 2.43', 'mat' => 'On the mat: you stay in plank or boat for one more breath while your core burns — calmly, breath steady. The burn is transformation, not punishment.'],
      ['icon' => 'eye', 'name' => 'Svādhyāya', 'tag' => 'Self-study', 'root' => 'sva = self, adhyāya = study, reading.', 'text' => 'Svadhyaya is also one of the niyamas: honest self-observation. Originally it also meant studying sacred texts — understanding that mirrors your own experience.', 'skt' => 'svādhyāyād iṣṭa-devatā-samprayogaḥ', 'tr' => 'Through self-study comes union with one’s chosen deity.', 'src' => 'Yoga Sutras 2.44', 'mat' => 'On the mat: notice what is happening today. Are your abs shaking, are you holding your breath, is your ego pushing ahead? Self-assessment tells you when to add fire and when to ease off.'],
    ],
    'h_kriya' => 'Kriya yoga — the yoga of action',
    'kriya_intro' => 'Patanjali joins tapas and svadhyaya with a third part. Together they form kriya yoga — the yoga of practice.',
    'kriya' => [['Tapas', 'effort'], ['Svādhyāya', 'awareness'], ['Īśvara-praṇidhāna', 'surrender, letting go']],
    'kriya_sum' => 'Kriya yoga', 'kriya_skt' => 'tapaḥ-svādhyāyeśvara-praṇidhānāni kriyā-yogaḥ', 'kriya_src' => 'Yoga Sutras 2.1',
    'h_fire' => 'Ideas of fire',
    'fire_intro' => 'Tapas and svadhyaya are not the only ideas of fire. These yoga concepts belong to the same fiery practice.',
    'fire' => [
      ['Agni', 'The fire of change', 'In Vedic thought, fire carries the offering to the gods and transforms it. In Ayurveda, jāṭharāgni is the digestive fire — an image of how we also “digest” experience.'],
      ['Maṇipūra', 'The navel chakra', 'The third chakra, the “city of jewels”, sits at the navel and its element is fire. Tradition links it to will and inner strength — exactly where Core yoga is aimed.'],
      ['Uḍḍīyāna bandha', 'The upward-flying lock', 'The abdominal lift described in the Hatha Yoga Pradipika. In Core yoga classes its gentle form — drawing the navel towards the spine — supports poses and transitions.'],
      ['Kapālabhāti', 'The shining breath', 'A cleansing technique with sharp exhalations driven by the belly. It is called the fiery breath and is practiced under a teacher’s guidance.'],
      ['Sthira sukham āsanam', 'Steady and comfortable', 'Yoga Sutra 2.46: a posture should be steady and comfortable. Fire needs balance — strength without strain.'],
      ['Abhyāsa & vairāgya', 'Practice and letting go', 'Yoga Sutra 1.12: the mind is stilled through persistent practice and non-attachment. The fire burns evenly when it is fed every day without grasping at results.'],
      ['Ahiṃsā', 'Non-harming', 'The first yama sets the limit for tapas: fire may warm but must not burn. Listen to your body — especially your wrists and lower back.'],
    ],
    'h_poses' => 'Fiery asanas',
    'poses' => [['Phalakāsana', 'Plank'], ['Chaturaṅga daṇḍāsana', 'Low plank'], ['Vasiṣṭhāsana', 'Side plank'], ['Nāvāsana', 'Boat'], ['Utkaṭāsana', '“Fierce pose”, or chair']],
    'flame' => 'Tapas lights the flame; svadhyaya looks into it.',

    'h_studio' => 'Asana-asema, Heinola',
    'studio_p' => 'I practice dynamic yoga at Asana-asema, a yoga studio on Torikatu in Heinola. Its teaching is built on the Vinyasa style: movement flows with the breath, often to music, and the themes change from balance to twists and core work. The studio also offers yoga for beginners, Core yoga, yin and power yoga, and aerial yoga.',
    'welcome' => 'You’re welcome to come and give it a try — no experience needed, and there are classes just for beginners.',
    'addr_label' => 'Address',
    'btn_site'    => 'Studio website',
    'btn_classes' => 'Class descriptions',

    'note' => 'Historical dates are approximate and based on scholarly estimates.',
    'home_btn' => 'Back to home',
  ],

  'fi' => [
    'loc'     => 'fi_FI',
    'title'   => 'Jooga – historia, filosofia ja vinyasa | Manalas Dungeon',
    'desc'    => 'Joogan historia ja filosofia – ja ennen kaikkea vinyasa, dynaaminen jooga, jota harrastan heinolalaisella Asana-asemalla.',
    'back'    => 'Etusivu',
    'eyebrow' => 'Hengitys · liike · läsnäolo',
    'h1'      => 'Jooga',
    'lead'    => 'Joogan historiaa ja filosofiaa – ja ennen kaikkea vinyasa, dynaaminen jooga, jota harrastan heinolalaisella Asana-asemalla.',
    'nav'     => ['history' => 'Historia', 'philosophy' => 'Filosofia', 'vinyasa' => 'Vinyasa', 'core' => 'Core', 'studio' => 'Asana-asema'],
    'photo_alt' => 'Marko ilmajoogatunnilla Asana-asemalla Heinolassa',
    'credit'  => 'Kuvattu Asana-asemalla Heinolassa',

    'h_history' => 'Historia',
    'history_intro' => 'Joogan juuret ovat muinaisessa Intiassa. Vuosituhansien aikana se kasvoi mielen meditatiivisesta harjoituksesta käytännöksi, jonka koko maailma tuntee.',
    'timeline' => [
      ['Muinainen Intia', 'Vedoista Upanishadeihin', 'Sana jooga juontuu sanskritin juuresta yuj, ”yhdistää, valjastaa”. Upanishadeissa, kuten Katha-upanishadissa, jooga kuvataan jo aistien ja mielen hallinnan tienä.'],
      ['Ajanlaskun taite', 'Bhagavad Gītā', 'Bhagavad Gītā esittelee joogan polkuja: karmajooga (epäitsekäs toiminta), bhaktijooga (antautuminen) ja jñānajooga (tieto).'],
      ['Ensimmäiset vuosisadat jaa.', 'Patañjalin Joogasutrat', 'Patañjalin Joogasutrat tiivistävät joogan filosofian 196 lyhyeen mietelauseeseen. Tutkijoiden arviot vaihtelevat, mutta moni ajoittaa tekstin noin vuoteen 400.'],
      ['1400-luku', 'Hathajooga', 'Hathajooga nostaa kehon keskiöön. Svātmārāman Hatha Yoga Pradīpikā kuvaa asanoita, hengitysharjoituksia (prāṇāyāma) ja puhdistustekniikoita.'],
      ['1893', 'Jooga saapuu länteen', 'Swami Vivekananda puhuu maailman uskontojen parlamentissa Chicagossa ja tuo joogan filosofian länsimaisen yleisön tietoisuuteen.'],
      ['1930-luku', 'Krishnamacharya Mysoressa', 'T. Krishnamacharya opettaa Mysoren palatsissa. Hänen virtaava, hengitykseen sidottu tapansa liittää asanat yhteen on monen modernin tyylin – myös vinyasan – lähde. Hänen oppilaitaan olivat B. K. S. Iyengar, K. Pattabhi Jois ja Indra Devi.'],
      ['1948', 'Ashtanga', 'K. Pattabhi Jois luo Ashtanga-järjestelmänsä, jossa aurinkotervehdyksen liikkeet sitovat kiinteän asanasarjan yhteen.'],
      ['2014–2016', 'Maailmanperintö', 'YK julistaa 21. kesäkuun kansainväliseksi joogapäiväksi (vietetty vuodesta 2015), ja vuonna 2016 UNESCO lisää joogan aineettoman kulttuuriperinnön luetteloon.'],
    ],

    'h_philosophy' => 'Filosofia',
    'philosophy_intro' => 'Jooga ei alun perin ollut liikuntaa vaan henkinen polku – tapa hiljentää mieli ja nähdä selkeästi. Asanat, joita nykyään harjoitellaan joogasaleilla, ovat vain yksi osa laajempaa kokonaisuutta.',
    'quote_tr'  => 'Jooga on mielen liikkeiden tyyntymistä.',
    'quote_src' => 'Joogasutrat 1.2',
    'h_limbs'   => 'Patañjalin kahdeksan osaa (aṣṭāṅga)',
    'limbs' => [
      ['Yama', 'Suhde muihin: väkivallattomuus, totuudellisuus'],
      ['Niyama', 'Suhde itseen: puhtaus, tyytyväisyys, itsetutkiskelu'],
      ['Āsana', 'Asento – vakaa ja mukava'],
      ['Prāṇāyāma', 'Hengityksen ohjaus'],
      ['Pratyāhāra', 'Aistien kääntäminen sisäänpäin'],
      ['Dhāraṇā', 'Keskittyminen'],
      ['Dhyāna', 'Meditaatio'],
      ['Samādhi', 'Uppoutuminen, ykseys'],
    ],

    'h_vinyasa' => 'Vinyasa – minun joogani',
    'vinyasa_p' => [
      'Nykyään harjoittelen ennen kaikkea dynaamista joogaa, vinyasaa. Sanskritin vinyāsa tarkoittaa ”asettamista erityisellä tavalla”: jokainen asento seuraa edellistä tietoisesti, ja hengitys kuljettaa liikettä. Sisäänhengitys avaa ja nostaa, uloshengitys taivuttaa ja laskee.',
      'Dynaamisessa joogassa asentoihin ei jäädä pitkäksi aikaa, vaan ne liitetään toisiinsa sujuvaksi virraksi. Harjoitus lämmittää kehon ja rakentaa voimaa, liikkuvuutta ja tasapainoa – ja kun huomio pysyy hengityksessä, mieli rauhoittuu liikkeen keskellä.',
      'Vinyasa juontuu Krishnamacharyan ja Pattabhi Joisin Ashtangasta, mutta toisin kuin Ashtangan kiinteässä sarjassa, vinyasa flow -tunnilla järjestys vaihtelee. Siksi jokainen tunti on vähän erilainen.',
    ],
    'elements' => [
      ['Surya namaskar', 'Aurinkotervehdys – tunnin selkäranka'],
      ['Ujjayi-hengitys', 'Kuuluva, rauhoittava hengitys nenän kautta'],
      ['Drishti', 'Katseen kiintopiste tasapainon tueksi'],
      ['Flow', 'Liikkeet sulautuvat yhdeksi virraksi'],
    ],
    'aerial_note' => 'Ilmajoogaakin olen kokeillut – sivun yläosan kuva on otettu Asana-asemalla ilmajoogatunnilla. Oma lajini on kuitenkin dynaaminen jooga matolla.',
    'h_surya'     => 'Aurinkotervehdys A – hengitys liike liikkeeltä',
    'surya_intro' => 'Aurinkotervehdys on vinyasan perusta: jokaisella liikkeellä on oma sisään- tai uloshengityksensä.',
    'b_in' => 'sisään', 'b_out' => 'ulos',
    'surya' => [
      ['in', 'Ūrdhva hastāsana', 'Kädet ylös'],
      ['out', 'Uttānāsana', 'Eteentaivutus'],
      ['in', 'Ardha uttānāsana', 'Puolinousu, selkä pitkäksi'],
      ['out', 'Chaturaṅga daṇḍāsana', 'Astu taakse ja laskeudu'],
      ['in', 'Ūrdhva mukha śvānāsana', 'Ylöspäin katsova koira'],
      ['out', 'Adho mukha śvānāsana', 'Alaspäin katsova koira – viisi hengitystä'],
      ['in', 'Ardha uttānāsana', 'Astu eteen, puolinousu'],
      ['out', 'Uttānāsana', 'Eteentaivutus'],
      ['in', 'Ūrdhva hastāsana', 'Nouse, kädet ylös'],
      ['out', 'Samasthiti', 'Takaisin seisomaan'],
    ],
    'h_styles' => 'Vinyasa muiden tyylien joukossa',
    'styles' => [
      ['Hatha', 'Rauhallisempi tahti, asennoissa viivytään pidempään.', false],
      ['Ashtanga', 'Sama kiinteä sarja joka kerta ja tarkka hengitysrytmi.', false],
      ['Vinyasa', 'Virtaava, hengitykseen sidottu ja vaihteleva – minun tyylini.', true],
      ['Power', 'Voimapainotteinen dynaaminen jooga, joka ammentaa Ashtangasta.', false],
      ['Yin', 'Passiivinen ja hidas, asentoja pidetään 3–5 minuuttia.', false],
    ],
    'h_arc' => 'Tunnin kaari',
    'arc'   => ['Maadoitus', 'Lämmittely', 'Dynaaminen flow', 'Yin-asennot', 'Rentoutus (śavāsana)'],

    'h_core' => 'Core-jooga – sisäinen tuli',
    'core_p' => [
      'Core-jooga vahvistaa keskivartalon syviä tukilihaksia – vatsaa, selkää ja lantionpohjaa – joogan asanoilla, ilman yhtäkään perinteistä vatsarutistusta. Vahva keskivartalo kantaa jokaisen siirtymän – lankusta chaturangaan, hypyistä tasapainoasentoihin.',
      'Asana-aseman Core-joogatunneilla luvataan kipakka polte koko keskivartaloon – ja juuri siinä joogan filosofia astuu matolle. Joogan perinteessä tuolla poltteella on nimi: tapas.',
    ],
    'niyama' => [
      ['icon' => 'flame', 'name' => 'Tapas', 'tag' => 'Sisäinen tuli', 'root' => 'Sanskritin juuresta tap – kuumentaa, polttaa.', 'text' => 'Tapas on yksi niyamoista: sitoutunut, hehkuva ponnistelu, joka muuttaa harjoittelijaa. Niin kuin tuli puhdistaa metallin, kuuma ja kärsivällinen harjoitus polttaa pois sen, mikä ei enää palvele.', 'skt' => 'kāya-indriya-siddhir aśuddhi-kṣayāt tapasaḥ', 'tr' => 'Tapaksen myötä epäpuhtaudet tuhoutuvat, ja keho ja aistit saavuttavat täyteytensä.', 'src' => 'Joogasutrat 2.43', 'mat' => 'Matolla: pysyt lankussa tai veneasennossa vielä yhden hengityksen, kun keskivartalo polttaa – rauhallisesti, hengitys tasaisena. Polte on muutosta, ei rangaistusta.'],
      ['icon' => 'eye', 'name' => 'Svādhyāya', 'tag' => 'Itsetutkiskelu', 'root' => 'sva = itse, adhyāya = tutkiminen, lukeminen.', 'text' => 'Myös svadhyaya kuuluu niyamoihin: se on itsensä rehellistä tarkastelua. Alun perin se tarkoitti myös pyhien tekstien opiskelua – ymmärrystä, joka peilaa omaa kokemusta.', 'skt' => 'svādhyāyād iṣṭa-devatā-samprayogaḥ', 'tr' => 'Itsetutkiskelun kautta syntyy yhteys valittuun jumaluuteen.', 'src' => 'Joogasutrat 2.44', 'mat' => 'Matolla: havainnoit, mitä tänään tapahtuu. Tärisevätkö vatsalihakset, pidätätkö hengitystä, puskeeko ego eteenpäin? Itsearviointi kertoo, milloin lisätä tulta ja milloin hellittää.'],
    ],
    'h_kriya' => 'Kriyājooga – toiminnan jooga',
    'kriya_intro' => 'Patañjali liittää tapaksen ja svadhyayan yhteen kolmannen osan kanssa. Yhdessä ne muodostavat kriyājoogan eli harjoituksen joogan.',
    'kriya' => [['Tapas', 'ponnistus'], ['Svādhyāya', 'tietoisuus'], ['Īśvara-praṇidhāna', 'antautuminen, irtipäästäminen']],
    'kriya_sum' => 'Kriyājooga', 'kriya_skt' => 'tapaḥ-svādhyāyeśvara-praṇidhānāni kriyā-yogaḥ', 'kriya_src' => 'Joogasutrat 2.1',
    'h_fire' => 'Tulen ajatelmat',
    'fire_intro' => 'Tapas ja svadhyaya eivät ole ainoat tuleen liittyvät ajatukset. Myös nämä joogan käsitteet kuuluvat samaan tuliseen harjoitukseen.',
    'fire' => [
      ['Agni', 'Muutoksen tuli', 'Vedalaisessa ajattelussa tuli vie uhrilahjan jumalille ja muuttaa sen. Ayurvedassa jāṭharāgni on ruoansulatuksen tuli – vertauskuva sille, miten ”sulatamme” myös kokemuksia.'],
      ['Maṇipūra', 'Napachakra', 'Kolmas chakra, ”jalokivien kaupunki”, sijaitsee navan seudulla, ja sen elementti on tuli. Perinteessä siihen liitetään tahto ja oma voima – juuri siihen Core-jooga keskittyy.'],
      ['Uḍḍīyāna bandha', 'Ylöspäin lentävä lukko', 'Hatha Yoga Pradīpikān kuvaama vatsan sisäänveto. Core-joogatunneilla sen kevyt muoto – navan veto kohti selkärankaa – tukee asentoja ja siirtymiä.'],
      ['Kapālabhāti', 'Kirkastava hengitys', 'Puhdistustekniikka, jossa uloshengitys tehdään terävästi vatsalla. Sitä kutsutaan tuliseksi hengitykseksi, ja sitä harjoitellaan opettajan ohjauksessa.'],
      ['Sthira sukham āsanam', 'Vakaa ja mukava', 'Joogasutra 2.46: asennon tulee olla vakaa ja mukava. Tuli tarvitsee tasapainon – voimaa ilman jännitystä.'],
      ['Abhyāsa & vairāgya', 'Harjoitus ja irtipäästäminen', 'Joogasutra 1.12: mieli tyyntyy sinnikkäällä harjoituksella ja kiinnittymättömyydellä. Tuli palaa tasaisesti, kun sitä ruokitaan päivittäin eikä tuloksia kahmita.'],
      ['Ahiṃsā', 'Väkivallattomuus', 'Ensimmäinen yama asettaa tapakselle rajan: tuli saa lämmittää mutta ei polttaa. Kuuntele kehoa – erityisesti ranteita ja alaselkää.'],
    ],
    'h_poses' => 'Tulisia asanoita',
    'poses' => [['Phalakāsana', 'Lankku'], ['Chaturaṅga daṇḍāsana', 'Matala lankku'], ['Vasiṣṭhāsana', 'Sivulankku'], ['Nāvāsana', 'Vene'], ['Utkaṭāsana', '”voimakas asento” eli tuoli']],
    'flame' => 'Tapas sytyttää liekin, svadhyaya katsoo siihen.',

    'h_studio' => 'Asana-asema, Heinola',
    'studio_p' => 'Harjoittelen dynaamista joogaa Asana-asemalla, heinolalaisessa joogastudiossa Torikadulla. Studion opetus perustuu vinyasa-tyyliin: liike virtaa hengityksen tahdissa, usein musiikin kanssa, ja teemat vaihtelevat tasapainosta kiertoihin ja keskivartaloon. Tarjolla on myös joogaa aloittelijoille, Core-joogaa, yin- ja power-joogaa sekä ilmajoogaa.',
    'welcome' => 'Tervetuloa kokeilemaan matalalla kynnyksellä – aiempaa kokemusta ei tarvita, ja aloittelijoille on omat tuntinsa.',
    'addr_label' => 'Osoite',
    'btn_site'    => 'Studion sivut',
    'btn_classes' => 'Tuntikuvaukset',

    'note' => 'Historian ajoitukset ovat likimääräisiä ja perustuvat tutkijoiden arvioihin.',
    'home_btn' => 'Takaisin etusivulle',
  ],
];
$T = $TT[$lang];
$ogImage = $base . 'images/joga_og.jpg';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?= head_analytics('    ') ?>
    <title><?= h($T['title']) ?></title>
    <meta name="description" content="<?= h($T['desc']) ?>">
    <meta name="author" content="Marko Nakari">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#9E1B32">
    <?= head_alternates('jg_url', $lang, '    ') ?>
    <!-- Open Graph -->
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="Manalas Dungeon">
    <meta name="title" property="og:title" content="<?= h($T['title']) ?>">
    <meta name="description" property="og:description" content="<?= h($T['desc']) ?>">
    <meta property="og:url" content="<?= h(jg_url($lang)) ?>">
    <meta name="image" property="og:image" content="<?= h($ogImage) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="<?= h($T['loc']) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="<?= h($ogImage) ?>">

    <link rel="stylesheet" href="<?= h(asset('manala.css')) ?>">
    <?= head_fonts('playfair-display-latin-wght-normal.woff2', '    ') ?>    <link rel="stylesheet" href="<?= h(asset('joga.css')) ?>">
</head>
<body class="jg-body">

    <!-- Paluu ja kielivalitsin — sama tyyli kuin Ziņas-, Ceļojums- ja Vidzeme-sivuilla -->
    <a class="jg-back" href="<?= h($homeUrl) ?>">&larr; <?= h($T['back']) ?></a>
    <div class="jg-lang">
        <?php foreach ($supported as $lg): ?>
        <a href="<?= h(jg_url($lg)) ?>" hreflang="<?= $lg ?>" class="<?= $lg === $lang ? 'active' : '' ?>"><?= strtoupper($lg) ?></a>
        <?php endforeach; ?>
    </div>

    <!-- ===== Hero ===== -->
    <!-- Tyylitelty taustakuva (ilmajooga, kultainen duotone) — teksti alareunassa -->
    <header class="jg-hero">
        <img class="jg-hero__img" src="<?= h(asset('images/joga_hero.webp')) ?>" alt="<?= h($T['photo_alt']) ?>" width="1820" height="2041" fetchpriority="high">
        <div class="jg-hero__text">
            <p class="jg-eyebrow"><?= h($T['eyebrow']) ?></p>
            <h1 class="jg-title"><?= h($T['h1']) ?></h1>
            <p class="jg-lead"><?= str_replace('Asana-asema', '<span class="jg-nowrap">Asana-asema</span>', h($T['lead'])) ?></p>
            <nav class="jg-toc" aria-label="<?= h($T['h1']) ?>">
                <?php foreach ($T['nav'] as $id => $label): ?>
                <a href="#<?= $id ?>"><?= h($label) ?></a>
                <?php endforeach; ?>
            </nav>
        </div>
        <span class="jg-hero__credit"><?= h($T['credit']) ?></span>
        <a class="jg-scroll" href="#history" aria-label="<?= h($T['nav']['history']) ?>">&#8964;</a>
    </header>

    <main class="jg-main">

        <!-- ===== Historia ===== -->
        <section class="jg-section" id="history">
            <h2 class="jg-h2"><?= h($T['h_history']) ?></h2>
            <p class="jg-intro"><?= h($T['history_intro']) ?></p>
            <ol class="jg-timeline">
                <?php foreach ($T['timeline'] as [$era, $head, $text]): ?>
                <li class="jg-tl">
                    <span class="jg-tl__era"><?= h($era) ?></span>
                    <h3 class="jg-tl__title"><?= h($head) ?></h3>
                    <p class="jg-tl__text"><?= h($text) ?></p>
                </li>
                <?php endforeach; ?>
            </ol>
        </section>

        <!-- ===== Filosofia ===== -->
        <section class="jg-section" id="philosophy">
            <h2 class="jg-h2"><?= h($T['h_philosophy']) ?></h2>
            <p class="jg-intro"><?= h($T['philosophy_intro']) ?></p>

            <blockquote class="jg-quote">
                <p class="jg-quote__skt" lang="sa-Latn">Yogaś citta-vṛtti-nirodhaḥ</p>
                <p class="jg-quote__tr"><?= h($T['quote_tr']) ?></p>
                <cite class="jg-quote__src"><?= h($T['quote_src']) ?></cite>
            </blockquote>

            <h3 class="jg-h3"><?= h($T['h_limbs']) ?></h3>
            <ol class="jg-limbs">
                <?php foreach ($T['limbs'] as $i => [$name, $text]): ?>
                <li class="jg-limb">
                    <span class="jg-limb__num"><?= $i + 1 ?></span>
                    <span class="jg-limb__name"><?= h($name) ?></span>
                    <span class="jg-limb__text"><?= h($text) ?></span>
                </li>
                <?php endforeach; ?>
            </ol>
        </section>

        <!-- ===== Vinyasa ===== -->
        <section class="jg-section jg-vinyasa" id="vinyasa">
            <h2 class="jg-h2"><?= h($T['h_vinyasa']) ?></h2>
            <div class="jg-vinyasa__grid">
                <div class="jg-vinyasa__text">
                    <?php foreach ($T['vinyasa_p'] as $p): ?>
                    <p><?= h($p) ?></p>
                    <?php endforeach; ?>
                </div>
                <ul class="jg-elements">
                    <?php foreach ($T['elements'] as [$name, $text]): ?>
                    <li class="jg-el">
                        <span class="jg-el__name"><?= h($name) ?></span>
                        <span class="jg-el__text"><?= h($text) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <p class="jg-aside"><?= h($T['aerial_note']) ?></p>

            <h3 class="jg-h3"><?= h($T['h_surya']) ?></h3>
            <p class="jg-sub"><?= h($T['surya_intro']) ?></p>
            <ol class="jg-surya">
                <?php foreach ($T['surya'] as $i => [$b, $skt, $tr]): ?>
                <li class="jg-sp jg-sp--<?= $b ?>">
                    <span class="jg-sp__breath"><span aria-hidden="true"><?= $b === 'in' ? '↑' : '↓' ?></span> <?= h($T['b_' . $b]) ?></span>
                    <span class="jg-sp__skt" lang="sa-Latn"><?= h($skt) ?></span>
                    <span class="jg-sp__tr"><?= h($tr) ?></span>
                </li>
                <?php endforeach; ?>
            </ol>

            <h3 class="jg-h3"><?= h($T['h_styles']) ?></h3>
            <ul class="jg-styles">
                <?php foreach ($T['styles'] as [$name, $text, $mine]): ?>
                <li class="jg-style<?= $mine ? ' jg-style--mine' : '' ?>">
                    <span class="jg-style__name"><?= h($name) ?></span>
                    <span class="jg-style__text"><?= h($text) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>

            <h3 class="jg-h3"><?= h($T['h_arc']) ?></h3>
            <ol class="jg-arc">
                <?php foreach ($T['arc'] as $i => $step): ?>
                <li class="jg-arc__step"><span class="jg-arc__n"><?= $i + 1 ?></span><?= h($step) ?></li>
                <?php endforeach; ?>
            </ol>

            <p class="jg-chill">Chill. It’s just yoga.</p>
        </section>

        <!-- ===== Core-jooga: tapas, svadhyaya ja tulen ajatelmat ===== -->
        <section class="jg-section jg-core" id="core">
            <h2 class="jg-h2"><?= h($T['h_core']) ?></h2>
            <div class="jg-core__intro">
                <?php foreach ($T['core_p'] as $p): ?>
                <p><?= h($p) ?></p>
                <?php endforeach; ?>
            </div>

            <div class="jg-niyama">
                <?php foreach ($T['niyama'] as $c): ?>
                <article class="jg-ny jg-ny--<?= h($c['icon']) ?>">
                    <span class="jg-ny__icon" aria-hidden="true">
                        <?php if ($c['icon'] === 'flame'): ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c4 0 7-2.8 7-6.8 0-3.4-2.2-5.6-3.6-7.6-.5 2-1.6 3.2-2.8 3.6.4-3.2-.8-6.4-3.6-9.2.2 3.6-1.6 5.8-3.2 7.8C4.6 11.4 5 13.6 5 15.2 5 19.2 8 22 12 22z"/><path d="M12 22c-1.8 0-3-1.3-3-3.1 0-1.8 1.6-3 3-5 1.4 2 3 3.2 3 5 0 1.8-1.2 3.1-3 3.1z"/></svg>
                        <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3.2"/></svg>
                        <?php endif; ?>
                    </span>
                    <p class="jg-ny__tag"><?= h($c['tag']) ?></p>
                    <h3 class="jg-ny__name" lang="sa-Latn"><?= h($c['name']) ?></h3>
                    <p class="jg-ny__root"><?= h($c['root']) ?></p>
                    <p class="jg-ny__text"><?= h($c['text']) ?></p>
                    <blockquote class="jg-ny__sutra">
                        <p class="jg-ny__skt" lang="sa-Latn"><?= h($c['skt']) ?></p>
                        <p class="jg-ny__tr"><?= h($c['tr']) ?></p>
                        <cite><?= h($c['src']) ?></cite>
                    </blockquote>
                    <p class="jg-ny__mat"><?= h($c['mat']) ?></p>
                </article>
                <?php endforeach; ?>
            </div>

            <div class="jg-kriya">
                <h3 class="jg-h3"><?= h($T['h_kriya']) ?></h3>
                <p class="jg-kriya__intro"><?= h($T['kriya_intro']) ?></p>
                <ol class="jg-kriya__eq">
                    <?php foreach ($T['kriya'] as $i => [$name, $mean]): ?>
                    <?php if ($i > 0): ?><li class="jg-kriya__op" aria-hidden="true">+</li><?php endif; ?>
                    <li class="jg-kriya__part"><span lang="sa-Latn"><?= h($name) ?></span><small><?= h($mean) ?></small></li>
                    <?php endforeach; ?>
                    <li class="jg-kriya__op" aria-hidden="true">=</li>
                    <li class="jg-kriya__part jg-kriya__part--sum"><span><?= h($T['kriya_sum']) ?></span></li>
                </ol>
                <p class="jg-kriya__sutra"><span lang="sa-Latn"><?= h($T['kriya_skt']) ?></span> — <?= h($T['kriya_src']) ?></p>
            </div>

            <h3 class="jg-h3"><?= h($T['h_fire']) ?></h3>
            <p class="jg-sub"><?= h($T['fire_intro']) ?></p>
            <ul class="jg-fire">
                <?php foreach ($T['fire'] as [$name, $tag, $text]): ?>
                <li class="jg-fi">
                    <span class="jg-fi__name" lang="sa-Latn"><?= h($name) ?></span>
                    <span class="jg-fi__tag"><?= h($tag) ?></span>
                    <span class="jg-fi__text"><?= h($text) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>

            <h3 class="jg-h3"><?= h($T['h_poses']) ?></h3>
            <ul class="jg-poses">
                <?php foreach ($T['poses'] as [$skt, $tr]): ?>
                <li><span lang="sa-Latn"><?= h($skt) ?></span> <?= h($tr) ?></li>
                <?php endforeach; ?>
            </ul>

            <p class="jg-flame"><?= h($T['flame']) ?></p>
        </section>

        <!-- ===== Asana-asema ===== -->
        <section class="jg-section" id="studio">
            <div class="jg-studio">
                <h2 class="jg-studio__title"><?= h($T['h_studio']) ?></h2>
                <p class="jg-studio__text"><?= h($T['studio_p']) ?></p>
                <p class="jg-studio__welcome"><?= h($T['welcome']) ?></p>
                <p class="jg-studio__addr"><span><?= h($T['addr_label']) ?></span> <?= h($STUDIO['addr']) ?></p>
                <div class="jg-studio__btns">
                    <a class="jg-btn jg-btn--primary" href="<?= h($STUDIO['site']) ?>" target="_blank" rel="noopener"><?= h($T['btn_site']) ?> <span aria-hidden="true">↗</span></a>
                    <a class="jg-btn" href="<?= h($STUDIO['classes']) ?>" target="_blank" rel="noopener"><?= h($T['btn_classes']) ?> <span aria-hidden="true">↗</span></a>
                </div>
            </div>
        </section>

    </main>

    <footer class="jg-footer">
        <p class="jg-footer__note"><?= h($T['note']) ?></p>
        <a class="jg-footer-link" href="<?= h($homeUrl) ?>"><span class="jg-arrow" aria-hidden="true">&larr;</span> <?= h($T['home_btn']) ?></a>
        <p class="jg-footer__copy">&copy; <a href="https://manalasdungeon.lv">Marko Nakari</a></p>
    </footer>

</body>
</html>
