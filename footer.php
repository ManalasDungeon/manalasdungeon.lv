<?php /* Jaettu footer. Odottaa: $lang */ ?>
    <footer class="lv-footer">
      <div class="lv-band"></div>
      <div class="lv-wrap lv-footer__grid">
        <div class="lv-footer__brand">
          <h3>Vitrupes Pludmale</h3>
          <p><?= t('footer.tagline') ?></p>
        </div>
        <div class="lv-footer__col">
          <h4><?= t('footer.links') ?></h4>
          <a href="https://www.cesis.lv/lv/">Cēsis</a>
          <a href="http://www.vidzeme.lv/lv">Vidzeme</a>
          <a href="https://www.limbazunovads.lv/lv">Limbaži</a>
          <a href="https://www.tuja.lv/">Tūja</a>
        </div>
        <div class="lv-footer__col">
          <h4><?= t('footer.pages') ?></h4>
          <a href="<?= e(lurl('')) ?>"><?= t('footer.p_home') ?></a>
          <a href="<?= e(lurl('kuvat.php')) ?>"><?= t('footer.p_g1') ?></a>
          <a href="<?= e(lurl('kuvat2.php')) ?>"><?= t('footer.p_g2') ?></a>
          <a href="<?= e(lurl('kuvat3.php')) ?>"><?= t('footer.p_g3') ?></a>
          <a href="https://manalasdungeon.lv/viesu_gramata.php"><?= t('footer.p_guestbook') ?></a>
          <a href="https://manalasdungeon.lv/celojums.php">Ceļojums pa Latviju</a>
        </div>
      </div>
      <div class="lv-footer__bottom">
        &copy; <a href="https://manalasdungeon.lv">Marko Nakari</a> &middot; <?= t('footer.role') ?> &middot; <span id="date_time"></span>
      </div>
    </footer>

    <button class="lv-sound" id="soundBtn" aria-label="<?= e(t('sound')) ?>" aria-pressed="false" title="<?= e(t('sound')) ?>">
      <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 9v6h4l5 5V4L8 9H4z"/><path d="M16.5 8.5a5 5 0 0 1 0 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    </button>
    <audio id="beachAudio" loop preload="none" src="audio/Tomubeach.wav"></audio>

    <script src="<?= e(asset('js/bootstrap-5.3.3.bundle.min.js')) ?>"></script>
    <script src="<?= e(asset('js/vitrupe.js')) ?>"></script>
