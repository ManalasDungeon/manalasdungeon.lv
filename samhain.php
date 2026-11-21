<?php
require_once __DIR__ . '/includes/bootstrap.php';   // yhteiset apufunktiot, kieli, <head>-palat
/* Samhain-aloitussivu — väliaikainen, vain 31.10.
   index.php näyttää tämän ja ohjaa pääsivulle 30 s kuluttua (tai ?enter=1). */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#07070b">
<title>Samhain</title>
<?= head_fonts('eb-garamond-latin-wght-normal.woff2', '') ?><style>
  *{box-sizing:border-box;margin:0;padding:0}
  html,body{height:100%}
  body{
    background:#07070b; color:#f3ece0;
    font-family:'EB Garamond', Georgia, serif;
    min-height:100vh; min-height:100dvh;
    display:flex; flex-direction:column; align-items:center;
    text-align:center; overflow:hidden; position:relative;
    padding:clamp(28px,6vw,60px) 20px 0;
  }
  body::after{
    content:''; position:fixed; inset:0; pointer-events:none; z-index:2;
    background:
      radial-gradient(60% 45% at 50% 78%, rgba(255,150,40,.10), transparent 72%),
      radial-gradient(120% 90% at 50% 50%, transparent 52%, rgba(0,0,0,.65) 100%);
  }
  .poem{
    max-width:30rem; font-size:clamp(1.14rem,2.6vw,1.6rem); line-height:1.62;
    letter-spacing:.01em; text-shadow:0 2px 22px rgba(0,0,0,.7);
    margin-top:clamp(8px,3vh,36px);
    animation:fade 2.6s ease both; position:relative; z-index:1;
  }
  .candle{
    margin-top:auto; max-height:56vh; width:auto; max-width:94vw; display:block;
    position:relative; z-index:1;
    animation:fade 3s ease .4s both, flick 3.6s ease-in-out .8s infinite;
  }
  @keyframes fade{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
  @keyframes flick{
    0%,100%{filter:drop-shadow(0 -6px 34px rgba(255,150,40,.22)) brightness(1)}
    45%{filter:drop-shadow(0 -8px 50px rgba(255,170,60,.36)) brightness(1.07)}
    70%{filter:drop-shadow(0 -6px 38px rgba(255,150,40,.26)) brightness(1.02)}
  }
  .bar{position:fixed; left:0; bottom:0; height:3px; width:100%; background:rgba(255,255,255,.08); z-index:3}
  .bar span{display:block; height:100%; width:0; background:linear-gradient(90deg,#e4c56a,#ffb347); animation:fill 30s linear forwards}
  @keyframes fill{to{width:100%}}
  .skip{
    position:fixed; top:16px; right:20px; z-index:3;
    color:rgba(243,236,224,.7); text-decoration:none; font-size:.92rem; letter-spacing:.02em;
    border-bottom:1px solid rgba(228,197,106,.4); padding-bottom:2px; transition:color .2s, border-color .2s;
  }
  .skip:hover{color:#fff; border-color:#fff}
  @media (prefers-reduced-motion:reduce){ *{animation:none !important} }
</style>
</head>
<body>
  <a class="skip" href="?enter=1">Enter &rarr;</a>

  <p class="poem">On Samhain night, set a lantern in your window for those who have gone before. Speak their names, remember their stories, and let its quiet light remind you: no soul is entirely lost while someone remembers.</p>

  <img class="candle" src="images/samhain_candle.jpg" alt="">

  <div class="bar"><span></span></div>

  <script nonce="<?= csp_nonce() ?>">
    setTimeout(function(){
      try { document.cookie = 'samhain_seen=1; path=/; max-age=86400'; } catch(e){}
      location.href = '?enter=1';
    }, 30000);
  </script>
</body>
</html>
