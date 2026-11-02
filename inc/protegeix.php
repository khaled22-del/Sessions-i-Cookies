<?php
declare(strict_types=1);
// Es carrega just després de session_start() a totes les pàgines privades.

const TEMPS_MAXIM_INACTIVITAT = 300; // 5 minuts (posar 30 per provar)

if (empty($_SESSION['usuari']) || !is_array($_SESSION['usuari'])) {
    header('Location: index.php?error=acces');
    exit;
}

$ultima = $_SESSION['ultima_activitat'] ?? 0;
if (!is_int($ultima) || time() - $ultima > TEMPS_MAXIM_INACTIVITAT) {
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
    header('Location: index.php?error=caducada');
    exit;
}

$_SESSION['ultima_activitat'] = time();
$usuari = $_SESSION['usuari'];
