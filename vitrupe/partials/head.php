<?php
/* Jaettu <head>-metaosio (SEO + sosiaalinen media).
   Odottaa: $page_title, $page_desc, $og_image (absoluuttinen URL), $canonical_path, $lang, $supported */
$locales = ['lv' => 'lv_LV', 'en' => 'en_US', 'fi' => 'fi_FI'];
?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google Analytics vain kävijän suostumuksella (pääsivuston analytics.js näyttää evästevalinnan) -->
    <script src="/analytics.js?v=<?= (int)(@filemtime(dirname(__DIR__, 2) . '/analytics.js') ?: 1) ?>"></script>
    <link rel="stylesheet" href="/fonts.css?v=<?= (int)(@filemtime(dirname(__DIR__, 2) . '/fonts.css') ?: 1) ?>">
    <title><?= e($page_title) ?></title>
    <meta name="description" content="<?= e($page_desc) ?>">
    <meta name="author" content="Marko Nakari">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#9E1B32">
    <link rel="canonical" href="<?= e(page_url($canonical_path, $lang)) ?>">
    <?= hreflang_tags($canonical_path) ?>

    <!-- Open Graph (Facebook, WhatsApp, LinkedIn) -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Manalas Dungeon">
    <meta property="og:title" content="<?= e($page_title) ?>">
    <meta property="og:description" content="<?= e($page_desc) ?>">
    <meta property="og:url" content="<?= e(page_url($canonical_path, $lang)) ?>">
    <meta property="og:image" content="<?= e($og_image) ?>">
    <meta property="og:image:alt" content="<?= e($page_title) ?>">
    <meta property="og:locale" content="<?= e($locales[$lang]) ?>">
    <?php foreach ($supported as $lg): if ($lg !== $lang): ?>
    <meta property="og:locale:alternate" content="<?= e($locales[$lg]) ?>">
    <?php endif; endforeach; ?>

    <!-- Twitter / X -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($page_title) ?>">
    <meta name="twitter:description" content="<?= e($page_desc) ?>">
    <meta name="twitter:image" content="<?= e($og_image) ?>">
