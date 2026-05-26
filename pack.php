<?php
/**
 * Utilidad para ofuscar el TOKEN y CHAT_ID de Telegram.
 *
 *   USO:
 *     1. Sube este archivo junto con config.php
 *     2. Abre en el navegador:  https://tu-dominio.com/pack.php?v=TU_TOKEN
 *     3. Copia el valor que aparece y pégalo en config.php ($_TK_PACK)
 *     4. Repite para tu CHAT_ID
 *     5. ⚠️ ELIMINA ESTE ARCHIVO DEL SERVIDOR cuando termines.
 */
require_once __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=utf-8');

$v = isset($_GET['v']) ? $_GET['v'] : '';
if ($v === '') {
    echo "Pasa el valor con ?v=...\n\nEjemplo:  pack.php?v=123456:ABCdef...\n";
    exit;
}

echo nq_pack($v);
