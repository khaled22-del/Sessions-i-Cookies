# TASCA-04 · Votacions de l'institut

Usuaris de prova: `anna@lacetania.cat` / `daw2026` (votant), `tutora@lacetania.cat` / `admin2026` (admin).

## Preguntes de reflexió

**1. Per què guardem qui ha votat al servidor i no en una cookie `ha_votat`?**
Perquè la cookie la controla el navegador de l'usuari: la pot esborrar, editar o crear a mà amb les eines de desenvolupador, i podria votar tantes vegades com volgués (o dir que no ha votat). El fitxer de vots és al servidor i l'usuari no el pot tocar.

**2. Què passa si l'usuari canvia el valor de `darrera_visita`?**
És un valor que ve de l'usuari i no es pot fiar. Abans de fer-lo servir comprovo que sigui una cadena de dígits (`ctype_digit`), que sigui més gran que 0 i que no sigui una data futura. Si no ho compleix, es tracta com a primera visita i no surt cap Warning.

**3. Diferència entre el timeout de 5 minuts i els 30 dies de la cookie.**
El timeout de 5 minuts és per inactivitat i es controla al servidor: guardem `ultima_activitat` dins `$_SESSION` (dades al servidor) i, si ha passat el límit, es destrueix la sessió. Els 30 dies són la caducitat de la cookie `darrera_visita`, que es guarda al navegador de l'usuari i només serveix per recordar l'hora de l'última visita.

**4. Per què no podem mostrar a la mateixa petició l'hora que acabem de guardar a `darrera_visita`?**
Perquè `setcookie()` només envia una capçalera `Set-Cookie` a la resposta; `$_COOKIE` es omple amb les cookies que el navegador envia a la petició, així que no es veu el nou valor fins a la petició següent. Per això llegeixo la cookie abans i després la actualitzo.
