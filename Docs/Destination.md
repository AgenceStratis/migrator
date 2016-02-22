Destination
===========

`dest` defines your output data type

### CLI

###### Example
```yaml
dest:
  type: cli
```


### CSV

###### Options
`string` **file** : Path to generated csv file\
`string` **delimiter** : Row values separator\
`string` **enclosure** : Strings enclosure\
`bool` **header** : Insert header in the first line

###### Example
```yaml
dest:
  type: csv
  options:
    file: file.csv
    delimiter: ","
    header: true
```


### JSON

###### Options
`string` **file** : Path to generated json file\
`bool` **pretty** : Readable output\
`bool` **convert_unicode** : Convert unicode characters

###### Example
```yaml
dest:
  type: json
  options:
    file: file.json
    pretty: true
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
`string` **insert_mode** : "insert", "replace", "not_exists"

###### Example
```yaml
dest:
  type: sql
  options:
    database_type: mysql
    database_name: io
    server: localhost
    username: root
    table: test
```