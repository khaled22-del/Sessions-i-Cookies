<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/inc/protegeix.php';
require_once __DIR__ . '/inc/funcions.php';
$dades = require __DIR__ . '/inc/dades.php';

$consulta = $dades['consulta'];

// Comprovacions, en aquest ordre
if ($usuari['rol'] !== 'votant') {
    afegeixMissatge('Només els votants poden votar.');
    header('Location: benvinguda.php');
    exit;
}
if (!votacioOberta($consulta['tancament'])) {
    afegeixMissatge('La votació està tancada.');
    header('Location: resultats.php');
    exit;
}
if (jaHaVotat($usuari['email'])) {
    afegeixMissatge('Ja has votat. Gràcies!');
    header('Location: resultats.php');
    exit;
}

$error = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $opcio = $_POST['opcio'] ?? null;
    if (is_string($opcio) && array_key_exists($opcio, $consulta['opcions'])) {
        guardaVot($usuari['email'], $opcio);
        afegeixMissatge('Vot registrat. Gràcies per participar!');
        header('Location: resultats.php');
        exit;
    }
    $error = 'Tria una de les opcions.';
}

$titol = 'Votar';
require __DIR__ . '/inc/capcalera.php';
?>
<h1><?= h($consulta['pregunta']) ?></h1>
<?php if ($error !== null): ?>
    <p class="error"><?= h($error) ?></p>
<?php endif; ?>
<form action="votacio.php" method="post">
    <?php foreach ($consulta['opcions'] as $clau => $text): ?>
        <p><label><input type="radio" name="opcio" value="<?= h((string) $clau) ?>"> <?= h($text) ?></label></p>
    <?php endforeach; ?>
    <button type="submit">Votar</button>
</form>
<?php require __DIR__ . '/inc/peu.php'; ?>
