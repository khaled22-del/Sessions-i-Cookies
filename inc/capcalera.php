<?php
// Variables esperades: $usuari (array de la sessió), $titol (opcional)
$titol = $titol ?? 'Votacions';
$missatgeFlash = llegeixMissatge();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($titol) ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 0 auto; padding: 1rem; }
        nav { border-bottom: 1px solid #ccc; padding-bottom: .5rem; margin-bottom: 1rem; }
        nav a { margin-right: 1rem; }
        .flash { background: #eef6ff; border: 1px solid #9cc; padding: .6rem; margin-bottom: 1rem; }
        .error { color: #b00; }
        table { border-collapse: collapse; }
        td, th { border: 1px solid #ccc; padding: .4rem .8rem; text-align: left; }
    </style>
</head>
<body>
<nav>
    <strong><?= h($usuari['nom'] ?? '') ?></strong> |
    <a href="benvinguda.php">Inici</a>
    <?php if (($usuari['rol'] ?? '') === 'votant'): ?>
        <a href="votacio.php">Votar</a>
    <?php endif; ?>
    <a href="resultats.php">Resultats</a>
    <a href="logout.php">Tancar sessió</a>
</nav>
<?php if ($missatgeFlash !== null): ?>
    <div class="flash"><?= h($missatgeFlash) ?></div>
<?php endif; ?>
