# GUIDE SET-UP

## Modifier le .env

    DATABASE_URL=mysql://root:@127.0.0.1:3306/slacklite
    
Avec l'url précédente on définit les paramètres suivants :    
    
>   DB_Name : root

>   DB_Password : ' '

>   DB : slacklite

## Générer la base de données

    php bin/console doctrine:database:create
 
## Migration des modèles de données

    php bin/console doctrine:migrations:migrate

## Seed de données

    php bin/console doctrine:fixtures:load --append

## Installer les dépendances

    composer install

## Lancer le projet

	php bin/console server:run
