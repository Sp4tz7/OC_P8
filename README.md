# OC_P8

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/6ef62ab4781a48b093f7eedc6be51602)](https://www.codacy.com/gh/Sp4tz7/OC_P8/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Sp4tz7/OC_P8&amp;utm_campaign=Badge_Grade) [![Build Status](https://travis-ci.com/Sp4tz7/OC_P8.svg?branch=main)](https://travis-ci.com/Sp4tz7/OC_P8)

This project is part of the 8th course of my OpenClassRooms course

- Enhance a Symfony framework
- Correct bugs and issues
- Add new functionalities
- Implement automatic tests

## Features

- Bind tasks to users
- Add user roles
- Manage authorizations
- Implement unit and functional tests
- Add a documentation
- Make an audit

### Requirements

In order to use this project, the following points must be respected

- PHP version >=7.3.12

### Installation

This project requires [PHP](https://php.net/) 7.3.12+ and [Composer](https://getcomposer.org/) to run.

Install the whole project from Github and run Composer vendors dependencies.

#### File

```sh
git clone https://github.com/Sp4tz7/OC_P8.git
cd OC_P8
composer install
```

### Configuration

Before running this project, you have to setup some configurations.

1. Copy the .env file to a .env.local file and edit the DB requested data.
3. Point your virtual host to the **Public** directory.

#### Install database

```
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load --append
```

#### Run automated tests

```
php bin/phpunit
```

[Link to the project api example](https://todoandco.siker.ch)

**Free Software, Hell Yeah!**
