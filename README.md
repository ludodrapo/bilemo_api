<p align="center">
  <img src="bilemo_logo_black.png" alt="logo Bilemo" width="300"/>
</p>

Project number 7 from the OpenClassRooms cursus on PHP/Symfony developpement.

API Rest coded by Ludo Drapo with Symfony 5.3, PHP 7.4.20 and MySql 5.7

To "try it at home", you can download these files or clone this repository.

You'll have to create a ".env.local" file with the access to your database server like this
```
###> doctrine/doctrine-bundle ###
DATABASE_URL="mysql:/db_/user:db_password@127.0.0.1:8889/db_name?serverVersion=5.7"
###> doctrine/doctrine-bundle ###
```
Then run
```
% composer install
```
After that, to create the database and load the fixtures, just run
```
% composer prepare
```
You will then have to configure your lexik/jwt-authentication-bundle by first running
```
% php bin/console lexik:jwt:generate-keypair
```
And then moving the environment parameters in your ".env.local" file
```
###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your_own_passphrase
###< lexik/jwt-authentication-bundle ###
```
To test the API, choose a client email (for instance client2@gmail.com) and the password is ... "password" !

So send in Postman (or any other tool):
```
POST https://your.local.server.address/api/login-check
```
With a json raw body containing:
```
{
    "username": "client2@gmail.com",
    "password": "password"
}
```

To access the html documentation (using swagger.ui), go to:
```
https://your.local.server.address/api/doc
```


