Source
======

`source` defines your input data

### CSV

###### Options
`string` **file** :  Path to csv file\
`string` **delimiter** : Value separator\
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
`string` **database_type** : mysql, sqlite...\
`string` **database_name**\
`string` **server**\
`string` **username**\
`string` **password**\
`string` **charset**\
`string` **table**\
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