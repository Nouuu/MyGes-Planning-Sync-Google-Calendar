# MyGes Planning Sync Google Calendar

Ce script permet de récupérer votre planning présent sur myges et de le synchroniser à un calendrier google.

## Installation :

Version php utilisé : 7.4

* Téléchargez le projet.

* Lancez la commande : 
    ```bash
    composer install
    ```
    Pour installer les dépendances nécéssaires
    
* Renommez le fichier `env.php.example` en `env.php` et remplissez les informations :
    * `user_login` identifiant myges (ex: jdupond)
    * `user_password` mot de passe myges
    * `calendar_api_application_name` [Nom de l'API google calendar](#api-google)
    * `calendar_api_auth_config_file` [Nom du fichiers d'authentification à l'API google calendar.](#api-google)
    * `calendar_id`[ Id du calendrier google](#calendrier-google) (ex: vf2kq9ary690m8iee8ahv3e3d0@group.calendar.google.com)

## Lancement
Une fois le fichier `env.php` rempli, lancez (avec linux ou phpstorm de préférence, **evitez git bash !**) la commande : `php index.php`.

Le programme vous demandera le nombre de jours à synchroniser à partir d'aujourd'hui. 

![image](https://i.imgur.com/qojtmG6.png)

## API google

Pour faire fonctionner le script, vous devez créer un API google calendar et enregistrer ses informations comme ceci :

* Rendez vous sur [https://developers.google.com/calendar/quickstart/php](https://developers.google.com/calendar/quickstart/php)
* Créez un projet Google Calendar API (retenez bien le nom que vous donnez à votre projet pour le `env.php`) : 

|   |   |
|---|---|
|![image](https://i.imgur.com/xZkQC03.png) | ![image](https://i.imgur.com/QVQ6vH2.png) |
|![image](https://i.imgur.com/AmHIOfb.png)||

* Enregistrez le fichier `credentials.json` à la racine du projet :

![image](https://i.imgur.com/XxVO6z5.png)


## Calendrier google

>  :warning: Veillez bien à créer un calendrier dédié UNIQUEMENT à votre planning ! Sinon le script supprimera les autres évènements présents dans votre calendrier ...

Pour récupérer l'id de votre calendrier : 
* Rendez vous sur [google calendar](https://calendar.google.com)
* Allez dans "Paramètres et partage" du calendrier dédié à votre planning
![image](https://i.imgur.com/QAZPssf.png)
* Vous trouverez l'id de votre agenda dans la section "Intégrer l'agenda"
![image](https://i.imgur.com/1p0Ra2q.png)
