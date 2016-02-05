Migrator Component
==============
PHP Data Import/Export (CSV, Database, CMS).

Usage
---------

```php
use Stratis\Migrator\Migrator;

$migrator = new Migrator( 'config.yaml' );
$migrator->process();
```

Configuration file
---------

* CSV file
```yaml
source/dest:
  type: csv
  options:
    file: examples/processor/data.csv
```

* JSON file
```yaml
source/dest:
  type: json
  options:
    file: examples/processor/data.json
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
