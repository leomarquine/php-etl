# Extractors
```php
$job->extract($type, $source, $options);
```
* `$type`: the type of the extractor (array, csv, etc).
* `$source`: the data source (path to a file, url or array).
* `$options`: an array containing the extractor options.


## Array
### Syntax
```php
$job->extract('array', $array, $options);
```
### Options
| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | null | Columns that will be extracted. |

### Examples
Extract all array columns:
```php
$job->extract('array', $array);
```
Extract specific columns:
```php
$options = [
    'columns' => ['id', 'name']
];

$job->extract('array', $array, $options);
```


## CSV
### Syntax
```php
$job->extract('csv', 'path/to/file.csv', $options);
```
### Options
| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | null | Columns that will be extracted. |
| delimiter | string | ';' | Delimiter that separates items. |
| enclosure | string | '"' | The value enclosure. |

### Examples
Extract from a CSV file with columns header:
```php
$job->extract('csv', 'path/to/file.csv');
```
Extract from a CSV file using custom columns:
```php
$options = [
    'columns' => [
        'id' => 1, // Index of the column. The first column is 1.
        'name' => 2
    ]
];

$job->extract('csv', 'path/to/file.csv', $options);
```


## Fixed Width
### Syntax
```php
$job->extract('fixedWidth', 'path/to/file.txt', $options);
```
### Options
| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | null | Columns that will be extracted. |

### Examples
Extract from a fixed width text file:
```php
$options = [
    'columns' => [
        'id' => [0, 5], // Start position and length of column.
        'name' => [5, 40],
    ]
];

$job->extract('fixedWidth', 'path/to/file.txt', $options);
```


## Json
### Syntax
```php
$job->extract('json', 'path/to/file.json', $options);
```
### Options
| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | null | Columns that will be extracted. |

### Examples
Extract from a Json file:
```php
$job->extract('json', 'path/to/file.json');
```
Extract from a Json file with custom attributes path:
```php
$options = [
    'columns' => [
        'id' => '$..bindings[*].id.value',
        'name' => '$..bindings[*].name.value'
    ]
];

$job->extract('json', 'path/to/file.json', $options);
```


## Query
### Syntax
```php
$job->extract('query', 'select * from table', $options);
```
### Options
| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | null | Columns that will be extracted. |
| connection | string | 'default' | Name of the database connection to use. |

### Examples
Extract from a database table using a custom query:
```php
$query = 'select * from users';

$job->extract('query', $query);
```
Extract from a database table using a custom query and bindings:
```php
$query = 'select * from users where status = ?';
$options = [
    'bindings' => ['active']
];

$job->extract('query', $query, $options);
```


## Table
### Syntax
```php
$job->extract('table', 'table_name', $options);
```
### Options
| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | null | Columns that will be extracted. |
| connection | string | 'default' | Name of the database connection to use. |
| where | array | [] | Array of where clause. For Example: ['status' => 'active']. |

### Examples
Extract from a database table:
```php
$job->extract('table', 'table_name');
```
Extract specific columns from a database table and a where clause:
```php
$ooptions = [
    'columns' => ['id', 'nome'],
    'where' => ['status' => 'active']
];

$job->extract('table', 'table_name', $options);
```


## XML
### Syntax
```php
$job->extract('xml', 'path/to/file.xml', $options);
```
### Options
| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | null | Columns that will be extracted. |
| loop | string | '/' | The path to loop. |

### Examples
Extract from a XML file:
```php
$job->extract('xml', 'path/to/file.xml');
```
Extract from a XML file with custom attributes and loop path:
```php
$options = [
    'columns' => [
        'id' => 'id/value',
        'name' => 'name/value',
    ],
    'loop' => '/users/user'
];

$job->extract('xml', 'path/to/file.xml', $options);
```
