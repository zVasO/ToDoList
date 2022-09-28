# Todolist
Projet 8 of OpenClassrooms

## Context
This Todolist is a new version of Todolist, this version run with the latest version of Symfony, Php and follow the [Symfony Best Practices](https://symfony.com/doc/current/best_practices.html)

## About the app
[![SymfonyInsight](https://insight.symfony.com/projects/52e05407-5747-4b9d-b8b1-f4828cf67ee7/big.svg)](https://insight.symfony.com/projects/52e05407-5747-4b9d-b8b1-f4828cf67ee7)

![alt text](https://img.shields.io/badge/php-8.1-blue) ![alt text](https://img.shields.io/badge/Symfony-6.1.4-black) ![alt text](https://img.shields.io/badge/Twig-3.4.2-green) 


## Getting started
Install the dependencies
```sh
composer install
```
Create your own database "todolist" and edit the .env file.
Update your database schema 
```bash
php bin/console doctrine:migrations:migrate
```

Install the fixtures 
```bash
php bin/console doctrine:fixtures:load
```

Run the test and get the code coverage
```bash
php bin/phpunit --coverage-html public/test-coverage
```
