<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/functions.php';

print("MYGES CALENDAR SYNC (by Nospy)" . PHP_EOL);
printDivider();

$days = $argv[1];

while (!ctype_digit($days) && $days >= 0) {
    print "Entrez le nombre de jours que vous souhaitez synchroniser à partir d'aujourd'hui : ";
    $days = trim(fgets(STDIN));
}

printDivider();
print "Connexion à MyGES..." . PHP_EOL;
$me = getMe();
print "Connecté !" . PHP_EOL;

printDivider();
printf("Récupération des cours sur %d jours..." . PHP_EOL, $days);
$agenda = getAgenda($me, $days);
printf("Réussi ! %d cours trouvés" . PHP_EOL, sizeof($agenda));

print "Traitement des doublons..." . PHP_EOL;
$agenda = removeDuplicate($agenda);
printf("Réussi ! %d cours uniques" . PHP_EOL, sizeof($agenda));

printDivider();
print "Connexion à l'API google..." . PHP_EOL;
$client = getCalendarClient();
print "Connecté !" . PHP_EOL;

printDivider();
printf("Récupération de la liste des cours présent sur le calendrier google sur %d jours..." . PHP_EOL, $days);
$events = getEvent($client, $days);
printf("Réussi ! %d cours trouvés" . PHP_EOL, sizeof($events));

printDivider();
print "Nettoyage des cours sur le calendrier google..." . PHP_EOL;
batchRemoveEvents($client, $events);
print "Les cours ont été supprimés de l'agenda google!" . PHP_EOL;

printDivider();
print "Ajout des cours sur le calendrier google..." . PHP_EOL;
batchAddEvents($client, $agenda);
printDivider();
print "Les cours ont été ajoutés!" . PHP_EOL;

print "Finit." . PHP_EOL;