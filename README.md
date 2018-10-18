#GUIDE SET-UP

## Modifier le .env

root est le user suivie du mot de passe (içi il n'y en a pas), slacklite est le nom donné à notre BDD
>   DATABASE_URL=mysql://root:@127.0.0.1:3306/slacklite

##Générer la base de données

 >  php bin/console doctrine:database:create
 
##Migration des modèles de données

>   php bin/console doctrine:migrations:migrate


