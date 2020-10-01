<?php

use MyGes\Client;
use MyGes\Me;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/models/Course.php';
require_once __DIR__ . '/models/Room.php';

function getMe(): Me
{
    return new Me(getClient());
}

function getClient(): Client
{
    try {
        return new Client('skolae-app', user_login, user_password);
    } catch (MyGes\Exceptions\BadCredentialsException $e) {
        die($e->getMessage()); // bad credentials
    }
}

function getAgenda(int $days = 7, Me $me): array
{
    return $me->getAgenda(getDateStart()->getTimestamp() * 1000, getDateEnd($days)->getTimestamp() * 1000);
}

function getDateStart(): DateTime
{
    $date = new DateTime();
    $date->setTime(0, 0, 0);
    return $date;
}

function getDateEnd(int $days): DateTime
{
    $end = new DateTime();
    date_add($end, date_interval_create_from_date_string($days . ' days'));
    $end->setTime(23, 59, 59);
    return $end;
}

function removeDuplicate(array $agenda): array
{
    $new_agenda = [];
    $previous = null;
    foreach ($agenda as $course) {
        if ($course->reservation_id === $previous) {
            continue;
        }
        $previous = $course->reservation_id;
        $new_agenda[] = $course;
    }
    return $new_agenda;
}

function showAgenda(array $agenda)
{
    foreach ($agenda as $course) {

        $course = Course::fromObject($course);

        echo "ID : " . $course->reservation_id;
        echo ", Type : " . $course->type;

        echo ", Cours : " . $course->name;

        $start = new DateTime();
        $start->setTimestamp($course->start_date / 1000);
        $start->add(date_interval_create_from_date_string("2 hours"));
        echo ", Debut : " . $start->format("d-m-Y à H:i");

        $end = new DateTime();
        $end->setTimestamp($course->end_date / 1000);
        $end->add(date_interval_create_from_date_string("2 hours"));
        echo ", Fin : " . $end->format("d-m-Y à H:i");

        if (!empty($course->rooms)) {
            echo ", Salle(s) : ";

            $room_str = "";
            foreach ($course->rooms as $room) {
                $room_str .= ", " . $room->campus . " - " . $room->name;
            }
            echo trim($room_str, ", ");
        }

        if (!empty($course->teacher) && strlen($course->teacher) > 1) {
            echo ", Intervenant : " . $course->teacher;
        }

        printf(PHP_EOL);
    }
}

function getCourseResume(Course $course): string
{
    $str = "ID : " . $course->reservation_id;
    $str .= ", Type : " . $course->type;

    $str .= ", Cours : " . $course->name;

    $start = new DateTime();
    $start->setTimestamp($course->start_date / 1000);
    $start->add(date_interval_create_from_date_string("2 hours"));
    $str .= ", Debut : " . $start->format("d-m-Y à H:i");

    $end = new DateTime();
    $end->setTimestamp($course->end_date / 1000);
    $end->add(date_interval_create_from_date_string("2 hours"));
    $str .= ", Fin : " . $end->format("d-m-Y à H:i");

    if (!empty($course->rooms)) {
        $str .= ", Salle(s) : ";

        $room_str = "";
        foreach ($course->rooms as $room) {
            $room_str .= ", " . $room->campus . " - " . $room->name;
        }
        $str .= trim($room_str, ", ");
    }

    if (!empty($course->teacher) && strlen($course->teacher) > 1) {
        $str .= ", Intervenant : " . $course->teacher;
    }
    return $str;
}

function getCalendarClient(): Google_Client
{
//    mygescalendar
    $client = new Google_Client();
    $client->setApplicationName(calendar_api_application_name);
    $client->setScopes([
        Google_Service_Calendar::CALENDAR
    ]);
    $client->setAuthConfig(calendar_api_auth_config_file);
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }

        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }

        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }

    return $client;
}

function getEvent(Google_Client $client, $days = 7)
{
    $service = new Google_Service_Calendar($client);

// Print the next 10 events on the user's calendar.
    $optParams = array(
        'orderBy' => 'startTime',
        'singleEvents' => TRUE,
        'timeMin' => getDateStart()->format('c'),
        'timeMax' => getDateEnd($days)->format('c')
    );

    return $service->events->listEvents(calendar_id, $optParams);

    /*if (empty($events)) {
        print "No upcoming events found.\n";
    } else {
        print "Upcoming events:\n";
        foreach ($events as $event) {
            $start = $event->start->dateTime;
            if (empty($start)) {
                $start = $event->start->date;
            }
            printf("%s - %s - (%s)\n", $event->getSummary(), $event->getDescription(), $start);
        }
    }*/
}

function removeEvents(Google_Client $client, $events)
{
    $service = new Google_Service_Calendar($client);

    foreach ($events as $event) {
        $service->events->delete(calendar_id, $event->getId());
    }

    /*    foreach ($events as $event) {
            $start = $event->start->dateTime;
            if (empty($start)) {
                $start = $event->start->date;
            }
            printf("%s - %s - (%s)\n", $event->getSummary(), $event->getLocation(), $start);
        }*/
}

function getDateTimeForEvent($msTimestamp): string
{
    $date = DateTime::createFromFormat('U', $msTimestamp / 1000);
    return $date->format(DateTime::RFC3339);
}

function addEvents(Google_Client $client, array $agenda)
{
    $service = new Google_Service_Calendar($client);
    $calendarId = calendar_id;


    foreach ($agenda as $course) {
        $course = Course::fromObject($course);
        printf("Ajout du cours :%s%s" . PHP_EOL, PHP_EOL, getCourseResume($course));

        $event = new Google_Service_Calendar_Event();
        $event->setSummary($course->name);
        //TODO set location and color
//        $event->setLocation()

        $description = "";

        if (!empty($course->teacher) && strlen($course->teacher) > 1) {
            $description .= "<span>Intervenant : " . $course->teacher . "</span><br>";
        }

        if (!empty($course->rooms)) {
            $description .= "<span>Salle(s) :<ul>";

            foreach ($course->rooms as $room) {
                $description .= "<li>" . $room->campus . " - " . $room->name . "</li>";
            }
            $description .= "</ul></span>";
        }

        $event->setDescription($description);

        $start = new Google_Service_Calendar_EventDateTime();
        $start->setDateTime(getDateTimeForEvent($course->start_date));
        $event->setStart($start);

        $end = new Google_Service_Calendar_EventDateTime();
        $end->setDateTime(getDateTimeForEvent($course->end_date));
        $event->setEnd($end);

        $service->events->insert($calendarId, $event);
    }
}

function printDivider()
{
    print "-------------------------------------------------------------------------------------------------------------" .
        "--------------------------------------------------------------------------------" . PHP_EOL;
}