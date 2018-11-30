# Korowai Framework

[![Build Status](https://travis-ci.org/korowai/framework.svg?branch=master)](https://travis-ci.org/korowai/framework)
[![Coverage Status](https://coveralls.io/repos/github/korowai/framework/badge.svg?branch=devel)](https://coveralls.io/github/korowai/framework?branch=devel)

Open Source software for exploring LDAP directories.

## Why Korowai?

The Korowai, also called the Kolufo, are the people who live in southeastern
West Papua in the Indonesian Province of Papua, close to the border with Papua
New Guinea. The majority of the Korowai clans live in tree houses on their
isolated territory.

Korowai life highly dependent on trees. Similarly, some admins' work depends on
LDAP database, which has tree structure.

## Notes for users

### Runtime Requirements

PHP>=7.0 is required. In addition to PHP, you may need to install these
packages

```shell
apt-get install php-xml php-mbstring
```

## Notes for developers

### Development requirements

- [docker](https://docker.com)

### Initial preparations

After you've just cloned

```shell
php bootstrap-dev && php composer install
```

### Running unit tests

```shell
php vendor/bin/phpunit
```

### Running integration tests

```shell
./docker-compose run --rm php-cli vendor/bin/behat
./docker-compose down
```

### Running CodeClimate

```shell
./docker-compose run --rm codeclimate analyze
./docker-compose down
```

### Generating api documentation

```shell
php phpdoc
```

The generated API docs go to ``build/docs/api/``.
