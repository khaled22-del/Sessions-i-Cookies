<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/inc/funcions.php';
$dades = require __DIR__ . '/inc/dades.php';

const DURADA_COOKIE = 30 * 24 * 3600; // 30 dies

/** Opcions comunes de les cookies */
function opcionsCookie(int $expira): array
{
    return [
        'expires' => $expira,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ];
}

// Si ja ha iniciat sessió, a benvinguda
if (!empty($_SESSION['usuari'])) {
    header('Location: benvinguda.php');
    exit;
}

//  Login (POST al mateix fitxer)
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $email = $_POST['email'] ?? '';
    $contrasenya = $_POST['contrasenya'] ?? '';
    $email = is_string($email) ? trim($email) : '';
    $contrasenya = is_string($contrasenya) ? $contrasenya : '';

    $trobat = buscaUsuari($dades['usuaris'], $email);

    if ($trobat !== null && hash_equals($trobat['contrasenya'], $contrasenya)) {
        session_regenerate_id(true);
        $_SESSION['usuari'] = [
            'email' => $trobat['email'],
            'nom' => $trobat['nom'],
            'rol' => $trobat['rol'],
        ];
        $_SESSION['hora_login'] = time();
        $_SESSION['ultima_activitat'] = time();

        // Cookie "Recorda el meu email"
        if (isset($_POST['recorda'])) {
            setcookie('email_recordat', $trobat['email'], opcionsCookie(time() + DURADA_COOKIE));
        } else {
            setcookie('email_recordat', '', opcionsCookie(time() - 3600));
        }

        header('Location: benvinguda.php');
        exit;
    }

    header('Location: index.php?error=credencials');
    exit;
}

// ---- Cookie de darrera visita (només es valida/mostra en GET) ----
$missatgeVisita = 'Benvingut! És la teva primera visita.';
$cookieVisita = $_COOKIE['darrera_visita'] ?? null;
if (is_string($cookieVisita) && ctype_digit($cookieVisita)) {
    $moment = (int) $cookieVisita;
    if ($moment > 0 && $moment <= time()) {
        $missatgeVisita = 'La teva darrera visita va ser ' . tempsDesDe($moment) . '.';
    }
}
// Actualitzem la cookie amb l'hora actual (abans de qualsevol sortida)
setcookie('darrera_visita', (string) time(), opcionsCookie(time() + DURADA_COOKIE));

// ---- Missatges d'error per GET ----
$missatgesError = [
    'credencials' => 'Credencials no vàlides.',
    'acces' => 'Primer has d\'iniciar sessió.',
    'caducada' => 'La sessió ha caducat per inactivitat.',
];
$codi = $_GET['error'] ?? '';
$missatgeError = (is_string($codi) && array_key_exists($codi, $missatgesError))
    ? $missatgesError[$codi]
    : null;

// ---- Email recordat (validat) ----
$emailRecordat = '';
$cookieEmail = $_COOKIE['email_recordat'] ?? null;
if (is_string($cookieEmail) && filter_var($cookieEmail, FILTER_VALIDATE_EMAIL) !== false) {
    $emailRecordat = $cookieEmail;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Votacions – Inici de sessió</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 420px; margin: 2rem auto; padding: 1rem; }
        label { display: block; margin-top: .8rem; font-weight: bold; }
        input[type=email], input[type=password] { width: 100%; padding: .5rem; box-sizing: border-box; }
        .error { color: #b00; }
        .visita { color: #555; }
    </style>
</head>
<body>
<h1>Votacions de l'institut</h1>
<p class="visita"><?= h($missatgeVisita) ?></p>
<?php if ($missatgeError !== null): ?>
    <p class="error"><?= h($missatgeError) ?></p>
<?php endif; ?>
<form action="index.php" method="post">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= h($emailRecordat) ?>" required>

    <label for="contrasenya">Contrasenya</label>
    <input type="password" id="contrasenya" name="contrasenya" required>

    <label><input type="checkbox" name="recorda" value="1"<?= $emailRecordat !== '' ? ' checked' : '' ?>> Recorda el meu email</label>

    <p><button type="submit">Entrar</button></p>
</form>
</body>
</html>
