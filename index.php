<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/functions.php';

print("MYGES CALENDAR SYNC (by Nospy)" . PHP_EOL);
printDivider();

print "Entrez le nombre de jours que vous souhaitez synchroniser à partir d'aujourd'hui : ";
$days = trim(fgets(STDIN));
if (!ctype_digit($days)) {
    die;
}

printDivider();
print "Connexion à myges..." . PHP_EOL;
$me = getMe();
print "Connecté !" . PHP_EOL;

printDivider();
printf("Récupération des cours sur %d jours..." . PHP_EOL, $days);
$agenda = getAgenda($days, $me);
printf("Réussi ! %d cours trouvés" . PHP_EOL, sizeof($agenda));

printDivider();
print "Connexion à l'API google..." . PHP_EOL;
$client = getCalendarClient();
print "Réussi !" . PHP_EOL;

printDivider();
printf("Récupération de la liste des cours présent sur le calendrier google sur %d jours..." . PHP_EOL, $days);
$events = getEvent($client, $days);
printf("Réussi ! %d cours trouvés" . PHP_EOL, sizeof($events));

printDivider();
print "Nettoyage des cours sur le calendrier google..." . PHP_EOL;
removeEvents($client, $events);
print "Les cours ont été supprimés de l'agenda google!" . PHP_EOL;

printDivider();
print "Ajout des cours sur le calendrier google..." . PHP_EOL;
addEvents($client, $agenda);
printDivider();
print "Les cours ont été ajoutés!" . PHP_EOL;

print "Finit." . PHP_EOL;