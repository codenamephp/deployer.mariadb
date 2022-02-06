# deployer.mariadb

![Packagist Version](https://img.shields.io/packagist/v/codenamephp/deployer.mariadb)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/codenamephp/deployer.mariadb)
![Lines of code](https://img.shields.io/tokei/lines/github/codenamephp/deployer.mariadb)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/codenamephp/deployer.mariadb)
![CI](https://github.com/codenamephp/deployer.mariadb/workflows/CI/badge.svg)
![Packagist Downloads](https://img.shields.io/packagist/dt/codenamephp/deployer.mariadb)
![GitHub](https://img.shields.io/github/license/codenamephp/deployer.mariadb)

## What is it?

This package adds tasks to push, pull and copy mariadb databases along with various subtasks for dumping, importing etc.

## Installation

Easiest way is via composer. Just run `composer require codenamephp/deployer.mariadb` in your cli which should install the latest version for you.

## Usage

Use the included tasks in your deployer file. You need to add the database configuration to your hosts:

```php
$deployerFunctions->localhost()
  ->set('database', [
    'user' => 'application',
    'password' => 'application',
    'name' => 'application',
    'host' => 'database',
  ]);
```

All supported options are documented in the `\de\codenamephp\deployer\mariadb\database\factory\database\iDatabase::fromArray` interface.

For the copy tasks you need to have ssh agent forwarding enabled since the hosts try to connect directly using a local key. This key must have access to both
servers. This way, the copy can work without the servers knowing about each other.