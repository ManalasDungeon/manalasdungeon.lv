<?php
/* Ziņas — administrācija. Lisää / muokkaa / poista uutisia ja matkamerkintöjä.
   Passkey-suojattu (passkey/guard.php): pääsy vain rekisteröidyillä laitteilla.
   Käyttää samaa db_config.php:tä. noindex. */

require __DIR__ . '/passkey/guard.php';
require __DIR__ . '/db_config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (empty($_SESSION['bcsrf'])) { $_SESSION['bcsrf'] = bin2hex(random_bytes(32)); }
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$msg=''; $err=false;

/* ---- Uloskirjautuminen (passkey-istunto) — CSRF-tunnisteella ---- */
if (($_GET['logout']??'')==='1') {
    if (hash_equals($_SESSION['bcsrf'], (string)($_GET['t']??''))) {
        unset($_SESSION['bcsrf']);
        pk_logout();
        header('Location: /passkey/login.php'); exit;
    }
    header('Location: blogi_admin.php'); exit;
}

$KUVADIR = __DIR__ . '/blogi_kuvat';

/* ---- Toiminnot (CSRF) ---- */
if ($_SERVER['REQUEST_METHOD']==='POST'
    && in_array($_POST['action']??'', ['add','update','delete','toggle','upload_img','delete_img'], true)) {
    if (!hash_equals($_SESSION['bcsrf'], (string)($_POST['csrf']??''))) {
        $msg='Sesijas kļūda.'; $err=true;
    } else {
        $act=$_POST['action'];
        if ($act==='upload_img') {
            if (empty($_FILES['kuva']['tmp_name']) || !is_uploaded_file($_FILES['kuva']['tmp_name'])) {
                $msg='Valitse kuvatiedosto.'; $err=true;
            } else {
                $info = @getimagesize($_FILES['kuva']['tmp_name']);
                $sallitut = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
                if (!$info || !isset($sallitut[$info['mime']])) {
                    $msg='Vain JPG, PNG, WEBP tai GIF.'; $err=true;
                } elseif ($_FILES['kuva']['size'] > 6*1024*1024) {
                    $msg='Kuva on liian suuri (max 6 MB).'; $err=true;
                } else {
                    if (!is_dir($KUVADIR)) {
                        @mkdir($KUVADIR, 0755, true);
                        // Estä skriptien ajo kuvakansiossa (kovennus)
                        @file_put_contents($KUVADIR.'/.htaccess',
                            "php_flag engine off\nRemoveHandler .php .phtml .php3 .php4 .php5 .php7 .php8\nRemoveType .php .phtml\n");
                    }
                    $ext = $sallitut[$info['mime']];
                    $pohja = pathinfo($_FILES['kuva']['name'], PATHINFO_FILENAME);
                    $pohja = preg_replace('/[^A-Za-z0-9_\-]+/','-', $pohja);
                    $pohja = trim($pohja,'-'); if ($pohja==='') $pohja='kuva';
                    $pohja = substr($pohja, 0, 40);
                    $nimi = $pohja.'-'.bin2hex(random_bytes(3)).'.'.$ext;
                    if (move_uploaded_file($_FILES['kuva']['tmp_name'], $KUVADIR.'/'.$nimi)) {
                        $msg = 'Kuva ladattu. Lisää tekstiin: [kuva:'.$nimi.']';
                    } else { $msg='Tallennus epäonnistui (tarkista kansion oikeudet).'; $err=true; }
                }
            }
        } elseif ($act==='delete_img') {
            $fn = basename((string)($_POST['fn']??''));
            if ($fn!=='' && preg_match('/^[A-Za-z0-9_\-.]+$/',$fn) && is_file($KUVADIR.'/'.$fn)) {
                @unlink($KUVADIR.'/'.$fn); $msg='Kuva poistettu.';
            }
        }
        if ($act==='add' || $act==='update') {
            $v=trim($_POST['virsraksts']??''); $t=trim($_POST['teksts']??'');
            $d=trim($_POST['izveidots']??''); $pub=isset($_POST['publicets'])?1:0;
            $kat=in_array($_POST['kategorija']??'zina', ['zina','celojums'], true) ? $_POST['kategorija'] : 'zina';
            // Päivämäärä tarkistetaan — virheellinen arvo ei kaada tallennusta (MySQL strict mode)
            $dt = date('Y-m-d H:i:s');
            if ($d !== '') {
                $o = DateTime::createFromFormat('Y-m-d\TH:i', $d) ?: DateTime::createFromFormat('Y-m-d\TH:i:s', $d);
                if ($o) { $dt = $o->format('Y-m-d H:i:s'); }
            }
            if ($v==='' || $t==='') { $msg='Otsikko ja teksti ovat pakollisia.'; $err=true; }
            elseif (mb_strlen($v)>160) { $msg='Otsikko enintään 160 merkkiä.'; $err=true; }
            else {
                if ($act==='add') {
                    db()->prepare("INSERT INTO zinas (virsraksts,teksts,kategorija,izveidots,publicets) VALUES (?,?,?,?,?)")
                        ->execute([$v,$t,$kat,$dt,$pub]);
                    $msg = $kat==='celojums' ? 'Ceļojuma ieraksts pievienots.' : 'Ziņa pievienota.';
                } else {
                    $id=(int)($_POST['id']??0);
                    db()->prepare("UPDATE zinas SET virsraksts=?,teksts=?,kategorija=?,izveidots=?,publicets=? WHERE id=?")
                        ->execute([$v,$t,$kat,$dt,$pub,$id]);
                    $msg='Ziņa atjaunināta.';
                }
            }
        } elseif ($act==='delete') {
            db()->prepare("DELETE FROM zinas WHERE id=?")->execute([(int)($_POST['id']??0)]);
            $msg='Ziņa dzēsta.';
        } elseif ($act==='toggle') {
            db()->prepare("UPDATE zinas SET publicets=1-publicets WHERE id=?")->execute([(int)($_POST['id']??0)]);
            $msg='Tila vaihdettu.';
        }
    }
}
$csrf=$_SESSION['bcsrf'];

/* ---- Muokattava ---- */
$edit=null;
if (isset($_GET['edit'])) {
    $st=db()->prepare("SELECT * FROM zinas WHERE id=?"); $st->execute([(int)$_GET['edit']]);
    $edit=$st->fetch();
}
$list = db()->query("SELECT id,virsraksts,izveidots,publicets,kategorija FROM zinas ORDER BY izveidots DESC, id DESC")->fetchAll();
$nowval = $edit ? date('Y-m-d\TH:i', strtotime($edit['izveidots'])) : date('Y-m-d\TH:i');
?>
<!DOCTYPE html>
<html lang="fi"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Ziņas — administrācija</title>
<link rel="stylesheet" href="blogi_admin.css">
</head>
<body class="ba-body">
<div class="ba-wrap">
  <div class="ba-head">
    <h1>Ziņas · administrācija</h1>
    <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap">
      <a class="ba-logout" href="/passkey/manage.php">Ierīces</a>
      <a class="ba-logout" href="?logout=1&amp;t=<?= h($csrf) ?>">Iziet</a>
    </div>
  </div>

  <?php if ($msg): ?><p class="ba-msg <?= $err?'ba-msg-err':'ba-msg-ok' ?>"><?= h($msg) ?></p><?php endif; ?>

    <form method="post" class="ba-card">
      <h2 class="ba-sub"><?= $edit ? 'Muokkaa ziņu' : 'Jauna ziņa' ?></h2>
      <input type="hidden" name="action" value="<?= $edit?'update':'add' ?>">
      <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
      <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>

      <label class="ba-label" for="virsraksts">Virsraksts (otsikko)</label>
      <input class="ba-input" type="text" id="virsraksts" name="virsraksts" maxlength="160" required
             value="<?= $edit ? h($edit['virsraksts']) : '' ?>">

      <label class="ba-label" for="teksts">Teksts</label>
      <textarea class="ba-input ba-textarea" id="teksts" name="teksts" rows="8" required><?= $edit ? h($edit['teksts']) : '' ?></textarea>
      <p class="ba-hint">Vinkki: lisää kuva tekstin sekaan kirjoittamalla <code>[kuva:tiedosto.jpg]</code> haluamaasi kohtaan. Lataa kuvat alla ja kopioi niiden merkintä.</p>

      <?php $katnyt = $edit ? ($edit['kategorija'] ?? 'zina') : 'zina'; ?>
      <label class="ba-label" for="kategorija">Kategoria</label>
      <select class="ba-input" id="kategorija" name="kategorija">
        <option value="zina" <?= $katnyt==='zina'?'selected':'' ?>>Ziņa (uutinen) — näkyy Manas ziņas -sivulla</option>
        <option value="celojums" <?= $katnyt==='celojums'?'selected':'' ?>>Ceļojums (matka) — näkyy matkasivulla</option>
      </select>

      <div class="ba-row">
        <div>
          <label class="ba-label" for="izveidots">Datums (järjestys)</label>
          <input class="ba-input" type="datetime-local" id="izveidots" name="izveidots" value="<?= h($nowval) ?>">
        </div>
        <label class="ba-check">
          <input type="checkbox" name="publicets" <?= (!$edit || (int)$edit['publicets']===1) ? 'checked' : '' ?>>
          Julkaistu
        </label>
      </div>

      <div class="ba-actions">
        <button class="ba-btn ba-btn--primary" type="submit"><?= $edit?'Tallenna muutokset':'Lisää ziņa' ?></button>
        <?php if ($edit): ?><a class="ba-btn" href="blogi_admin.php">Peruuta</a><?php endif; ?>
      </div>
    </form>

    <?php
      // Vain turvallisesti nimetyt kuvat (sama merkistö kuin latauksessa) — estää XSS:n onclick-koodissa
      $kuvat = is_dir($KUVADIR)
        ? array_values(array_filter(scandir($KUVADIR), function($f){
              return preg_match('/^[A-Za-z0-9_\-.]+\.(jpe?g|png|webp|gif)$/i', $f);
          }))
        : [];
      rsort($kuvat);
    ?>
    <div class="ba-card">
      <h2 class="ba-sub">Attēli / Kuvat (<?= count($kuvat) ?>)</h2>
      <form method="post" enctype="multipart/form-data" class="ba-uploadrow">
        <input type="hidden" name="action" value="upload_img">
        <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
        <input class="ba-input" type="file" name="kuva" accept="image/*" required>
        <button class="ba-btn ba-btn--primary" type="submit">Lataa kuva</button>
      </form>
      <?php if (!$kuvat): ?>
        <p class="ba-empty" style="margin-top:1rem;">Ei vielä kuvia.</p>
      <?php else: ?>
        <div class="ba-gallery">
          <?php foreach ($kuvat as $k): ?>
            <figure class="ba-thumb">
              <img src="blogi_kuvat/<?= h(rawurlencode($k)) ?>" alt="" loading="lazy">
              <code class="ba-code" onclick="navigator.clipboard&&navigator.clipboard.writeText('[kuva:<?= h($k) ?>]')" title="Kopioi merkintä">[kuva:<?= h($k) ?>]</code>
              <form method="post" onsubmit="return confirm('Poistetaanko kuva?');">
                <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
                <input type="hidden" name="action" value="delete_img">
                <input type="hidden" name="fn" value="<?= h($k) ?>">
                <button class="ba-btn ba-btn--sm ba-btn--del" type="submit">Poista</button>
              </form>
            </figure>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="ba-card">
      <h2 class="ba-sub">Ziņas (<?= count($list) ?>)</h2>
      <?php if (!$list): ?>
        <p class="ba-empty">Ei vielä uutisia.</p>
      <?php else: foreach ($list as $r): ?>
        <div class="ba-item <?= (int)$r['publicets']===0?'ba-item--hidden':'' ?>">
          <div class="ba-item-main">
            <span class="ba-item-date"><?= h(date('d.m.Y', strtotime($r['izveidots']))) ?></span>
            <span class="ba-item-title"><?= h($r['virsraksts']) ?></span>
            <?php if (($r['kategorija'] ?? 'zina')==='celojums'): ?><span class="ba-tag ba-tag--cel">ceļojums</span><?php endif; ?>
            <?php if ((int)$r['publicets']===0): ?><span class="ba-tag">piilossa</span><?php endif; ?>
          </div>
          <div class="ba-item-act">
            <a class="ba-btn ba-btn--sm" href="?edit=<?= (int)$r['id'] ?>">Muokkaa</a>
            <form method="post"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="ba-btn ba-btn--sm" name="action" value="toggle"><?= (int)$r['publicets']===1?'Piilota':'Julkaise' ?></button></form>
            <form method="post" onsubmit="return confirm('Dzēst šo ziņu?');"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="ba-btn ba-btn--sm ba-btn--del" name="action" value="delete">Dzēst</button></form>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>

</div>
</body>
</html>
