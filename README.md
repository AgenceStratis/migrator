Migrator Component
==============
PHP Data Import/Export (CSV, Database, CMS).

Usage
---------

```php
use Stratis\Component\Migrator\Migrator;

$migrator = new Migrator('config.yml');
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
    delimiter: ","
```

* JSON file
```yaml
source/dest:
  type: json
  options:
    file: examples/processor/data.json
    pretty: false
    convert_unicode: false
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

Additionnal functions and utilites can be added via processors
Note: Some processors are only availables for Values or Fields

* Processor
```yaml
processors:
  fields/values:
    fieldName:
      processorName: arg
```

* Example
```yaml
processors:
  values:
    uid:
      mathAdd: 20
    title:
      subStr: [0, 20]
    price:
      mathMult: 1.5
      toInteger:
      mathAdd: 10
  fields:
  
```
