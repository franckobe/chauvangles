# GUIDE SET-UP

## Choix de techno
Nous avons décidé de choisir PHP car c'est une compétence que toute l'équipe partage. Le choix de symfony a été fait car il est un framework populaire sur lequel 
nous avions tous l'occasion de monter en compétence durant le projet.

## Php version
php 7.1.12

Server local: Laragon, mamp, wamp

## Installer les dépendances

    composer install
    
## Modifier le .env

    DATABASE_URL=mysql://root:@127.0.0.1:3306/slacklite
    
Avec l'url précédente on définit les paramètres suivants :    
    
>   DB_Name : root

>   DB_Password : ' '

>   DB : slacklite

## Générer la base de données

    php bin/console doctrine:database:create
 
## Migration des modèles de données

>   N oubliez pas de supprimer les migrations existantes "src/Migrations/Version[...]"
    
    php bin/console make:migration
    php bin/console doctrine:migrations:migrate

## Seed de données

    php bin/console doctrine:fixtures:load --append


## Lancer le projet

	php bin/console server:run
