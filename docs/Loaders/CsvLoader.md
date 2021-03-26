# CsvLoader

CSV files generation.

```php
/** @var \Wizaplace\Etl\Loaders\CsvLoader $csvLoader */
$etl->load($csvLoader, 'outputFileName', $options);
```

## Options

### Delimiter

Field delimiter (one character only).

| Type   | Default value |
| ------ | ------------- |
| string | ;             |

```php
$options = [CsvLoader::DELIMITER => ';'];
```

### Enclosure

Enclosure character (one character only).

| Type   | Default value |
| ------ | ------------- |
| string | "             |

```php
$options = [CsvLoader::ENCLOSURE => '"'];
```

### Escaping character

Escaping character (one character only).

| Type   | Default value |
| ------ | ------------- |
| string | \\            |

```php
$options = [CsvLoader::ESCAPE_CHAR' => '\\'];
```

### Line per file

How many lines each file should contains at max (-1 = no limit).

If **no limit**, the loader will output in one file

> **Example**: outputFile.csv.

Else the loader will split the data into multiple files, with their names suffixed by the file's number.

> **Example**: outputFile_1.csv / outputFile_2.csv / ...

| Type    | Default value |
| ------- | ------------- |
| integer | -1            |

```php
$options = [CsvLoader::LINE_PER_FILE => 1000];
```
