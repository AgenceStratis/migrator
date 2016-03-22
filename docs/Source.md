Source
======

`source` defines your input data

### CSV

###### Options
`string` **file** :  Path to csv file<br>
`string` **delimiter** : Value separator<br>
`bool` **header** : Use first line as header

###### Example
```yaml
source:
  type: csv
  options:
    file: file.csv
    delimiter: ","
    header: true
```


### JSON

###### Options
`string` **file** : Path to json file

###### Example
```yaml
source:
  type: json
  options:
    file: file.json
```


### SQL

###### Options
`string` **database_type** : mysql, sqlite...<br>
`string` **database_name**<br>
`string` **server**<br>
`string` **username**<br>
`string` **password**<br>
`string` **charset**<br>
`string` **table**<br>
`string` **query**

###### Example
```yaml
source:
  type: sql
  options:
    database_type: mysql
    database_name: io
    server: localhost
    username: root
    table: test
```