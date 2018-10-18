#GUIDE SET-UP

<p>Créer une base de donnée nommée slacklite</p>
    
##Modifier le .env
<p>root est le user suivie du mot de passe (içi il n'y en a pas)</p>
	DATABASE_URL=mysql://root:@127.0.0.1:3306/slacklite
    

##Faire une migration
	php bin/console doctrine:migrations:migrate

##Installer / Lancer le projet
	composer install
	php bin/console server:run

