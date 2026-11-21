<?php require __DIR__ . '/lang.php';
$canonical_path = ''; $active = 'home';
$page_title = 'Vitrupes Pludmale — ' . t('index.kicker');
$page_desc  = t('index.lead');
$og_image   = $BASE . 'images/ranta1.jpg';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
  <head>
    <?php require __DIR__ . '/partials/head.php'; ?>

    <script type="text/javascript" src="<?= e(asset('js/date_time.js')) ?>"></script>
    <link href="<?= e(asset('css/bootstrap-5.3.3.min.css')) ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/latviatyyli.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/index.css')) ?>">
    <?php require __DIR__ . '/partials/jsonld.php'; ?>
  </head>

  <body>

    <?php require __DIR__ . '/partials/nav.php'; ?>

    <!-- HERO -->
    <header class="lv-hero-full" id="top">
      <div class="lv-hero-full__bg" id="heroBg" data-hero="vitrupe02,vitrupe03,vitrupe04,vitrupe05,vitrupe06,vitrupe07,vitrupe08,vitrupe09" style="background-image:url('images/vitrupe02.webp')"></div>
      <div class="lv-hero-full__content reveal">
        <p class="lv-eyebrow lv-eyebrow--light"><?= t('index.kicker') ?></p>
        <h1 class="lv-hero-full__title">Vitrupes<br>Pludmale</h1>
        <p class="lv-hero-full__lead"><?= t('index.lead') ?></p>
        <div class="lv-hero-full__actions">
          <a href="<?= e(lurl('kuvat.php')) ?>" class="lv-btn"><?= t('index.cta_gallery') ?></a>
          <a href="#karte" class="lv-btn lv-btn--ghost"><?= t('index.cta_map') ?></a>
        </div>
      </div>
      <a href="#stasts" class="lv-hero-full__scroll" aria-label="scroll"><span></span></a>
    </header>

    <div class="lv-band"></div>

    <!-- INTRO -->
    <section class="lv-section" id="stasts">
      <div class="lv-wrap lv-intro">
        <div class="lv-intro__text reveal">
          <p class="lv-eyebrow"><?= t('index.intro_kicker') ?></p>
          <h2><?= t('index.intro_title') ?></h2>
          <p><?= t('index.intro_body') ?></p>
          <a href="https://www.latvia.travel/en/vidzeme-shoreline" class="lv-textlink"><?= t('index.intro_link') ?> &rarr;</a>
        </div>
        <figure class="lv-intro__media reveal">
          <img src="images/t/ranta6.webp" srcset="images/t/ranta6.webp 800w, images/ranta6.webp 1800w" sizes="(max-width: 900px) 100vw, 50vw" alt="<?= e(t('gallery.cap.ranta6')) ?>" loading="lazy" decoding="async">
        </figure>
      </div>
    </section>

    <!-- HIGHLIGHTS -->
    <section class="lv-section lv-section--tint">
      <div class="lv-wrap">
        <div class="lv-section__head reveal">
          <p class="lv-eyebrow"><?= t('index.hi_kicker') ?></p>
          <h2><?= t('index.hi_title') ?></h2>
        </div>
        <div class="lv-features">
          <a class="lv-feature reveal" href="https://www.latvia.travel/en/vidzeme-shoreline">
            <div class="lv-feature__img" style="background-image:url('images/t/ranta5.webp')"></div>
            <div class="lv-feature__body"><h3><?= t('index.hi1_title') ?></h3><p><?= t('index.hi1_body') ?></p><span class="lv-feature__more">Latvia.travel &rarr;</span></div>
          </a>
          <a class="lv-feature reveal" href="https://www.latvia.travel/lv/9-skaistas-pludmales-latvija">
            <div class="lv-feature__img" style="background-image:url('images/t/ranta7.webp')"></div>
            <div class="lv-feature__body"><h3><?= t('index.hi2_title') ?></h3><p><?= t('index.hi2_body') ?></p><span class="lv-feature__more">Latvia.travel &rarr;</span></div>
          </a>
          <a class="lv-feature reveal" href="https://www.salacgriva.lv/lat/salacgrivas_novads/">
            <div class="lv-feature__img" style="background-image:url('images/rantaback.webp')"></div>
            <div class="lv-feature__body"><h3><?= t('index.hi3_title') ?></h3><p><?= t('index.hi3_body') ?></p><span class="lv-feature__more">Salacgriva.lv &rarr;</span></div>
          </a>
        </div>
      </div>
    </section>

    <!-- QUOTE -->
    <section class="lv-quote" style="background-image:url('images/ranta4.webp')">
      <blockquote class="lv-quote__inner reveal">
        <p>&bdquo;<?= t('index.quote') ?>&rdquo;</p>
        <cite><?= t('index.quote_cite') ?></cite>
      </blockquote>
    </section>

    <!-- GALLERY PREVIEW -->
    <section class="lv-section" id="galerija">
      <div class="lv-wrap">
        <div class="lv-section__head reveal">
          <p class="lv-eyebrow"><?= t('index.gp_kicker') ?></p>
          <h2><?= t('index.gp_title') ?></h2>
        </div>
        <div class="lv-gallery reveal">
          <a class="lv-gallery__item lv-gallery__item--big" href="<?= e(lurl('kuvat.php')) ?>"><img src="images/t/ranta2.webp" srcset="images/t/ranta2.webp 800w, images/ranta2.webp 1800w" sizes="(max-width: 700px) 100vw, 66vw" alt="<?= e(t('gallery.cap.ranta2')) ?>" loading="lazy" decoding="async"></a>
          <a class="lv-gallery__item" href="<?= e(lurl('kuvat.php')) ?>"><img src="images/t/ranta3.webp" srcset="images/t/ranta3.webp 800w, images/ranta3.webp 1800w" sizes="(max-width: 700px) 100vw, 33vw" alt="<?= e(t('gallery.cap.ranta3')) ?>" loading="lazy" decoding="async"></a>
          <a class="lv-gallery__item" href="<?= e(lurl('kuvat.php')) ?>"><img src="images/t/ranta8.webp" srcset="images/t/ranta8.webp 800w, images/ranta8.webp 1800w" sizes="(max-width: 700px) 100vw, 33vw" alt="<?= e(t('gallery.cap.ranta8')) ?>" loading="lazy" decoding="async"></a>
          <a class="lv-gallery__item lv-gallery__item--wide" href="<?= e(lurl('kuvat.php')) ?>"><img src="images/t/ranta6.webp" srcset="images/t/ranta6.webp 800w, images/ranta6.webp 1800w" sizes="(max-width: 700px) 100vw, 66vw" alt="<?= e(t('gallery.cap.ranta6')) ?>" loading="lazy" decoding="async"></a>
        </div>
        <div class="lv-center reveal"><a href="<?= e(lurl('kuvat.php')) ?>" class="lv-btn"><?= t('index.gp_btn') ?></a></div>
      </div>
    </section>

    <!-- CULTURE -->
    <section class="lv-culture" style="background-image:url('images/profile_bg.webp')">
      <div class="lv-culture__inner reveal">
        <p class="lv-eyebrow lv-eyebrow--light"><?= t('index.cul_kicker') ?></p>
        <h2><?= t('index.cul_title') ?></h2>
        <p><?= t('index.cul_body') ?></p>
      </div>
    </section>

    <!-- MAP -->
    <section class="lv-section lv-section--tint" id="karte">
      <div class="lv-wrap lv-map">
        <div class="lv-map__text reveal">
          <p class="lv-eyebrow"><?= t('index.map_kicker') ?></p>
          <h2><?= t('index.map_title') ?></h2>
          <p><?= t('index.map_body') ?></p>
          <a href="https://www.google.com/maps/place/57.6250705,24.3839467" class="lv-textlink" target="_blank" rel="noopener"><?= t('index.map_link') ?> &rarr;</a>
        </div>
        <div class="lv-map__embed reveal">
          <iframe title="<?= e(t('index.map_title')) ?> — Google Maps" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5081.19025831626!2d24.38394674412599!3d57.62507045798023!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46ec1873e0b22519%3A0xe9406ae2d3ceedf5!2sParking!5e0!3m2!1sfi!2sfi!4v1728459108262!5m2!1sfi!2sfi" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </section>

    <!-- RECOMMENDATION + GUESTBOOK -->
    <section class="lv-section">
      <div class="lv-wrap">
        <div class="lv-section__head reveal">
          <p class="lv-eyebrow"><?= t('index.rec_kicker') ?></p>
          <h2><?= t('index.rec_title') ?></h2>
        </div>
        <div class="lv-links reveal">
          <a class="lv-gb-image lv-gb-image--contain" href="https://tedijs.lv/" aria-label="Tedijs"><img src="images/tedijs.webp" alt="Tedijs" loading="lazy" width="344" height="188"></a>
          <a class="lv-gb-image" href="https://manalasdungeon.lv/viesu_gramata.php" aria-label="<?= e(t('nav.guestbook')) ?>"><img src="images/viesu_gramata.webp" alt="<?= e(t('nav.guestbook')) ?>" loading="lazy" decoding="async"></a>
        </div>
      </div>
    </section>

    <?php require __DIR__ . '/partials/footer.php'; ?>
  </body>
</html>
