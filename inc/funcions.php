<?php
declare(strict_types=1);

require_once __DIR__ . '/vots.php';

/** Retorna l'usuari amb aquest email, o null. */
function buscaUsuari(array $usuaris, string $email): ?array
{
    foreach ($usuaris as $usuari) {
        if (($usuari['email'] ?? null) === $email) {
            return $usuari;
        }
    }
    return null;
}

/** "fa menys d'un minut", "fa 5 minuts", "fa 2 hores", "fa 3 dies" */
function tempsDesDe(int $moment): string
{
    $segons = max(0, time() - $moment);

    if ($segons < 60) {
        return "fa menys d'un minut";
    }
    if ($segons < 3600) {
        $n = intdiv($segons, 60);
        return 'fa ' . $n . ($n === 1 ? ' minut' : ' minuts');
    }
    if ($segons < 86400) {
        $n = intdiv($segons, 3600);
        return 'fa ' . $n . ($n === 1 ? ' hora' : ' hores');
    }
    $n = intdiv($segons, 86400);
    return 'fa ' . $n . ($n === 1 ? ' dia' : ' dies');
}

/** True si encara no s'ha arribat a la data de tancament. */
function votacioOberta(string $tancament): bool
{
    $limit = strtotime($tancament);
    if ($limit === false) {
        return false;
    }
    return time() < $limit;
}

/** True si l'email ja és al fitxer de vots. */
function jaHaVotat(string $email): bool
{
    return array_key_exists($email, llegeixVots());
}

/** ['si' => 2, 'no' => 1, 'blanc' => 0] amb totes les opcions. */
function recompte(array $vots, array $opcions): array
{
    $resultat = [];
    foreach ($opcions as $clau => $text) {
        $resultat[$clau] = 0;
    }
    foreach ($vots as $opcio) {
        if (is_string($opcio) && array_key_exists($opcio, $resultat)) {
            $resultat[$opcio]++;
        }
    }
    return $resultat;
}

/** Guarda un missatge flash a la sessió. */
function afegeixMissatge(string $text): void
{
    $_SESSION['missatge'] = $text;
}

/** Retorna el missatge flash i l'esborra de la sessió. */
function llegeixMissatge(): ?string
{
    $missatge = $_SESSION['missatge'] ?? null;
    unset($_SESSION['missatge']);
    return is_string($missatge) ? $missatge : null;
}

/** Nombre d'usuaris amb rol votant. */
function totalVotants(array $usuaris): int
{
    $n = 0;
    foreach ($usuaris as $usuari) {
        if (($usuari['rol'] ?? '') === 'votant') {
            $n++;
        }
    }
    return $n;
}

/** Nombre de vots emesos per usuaris amb rol votant. */
function votsDeVotants(array $usuaris, array $vots): int
{
    $n = 0;
    foreach ($vots as $email => $opcio) {
        $usuari = buscaUsuari($usuaris, (string) $email);
        if ($usuari !== null && $usuari['rol'] === 'votant') {
            $n++;
        }
    }
    return $n;
}

/** Escapa text per mostrar-lo a l'HTML. */
function h(?string $text): string
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}
