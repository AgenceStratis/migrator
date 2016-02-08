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

I/O formats must be specified, here are some examples:

* CSV file
```yaml
source/dest:
  type: csv
  options:
    file: examples/processor/data.csv
    header: true
    fields: [ ... ]
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

Additionnal functions and utilites can be added via processors:

* Value processor
```yaml
processors:
  values:
    - ?: [ ... ]
```

* Field processor
```yaml
processors:
  fields:
    - ?: [ ... ]
```
