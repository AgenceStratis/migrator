Migrator Component
==================

Migrator is a data transfer PHP application, based on [ddeboer/data-import](https://github.com/ddeboer/data-import)


Install
-------

You can install Migrator with composer by adding this configuration to your **composer.json**

```json
{
  "repositories": [
    {
      "url": "https://github.com/AgenceStratis/migrator.git",
      "type": "git"
    }
  ],
  "require": {
    "agencestratis/migrator": "dev-master"
  }
}
```

Then you can download it with `composer install`


Configuration
-------------

Configuration files use YAML language\
It must include a [source](Source.md) and a [destination](Destination.md)

###### Example

```yaml
source:
  type: sql
  options:
    database_name: app
    username: root
    table: users

dest:
  type: csv
  options:
    file: users.csv
```

Usage
-----

```php
use Stratis\Component\Migrator\Migrator;

$migrator = new Migrator('config.yml');
$migrator->process();
```
