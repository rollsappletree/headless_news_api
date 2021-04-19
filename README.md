# Welcome to Headless News Api 👋
![Version](https://img.shields.io/badge/version-0.4beta-blue.svg?cacheSeconds=2592000)
[![Twitter: rollsappletree](https://img.shields.io/twitter/follow/rollsappletree.svg?style=social)](https://twitter.com/rollsappletree)

> This project expose some simple api's to handle an headless news cms. 
> 
> You will be able to: 
> * create users and assign them roles
> * list all news
> * list a news identified by ID (Iri)
> * create a news
> * update, replace, delete a news
> * list all news' comments
> * list a comment by id
> * create new comments for a news
> * update, replace, delete a comment 

# Tecnologies
## Api Platform
> This project uses [Api Platform](https://api-platform.com/) as it's base component. 
> 
> API Platform is a set of tools to build and consume web APIs that relies on Symfony Framework.
## Docker
> This project ships with a complete-working-out-of-the-box environment made with Docker.
> It consist of: 
> * PHP 8.0.3 fpm with Composer2, OpCache, ACPU and Xdebug already configured.
> * [Caddy web server](https://caddyserver.com/) with the [Mercure](https://api-platform.com/docs/core/mercure/) (real-time and async) and [Vulcain](https://vulcain.rocks/) (relations preloading) modules
> * Postgres DB
> 
> You can override the configuration (mainly ports etc) by modifying `docker-compose.override.yml` conf file.

## Install
The main part of this project is no the `api` directory. So switch to `api` and install all the stuff:
```sh
cd api
make first_run
```
This command will:
* Prepare Docker environment
* Start the container
* Prepare all the ssl certificates for JWT to work
* Create the DB and all the tables
* Load the fixtures

Then go to [https://localhost](https://localhost) and accept the self signed certificate. You will be prompted with the default API Platform page. 

Go to [https://localhost/docs](https://localhost/docs) for the OpenApi docs

nb: all the makefile scripts are in `api` directory
## Run tests

```sh
make run_tests
```

## Other Commands:
```sh
make start                     #Start docker
make stop                      #Stop docker
make bash                      #Open a bash into php container
make help                      #Show all commands infos
```

## Technical infos:
* [PHP Coding Standards Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
* [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/)

## TO DO
* [UUID](https://symfony.com/doc/current/components/uid.html)
* [DTO](https://api-platform.com/docs/core/dto/)
* [Timestampable](https://symfony.com/doc/4.1/doctrine/common_extensions.html)

## Author

👤 **Carmelo Badalamenti [aka rollsappletree]**

* Twitter: [@rollsappletree](https://twitter.com/rollsappletree)
* Github: [@rollsappletree](https://github.com/rollsappletree)
* LinkedIn: [@rollsappletree](https://linkedin.com/in/rollsappletree)

## Show your support

Give a ⭐️ if this project helped you!


***
_This README was generated with ❤️ by [readme-md-generator](https://github.com/kefranabg/readme-md-generator)_
