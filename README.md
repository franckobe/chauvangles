# GUIDE SET-UP

## Choix de techno
Nous avons décidé de choisir PHP car c'est une compétence que toute l'équipe partage. Le choix de symfony a été fait car il est un framework populaire sur lequel 
nous avions tous l'occasion de monter en compétence durant le projet.

## Versions

>  PHP 7.2.10
 
>  MySQL 5.7.23 
 
>  Apache 2.4.35

## Server local

Laragon, mamp, wamp

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
    
>   La table user est désormais remplie, le mdp pour chaque user est "toto"

## Lancer le projet

	php bin/console server:run

##JWT 
> Si jamais le login ne fonctionne pas avec une erreur sur le JWT il faut re-générer une paire de clefs

    openssl genrsa -out config/jwt/private.pem -aes256 4096
    
    openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
    
La passphrase qui est demandée lors de la génération des clefs doit être remplacée dans le fichier .env

    JWT_PASSPHRASE=mapassphrase
    
L'ensemble des messages renvoyés par l'api sont cryptés et encodé à l'aide de la variale d'environnement suivante:

    APP_SECRET
    
Pour décoder le JWT et analyser son contenu aucune clef ou passphrase n'est nécessaire il suffit de se rendre sur: 

    https://jwt.io/