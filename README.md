Migrator Component
==============
PHP Data Import/Export (CSV, Database, CMS).

Usage
---------

```php
use Stratis\Migrator\Migrator;

$migrator = new Migrator( 'config.yaml' );
```

Configuration file
---------

* CSV file
```yaml
source/dest:
  type: csv
  options:
    file: examples/processor/source.csv
```

* SQL Database
```yaml
source/dest:
  type: sql
  options:
    database_type: mysql
    database_name: io
    server: localhost
    username: root
    password: 
    charset: utf8
    table: test
```

* Processors
```yaml
processors:
  values:
  fields:
```
