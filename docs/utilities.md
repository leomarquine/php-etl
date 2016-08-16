# Utilities
```php
$job->utility($type, $options);
```
* `$type`: the type of the utility (command, etc).
* `$options`: an array containing the utility options.


## Command
### Syntax
```php
$job->utility('command', $options);
```
### Options
| Name | Type   | Default | Description |
| ---- | ------ |-------- | ----------- |
| command | string | null | Command to be executed. |
| commands | array | null | Array of commands to be executed. |

### Examples
Execute a command:
```php
$options = [
    'command' => 'cp /path/to/file.csv /new/path/file.csv'
];

$job->utility('command', $options);
```
Execute multiple commands:
```php
$options = [
    'commands' => [
        'cp /path/to/file.csv /new/path/file.csv',
        'chmod 777 /new/path/file.csv'
    ]
];

$job->utility('command', $options);
```
