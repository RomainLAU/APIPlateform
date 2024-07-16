# API Plateform

This is a Symfony API that allows waiters, barmen and bosses to manage data.

Only member of this project : Romain LAURENT

## ENV VARIABLES

`JWT_PASSPHRASE` = to generate with the command `php bin/console lexik:jwt:generate-keypair`
`DATABASE_URL` = some examples are present in the `.env` file so you can simply modify the one you want

## To start using the API

Run `composer require` to install all the dependencies

Run `php bin/console doctrine:database:create` to create the database

Run `symfony serve` to start the API

To be able to use the API you should create manually a user with a `ROLE_ADMIN` role so you can after create any users through the API
