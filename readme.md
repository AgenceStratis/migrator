Migrator Component
==================

Migrator is a data transfer PHP application, based on [ddeboer/data-import](https://github.com/ddeboer/data-import)


Use as a library
----------------

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
    "AgenceStratis/migrator": "dev-master"
  }
}
```

Then you can download it with `composer install` and use it in your project


```php
use Stratis\Component\Migrator\Migrator;

$migrator = new Migrator('config.yml');
$migrator->process();
```


Use as an executable
--------------------

It's possible to create an application with [PHP Box](https://github.com/box-project/box2).

Just clone this repo and run `box.phar build`

After `migrator.phar` is built, you can now use it with YAML files as parameter

**Note:** All the files will be merged in one configuration



Configuration
-------------

Configuration files use YAML language<br>
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
