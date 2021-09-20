<p align="center">
  <img src="bilemo_logo_black.png" alt="logo Bilemo" width="300"/>
</p>

Project number 7 from the OpenClassRooms cursus on PHP/Symfony developpement.

API Rest coded by Ludo Drapo with Symfony 5.3, PHP 7.4.20 and MySql 5.7

To "try it at home", you can download these files, or clone this repository.

You'll have to configure your .env.local with the access to your database server like this
```
###> doctrine/doctrine-bundle ###
DATABASE_URL="mysql:/db_/user:db_password@127.0.0.1:8889/db_name?serverVersion=5.7"
###> doctrine/doctrine-bundle ###
```
then run
```
% composer install
```
And after that, to create the database and load the fixtures, just run
```
% composer prepare
```
Finaly, you will have to configure your lexik/JWT-authentication-bundle  in your env.local too
```
###> symfony/mailer ###
MAILER_DSN= (...)
###> symfony/mailer ###
```
To access the documentation (using swagger.ui), go to "/api/doc"
