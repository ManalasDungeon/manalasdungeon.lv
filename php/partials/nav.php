<?php /* Jaettu navipalkki. Odottaa: $active ('home'|'gallery'), $lang, $supported, $canonical_path */
$langflag = ['lv' => '🇱🇻', 'en' => '🇬🇧', 'fi' => '🇫🇮'];
$langname = ['lv' => 'Latviešu', 'en' => 'English', 'fi' => 'Suomi'];
?>
    <nav class="navbar fixed-top navbar-expand-lg navbar-light lv-nav lv-nav-overlay" id="siteNav">
      <a class="navbar-brand" href="<?= e(lurl('')) ?>">VITRUPES PLUDMALE</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Menu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item <?= $active === 'home' ? 'active' : '' ?>"><a class="nav-link" href="<?= e(lurl('')) ?>"><?= t('nav.home') ?></a></li>
          <li class="nav-item <?= $active === 'gallery' ? 'active' : '' ?>"><a class="nav-link" href="<?= e(lurl('kuvat.php')) ?>"><?= t('nav.gallery') ?></a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navDrop" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= t('nav.links') ?></a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navDrop">
              <a class="dropdown-item" href="https://www.cesis.lv/lv/">Cēsis</a>
              <a class="dropdown-item" href="http://www.vidzeme.lv/lv">Vidzeme</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="https://www.limbazunovads.lv/lv">Limbaži</a>
              <a class="dropdown-item" href="https://www.tuja.lv/">Tūja</a>
            </div>
          </li>
          <li class="nav-item"><a class="nav-link" href="https://latvija.lv/ParPortalu/Info_lapa"><?= t('nav.latvia') ?></a></li>
          <li class="nav-item"><a class="nav-link lv-nav__cta" href="https://manalasdungeon.lv/viesu_gramata.php"><?= t('nav.guestbook') ?></a></li>
          <li class="nav-item dropdown lv-lang">
            <a class="nav-link dropdown-toggle lv-lang__toggle" href="#" id="langDrop" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="lv-lang__flag"><?= $langflag[$lang] ?></span> <?= strtoupper($lang) ?>
            </a>
            <div class="dropdown-menu dropdown-menu-end lv-lang__menu" aria-labelledby="langDrop">
              <?php foreach ($supported as $lg): if ($lg === $lang) continue; ?>
              <a class="dropdown-item lv-lang__item" href="<?= e(page_url($canonical_path, $lg)) ?>" hreflang="<?= $lg ?>">
                <span class="lv-lang__flag"><?= $langflag[$lg] ?></span> <?= $langname[$lg] ?>
              </a>
              <?php endforeach; ?>
            </div>
          </li>
        </ul>
      </div>
    </nav>
