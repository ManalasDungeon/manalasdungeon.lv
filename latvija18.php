<?php
require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, kieli, <head>-palat
/* Latvijan itsenäisyyspäivän avausanimaatio — vain 18.11.2026 (Riian aikaa).
   index.php näyttää tämän kerran ja ohjaa etusivulle 30 s kuluttua (tai ?enter=1).
   Poistuu käytöstä itsestään 19.11. */
$__lang  = (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'fi'], true)) ? $_GET['lang'] : '';
$enterUrl = '?enter=1' . ($__lang ? '&lang=' . $__lang : '');
$skip = ['' => 'Ieiet vietnē', 'en' => 'Enter the site', 'fi' => 'Siirry sivustolle'][$__lang];
?>
<!DOCTYPE html>
<html lang="lv">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#8E2933">
<title>18. novembris — Daudz laimes dzimšanas dienā, Latvija!</title>
<?= head_fonts('lora-latin-wght-normal.woff2', '') ?><style>
  :root{ --car:#9A2B36; --car-d:#7E222C; --car-l:#A9333F; --gold:#E4C77A; --white:#fff; }
  *{box-sizing:border-box;margin:0;padding:0}
  html,body{height:100%}
  body{
    min-height:100vh; min-height:100dvh; overflow:hidden; position:relative;
    background:radial-gradient(120% 90% at 50% 50%, #A3303B 0%, #8E2933 55%, #761F28 100%);
    color:#fff; font-family:'Poppins', system-ui, sans-serif; text-align:center;
    display:grid; grid-template-rows:2fr 1fr 2fr;           /* lipun mittasuhteet 2 : 1 : 2 */
    animation:bgIn 1.2s ease both;
  }
  @keyframes bgIn{from{opacity:0}}
  canvas{position:fixed; inset:0; width:100%; height:100%; pointer-events:none; z-index:0}
  .top,.band,.bottom{position:relative; z-index:1; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:0 20px}
  .top{justify-content:flex-end; padding-bottom:clamp(14px,4vh,48px)}
  .bottom{justify-content:flex-start; padding-top:clamp(14px,4vh,44px)}

  /* Brīvības pieminekļa Milda taustalla: viininpunaiseksi sävytetty, pehmennetty siluetti, kultaiset tähdet */
  .milda{position:fixed; left:50%; top:1.5vh; height:88vh; width:auto; z-index:0; pointer-events:none;
    transform:translateX(-50%); opacity:0;
    -webkit-mask-image:linear-gradient(#000 70%, transparent 100%); mask-image:linear-gradient(#000 70%, transparent 100%);
    animation:mildaIn 3.2s cubic-bezier(.2,.7,.2,1) .2s forwards, mildaGlow 5s ease-in-out 3.4s infinite}
  @keyframes mildaIn{from{opacity:0; transform:translateX(-50%) translateY(4vh) scale(.97)} to{opacity:.95; transform:translateX(-50%)}}
  @keyframes mildaGlow{0%,100%{filter:drop-shadow(0 0 0 rgba(243,217,138,0))} 50%{filter:drop-shadow(0 0 14px rgba(243,217,138,.35))}}

  .date{font-weight:400; font-size:clamp(.9rem,2vw,1.55rem); letter-spacing:.42em; margin-right:-.42em; opacity:0;
    animation:spread 1.4s cubic-bezier(.2,.7,.2,1) .8s both}
  @keyframes spread{from{opacity:0; letter-spacing:1.1em; filter:blur(6px)} to{opacity:.95; letter-spacing:.42em; filter:none}}
  .rule{width:clamp(70px,6.5vw,124px); height:2px; background:rgba(255,255,255,.85); margin:clamp(10px,2.6vh,30px) 0; transform:scaleX(0);
    animation:grow .9s ease 1.4s both}
  .bottom .rule{animation-delay:5.6s}
  @keyframes grow{to{transform:scaleX(1)}}

  .script{font-family:'Lora', Georgia, serif; font-style:italic; font-weight:500; line-height:1.12;
    font-size:clamp(1.9rem,6.2vw,5.6rem); text-shadow:0 4px 30px rgba(0,0,0,.18)}
  .script span{display:inline-block; opacity:0; transform:translateY(.45em); animation:rise .8s cubic-bezier(.2,.7,.2,1) both}
  @keyframes rise{to{opacity:1; transform:none}}

  /* Valkoinen raita ja LATVIJA! */
  .band{background:transparent; overflow:hidden}
  .band::before{content:''; position:absolute; inset:0; background:#fff; transform:scaleX(0);
    animation:band 1.1s cubic-bezier(.7,0,.2,1) 2.5s forwards; box-shadow:0 0 60px rgba(255,255,255,.25)}
  @keyframes band{to{transform:scaleX(1)}}
  .band::after{                                   /* kiiltojuova raidan yli */
    content:''; position:absolute; top:0; bottom:0; left:-30%; width:22%;
    background:linear-gradient(100deg, transparent, rgba(228,199,122,.35), transparent);
    transform:skewX(-18deg); animation:shine 6s ease-in-out 4.8s infinite}
  @keyframes shine{0%{left:-30%} 35%,100%{left:130%}}
  .word{position:relative; display:flex; font-family:'Lora', Georgia, serif; font-weight:600; color:var(--car);
    font-size:clamp(2.6rem,10vw,9.2rem); line-height:1}
  .word span{display:inline-block; margin:0 .13em; opacity:0; transform:translateY(-60%) scale(1.25);
    animation:drop .7s cubic-bezier(.3,1.5,.5,1) both}
  @keyframes drop{60%{opacity:1} to{opacity:1; transform:none}}

  .sub{font-weight:400; font-size:clamp(.7rem,1.55vw,1.32rem); letter-spacing:.2em; margin-right:-.2em; opacity:0; animation:fade 1s ease 6.1s both}
  .years{font-weight:300; font-size:clamp(.8rem,1.6vw,1.4rem); letter-spacing:.24em; margin:clamp(8px,1.8vh,20px) -.24em 0 0; opacity:0; animation:fade 1s ease 6.6s both; font-variant-numeric:tabular-nums}
  @keyframes fade{to{opacity:.82}}

  .skip{position:fixed; top:16px; right:20px; z-index:3; color:rgba(255,255,255,.82); text-decoration:none;
    font-size:.88rem; letter-spacing:.06em; border-bottom:1px solid rgba(228,199,122,.6); padding-bottom:2px; transition:color .2s, border-color .2s}
  .skip:hover{color:#fff; border-color:#fff}
  .bar{position:fixed; left:0; bottom:0; height:3px; width:100%; background:rgba(255,255,255,.12); z-index:3}
  .bar span{display:block; height:100%; width:0; background:linear-gradient(90deg,#fff,var(--gold)); animation:fill 30s linear forwards}
  @keyframes fill{to{width:100%}}

  @media (max-width:600px){
    .word{font-size:clamp(2.6rem,14.5vw,5rem)}
    .word span{margin:0 .035em}
    .script{font-size:clamp(1.7rem,8.4vw,2.6rem)}
    .sub{letter-spacing:.12em; margin-right:-.12em; max-width:22em; line-height:1.6}
  }
  @media (prefers-reduced-motion:reduce){
    *,*::before{animation:none !important}
    .date,.script span,.word span,.sub,.years{opacity:1; transform:none}
    .milda{opacity:.95; transform:translateX(-50%)}
    .rule,.band::before{transform:none}
    .band::after{display:none}
  }
</style>
</head>
<body>
  <img class="milda" src="images/latvija18_milda.webp?v=<?= @filemtime(__DIR__ . '/images/latvija18_milda.webp') ?: 1 ?>" alt="" aria-hidden="true">
  <canvas id="fx" aria-hidden="true"></canvas>
  <a class="skip" href="<?= htmlspecialchars($enterUrl, ENT_QUOTES, 'UTF-8') ?>"><?= $skip ?> &rarr;</a>

  <section class="top">
    <p class="date">18. NOVEMBRIS</p>
    <div class="rule"></div>
    <h1 class="script">
      <span style="animation-delay:1.7s">Daudz</span> <span style="animation-delay:1.88s">laimes</span>
      <span style="animation-delay:2.06s">dzimšanas</span> <span style="animation-delay:2.24s">dienā,</span>
    </h1>
  </section>

  <section class="band">
    <p class="word" role="img" aria-label="LATVIJA!">
      <?php foreach (preg_split('//u', 'LATVIJA!', -1, PREG_SPLIT_NO_EMPTY) as $i => $ch): ?><span aria-hidden="true" style="animation-delay:<?= 3.3 + $i * .11 ?>s"><?= $ch ?></span><?php endforeach; ?>
    </p>
  </section>

  <section class="bottom">
    <p class="script">
      <span style="animation-delay:4.7s">Saules</span> <span style="animation-delay:4.9s">mūžu</span>
      <span style="animation-delay:5.1s">Latvijai!</span>
    </p>
    <div class="rule"></div>
    <p class="sub">LATVIJAS REPUBLIKAS PROKLAMĒŠANAS 108. GADADIENA</p>
    <p class="years">1918 – <span id="yr">2026</span></p>
  </section>

  <div class="bar"><span></span></div>

  <script nonce="<?= csp_nonce() ?>">
  (function(){
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* 1918 → 2026 lasketaan vuosiluvut ylös, kun rivi ilmestyy */
    var yr = document.getElementById('yr');
    if (!reduce) {
      yr.textContent = '1918';
      setTimeout(function(){
        var t0 = null;
        (function step(t){ if(!t0) t0 = t; var k = Math.min(1,(t-t0)/1600), e = 1-Math.pow(1-k,3);
          yr.textContent = String(Math.round(1918 + 108*e)); if (k < 1) requestAnimationFrame(step); })(performance.now());
      }, 6700);
    }

    /* Ilotulitteet ja konfetti (valkoinen, kulta, viininpunainen) */
    var cv = document.getElementById('fx'), ctx = cv.getContext('2d');
    var W, H, dpr = Math.min(window.devicePixelRatio || 1, 2), parts = [], conf = [];
    function size(){ W = cv.width = innerWidth * dpr; H = cv.height = innerHeight * dpr; }
    size(); addEventListener('resize', size);
    if (reduce) return;

    var COLORS = ['255,255,255', '243,217,138', '228,199,122', '255,236,200'];
    function burst(){
      var x = W * (0.12 + Math.random() * 0.76), y = H * (0.12 + Math.random() * 0.3);
      var n = innerWidth < 600 ? 46 : 80, c = COLORS[Math.floor(Math.random() * COLORS.length)];
      for (var i = 0; i < n; i++) {
        var a = (Math.PI * 2 * i) / n + Math.random() * .1, s = (1.6 + Math.random() * 2.6) * dpr;
        parts.push({ x:x, y:y, vx:Math.cos(a)*s, vy:Math.sin(a)*s, life:1, c:c, r:(1 + Math.random()*1.4)*dpr });
      }
    }
    var confColors = ['#ffffff', '#F3D98A', '#C9414F', '#ffffff'];
    function addConf(){
      conf.push({ x:Math.random()*W, y:-20*dpr, w:(5+Math.random()*6)*dpr, h:(8+Math.random()*8)*dpr,
        vy:(0.6+Math.random()*1.2)*dpr, vx:(Math.random()-.5)*0.6*dpr, rot:Math.random()*6, vr:(Math.random()-.5)*0.08,
        sway:Math.random()*6, c:confColors[Math.floor(Math.random()*confColors.length)] });
    }

    var start = performance.now(), lastBurst = 0;
    function frame(t){
      var el = t - start;
      ctx.clearRect(0, 0, W, H);
      if (el > 5200 && t - lastBurst > (innerWidth < 600 ? 2200 : 1500)) { burst(); lastBurst = t; }
      if (el > 3600 && conf.length < (innerWidth < 600 ? 40 : 90) && Math.random() < .35) addConf();

      for (var i = parts.length - 1; i >= 0; i--) {
        var p = parts[i];
        p.vx *= .985; p.vy = p.vy * .985 + .035 * dpr; p.x += p.vx; p.y += p.vy; p.life -= .011;
        if (p.life <= 0) { parts.splice(i, 1); continue; }
        ctx.beginPath(); ctx.fillStyle = 'rgba(' + p.c + ',' + p.life.toFixed(3) + ')';
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2); ctx.fill();
      }
      for (var j = conf.length - 1; j >= 0; j--) {
        var q = conf[j];
        q.y += q.vy; q.x += q.vx + Math.sin((t / 600) + q.sway) * .4 * dpr; q.rot += q.vr;
        if (q.y > H + 30) { conf.splice(j, 1); continue; }
        ctx.save(); ctx.translate(q.x, q.y); ctx.rotate(q.rot);
        ctx.globalAlpha = .85; ctx.fillStyle = q.c;
        ctx.fillRect(-q.w/2, -q.h/2 * Math.abs(Math.cos(t/300 + q.sway)), q.w, q.h * Math.abs(Math.cos(t/300 + q.sway)));
        ctx.restore();
      }
      requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  })();

  /* Etusivulle 30 s kuluttua; eväste estää näyttämästä uudelleen saman päivän aikana */
  setTimeout(function(){
    try { document.cookie = 'lv18_seen=1; path=/; max-age=86400; SameSite=Lax'; } catch(e){}
    location.href = <?= json_encode($enterUrl) ?>;
  }, 30000);
  document.querySelector('.skip').addEventListener('click', function(){
    try { document.cookie = 'lv18_seen=1; path=/; max-age=86400; SameSite=Lax'; } catch(e){}
  });
  </script>
</body>
</html>
