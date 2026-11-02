<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/inc/protegeix.php';
require_once __DIR__ . '/inc/funcions.php';
$dades = require __DIR__ . '/inc/dades.php';

$consulta = $dades['consulta'];
$oberta = votacioOberta($consulta['tancament']);
$haVotat = jaHaVotat($usuari['email']);
$horaLogin = (int) ($_SESSION['hora_login'] ?? time());
$dataTancament = date('d/m/Y \a \l\e\s H:i', (int) strtotime($consulta['tancament']));

$titol = 'Inici';
require __DIR__ . '/inc/capcalera.php';
?>
<h1>Hola, <?= h($usuari['nom']) ?>!</h1>
<p>Has iniciat sessió <?= h(tempsDesDe($horaLogin)) ?>.</p>
<h2><?= h($consulta['pregunta']) ?></h2>
<?php if ($oberta): ?>
    <p>La votació és oberta fins al <?= h($dataTancament) ?>.</p>
<?php else: ?>
    <p>La votació està tancada (va acabar el <?= h($dataTancament) ?>).</p>
<?php endif; ?>

<?php if ($usuari['rol'] === 'votant'): ?>
    <?php if ($haVotat): ?>
        <p>Ja has votat. <a href="resultats.php">Veure els resultats</a></p>
    <?php elseif ($oberta): ?>
        <p><a href="votacio.php">Votar ara</a></p>
    <?php endif; ?>
<?php endif; ?>
<?php if ($usuari['rol'] === 'admin'): ?>
    <p><a href="resultats.php">Veure els resultats</a></p>
<?php endif; ?>
<?php require __DIR__ . '/inc/peu.php'; ?>
