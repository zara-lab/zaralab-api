# ZaraLab API

A RESTfull API by [ZaraLab][zaralab] team.

## Requirements (development)

- running MySQL server 5+ (default port)
- PHP 5.4+
- PDO PHP extension (cli mode) - php5-mysql on Ubuntu/Debian distributions
- Xdebug PHP extension (cli mode) - php5-xdebug on Ubuntu/Debian distributions
- [Develo][develo] tool
- (optional) global installed [Composer][composer]

## Environment Setup

- Install MySQL (skip if already installed). Read the instructions for your operating system. For Ubuntu users

```bash
$ sudo apt-get install mysql-server mysql-client mysql-common
```

- Install PHP and required extensions (skip if already installed). Read the installation instructions for your operating system. For Ubuntu users:

```bash
$ sudo apt-get install php5 php5-mysql php5-xdebug
```

- Install [develo][develo]. Read manual installation instructions on the develo project repository. Using make:

```bash
$ git clone https://github.com/mignev/develo.git
$ cd develo
$ make install
```

- (optional) Install composer - [read this](http://askubuntu.com/questions/116960/global-installation-of-composer-manual).

## Project Setup

- Fork & Clone

```
$ git clone git@github.com:YOUR_USER/zaralab-api.git
$ cd zaralab-api
```

- Set some parameters:

```
$ cp app/config/parameters.php.dist app/config/parameters.php
$ vim app/config/parameters.php
```

Use your favorite editor in case you don't use `vim`.

The only parameters you need to set are `db.user` (your database user, most probably `root`, SHOULD have CREATE DATABASE permissions), `db.password` (your database user password).

- Run the setup script

```
$ develo setup
```

You should be all set!

[develo]: http://github.com/mignev/develo "develo tool"
[composer]: http://getcomposer.org/ "Composer - Dependency Manager for PHP"
[zaralab]: http://zaralab.org/ "ZaraLab Hackerspace Website"
