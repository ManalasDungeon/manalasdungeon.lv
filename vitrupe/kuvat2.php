<?php require __DIR__ . '/lang.php';
$canonical_path = 'kuvat2.php'; $active = 'gallery';
$photos = ['vitrupe01','vitrupe02','vitrupe03','vitrupe04','vitrupe05','vitrupe06','vitrupe07','vitrupe08','vitrupe09','vitrupe10'];
$page_title = t('gallery.title2') . ' — Vitrupes Pludmale';
$page_desc  = t('gallery.lead2');
$og_image   = $BASE . 'images/vitrupe_hero2.jpg';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
  <head>
    <?php require __DIR__ . '/partials/head.php'; ?>

    <script type="text/javascript" src="<?= e(asset('js/date_time.js')) ?>"></script>
    <link href="<?= e(asset('css/bootstrap-5.3.3.min.css')) ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/latviatyyli.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/index.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/kuvatyylit.css')) ?>">
    <?php require __DIR__ . '/partials/jsonld.php'; ?>
  </head>

  <body>

    <?php require __DIR__ . '/partials/nav.php'; ?>

    <header class="lv-hero-full lv-hero-full--sub" id="top">
      <div class="lv-hero-full__bg" style="background-image:url('images/vitrupe_hero2.webp')"></div>
      <div class="lv-hero-full__content reveal">
        <p class="lv-eyebrow lv-eyebrow--light"><?= t('index.kicker') ?></p>
        <h1 class="lv-hero-full__title"><?= t('gallery.title2') ?></h1>
        <p class="lv-hero-full__lead"><?= t('gallery.lead2') ?></p>
      </div>
      <a href="#galerija" class="lv-hero-full__scroll" aria-label="scroll"><span></span></a>
    </header>

    <div class="lv-band"></div>

    <section class="lv-section" id="galerija">
      <div class="lv-wrap">
        <div class="gallery-grid reveal">
          <?php foreach ($photos as $img): $c = t('gallery.cap.' . $img); ?>
          <div class="gallery">
            <div class="gallery__media"><img src="images/t/<?= $img ?>.webp" srcset="images/t/<?= $img ?>.webp 800w, images/<?= $img ?>.webp 1800w" sizes="(max-width: 560px) 100vw, (max-width: 900px) 50vw, 33vw" data-full="images/<?= $img ?>.webp" alt="<?= e($c) ?>" loading="lazy" decoding="async" tabindex="0" role="button"></div>
            <div class="desc"><?= $c ?></div>
          </div>
          <?php endforeach; ?>
        </div>

        <nav class="lv-pager" aria-label="Pages">
          <a class="lv-pager__btn" href="<?= e(lurl('kuvat.php')) ?>"><span class="lv-pager__arrow lv-pager__arrow--prev" aria-hidden="true">&larr;</span> <?= t('gallery.pager_prev') ?></a>
          <span class="lv-pager__count">2 / 3</span>
          <a class="lv-pager__btn" href="<?= e(lurl('kuvat3.php')) ?>"><?= t('gallery.pager_next') ?> <span class="lv-pager__arrow lv-pager__arrow--next" aria-hidden="true">&rarr;</span></a>
        </nav>
      </div>
    </section>

    <!-- Lightbox ennen alatunnistetta: vitrupe.js (alatunnisteessa) löytää sen heti -->
    <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="<?= e(t('gallery.title')) ?>">
      <button type="button" class="lightbox__close" aria-label="<?= e(t('close')) ?>">&times;</button>
      <img id="lightbox-img" src="" alt="">
      <div class="lightbox__caption" id="lightbox-cap"></div>
    </div>
    <?php require __DIR__ . '/partials/footer.php'; ?>

  </body>
</html>
