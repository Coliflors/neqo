<?php
/* =========================================================
 *  Configuración de Telegram (ofuscada)
 *
 *  CÓMO AÑADIR TU TOKEN:
 *    1. Abre la utilidad incluida en este mismo archivo:
 *         php -r "require 'config.php'; echo nq_pack('TU_TOKEN_AQUI');"
 *       (también funciona desde tu hosting con un script temporal)
 *    2. Copia el string que sale y pégalo en $_TK_PACK abajo.
 *    3. Haz lo mismo con tu CHAT_ID y pégalo en $_CH_PACK.
 *
 *  Si prefieres lo rápido: usa nq_pack() en línea con tu valor
 *  y reemplaza la línea $_TK_PACK / $_CH_PACK.
 * ========================================================= */

// Tabla "envuelta" - el token y chat_id ofuscados.
// Reemplaza los valores entre comillas con la salida de nq_pack().
$_TK_PACK = '8391713374:AAGkurqOVeJjE1i29jh5IaCJG8PU8qeqxhE';
$_CH_PACK = '7655000874';

/**
 * Empaqueta un valor (úsalo una sola vez para generar el string).
 * Capa: rotación XOR con clave fija + base64 + reverse.
 */
function nq_key() {
    // Clave fija (no depende del entorno). Cambia este string si quieres
    // invalidar paquetes antiguos.
    return 'n3qU0-fx-9k2P_T0k3n-Cipher-2026';
}

function nq_pack($plain) {
    $key = nq_key();
    $out = '';
    for ($i = 0, $n = strlen($plain); $i < $n; $i++) {
        $out .= chr(ord($plain[$i]) ^ ord($key[$i % strlen($key)]));
    }
    return strrev(base64_encode($out));
}

/**
 * Desempaqueta el valor ofuscado (uso interno).
 */
function nq_unpack($packed) {
    // Si el "pack" parece estar en texto plano (placeholder) lo retorna como está.
    if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $packed)) return $packed;
    $decoded = base64_decode(strrev($packed), true);
    if ($decoded === false) return $packed;

    $key = nq_key();
    $out = '';
    for ($i = 0, $n = strlen($decoded); $i < $n; $i++) {
        $out .= chr(ord($decoded[$i]) ^ ord($key[$i % strlen($key)]));
    }
    return $out;
}

// Constantes que usa send.php
define('TELEGRAM_BOT_TOKEN', nq_unpack($_TK_PACK));
define('TELEGRAM_CHAT_ID',   nq_unpack($_CH_PACK));
