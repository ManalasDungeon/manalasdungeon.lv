<?php require __DIR__ . '/lang.php';
$canonical_path = 'kuvat.php'; $active = 'gallery';
$photos = ['ranta1','ranta2','ranta3','ranta4','ranta5','ranta6','ranta7','ranta8','ranta9','ranta10'];
$page_title = t('gallery.title') . ' — Vitrupes Pludmale';
$page_desc  = t('gallery.lead');
$og_image   = $BASE . 'images/ranta5.jpg';
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
      <div class="lv-hero-full__bg" style="background-image:url('images/pludmales1.jpg')"></div>
      <div class="lv-hero-full__content reveal">
        <p class="lv-eyebrow lv-eyebrow--light"><?= t('index.kicker') ?></p>
        <h1 class="lv-hero-full__title"><?= t('gallery.title') ?></h1>
        <p class="lv-hero-full__lead"><?= t('gallery.lead') ?></p>
      </div>
      <a href="#galerija" class="lv-hero-full__scroll" aria-label="scroll"><span></span></a>
    </header>

    <div class="lv-band"></div>

    <section class="lv-section" id="galerija">
      <div class="lv-wrap">
        <div class="gallery-grid reveal">
          <?php foreach ($photos as $img): $c = t('gallery.cap.' . $img); ?>
          <div class="gallery">
            <div class="gallery__media"><img src="images/<?= $img ?>.jpg" alt="<?= e($c) ?>" loading="lazy" onclick="openImg(this)"></div>
            <div class="desc"><?= $c ?></div>
          </div>
          <?php endforeach; ?>
        </div>

        <nav class="lv-pager" aria-label="Pages">
          <span class="lv-pager__count">1 / 3</span>
          <a class="lv-pager__btn" href="<?= e(lurl('kuvat2.php')) ?>"><?= t('gallery.pager_next') ?> <span class="lv-pager__arrow lv-pager__arrow--next" aria-hidden="true">&rarr;</span></a>
        </nav>
      </div>
    </section>

    <div class="lv-band"></div>

    <section class="lv-section lv-section--tint" id="karte">
      <div class="lv-wrap">
        <div class="lv-section__head reveal">
          <p class="lv-eyebrow"><?= t('gallery.map_kicker') ?></p>
          <h2><?= t('gallery.map_title') ?></h2>
        </div>
        <div class="embed-grid embed-grid--single reveal">
          <div class="lv-embed">
            <iframe title="Google Maps" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5081.19025831626!2d24.38394674412599!3d57.62507045798023!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46ec1873e0b22519%3A0xe9406ae2d3ceedf5!2sParking!5e0!3m2!1sfi!2sfi!4v1728459108262!5m2!1sfi!2sfi" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>
      </div>
    </section>

    <?php require __DIR__ . '/partials/footer.php'; ?>

    <div class="lightbox" id="lightbox" onclick="closeImg()">
      <span class="lightbox__close" onclick="closeImg()" aria-label="X">&times;</span>
      <img id="lightbox-img" src="" alt="">
      <div class="lightbox__caption" id="lightbox-cap"></div>
    </div>
  </body>
</html>
