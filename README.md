#GUIDE SET-UP

    Créer une base de donnée nommée slacklite
    
##Modifier le .env
root est le user suivie du mot de passe (içi il n'y en a pas)
>   DATABASE_URL=mysql://root:@127.0.0.1:3306/slacklite
    

##Faire une migration
>   php bin/console doctrine:migrations:migrate

#test#
