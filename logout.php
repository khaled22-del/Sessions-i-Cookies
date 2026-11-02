<?php
declare(strict_types=1);

session_start();

$nom = $_SESSION['usuari']['nom'] ?? 'usuari';
$nom = is_string($nom) ? $nom : 'usuari';

// Buidem la sessió, esborrem la cookie de sessió i la destruïm.
// NO s'esborren darrera_visita ni email_recordat.
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $p['path'],
        'domain' => $p['domain'],
        'secure' => $p['secure'],
        'httponly' => $p['httponly'],
        'samesite' => 'Lax',
    ]);
}
session_destroy();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Sessió tancada</title>
</head>
<body>
<h1>Fins la propera, <?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') ?>!</h1>
<p><a href="index.php">Tornar a entrar</a></p>
</body>
</html>
