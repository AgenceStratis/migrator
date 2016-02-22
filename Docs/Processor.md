Processor
=========

`processors` are integrated functions that can change values and field names of your data


### Common

`mixed` **set** : Set the value to a new one

`string` **copy** : Copy a column value to another

`void` **delete** : Delete a column


### String

`string` **split** : Explodes a string in an array

`void` **upper_case** : Set a string to upper case

`array` **replace** : Replace a value by another

`void` **html_entity_decode** : Replace html entities by readable characters

`void` **strip_tags** : Removes HTML tags


### Numeric

`int` **add** : Add a value

`int` **sub** : Sub a value

`int` **mult** : Multiply a value

`int` **div** : Divide a value (cannot equal 0)


### Array

`void` **first** : Get the first item of an array

`void` **last** : Get the last item

`string` **join** : Join values with a custom delimiter

