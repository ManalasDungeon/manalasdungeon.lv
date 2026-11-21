<?php
/* Structured data (JSON-LD) hakukoneille. Odottaa: $lang, $canonical_path.
   Kuvaus, url ja kieli vaihtuvat valitun kielen mukaan. */
$ld = [
  '@context'    => 'https://schema.org',
  '@type'       => 'TouristAttraction',
  'name'        => 'Vitrupes Pludmale',
  'description' => t('index.lead'),
  'url'         => page_url($canonical_path, $lang),
  'image'       => [
    'https://manalasdungeon.lv/vitrupe/images/ranta1.jpg',
    'https://manalasdungeon.lv/vitrupe/images/vitrupe_hero2.jpg',
  ],
  'touristType'  => ['Nature', 'Beach'],
  'isAccessibleForFree' => true,
  'address'     => [
    '@type'          => 'PostalAddress',
    'addressRegion'  => 'Vidzeme',
    'addressCountry' => 'LV',
  ],
  'geo'         => [
    '@type'     => 'GeoCoordinates',
    'latitude'  => 57.625070,
    'longitude' => 24.383947,
  ],
];
?>
    <script type="application/ld+json">
    <?= json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>

    </script>
