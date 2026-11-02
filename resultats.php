<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/inc/protegeix.php';
require_once __DIR__ . '/inc/funcions.php';
$dades = require __DIR__ . '/inc/dades.php';

$consulta = $dades['consulta'];

$pucVeure = $usuari['rol'] === 'admin'
    || jaHaVotat($usuari['email'])
    || !votacioOberta($consulta['tancament']);

if (!$pucVeure) {
    afegeixMissatge('Podràs veure els resultats quan hagis votat.');
    header('Location: votacio.php');
    exit;
}

$vots = llegeixVots();
$comptes = recompte($vots, $consulta['opcions']);
$total = array_sum($comptes);
$votantsTotals = totalVotants($dades['usuaris']);
$votantsQueHanVotat = votsDeVotants($dades['usuaris'], $vots);

$titol = 'Resultats';
require __DIR__ . '/inc/capcalera.php';
?>
<h1>Resultats</h1>
<h2><?= h($consulta['pregunta']) ?></h2>
<table>
    <tr><th>Opció</th><th>Vots</th><th>%</th></tr>
    <?php foreach ($consulta['opcions'] as $clau => $text):
        $n = $comptes[$clau] ?? 0;
        $pct = $total > 0 ? (int) round($n * 100 / $total) : 0;
    ?>
        <tr>
            <td><?= h($text) ?></td>
            <td><?= $n ?></td>
            <td><?= $pct ?>%</td>
        </tr>
    <?php endforeach; ?>
</table>
<p>Participació: <?= $votantsQueHanVotat ?> de <?= $votantsTotals ?> votants</p>
<?php require __DIR__ . '/inc/peu.php'; ?>
