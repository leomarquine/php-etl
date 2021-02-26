# Wizaplace PHP ETL (WP-ETL)

## Coding Standards

Coding standards generally follow Symfony and PSR2, with these exceptions:

- There should be a space on either side of the string concatenation operator
- A standalone incrementer come after the variable
- Reference classes in the top level namespace directly (e.g., \PDO) rather than via use statements
- Use inline PHPDoc blocks when they contain just one tag (e.g. /** {@inheritdoc} */ and /** @var string[] */);

See .php_cs.dist for further information.

Your code must also pass scans via phpcs and phpstan. More details can be seen in their respective
configuration files.

## Testing

Test coverage should be supplied for all new and modified code. Where possible, use mocks if needed
for unit tests. If required to adequately test functionality, you may use Sqlite for testing, but this should be
avoided in most cases.

Test code should be considered first-class code and should comply with all coding standards discussed above.
